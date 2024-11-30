<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'place_id',
        'date_start',
        'date_end',
        'days_count',
        'status',
        'payment_status',
        'total_price',
    ];

    // Связь с пользователем
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Связь с местом
    public function place()
    {
        return $this->belongsTo(Place::class, 'place_id');
    }

    public function tariffs()
    {
        return $this->belongsToMany(Tariff::class, 'order_tariffs')
            ->withPivot('place_id', 'date_start', 'date_end', 'status');
    }

    public function options()
    {
        return $this->belongsToMany(Option::class, 'order_options')
            ->withPivot('count');
    }
}
