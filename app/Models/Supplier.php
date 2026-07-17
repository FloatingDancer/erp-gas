<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    protected $fillable = [
        'name',
        'phone',
        'email',
        'address',
        'latitude',
        'longitude',
    ];

    public function purchases()
    {
        return $this->hasMany(Purchase::class);
    }
}
