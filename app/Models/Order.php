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
}
