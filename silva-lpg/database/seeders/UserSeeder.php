<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

/**
 * Seeds the two PERMANENT accounts for the business: the Owner (admin) and the Cashier.
 * Delivery staff are intentionally NOT seeded here — they are not permanent employees,
 * so their accounts are created and managed by the Owner through the
 * "Manage Delivery Staff" screen (see Admin\DeliveryStaffController).
 *
 * Change these credentials before deploying to production.
 */
class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'owner@silvalpg.com'],
            [
                'name' => 'Jeric Silva',
                'password' => Hash::make('Owner@12345'),
                'role' => 'admin',
                'phone' => '09170000001',
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );

        User::updateOrCreate(
            ['email' => 'cashier@silvalpg.com'],
            [
                'name' => 'Silva LPG Cashier',
                'password' => Hash::make('Cashier@12345'),
                'role' => 'cashier',
                'phone' => '09170000002',
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );
    }
}
