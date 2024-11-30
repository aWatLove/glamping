<?php

namespace App\Http\Controllers;

use App\Http\Resources\OptionsResource;
use App\Models\Option;
use Illuminate\Http\Request;

class OptionController extends Controller
{
    public function __construct()
    {
        // Ограничиваем доступ к методам по ролям
        $this->middleware('auth:sanctum')->only(['create', 'update', 'destroy']);
    }

    // Получить все опции
    public function index()
    {

        $option = Option::where('is_del', false)->orderBy('updated_at', 'desc')->get();
        return OptionsResource::collection($option);
    }

    // Получить одну опцию по ID
    public function show($id)
    {
        $option = Option::findOrFail($id);
        return new OptionsResource($option);
    }

    // Создание новой опции (только для пользователей, менеджеров и админов)
    public function store(Request $request)
    {
        $this->authorize('create', Option::class); // Проверка на роль

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'price' => 'required|numeric',
            'count' => 'required|numeric|min:1',
        ]);

        $option = Option::create($validated);
        return new OptionsResource($option);
    }

    // Обновление опции (только для менеджеров и админов)
    public function update(Request $request, $id)
    {
        $this->authorize('update', Option::class); // Проверка на роль

        $option = Option::findOrFail($id);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'price' => 'required|numeric',
            'count' => 'required|numeric|min:1',
        ]);

        $option->update($validated);
        return new OptionsResource($option);
    }

    // Удаление опции (только для админов)
    public function destroy($id)
    {
        $this->authorize('delete', Option::class); // Проверка на роль

        $option = Option::findOrFail($id);

        $option->is_del = true;
        $option->save();
        return response()->json(['message' => 'Option deleted successfully']);
    }
}
