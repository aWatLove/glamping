<?php

namespace App\Http\Resources;

use App\Models\Tariff;
use App\Models\OrderTariff;
use Illuminate\Http\Resources\Json\JsonResource;

class TariffsResourceForUsers extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'price_per_day' => $this->price_per_day,
            'photo' => $this->photo,
            'booking' => $this->orders ? $this->orders->map(function ($tariff) {
                $orderTariff = OrderTariff::where('tariff_id', $tariff->id)
                    ->first();

                return [
                    'date_start' => $orderTariff->date_start,
                    'date_end' => $orderTariff->date_end,
                ];
            }) : [],
        ];
    }
}
