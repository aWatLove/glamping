<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'user' => $this->user ? [
                'id' => $this->user->id,
                'username' => $this->user->username,
                'firstname' => $this->user->firstname,
                'lastname' => $this->user->lastname,
                'email' => $this->user->email,
                'phone' => $this->user->phone,
            ] : null,
            'place' => $this->place ? [
                'id' => $this->place->id,
                'title' => $this->place->title,
                'XCoordinate' => $this->place->x_coordinate,
                'YCoordinate' => $this->place->y_coordinate,
            ] : null,
            'tariffs' => $this->tariffs ? $this->tariffs->map(function ($tariff) {
                return [
                    'id' => $tariff->id,
                    'title' => $tariff->title,
                    'description' => $tariff->description,
                    'price_per_day' => $tariff->price_per_day,
                    'photo' => $tariff->photo,
                    'status' => 'Обрабатывается',
                ];
            }) : [],
            'days_count' => $this->days_count,
            'status' => $this->status === 'pending' ? 'Обрабатывается' : $this->status,
            'date_start' => $this->date_start,
            'date_end' => $this->date_end,
            'additional_options' => $this->options ? $this->options->map(function ($option) {
                return [
                    'id' => $option->id,
                    'title' => $option->title,
                    'price' => $option->price,
                    'count' => $option->pivot->count ?? 0,
                ];
            }) : [],
            'total_price' => $this->total_price,
            'payment_status' => $this->payment_status === 'unpaid' ? 'Не оплачено' : $this->payment_status,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}

