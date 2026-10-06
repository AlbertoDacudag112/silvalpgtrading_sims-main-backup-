<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained();
            $table->unsignedInteger('quantity');

            // Per the business rule: if the customer has an empty cylinder to exchange,
            // they only pay gas price. If they have none, they pay gas price + cylinder_fee (₱1,500 default).
            $table->boolean('has_own_cylinder')->default(true);

            $table->decimal('unit_gas_price', 10, 2);   // snapshot of product gas_price at time of order
            $table->decimal('unit_cylinder_fee', 10, 2)->default(0); // snapshot, 0 if has_own_cylinder
            $table->decimal('line_total', 10, 2);        // (unit_gas_price + unit_cylinder_fee) * quantity
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};
