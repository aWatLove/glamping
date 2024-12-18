<?php

namespace App\Http\Controllers;

use App\Http\Resources\TariffsResourceForManagers;
use App\Http\Resources\TariffsResourceForUsers;
use App\Models\Tariff;
use Illuminate\Http\Request;

class TariffController extends Controller
{
    public function __construct()
    {
        // Ограничиваем доступ к методам по ролям
        $this->middleware('auth:sanctum')->only(['show','create', 'update', 'destroy', 'getTariffBookingById']);
    }

    public function index()
    {
        $this->authorize('show', Tariff::class); // Проверка на роль
        return Tariff::where('is_del', false)->get();
    }

    public function show($id)
    {
        $this->authorize('show', Tariff::class); // Проверка на роль

        $tariff = Tariff::findOrFail($id);
        return $tariff;
    }

    public function store(Request $request)
    {
        $this->authorize('create', Tariff::class); // Проверка на роль

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'price_per_day' => 'required|numeric',
            'photo' => 'required|string',
            'base_id' => 'required|exists:bases,id',
        ]);

        $tariff = Tariff::create($validated);
        return response()->json($tariff, 201);
    }

    public function update(Request $request, $id)
    {
        $this->authorize('update', Tariff::class); // Проверка на роль

        $tariff = Tariff::findOrFail($id);
        $validated = $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'description' => 'sometimes|required|string',
            'price_per_day' => 'sometimes|required|numeric',
            'photo' => 'sometimes|required|string',
            'base_id' => 'sometimes|required|exists:bases,id',
            'is_del' => 'sometimes|required|boolean',
        ]);
        $tariff->update($validated);
        return $tariff;
    }

    public function destroy($id)
    {
        $this->authorize('delete', Tariff::class); // Проверка на роль

        $tariff = Tariff::findOrFail($id);
        $tariff->update(['is_del' => true]);
        return response(null, 204);
    }

    public function getAllByBaseId($base_id)
    {
        $tariff = Tariff::where('base_id', $base_id)->get();

        if (!$tariff) {
            return response()->json([
                'message' => "Tariff with base_ID $base_id not found.",
            ], 404); // Если тариф не найден, возвращаем ошибку
        }

        return $tariff;
    }

    public function getTariffBookingById($id)
    {
        $this->authorize('getTariffBookingById', Tariff::class); // Проверка на роль
        $tariff = Tariff::findOrFail($id);

        if (!$tariff) {
            return response()->json([
                'message' => "Tariff with ID $id not found.",
            ], 404); // Если тариф не найден, возвращаем ошибку
        }
        if(auth()->user()->role === 'admin'){

            return new TariffsResourceForManagers($tariff);
        }

            return new TariffsResourceForUsers($tariff);

    }
}
