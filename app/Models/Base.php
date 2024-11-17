<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Base extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'coordinate_x',
        'coordinate_y',
    ];

    // Связь с местами
    public function places()
    {
        return $this->hasMany(Place::class);
    }

    // Связь с тарифами
    public function tariffs()
    {
        return $this->hasMany(Tariff::class);
    }
}
