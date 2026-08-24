<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_returns', function (Blueprint $table) {
            $table->id();
            $table->string('return_number')->unique();
            $table->foreignId('order_id')->nullable()->constrained('orders')->nullOnDelete();
            $table->foreignId('delivery_id')->nullable()->constrained('deliveries')->nullOnDelete();
            $table->foreignId('customer_id')->constrained('customers')->cascadeOnDelete();
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            $table->integer('quantity');
            $table->enum('condition', ['Good', 'Damaged'])->default('Good'); // Bagus / Rusak
            $table->enum('return_type', ['Exchange', 'Refund', 'Credit'])->default('Exchange'); // Ganti Barang / Refund Uang / Potong Nota
            $table->decimal('refund_amount', 15, 2)->default(0);
            $table->text('reason')->nullable();
            $table->enum('status', ['Pending', 'Approved', 'Rejected'])->default('Approved');
            $table->date('return_date');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_returns');
    }
};
