<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_code')->unique(); // e.g. ORD-000123, shown to staff/customer
            $table->foreignId('customer_id')->constrained()->cascadeOnDelete();
            $table->date('order_date');
            $table->string('delivery_address');

            // Pricing snapshot (order_items holds line detail; these are the totals shown on the order)
            $table->decimal('subtotal', 10, 2)->default(0);
            $table->decimal('cylinder_fee_total', 10, 2)->default(0);
            $table->decimal('grand_total', 10, 2)->default(0);

            // Payment: business only accepts cash or GCash in full — no partial/down payment, per company policy
            $table->enum('payment_method', ['cash', 'gcash']);
            $table->boolean('is_paid')->default(false);
            $table->timestamp('paid_at')->nullable();

            // Order lifecycle
            $table->enum('status', ['pending', 'out_for_delivery', 'completed', 'cancelled'])->default('pending');
            $table->foreignId('assigned_delivery_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('delivered_at')->nullable();

            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
