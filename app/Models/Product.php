<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'name',
        'category',
        'price',
        'stock',
        'damaged_stock',
    ];

    public function returns()
    {
        return $this->hasMany(ProductReturn::class);
    }
}