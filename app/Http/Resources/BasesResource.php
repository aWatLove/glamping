<?php

namespace App\Http\Resources;

use App\Models\OrderTariff;
use Illuminate\Http\Resources\Json\JsonResource;

class BasesResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'coordinate_x' => $this->coordinate_x,
            'coordinate_y' => $this->coordinate_y,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}

