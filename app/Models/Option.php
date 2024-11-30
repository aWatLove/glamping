<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Option extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'price',
        'count',
        'is_del',
    ];

    public function orders()
    {
        return $this->belongsToMany(Order::class, 'order_options')
            ->withPivot('count');
    }

}
