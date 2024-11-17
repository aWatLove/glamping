<?php

namespace App\Http\Controllers;

use App\Models\Place;
use Illuminate\Http\Request;

class PlaceController extends Controller
{
    public function __construct()
    {
        // Ограничиваем доступ к методам по ролям
        $this->middleware('auth:sanctum')->only(['create', 'update', 'destroy']);
    }

    public function index()
    {
        return response()->json(Place::all());
    }

    public function show($id)
    {
        $place = Place::find($id);

        if (!$place) {
            return response()->json(['message' => 'Place not found'], 404);
        }

        return response()->json($place);
    }

    public function store(Request $request)
    {
        $this->authorize('create', Place::class); // Проверка на роль

        $validated = $request->validate([
            'title' => 'required|string',
            'description' => 'nullable|string',
            'coordinatex' => 'required|numeric',
            'coordinatey' => 'required|numeric',
            'photo' => 'nullable|string',
            'base_id' => 'required|exists:bases,id',
            'tariffs_limit' => 'required|integer',
            'is_del' => 'boolean',
        ]);

        $place = Place::create($validated);

        return response()->json($place, 201);
    }

    public function update(Request $request, $id)
    {
        $this->authorize('update', Place::class); // Проверка на роль
        $place = Place::find($id);

        if (!$place) {
            return response()->json(['message' => 'Place not found'], 404);
        }

        $validated = $request->validate([
            'title' => 'required|string',
            'description' => 'nullable|string',
            'coordinatex' => 'required|numeric',
            'coordinatey' => 'required|numeric',
            'photo' => 'nullable|string',
            'base_id' => 'required|exists:bases,id',
            'tariffs_limit' => 'required|integer',
            'is_del' => 'boolean',
        ]);

        $place->update($validated);

        return response()->json($place);
    }

    public function destroy($id)
    {
        $this->authorize('delete', Place::class); // Проверка на роль
        $place = Place::find($id);

        if (!$place) {
            return response()->json(['message' => 'Place not found'], 404);
        }

        $place->delete();

        return response()->json(['message' => 'Place deleted']);
    }
}
