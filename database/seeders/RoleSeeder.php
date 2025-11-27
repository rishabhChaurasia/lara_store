<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create basic roles with their permissions
        Role::updateOrCreate(
            ['slug' => 'super-admin'],
            [
                'name' => 'Super Admin',
                'permissions' => ['*'], // Wildcard for all permissions
            ]
        );

        Role::updateOrCreate(
            ['slug' => 'product-manager'],
            [
                'name' => 'Product Manager',
                'permissions' => [
                    'products.view',
                    'products.create',
                    'products.update',
                    'products.delete',
                    'categories.view',
                    'categories.create',
                    'categories.update',
                    'categories.delete',
                ],
            ]
        );

        Role::updateOrCreate(
            ['slug' => 'order-manager'],
            [
                'name' => 'Order Manager',
                'permissions' => [
                    'orders.view',
                    'orders.update',
                    'orders.delete',
                    'customers.view',
                ],
            ]
        );

        Role::updateOrCreate(
            ['slug' => 'marketing-manager'],
            [
                'name' => 'Marketing Manager',
                'permissions' => [
                    'coupons.view',
                    'coupons.create',
                    'coupons.update',
                    'coupons.delete',
                    'reviews.view',
                    'reviews.moderate',
                ],
            ]
        );

        Role::updateOrCreate(
            ['slug' => 'customer-support'],
            [
                'name' => 'Customer Support',
                'permissions' => [
                    'customers.view',
                    'orders.view',
                    'help-desk.access',
                    'tickets.manage',
                ],
            ]
        );
    }
}