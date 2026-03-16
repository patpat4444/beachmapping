<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'description', 'latitude', 'longitude', 'rating', 'image', 'address',
        'fees', 'facilities', 'cottage', 'maps_embed_url',
    ];
}
