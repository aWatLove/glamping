<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Base;
use Illuminate\Http\Request;

class BasesController extends Controller
{
    public function __construct()
    {
        // Ограничиваем доступ к методам по ролям
        $this->middleware('auth:sanctum')->only(['show', 'create', 'update', 'destroy']);
    }

    public function index()
    {
        $this->authorize('show', Base::class); // Проверка на роль

        return Base::all();
    }

    public function show($id)
    {
        $this->authorize('show', Base::class); // Проверка на роль

        return Base::findOrFail($id);
    }

    public function store(Request $request)
    {
        $this->authorize('create', Base::class); // Проверка на роль

        $validated = $request->validate([
            'title' => 'required|string',
            'coordinate_x' => 'required|numeric',
            'coordinate_y' => 'required|numeric',
        ]);

        return Base::create($validated);
    }

    public function update(Request $request, $id)
    {
        $this->authorize('update', Base::class); // Проверка на роль

        $base = Base::findOrFail($id);

        $validated = $request->validate([
            'title' => 'required|string',
            'coordinate_x' => 'required|numeric',
            'coordinate_y' => 'required|numeric',
        ]);

        $base->update($validated);

        return $base;
    }

    public function destroy($id)
    {
        $this->authorize('delete', Base::class); // Проверка на роль

        $base = Base::findOrFail($id);
        $base->delete();

        return response()->json(['message' => 'Base deleted successfully']);
    }
}
