<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Driver extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'phone', 'license_plate', 'status'];

    public function deliveries()
    {
        return $this->hasMany(Delivery::class);
    }

    public function user()
    {
        return $this->hasOne(User::class);
    }
}
