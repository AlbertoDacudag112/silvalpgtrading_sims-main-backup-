<?php

namespace App\Services;

use App\Models\InventoryLog;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Customer;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class OrderService
{
    /**
     * Create an order with line items, enforcing the business's pricing & stock rules:
     *  - Customer WITH an empty cylinder to exchange -> pays gas price only.
     *  - Customer WITHOUT a cylinder -> pays gas price + cylinder_fee (₱1,500 default).
     *  - Payment method is cash or gcash ONLY, paid in full — no partial/down payment ever.
     *  - Stock is deducted immediately since an order = a confirmed sale in this business.
     */
    public function createOrder(array $customerData, array $items, string $deliveryAddress, string $paymentMethod, int $createdBy, ?string $notes = null): Order
    {
        if (! in_array($paymentMethod, ['cash', 'gcash'], true)) {
            throw ValidationException::withMessages(['payment_method' => 'Only Cash or GCash payment is accepted.']);
        }

        return DB::transaction(function () use ($customerData, $items, $deliveryAddress, $paymentMethod, $createdBy, $notes) {
            $customer = Customer::create($customerData);

            $subtotal = 0;
            $cylinderFeeTotal = 0;
            $lineItems = [];

            foreach ($items as $item) {
                /** @var Product $product */
                $product = Product::lockForUpdate()->findOrFail($item['product_id']);
                $qty = (int) $item['quantity'];
                $hasOwnCylinder = (bool) ($item['has_own_cylinder'] ?? true);

                if ($qty < 1) {
                    throw ValidationException::withMessages(['quantity' => 'Quantity must be at least 1.']);
                }

                if ($product->current_stock < $qty) {
                    throw ValidationException::withMessages([
                        'stock' => "Insufficient stock for {$product->name}. Available: {$product->current_stock}.",
                    ]);
                }

                $unitCylinderFee = $hasOwnCylinder ? 0 : $product->cylinder_fee;
                $lineTotal = ($product->gas_price + $unitCylinderFee) * $qty;

                $subtotal += $product->gas_price * $qty;
                $cylinderFeeTotal += $unitCylinderFee * $qty;

                $lineItems[] = [
                    'product' => $product,
                    'quantity' => $qty,
                    'has_own_cylinder' => $hasOwnCylinder,
                    'unit_gas_price' => $product->gas_price,
                    'unit_cylinder_fee' => $unitCylinderFee,
                    'line_total' => $lineTotal,
                ];
            }

            $order = Order::create([
                'order_code' => 'ORD-' . strtoupper(Str::random(6)),
                'customer_id' => $customer->id,
                'order_date' => now()->toDateString(),
                'delivery_address' => $deliveryAddress,
                'subtotal' => $subtotal,
                'cylinder_fee_total' => $cylinderFeeTotal,
                'grand_total' => $subtotal + $cylinderFeeTotal,
                'payment_method' => $paymentMethod,
                'is_paid' => true,
                'paid_at' => now(),
                'status' => 'pending',
                'created_by' => $createdBy,
                'notes' => $notes,
            ]);

            foreach ($lineItems as $li) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $li['product']->id,
                    'quantity' => $li['quantity'],
                    'has_own_cylinder' => $li['has_own_cylinder'],
                    'unit_gas_price' => $li['unit_gas_price'],
                    'unit_cylinder_fee' => $li['unit_cylinder_fee'],
                    'line_total' => $li['line_total'],
                ]);

                $product = $li['product'];
                $product->decrement('current_stock', $li['quantity']);

                InventoryLog::create([
                    'product_id' => $product->id,
                    'type' => 'sale',
                    'quantity_change' => -$li['quantity'],
                    'stock_after' => $product->fresh()->current_stock,
                    'order_id' => $order->id,
                    'note' => "Sold via order {$order->order_code}",
                    'created_by' => $createdBy,
                ]);
            }

            return $order->fresh(['items.product', 'customer']);
        });
    }

    /** Restock a product (owner/cashier receiving new stock from supplier). */
    public function restock(Product $product, int $quantity, int $createdBy, ?string $note = null): Product
    {
        if ($quantity < 1) {
            throw ValidationException::withMessages(['quantity' => 'Restock quantity must be at least 1.']);
        }

        return DB::transaction(function () use ($product, $quantity, $createdBy, $note) {
            $product = Product::lockForUpdate()->findOrFail($product->id);

            $newStock = min($product->current_stock + $quantity, $product->max_capacity);
            $actualAdded = $newStock - $product->current_stock;

            $product->update(['current_stock' => $newStock]);

            InventoryLog::create([
                'product_id' => $product->id,
                'type' => 'restock',
                'quantity_change' => $actualAdded,
                'stock_after' => $newStock,
                'note' => $note ?? 'Stock received from supplier',
                'created_by' => $createdBy,
            ]);

            return $product->fresh();
        });
    }

    /** Assign an order to a delivery staff member and mark it out for delivery. */
    public function assignDelivery(Order $order, int $deliveryStaffId): Order
    {
        $order->update([
            'assigned_delivery_id' => $deliveryStaffId,
            'status' => 'out_for_delivery',
        ]);

        return $order->fresh();
    }

    /** Cancel an order and restock the reserved items back to inventory. */
    public function cancelOrder(Order $order, int $cancelledBy, ?string $reason = null): Order
    {
        if ($order->status === 'completed') {
            throw ValidationException::withMessages(['status' => 'A completed order cannot be cancelled.']);
        }

        return DB::transaction(function () use ($order, $cancelledBy, $reason) {
            foreach ($order->items as $item) {
                $product = Product::lockForUpdate()->findOrFail($item->product_id);
                $newStock = min($product->current_stock + $item->quantity, $product->max_capacity);
                $product->update(['current_stock' => $newStock]);

                InventoryLog::create([
                    'product_id' => $product->id,
                    'type' => 'adjustment',
                    'quantity_change' => $item->quantity,
                    'stock_after' => $newStock,
                    'order_id' => $order->id,
                    'note' => 'Restocked due to order cancellation: ' . ($reason ?? 'no reason given'),
                    'created_by' => $cancelledBy,
                ]);
            }

            $order->update(['status' => 'cancelled', 'notes' => trim(($order->notes ?? '') . ' | Cancelled: ' . $reason)]);

            return $order->fresh();
        });
    }
}
