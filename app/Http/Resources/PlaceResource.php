<?php

namespace App\Http\Resources;

use App\Models\OrderTariff;
use Illuminate\Http\Resources\Json\JsonResource;

class PlaceResource extends JsonResource
{
    public function toArray($request)
    {
        $orderTariff = OrderTariff::where('place_id', $this->id)->get();
        $tariffs = $orderTariff->pluck('tariff_id')->toArray();
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'XCoordinate' => $this->coordinatex,
            'YCoordinate' => $this->coordinatey,
            'tariffs' => $tariffs->tariffs->map(function ($tariff) {
                return [
                    'id' => $tariff->id,
                    'title' => $tariff->title,
                    'description' => $tariff->description,
                    'price_per_day' => $tariff->price_per_day,
                    'photo' => $tariff->photo,
                ];
            }),
            'tariffs_limit' => $this->tariffs_limit,
            'photo' => $this->photo,
        ];
    }
}

