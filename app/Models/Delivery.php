<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Delivery extends Model
{
    protected $fillable = [
        'order_id',
        'driver_id',
        'driver_name',
        'delivery_date',
        'status'
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function driver()
    {
        return $this->belongsTo(Driver::class);
    }
}