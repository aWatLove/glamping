<?php

namespace App\Http\Resources;

use App\Models\OrderTariff;
use App\Models\Tariff;
use Illuminate\Http\Resources\Json\JsonResource;

class PlaceResource extends JsonResource
{
    public function toArray($request)
    {
        // Получаем все тарифы с таким же base_id, как у текущего места
        $tariffs = Tariff::where('base_id', $this->base_id)->get();

        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'XCoordinate' => $this->coordinatex,
            'YCoordinate' => $this->coordinatey,
            'tariffs' => $tariffs->map(function ($tariff) {
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

