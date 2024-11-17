<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{

    public function __construct()
    {
        // Ограничиваем доступ к методам по ролям
        $this->middleware('auth:sanctum')->only(['show', 'create', 'update', 'destroy']);
    }

    public function index()
    {
        $this->authorize('show', Order::class); // Проверка на роль
        return Order::all();
    }

    public function show($id)
    {
        $this->authorize('show', Order::class); // Проверка на роль
        return Order::findOrFail($id);
    }

    public function store(Request $request)
    {
        $this->authorize('create', Order::class); // Проверка на роль

        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'place_id' => 'required|exists:places,id',
            'date_start' => 'required|date',
            'date_end' => 'required|date|after_or_equal:date_start',
            'days_count' => 'required|integer',
            'status' => 'required|string',
            'payment_status' => 'required|string',
            'total_price' => 'required|numeric',
        ]);
        return Order::create($validated);
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
