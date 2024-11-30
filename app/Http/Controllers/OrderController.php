<?php

namespace App\Http\Controllers;

use App\Http\Resources\OrderResource;
use App\Models\Option;
use App\Models\Order;
use App\Models\OrderTariff;
use App\Models\Place;
use App\Models\Tariff;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{

    public function __construct()
    {
        // Ограничиваем доступ к методам по ролям
        $this->middleware('auth:sanctum')->only(['show', 'create', 'update', 'destroy', 'adminRules', 'pay', 'cancel']);
    }

    public function index()
    {
        $this->authorize('show', Order::class); // Проверка на роль


        if (auth()->user()->role === 'admin') {
            $orders = Order::orderBy('updated_at', 'desc')->get();

            return OrderResource::collection($orders);
        }

        $userId = auth()->id();

        $orders = Order::where('user_id', $userId)->orderBy('updated_at', 'desc')->get();

        return OrderResource::collection($orders);
    }

    public function getOrdersByUser($id)
    {
        $this->authorize('adminRules', Order::class);

        // Получаем все заказы, у которых user_id совпадает с переданным в параметре id
        $orders = Order::where('user_id', $id)->orderBy('updated_at', 'desc')->get();

        // Возвращаем их в формате Resource
        return OrderResource::collection($orders);
    }

    public function show($id)
    {
        $this->authorize('show', Order::class); // Проверка на роль

        $order = Order::find($id);

        if (!$order) {
            return response()->json([
                'message' => "Order with ID $id not found.",
            ], 404); // HTTP 404 Not Found
        }

        // Проверка, если пользователь не администратор, то он может просматривать только свои заказы
        if (auth()->user()->role !== 'admin' && auth()->user()->id !== $order->user_id) {
            return response()->json([
                'message' => "You are not authorized to view this order.",
            ], 403); // HTTP 403 Forbidden
        }

        return new OrderResource($order);
    }

    public function store(Request $request)
    {
        $this->authorize('create', Order::class); // Проверка на роль

        $userId = auth()->id(); // достаем user_id из токена

        // Валидация данных
        $validatedData = $request->validate([
            'place_id' => 'required|exists:places,id',
            'days_count' => 'required|integer|min:1',
            'tariff_ids' => 'required|array|min:1',
            'tariff_ids.*' => 'integer|exists:tariffs,id',
            'optional_ids' => 'required|array',
            'optional_ids.*.id' => 'required|integer|exists:options,id',
            'optional_ids.*.count' => 'required|integer|min:1',
            'date_start' => 'required|date',
            'date_end' => 'required|date|after:date_start',
        ]);

        // Получаем лимит для места
        $place = Place::find($validatedData['place_id']);
        $tariffLimit = $place->tariffs_limit;

        // Считаем текущее количество бронирований для всех переданных тарифов на указанное место и даты
        $currentBookings = DB::table('order_tariffs')
            ->where('place_id', $validatedData['place_id'])
            ->where(function ($query) use ($validatedData) {
                $query->whereBetween('date_start', [$validatedData['date_start'], $validatedData['date_end']])
                    ->orWhereBetween('date_end', [$validatedData['date_start'], $validatedData['date_end']])
                    ->orWhere(function ($query) use ($validatedData) {
                        $query->where('date_start', '<=', $validatedData['date_start'])
                            ->where('date_end', '>=', $validatedData['date_end']);
                    });
            })
            ->count();

        // Считаем количество новых тарифов
        $newTariffsCount = count($validatedData['tariff_ids']);

        // Проверяем, превышает ли общее количество бронирований лимит
        if (($currentBookings + $newTariffsCount) > $tariffLimit) {
            return response()->json([
                'message' => "Booking exceeds the tariff limit for this place. Maximum allowed: $tariffLimit.",
            ], 422);
        }

        // Проверка, что все опции доступны в указанном количестве
        foreach ($validatedData['optional_ids'] as $optionData) {
            $option = Option::find($optionData['id']);
            if ($option->count < $optionData['count']) {
                return response()->json([
                    'message' => "Option ID {$optionData['id']} is not available in the requested quantity.",
                ], 422);
            }
        }

        // Создание заказа
        $order = Order::create([
            'user_id' => $userId,
            'place_id' => $validatedData['place_id'],
            'date_start' => $validatedData['date_start'],
            'date_end' => $validatedData['date_end'],
            'days_count' => $validatedData['days_count'],
            'status' => 'Обрабатывается',
            'payment_status' => 'Не оплачено',
            'total_price' => 0,
        ]);

        // Привязка тарифов к заказу
        $tariffs = Tariff::whereIn('id', $validatedData['tariff_ids'])->get();
        foreach ($tariffs as $tariff) {
            $order->tariffs()->attach($tariff->id, [
                'place_id' => $validatedData['place_id'],
                'date_start' => $validatedData['date_start'],
                'date_end' => $validatedData['date_end'],
                'status' => 'Обрабатывается',
            ]);
            $order->total_price += $tariff->price_per_day * $validatedData['days_count'];
        }

        // Привязка опций к заказу
        foreach ($validatedData['optional_ids'] as $optionData) {
            $option = Option::find($optionData['id']);
            $order->options()->attach($option->id, [
                'count' => $optionData['count'],
            ]);
            $order->total_price += $option->price * $optionData['count'];

            // Уменьшение доступного количества опций
            $option->count -= $optionData['count'];
            $option->save();
        }

        // Сохранение итоговой цены заказа
        $order->save();

        return new OrderResource($order);
    }


    public function update(Request $request, $id)
    {
        $this->authorize('update', Order::class); // Проверка на роль
        $order = Order::findOrFail($id);
        $validated = $request->validate([
            'date_start' => 'sometimes|required|date',
            'date_end' => 'sometimes|required|date|after_or_equal:date_start',
            'days_count' => 'sometimes|required|integer',
            'status' => 'sometimes|required|string',
            'payment_status' => 'sometimes|required|string',
            'total_price' => 'sometimes|required|numeric',
        ]);
        $order->update($validated);
        return $order;
    }

    public function destroy($id)
    {
        $this->authorize('delete', Order::class); // Проверка на роль
        $order = Order::findOrFail($id);
        $order->delete();
        return response(null, 204);
    }


    public function process($id, Request $request)
    {
        // Проверка на роль (admin)
        $this->authorize('adminRules', Order::class);

        // Находим заказ по ID
        $order = Order::find($id);

        if (!$order) {
            return response()->json([
                'message' => "Order with ID $id not found.",
            ], 404); // Если заказ не найден, возвращаем ошибку
        }

        // Проверяем, что статус передан в запросе
        $status = $request->query('status');

        // Проверка на валидность статуса
        $validStatuses = [
            'Обрабатывается',
            'В пути',
            'Доставлено',
            'Отменено',
            'Ошибка',
        ];

        if (!in_array($status, $validStatuses)) {
            return response()->json([
                'message' => "Invalid status provided.",
            ], 422); // Если статус невалидный, возвращаем ошибку
        }

        // Обновляем статус заказа
        $order->status = $status;
        $order->save();

        // Возвращаем успешный ответ с новым статусом
        return response()->json([
            'message' => "Order status updated successfully.",
            'order' => new OrderResource($order),
        ], 200);
    }

    // Метод для изменения статуса тарифа в заказе
    public function updateTariffStatus(Request $request, $orderId, $tariffId)
    {
        $this->authorize('adminRules', Order::class);

        $validStatuses = [
            'Обрабатывается',
            'В пути',
            'Доставлено',
            'Отменено',
            'Ошибка',
        ];

        // Проверяем, что статус передан в запросе
        $status = $request->query('status');
        if (!in_array($status, $validStatuses)) {
            return response()->json([
                'message' => 'Invalid status provided.',
            ], 422); // Статус невалидный
        }

        // Находим заказ по ID
        $order = Order::find($orderId);
        if (!$order) {
            return response()->json([
                'message' => "Order with ID $orderId not found.",
            ], 404); // Заказ не найден
        }

        // Находим тариф в таблице order_tariffs для указанного заказа и тарифа
        $orderTariff = OrderTariff::where('order_id', $orderId)
            ->where('tariff_id', $tariffId)
            ->first();

        if (!$orderTariff) {
            return response()->json([
                'message' => "Tariff with ID $tariffId not found in order with ID $orderId.",
            ], 404); // Тариф не найден в заказе
        }

        // Обновляем статус тарифа
        $orderTariff->status = $status;
        $orderTariff->save();

        return response()->json([
            'message' => "Tariff status updated successfully.",
            'order_tariff' => new OrderResource($order), // Возвращаем обновленные данные о тарифе
        ]);
    }

    // Метод для отмены заказа
    public function cancel(Request $request, $orderId)
    {

        $this->authorize('cancel', Order::class);

        $order = Order::find($orderId);

        if (!$order) {
            return response()->json([
                'message' => "Order with ID $orderId not found.",
            ], 404); // Если заказ не найден
        }

        // Проверяем, является ли пользователь владельцем заказа или администратором
        if (auth()->user()->role !== 'admin') {
            if ($order->user_id !== auth()->id()) {
                return response()->json([
                    'message' => "You are not authorized to cancel this order.",
                ], 403); // Если пользователь не владелец и не администратор
            }
        }


        // Проверяем, что заказ можно отменить (до даты начала)
        if (Carbon::parse($order->date_start)->isPast()) {
            return response()->json([
                'message' => "The order cannot be canceled after the start date.",
            ], 422); // Если дата начала уже прошла
        }

        // Отменяем заказ
        $order->status = 'Отменен';
        $order->save();

        return response()->json([
            'message' => "Order with ID $orderId has been successfully canceled.",
            'order' => new OrderResource($order), // Возвращаем данные обновленного заказа
        ]);
    }

    // Метод для обработки мока платёжного шлюза
    public function pay(Request $request, $orderId)
    {

        $this->authorize('pay', Order::class);


        // Найдем заказ по ID
        $order = Order::find($orderId);

        if (!$order) {
            return response()->json([
                'message' => "Order with ID $orderId not found.",
            ], 404); // Если заказ не найден
        }

        // Проверяем, что заказ не был отменен или уже оплачен
        if ($order->status === 'Отменен') {
            return response()->json([
                'message' => "Order with ID $orderId is canceled and cannot be paid.",
            ], 422); // Если заказ отменен
        }

        if ($order->payment_status === 'Оплачено') {
            return response()->json([
                'message' => "Order with ID $orderId has already been paid.",
            ], 422); // Если заказ уже оплачен
        }

        // Проверка на то, что текущий пользователь является владельцем заказа или администратором
        if ($order->user_id !== Auth::id()) {
            return response()->json([
                'message' => "You are not authorized to pay this order.",
            ], 403);
        }

        // Мокируем успешную оплату
        $order->payment_status = 'Оплачено';
        $order->save();

        return response()->json([
            'message' => "Order with ID $orderId has been successfully paid.",
            'order' => new OrderResource($order), // Возвращаем данные обновленного заказа
        ]);
    }
}
