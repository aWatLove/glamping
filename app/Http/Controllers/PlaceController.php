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

    public function getPlacesByArround(Request $request)
    {
        // Получаем параметры из запроса
        $Xcoord = $request->query('Xcoord');
        $Ycoord = $request->query('Ycoord');
        $Radius = $request->query('Radius');

        // Получаем все места
        $places = Place::all();

        // Преобразуем координаты в радианы
        $lat1 = deg2rad($Xcoord);
        $lon1 = deg2rad($Ycoord);

        // Фильтруем места
        $filteredPlaces = $places->filter(function ($place) use ($lat1, $lon1, $Radius) {
            // Преобразуем координаты места в радианы
            $lat2 = deg2rad((float)$place->XCoordinate);
            $lon2 = deg2rad((float)$place->YCoordinate);


            // Расстояние
            $distance = $this->haversine($lat1, $lon1, $lat2, $lon2);

            // Возвращаем только места, расстояние до которых меньше или равно радиусу
            return $distance <= $Radius;
        });

        return response()->json($filteredPlaces->values());
    }

    function haversine($lat1, $lon1,
                       $lat2, $lon2)
    {
        // distance between latitudes
        // and longitudes
        $dLat = ($lat2 - $lat1) *
            M_PI / 180.0;
        $dLon = ($lon2 - $lon1) *
            M_PI / 180.0;

        // convert to radians
        $lat1 = ($lat1) * M_PI / 180.0;
        $lat2 = ($lat2) * M_PI / 180.0;

        // apply formulae
        $a = pow(sin($dLat / 2), 2) +
            pow(sin($dLon / 2), 2) *
            cos($lat1) * cos($lat2);
        $rad = 6371;
        $c = 2 * asin(sqrt($a));
        return $rad * $c;
    }
}
