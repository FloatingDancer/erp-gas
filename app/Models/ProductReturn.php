<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductReturn extends Model
{
    protected $fillable = [
        'return_number',
        'order_id',
        'delivery_id',
        'customer_id',
        'product_id',
        'quantity',
        'condition',
        'return_type',
        'refund_amount',
        'reason',
        'status',
        'return_date',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function delivery()
    {
        return $this->belongsTo(Delivery::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
