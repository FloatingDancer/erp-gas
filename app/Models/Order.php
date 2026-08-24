<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
    'customer_id',
    'product_id',
    'quantity',
    'total_amount',
    'order_date',
    'status',
];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
    public function details()
    {
        return $this->hasMany(OrderDetail::class);
    }
    public function delivery()
    {
        return $this->hasOne(Delivery::class);
    }
    public function invoice()
    {
        return $this->hasOne(Invoice::class);
    }
    public function returns()
    {
        return $this->hasMany(ProductReturn::class);
    }
}