<?php

namespace Database\Seeders;

use App\Models\Supplier;
use Illuminate\Database\Seeder;

class SupplierSeeder extends Seeder
{
    public function run(): void
    {
        $suppliers = [
            [
                'name' => 'GreenCycle Packaging Limited',
                'contact_person' => 'Luyando Phiri',
                'email' => 'accounts@greencyclepackaging.test',
                'phone' => '+260971000101',
                'address' => 'Plot 14, Buyantanshi Road, Lusaka',
                'is_active' => true,
            ],
            [
                'name' => 'MetalCore Industrial Supplies',
                'contact_person' => 'Brian Tembo',
                'email' => 'sales@metalcore.test',
                'phone' => '+260971000102',
                'address' => 'Mungwi Road Industrial Area, Lusaka',
                'is_active' => true,
            ],
            [
                'name' => 'EcoFuel Logistics Zambia',
                'contact_person' => 'Natasha Chileshe',
                'email' => 'billing@ecofuel.test',
                'phone' => '+260971000103',
                'address' => 'Great North Road, Kabwe',
                'is_active' => true,
            ],
            [
                'name' => 'Urban Fleet Maintenance Hub',
                'contact_person' => 'Moses Kaluba',
                'email' => 'service@urbanfleet.test',
                'phone' => '+260971000104',
                'address' => 'Makeni Konga, Lusaka',
                'is_active' => true,
            ],
            [
                'name' => 'CleanStream Utility Services',
                'contact_person' => 'Ruth Mwansa',
                'email' => 'finance@cleanstream.test',
                'phone' => '+260971000105',
                'address' => 'Ndola Central Business District, Ndola',
                'is_active' => true,
            ],
        ];

        foreach ($suppliers as $supplier) {
            Supplier::updateOrCreate(
                ['name' => $supplier['name']],
                $supplier
            );
        }
    }
}