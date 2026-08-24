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
        'status',
        'latitude',
        'longitude'
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function driver()
    {
        return $this->belongsTo(Driver::class);
    }

    public function returns()
    {
        return $this->hasMany(ProductReturn::class);
    }

    protected static function booted()
    {
        static::saved(function ($delivery) {
            // Send email & WhatsApp only if the status was changed or recently created with these statuses
            if ($delivery->wasChanged('status') || $delivery->wasRecentlyCreated) {
                if (in_array($delivery->status, ['On Delivery', 'Delivered'])) {
                    self::sendStatusEmail($delivery);
                    self::flashWhatsAppUrl($delivery);
                }
            }
        });
    }

    public static function sendStatusEmail($delivery)
    {
        try {
            $delivery->load(['order.customer', 'order.product']);
            $customer = $delivery->order->customer ?? null;
            
            if (!$customer || !$customer->email) {
                \App\Models\ActivityLog::create([
                    'user_id' => auth()->id(),
                    'action' => 'Email Failed',
                    'description' => 'Gagal mengirim email untuk Order #' . $delivery->order_id . ': Email customer kosong.'
                ]);
                return;
            }
            
            $email = $customer->email;
            $statusText = $delivery->status === 'On Delivery' ? 'Sedang Dikirim' : 'Telah Sampai';
            $subject = "Update Pengiriman Order #" . str_pad($delivery->order_id, 5, '0', STR_PAD_LEFT) . " - " . $statusText;
            
            \Illuminate\Support\Facades\Mail::html(
                self::getEmailHtmlContent($delivery),
                function ($message) use ($email, $subject) {
                    $message->to($email)->subject($subject);
                }
            );
            
            \App\Models\ActivityLog::create([
                'user_id' => auth()->id(),
                'action' => 'Email Sent',
                'description' => 'Berhasil mengirim email status pengiriman (' . $statusText . ') untuk Order #' . $delivery->order_id . ' ke ' . $email
            ]);
            
        } catch (\Throwable $e) {
            \App\Models\ActivityLog::create([
                'user_id' => auth()->id(),
                'action' => 'Email Failed',
                'description' => 'Gagal mengirim email untuk Order #' . $delivery->order_id . '. Error: ' . substr($e->getMessage(), 0, 200)
            ]);
        }
    }

    public static function getEmailHtmlContent($delivery)
    {
        $customerName = $delivery->order->customer->customer_name ?? 'Pelanggan';
        $productName = $delivery->order->product->name ?? 'Produk';
        $qty = $delivery->order->quantity ?? 0;
        $driverName = $delivery->driver_name ?? 'Driver Kami';
        $statusText = $delivery->status === 'On Delivery' ? 'SEDANG DIKIRIM (On Delivery)' : 'TELAH DITERIMA (Delivered)';
        
        $statusDesc = $delivery->status === 'On Delivery' 
            ? "Pesanan Anda saat ini sedang dalam perjalanan menuju alamat Anda bersama driver kami: <strong>{$driverName}</strong>." 
            : "Pesanan Anda telah berhasil dikirimkan dan diterima oleh penerima di lokasi Anda.";

        return "
        <div style=\"font-family: 'Inter', Arial, sans-serif; max-width: 600px; margin: auto; padding: 24px; border: 1px solid #eaeaea; border-radius: 12px;\">
            <h2 style=\"color: #0f172a; border-bottom: 2px solid #eaeaea; padding-bottom: 12px; margin-top: 0;\">TK. NAGA SAKTI JAYA</h2>
            <p>Halo <strong>{$customerName}</strong>,</p>
            <p>Berikut kami sampaikan update status pengiriman untuk pesanan Anda:</p>
            
            <div style=\"background: #f8fafc; border-radius: 8px; padding: 16px; margin: 20px 0;\">
                <table style=\"width: 100%; border-collapse: collapse;\">
                    <tr>
                        <td style=\"padding: 6px 0; color: #64748b; font-size: 13.5px;\">No. Order:</td>
                        <td style=\"padding: 6px 0; font-weight: 600; color: #0f172a; text-align: right;\">#ORD-" . str_pad($delivery->order_id, 5, '0', STR_PAD_LEFT) . "</td>
                    </tr>
                    <tr>
                        <td style=\"padding: 6px 0; color: #64748b; font-size: 13.5px;\">Produk:</td>
                        <td style=\"padding: 6px 0; font-weight: 600; color: #0f172a; text-align: right;\">{$productName} ({$qty} tabung)</td>
                    </tr>
                    <tr>
                        <td style=\"padding: 6px 0; color: #64748b; font-size: 13.5px;\">Driver:</td>
                        <td style=\"padding: 6px 0; font-weight: 600; color: #0f172a; text-align: right;\">{$driverName}</td>
                    </tr>
                    <tr>
                        <td style=\"padding: 6px 0; color: #64748b; font-size: 13.5px;\">Status Pengiriman:</td>
                        <td style=\"padding: 6px 0; font-weight: 700; color: " . ($delivery->status === 'On Delivery' ? '#c2410c' : '#15803d') . "; text-align: right;\">{$statusText}</td>
                    </tr>
                </table>
            </div>
            
            <p style=\"color: #374151; line-height: 1.6;\">{$statusDesc}</p>
            
            <p style=\"color: #64748b; font-size: 12px; border-top: 1px solid #eaeaea; padding-top: 16px; margin-top: 24px;\">
                Terima kasih telah mempercayai kami untuk kebutuhan gas Anda.<br>
                <strong>TK. NAGA SAKTI JAYA</strong>
            </p>
        </div>
        ";
    }

    public static function flashWhatsAppUrl($delivery)
    {
        try {
            $delivery->load(['order.customer', 'order.product']);
            $customer = $delivery->order->customer ?? null;
            
            if (!$customer || !$customer->phone) {
                return;
            }
            
            $phone = preg_replace('/[^0-9]/', '', $customer->phone);
            if (str_starts_with($phone, '0')) {
                $phone = '62' . substr($phone, 1);
            }
            
            $customerName = $customer->customer_name ?? 'Pelanggan';
            $productName = $delivery->order->product->name ?? 'Produk';
            $qty = $delivery->order->quantity ?? 0;
            $driverName = $delivery->driver_name ?? 'Driver Kami';
            $statusText = $delivery->status === 'On Delivery' ? 'SEDANG DIKIRIM (On Delivery)' : 'TELAH DITERIMA (Delivered)';
            
            $statusDesc = $delivery->status === 'On Delivery' 
                ? "Pesanan Anda saat ini sedang dalam perjalanan menuju alamat Anda bersama driver kami: *{$driverName}*." 
                : "Pesanan Anda telah berhasil dikirimkan dan diterima oleh penerima di lokasi Anda.";
                
            $message = "Halo *{$customerName}*,\n\nBerikut kami sampaikan update status pengiriman untuk pesanan Anda:\n\n"
                     . "*Detail Pengiriman:*\n"
                     . "- *No. Order:* #ORD-" . str_pad($delivery->order_id, 5, '0', STR_PAD_LEFT) . "\n"
                     . "- *Produk:* {$productName} ({$qty} tabung)\n"
                     . "- *Driver:* {$driverName}\n"
                     . "- *Status:* {$statusText}\n\n"
                     . "{$statusDesc}\n\n"
                     . "Terima kasih telah mempercayai kami untuk kebutuhan gas Anda.\n"
                     . "*TK. NAGA SAKTI JAYA*";
            
            $waUrl = "https://api.whatsapp.com/send?phone=" . $phone . "&text=" . rawurlencode($message);
            
            session()->flash('wa_url', $waUrl);
            
        } catch (\Throwable $e) {
            // Ignore error
        }
    }
}