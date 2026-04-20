<?php

namespace Database\Seeders;

use App\Models\Account;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            SupplierSeeder::class,
            PosProductSeeder::class,
        ]);
        $bootstrapAdminEmail = env('BOOTSTRAP_ADMIN_EMAIL');
        $bootstrapAdminName = env('BOOTSTRAP_ADMIN_NAME', 'Admin User');
        $bootstrapAdminPassword = env('BOOTSTRAP_ADMIN_PASSWORD');

        $accounts = [
            ['code' => '1100', 'name' => 'Cash / Bank', 'type' => 'asset', 'group_name' => 'valuables'],
            ['code' => '1200', 'name' => 'Inventory - Raw', 'type' => 'asset', 'group_name' => 'valuables'],
            ['code' => '1210', 'name' => 'Inventory - Processed', 'type' => 'asset', 'group_name' => 'valuables'],
            ['code' => '1500', 'name' => 'Machinery', 'type' => 'asset', 'group_name' => 'valuables'],
            ['code' => '1600', 'name' => 'Vehicles', 'type' => 'asset', 'group_name' => 'valuables'],

            ['code' => '2100', 'name' => 'Supplier Bills', 'type' => 'liability', 'group_name' => 'debts'],
            ['code' => '2200', 'name' => 'Statutory Payables (ZRA/NAPSA)', 'type' => 'liability', 'group_name' => 'debts'],

            ['code' => '4100', 'name' => 'Sales of Recycled Goods', 'type' => 'income', 'group_name' => 'money_in'],
            ['code' => '4200', 'name' => 'Collection Fees', 'type' => 'income', 'group_name' => 'money_in'],

            ['code' => '5100', 'name' => 'Buying Waste', 'type' => 'cogs', 'group_name' => 'direct_costs'],
            ['code' => '5200', 'name' => 'Factory Wages', 'type' => 'cogs', 'group_name' => 'direct_costs'],
            ['code' => '5300', 'name' => 'Machine Power / Fuel', 'type' => 'cogs', 'group_name' => 'direct_costs'],

            ['code' => '6100', 'name' => 'Admin Salaries', 'type' => 'expense', 'group_name' => 'general_costs'],
            ['code' => '6200', 'name' => 'Marketing', 'type' => 'expense', 'group_name' => 'general_costs'],
            ['code' => '6300', 'name' => 'Repairs', 'type' => 'expense', 'group_name' => 'general_costs'],
            ['code' => '6400', 'name' => 'Depreciation', 'type' => 'expense', 'group_name' => 'general_costs'],
        ];

        foreach ($accounts as $account) {
            Account::updateOrCreate(
                ['code' => $account['code']],
                [
                    'name' => $account['name'],
                    'type' => $account['type'],
                    'group_name' => $account['group_name'],
                    'is_active' => true,
                ]
            );
        }


        User::updateOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Admin User',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );

        User::updateOrCreate(
            ['email' => 'accountant@example.com'],
            [
                'name' => 'Accountant User',
                'password' => Hash::make('password'),
                'role' => 'accountant',
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );

        User::updateOrCreate(
            ['email' => 'salesperson@example.com'],
            [
                'name' => 'Salesperson User',
                'password' => Hash::make('password'),
                'role' => 'salesperson',
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );

        if ($bootstrapAdminEmail && $bootstrapAdminPassword) {
            User::updateOrCreate(
                ['email' => $bootstrapAdminEmail],
                [
                    'name' => $bootstrapAdminName,
                    'password' => Hash::make($bootstrapAdminPassword),
                    'role' => 'admin',
                    'is_active' => true,
                    'email_verified_at' => now(),
                ]
            );
        }
    }
}
