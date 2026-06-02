<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->foreignId('product_id')->nullable()->after('customer_id');
            $table->integer('quantity')->default(1)->after('product_id');
            $table->decimal('total_amount', 12, 2)->default(0)->after('quantity');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn([
                'product_id',
                'quantity',
                'total_amount'
            ]);
        });
    }
};