<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    protected $fillable = [
        'order_id',
        'total_amount',
        'status'
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    protected static function booted()
    {
        static::saved(function ($invoice) {
            if ($invoice->status === 'Paid') {
                // Check if delivery already exists for this order
                $delivery = \App\Models\Delivery::where('order_id', $invoice->order_id)->first();
                
                if (!$delivery) {
                    // Find first active driver if available
                    $driver = \App\Models\Driver::where('status', 'Active')->first();
                    
                    \App\Models\Delivery::create([
                        'order_id' => $invoice->order_id,
                        'driver_id' => $driver ? $driver->id : null,
                        'driver_name' => $driver ? $driver->name : 'Belum Ditentukan',
                        'delivery_date' => now()->toDateString(),
                        'status' => 'On Delivery',
                    ]);
                    
                    // Log activity
                    \App\Models\ActivityLog::log(
                        'Create', 
                        'Otomatis menjadwalkan pengiriman On Delivery untuk Order #' . $invoice->order_id . ' karena invoice telah Lunas (Paid)'
                    );
                } else {
                    // If delivery exists but status is not Delivered, update to On Delivery
                    if ($delivery->status !== 'Delivered') {
                        $delivery->update(['status' => 'On Delivery']);
                        
                        \App\Models\ActivityLog::log(
                            'Update', 
                            'Otomatis memperbarui status pengiriman #' . $delivery->id . ' menjadi On Delivery karena invoice telah Lunas (Paid)'
                        );
                    }
                }
            }
        });
    }
}