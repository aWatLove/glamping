<?php

namespace App\Http\Controllers;

use App\Http\Resources\PlaceArroundResource;
use App\Http\Resources\PlaceResource;
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
        $places = Place::all();
        return PlaceResource::collection($places);
    }

    public function show($id)
    {
        $place = Place::find($id);

        if (!$place) {
            return response()->json(['message' => 'Place not found'], 404);
        }

        return new PlaceResource($place);
    }

    public function store(Request $request)
    {
        $this->authorize('create', Place::class); // Проверка на роль

        $validated = $request->validate([
            'title' => 'required|string',
            'description' => 'nullable|string',
            'XCoordinate' => 'required|numeric',
            'YCoordinate' => 'required|numeric',
            'base_id' => 'required|exists:bases,id',
            'tariff_limit' => 'required|integer',
            'photo' => 'nullable|string',

        ]);

        // Преобразование имен полей для соответствия БД
        $validated['coordinatex'] = $validated['XCoordinate'];
        $validated['coordinatey'] = $validated['YCoordinate'];
        unset($validated['XCoordinate'], $validated['YCoordinate']);

        $place = Place::create($validated);

        return new PlaceResource($place);
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
        $latitudeFrom = (float)$request->query('Xcoord');
        $longitudeFrom = (float)$request->query('Ycoord');
        $Radius = (float)$request->query('Radius', 50);


        // Получаем все места
        $places = Place::all();


        // Фильтруем места
        $filteredPlaces = $places->filter(function ($place) use ($latitudeFrom, $longitudeFrom, $Radius) {
            // Преобразуем координаты места в радианы
            $latitudeTo = (float)$place->coordinatex;
            $longitudeTo = (float)$place->coordinatey;


            // Расстояние
            $distance = $this->haversineGreatCircleDistance($latitudeFrom, $longitudeFrom, $latitudeTo, $longitudeTo);

            // Возвращаем только места, расстояние до которых меньше или равно радиусу
            return $distance <= $Radius;
        });

        //return response()->json($filteredPlaces->values());
        return PlaceArroundResource::collection($filteredPlaces);
    }

    function haversineGreatCircleDistance(
        $latitudeFrom, $longitudeFrom, $latitudeTo, $longitudeTo, $earthRadius = 6371)
    {
        // convert from degrees to radians
        $latFrom = deg2rad($latitudeFrom);
        $lonFrom = deg2rad($longitudeFrom);
        $latTo = deg2rad($latitudeTo);
        $lonTo = deg2rad($longitudeTo);

        $latDelta = $latTo - $latFrom;
        $lonDelta = $lonTo - $lonFrom;

        $angle = 2 * asin(sqrt(pow(sin($latDelta / 2), 2) +
                cos($latFrom) * cos($latTo) * pow(sin($lonDelta / 2), 2)));

        return $angle * $earthRadius;
    }
}
