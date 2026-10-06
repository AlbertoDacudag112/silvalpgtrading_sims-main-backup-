<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');                 // e.g. "LPG Cylinder 11kg"
            $table->decimal('gas_price', 10, 2);     // price of gas refill only (cylinder exchange)
            $table->decimal('cylinder_fee', 10, 2)->default(1500.00); // extra fee if customer has NO cylinder to exchange
            $table->unsignedInteger('current_stock')->default(0);
            $table->unsignedInteger('max_capacity')->default(50);
            $table->unsignedInteger('reorder_level')->default(20);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
