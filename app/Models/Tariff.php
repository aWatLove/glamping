<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tariff extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'price_per_day',
        'photo',
        'base_id',
        'is_del',
    ];

    public function base()
    {
        return $this->belongsTo(Base::class);
    }
}
