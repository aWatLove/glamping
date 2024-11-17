<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderOption extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'option_id',
        'count',
    ];

    // Связь с заказом
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    // Связь с опцией
    public function option()
    {
        return $this->belongsTo(Option::class);
    }
}
