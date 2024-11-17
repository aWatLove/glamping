<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderTariff extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'tariff_id',
        'place_id',
        'date_start',
        'date_end',
        'status',
    ];

    // Связь с заказом
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    // Связь с тарифом
    public function tariff()
    {
        return $this->belongsTo(Tariff::class);
    }

    // Связь с местом
    public function place()
    {
        return $this->belongsTo(Place::class);
    }
}
