<?php

namespace App\Http\Resources;

use App\Models\OrderTariff;
use Illuminate\Http\Resources\Json\JsonResource;

class OptionsResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'price' => $this->price,
            'count' => $this->count,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}

