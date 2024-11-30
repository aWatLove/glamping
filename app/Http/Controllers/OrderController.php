<?php

namespace App\Http\Controllers;

use App\Http\Resources\OrderResource;
use App\Models\Option;
use App\Models\Order;
use App\Models\Tariff;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{

    public function __construct()
    {
        // Ограничиваем доступ к методам по ролям
        $this->middleware('auth:sanctum')->only(['show', 'create', 'update', 'destroy', 'adminRules']);
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
            'tariff_ids' => 'required|array',
            'tariff_ids.*' => 'integer|exists:tariffs,id',
            'optional_ids' => 'required|array',
            'optional_ids.*.id' => 'required|integer|exists:options,id',
            'optional_ids.*.count' => 'required|integer|min:1',
            'date_start' => 'required|date',
            'date_end' => 'required|date|after:date_start',
        ]);

        // Проверка на пересечение дат для тарифов
        foreach ($validatedData['tariff_ids'] as $tariffId) {
            $isOccupied = DB::table('order_tariffs')
                ->where('tariff_id', $tariffId)
                ->where('place_id', $validatedData['place_id'])
                ->where(function ($query) use ($validatedData) {
                    $query->whereBetween('date_start', [$validatedData['date_start'], $validatedData['date_end']])
                        ->orWhereBetween('date_end', [$validatedData['date_start'], $validatedData['date_end']])
                        ->orWhere(function ($query) use ($validatedData) {
                            $query->where('date_start', '<=', $validatedData['date_start'])
                                ->where('date_end', '>=', $validatedData['date_end']);
                        });
                })
                ->exists();

            if ($isOccupied) {
                return response()->json([
                    'message' => "Tariff ID $tariffId is already booked for the selected dates.",
                ], 422);
            }
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
            'status' => 'pending',
            'payment_status' => 'unpaid',
            'total_price' => 0,
        ]);

        // Привязка тарифов к заказу
        $tariffs = Tariff::whereIn('id', $validatedData['tariff_ids'])->get();
        foreach ($tariffs as $tariff) {
            $order->tariffs()->attach($tariff->id, [
                'place_id' => $validatedData['place_id'],
                'date_start' => $validatedData['date_start'],
                'date_end' => $validatedData['date_end'],
                'status' => 'pending',
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
}
