<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Resources\BasesResource;
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

        $bases = Base::where('is_del', false)->orderBy('updated_at', 'desc')->get();

        // Возвращаем их в формате Resource
        return BasesResource::collection($bases);
    }

    public function show($id)
    {
        $this->authorize('show', Base::class); // Проверка на роль

        $base = Base::find($id);

        if (!$base) {
            return response()->json([
                'message' => "Base with ID $id not found.",
            ], 404); // HTTP 404 Not Found
        }
        return new BasesResource($base);
    }

    public function store(Request $request)
    {
        $this->authorize('create', Base::class); // Проверка на роль

        $validated = $request->validate([
            'title' => 'required|string',
            'coordinate_x' => 'required|numeric',
            'coordinate_y' => 'required|numeric',
        ]);

        return new BasesResource(Base::create($validated));
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

        return new BasesResource($base);
    }

    public function destroy($id)
    {
        $this->authorize('delete', Base::class); // Проверка на роль

        $base = Base::findOrFail($id);

        $base->is_del = true;
        $base->save();

        return response()->json(['message' => 'Base deleted successfully']);
    }
}
