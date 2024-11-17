<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Place extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'coordinatex',
        'coordinatey',
        'photo',
        'base_id',
        'tariffs_limit',
        'is_del'
    ];

    public function base()
    {
        return $this->belongsTo(Base::class);
    }
}
