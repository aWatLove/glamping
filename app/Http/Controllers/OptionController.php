<?php

namespace App\Http\Controllers;

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
        return Option::where('is_del', false)->get();
    }

    // Получить одну опцию по ID
    public function show($id)
    {
        $option = Option::findOrFail($id);
        return $option;
    }

    // Создание новой опции (только для пользователей, менеджеров и админов)
    public function store(Request $request)
    {
        $this->authorize('create', Option::class); // Проверка на роль

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'price' => 'required|numeric',
            'is_del' => 'nullable|boolean',
        ]);

        $option = Option::create($validated);
        return response()->json($option, 201);
    }

    // Обновление опции (только для менеджеров и админов)
    public function update(Request $request, $id)
    {
        $this->authorize('update', Option::class); // Проверка на роль

        $option = Option::findOrFail($id);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'price' => 'required|numeric',
            'is_del' => 'nullable|boolean',
        ]);

        $option->update($validated);
        return response()->json($option);
    }

    // Удаление опции (только для админов)
    public function destroy($id)
    {
        $this->authorize('delete', Option::class); // Проверка на роль

        $option = Option::findOrFail($id);
        $option->delete();
        return response()->json(['message' => 'Option deleted successfully']);
    }
}
