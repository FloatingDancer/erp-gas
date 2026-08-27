<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('product_returns', function (Blueprint $table) {
            $table->string('return_category')->default('Customer')->after('return_number'); // Customer / Supplier
            $table->foreignId('customer_id')->nullable()->change();
            $table->foreignId('supplier_id')->nullable()->after('customer_id')->constrained('suppliers')->nullOnDelete();
            $table->foreignId('purchase_id')->nullable()->after('delivery_id')->constrained('purchases')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('product_returns', function (Blueprint $table) {
            $table->dropForeign(['supplier_id']);
            $table->dropForeign(['purchase_id']);
            $table->dropColumn(['return_category', 'supplier_id', 'purchase_id']);
        });
    }
};
