<?php

namespace Database\Seeders;

use App\Models\PosProduct;
use Illuminate\Database\Seeder;

class PosProductSeeder extends Seeder
{
    public function run(): void
    {
        $products = [
            // 1. TYRES
            ['sku' => 'TYR-001', 'name' => "26' Ballon Tyre (Hartex Brand)", 'category' => 'Tyres', 'stock' => 20, 'price' => 0],
            ['sku' => 'TYR-002', 'name' => "28' Tyre (Hartex Brand)", 'category' => 'Tyres', 'stock' => 20, 'price' => 0],
            ['sku' => 'TYR-003', 'name' => "26' Buffalo Tyre (Original Brand)", 'category' => 'Tyres', 'stock' => 20, 'price' => 0],
            ['sku' => 'TYR-004', 'name' => "26' Mountain Tyre (World Cut Brand)", 'category' => 'Tyres', 'stock' => 20, 'price' => 0],

            // 2. RIMS (40 holes heavy duty)
            ['sku' => 'RIM-001', 'name' => "28' Rim (40 Holes Heavy Duty)", 'category' => 'Rims', 'stock' => 20, 'price' => 0],
            ['sku' => 'RIM-002', 'name' => "26' Ballon Rim (40 Holes Heavy Duty)", 'category' => 'Rims', 'stock' => 20, 'price' => 0],
            ['sku' => 'RIM-003', 'name' => "26' Mountain Rim (40 Holes Heavy Duty)", 'category' => 'Rims', 'stock' => 20, 'price' => 0],

            // 3. FORKS
            ['sku' => 'FRK-001', 'name' => 'Mountain Fork', 'category' => 'Forks', 'stock' => 21, 'price' => 0],
            ['sku' => 'FRK-002', 'name' => "28' Fork", 'category' => 'Forks', 'stock' => 21, 'price' => 0],
            ['sku' => 'FRK-003', 'name' => "26' Ballon Fork", 'category' => 'Forks', 'stock' => 21, 'price' => 0],

            // 4. BALL BEARINGS
            ['sku' => 'BRG-001', 'name' => 'Ball Bearing - Bottom / Rear', 'category' => 'Ball Bearings', 'stock' => 40, 'price' => 0],
            ['sku' => 'BRG-002', 'name' => 'Ball Bearing - Front', 'category' => 'Ball Bearings', 'stock' => 40, 'price' => 0],
            ['sku' => 'BRG-003', 'name' => 'Ball Bearing - Neck', 'category' => 'Ball Bearings', 'stock' => 40, 'price' => 0],
            ['sku' => 'BRG-004', 'name' => 'Fork Fittings', 'category' => 'Ball Bearings', 'stock' => 45, 'price' => 0],

            // 5. PEDALS
            ['sku' => 'PDL-001', 'name' => "28' Rubber Pedals with Metals", 'category' => 'Pedals', 'stock' => 80, 'price' => 0],
            ['sku' => 'PDL-002', 'name' => 'Mountain Metallic Pedals', 'category' => 'Pedals', 'stock' => 80, 'price' => 0],
            ['sku' => 'PDL-003', 'name' => "Plastic Pedals (Mountain/Buffalo/26' Ballon)", 'category' => 'Pedals', 'stock' => 85, 'price' => 0],

            // 6. CRANKERS
            ['sku' => 'CRK-001', 'name' => 'Mountain Cranker MTC', 'category' => 'Crankers', 'stock' => 90, 'price' => 0],
            ['sku' => 'CRK-002', 'name' => 'Mountain Cranker MTC Single', 'category' => 'Crankers', 'stock' => 90, 'price' => 0],
            ['sku' => 'CRK-003', 'name' => "28'/26' Ballon Cranker 44T", 'category' => 'Crankers', 'stock' => 90, 'price' => 0],
            ['sku' => 'CRK-004', 'name' => "28'/26' Ballon Cranker 46T", 'category' => 'Crankers', 'stock' => 90, 'price' => 0],
            ['sku' => 'CRK-005', 'name' => "28'/26' Ballon Cranker 48T", 'category' => 'Crankers', 'stock' => 90, 'price' => 0],
            ['sku' => 'CRK-006', 'name' => "28'/26' Ballon Cranker 52T", 'category' => 'Crankers', 'stock' => 90, 'price' => 0],

            // 7. FREEWHEELS
            ['sku' => 'FWL-001', 'name' => 'Mountain Freewheel 7-Speed', 'category' => 'Freewheels', 'stock' => 20, 'price' => 0],
            ['sku' => 'FWL-002', 'name' => "28'/26' Ballon Freewheel 16T", 'category' => 'Freewheels', 'stock' => 20, 'price' => 0],
            ['sku' => 'FWL-003', 'name' => "28'/26' Ballon Freewheel 18T", 'category' => 'Freewheels', 'stock' => 20, 'price' => 0],
            ['sku' => 'FWL-004', 'name' => "28'/26' Ballon Freewheel 20T", 'category' => 'Freewheels', 'stock' => 20, 'price' => 0],
            ['sku' => 'FWL-005', 'name' => "28'/26' Ballon Freewheel 22T", 'category' => 'Freewheels', 'stock' => 20, 'price' => 0],

            // 8. SPOKES (Big)
            ['sku' => 'SPK-001', 'name' => 'Mountain Spokes (Big)', 'category' => 'Spokes', 'stock' => 50, 'price' => 0],
            ['sku' => 'SPK-002', 'name' => "28' Spokes (Big)", 'category' => 'Spokes', 'stock' => 50, 'price' => 0],
            ['sku' => 'SPK-003', 'name' => "26' Ballon Spokes (Big)", 'category' => 'Spokes', 'stock' => 50, 'price' => 0],
            ['sku' => 'SPK-004', 'name' => 'Champion Spokes (Big)', 'category' => 'Spokes', 'stock' => 50, 'price' => 0],

            // 9. HUBS
            ['sku' => 'HUB-001', 'name' => "28'/26' Rear Hub - Gold", 'category' => 'Hubs', 'stock' => 60, 'price' => 0],
            ['sku' => 'HUB-002', 'name' => "28'/26' Rear Hub - Silver", 'category' => 'Hubs', 'stock' => 60, 'price' => 0],
            ['sku' => 'HUB-003', 'name' => "28'/26' Front Hub - Gold", 'category' => 'Hubs', 'stock' => 60, 'price' => 0],
            ['sku' => 'HUB-004', 'name' => "28'/26' Front Hub - Silver", 'category' => 'Hubs', 'stock' => 60, 'price' => 0],

            // 10. AXLES
            ['sku' => 'AXL-001', 'name' => "28'/26' Rear Axle - Gold", 'category' => 'Axles', 'stock' => 60, 'price' => 0],
            ['sku' => 'AXL-002', 'name' => "28'/26' Rear Axle - Silver", 'category' => 'Axles', 'stock' => 60, 'price' => 0],
            ['sku' => 'AXL-003', 'name' => "28'/26' Rear Axle - Black", 'category' => 'Axles', 'stock' => 60, 'price' => 0],
            ['sku' => 'AXL-004', 'name' => "28'/26' Front Axle - Gold", 'category' => 'Axles', 'stock' => 20, 'price' => 0],
            ['sku' => 'AXL-005', 'name' => "28'/26' Front Axle - Silver", 'category' => 'Axles', 'stock' => 20, 'price' => 0],
            ['sku' => 'AXL-006', 'name' => "28'/26' Bottom Axle - Black", 'category' => 'Axles', 'stock' => 21, 'price' => 0],
            ['sku' => 'AXL-007', 'name' => 'Field Cartilage - With Pin', 'category' => 'Axles', 'stock' => 30, 'price' => 0],
            ['sku' => 'AXL-008', 'name' => 'Field Cartilage - With Nut', 'category' => 'Axles', 'stock' => 30, 'price' => 0],

            // 11. PUMPS
            ['sku' => 'PMP-001', 'name' => 'Small Pump', 'category' => 'Pumps', 'stock' => 80, 'price' => 0],
            ['sku' => 'PMP-002', 'name' => 'Medium Pump', 'category' => 'Pumps', 'stock' => 80, 'price' => 0],
            ['sku' => 'PMP-003', 'name' => 'Big Pump', 'category' => 'Pumps', 'stock' => 80, 'price' => 0],

            // 12. CHAINS (Big chains in boxes)
            ['sku' => 'CHN-001', 'name' => 'Mountain Chains Box', 'category' => 'Chains', 'stock' => 6, 'price' => 0],
            ['sku' => 'CHN-002', 'name' => "28'/26' Chains Box", 'category' => 'Chains', 'stock' => 6, 'price' => 0],

            // 13-16. TUBES
            ['sku' => 'TUB-001', 'name' => "28' Tube", 'category' => 'Tubes', 'stock' => 120, 'price' => 0],
            ['sku' => 'TUB-002', 'name' => "26' Big Tyre Tube", 'category' => 'Tubes', 'stock' => 120, 'price' => 0],
            ['sku' => 'TUB-003', 'name' => "26' Mountain Tube", 'category' => 'Tubes', 'stock' => 120, 'price' => 0],
            ['sku' => 'TUB-004', 'name' => "26' Champion Tube", 'category' => 'Tubes', 'stock' => 120, 'price' => 0],

            // 17. BRAKES - Mountain
            ['sku' => 'BRK-001', 'name' => 'Mountain Brake Set', 'category' => 'Brakes', 'stock' => 45, 'price' => 0],
            ['sku' => 'BRK-002', 'name' => 'Mountain Rubber Grip', 'category' => 'Brakes', 'stock' => 70, 'price' => 0],
            ['sku' => 'BRK-003', 'name' => 'Mountain Brake Handle - Plastic', 'category' => 'Brakes', 'stock' => 70, 'price' => 0],
            ['sku' => 'BRK-004', 'name' => 'Mountain Brake Handle - Metallic', 'category' => 'Brakes', 'stock' => 70, 'price' => 0],
            ['sku' => 'BRK-005', 'name' => 'Mountain Brake Handle - Aluminum', 'category' => 'Brakes', 'stock' => 70, 'price' => 0],
            ['sku' => 'BRK-006', 'name' => 'Mountain Disc Brake', 'category' => 'Brakes', 'stock' => 50, 'price' => 0],
            ['sku' => 'BRK-007', 'name' => 'Mountain Brake Long Cable', 'category' => 'Brakes', 'stock' => 10, 'price' => 0],
            ['sku' => 'BRK-008', 'name' => 'Mountain Brake Front Cable', 'category' => 'Brakes', 'stock' => 10, 'price' => 0],
            // 18-19. BRAKES - Full Sets
            ['sku' => 'BRK-009', 'name' => "28' Brake Full Set", 'category' => 'Brakes', 'stock' => 50, 'price' => 0],
            ['sku' => 'BRK-010', 'name' => "26' Brake Full Set", 'category' => 'Brakes', 'stock' => 50, 'price' => 0],
            // 20. BRAKES - Foot Brake Hub
            ['sku' => 'BRK-011', 'name' => "26' Foot Brake Hub (36 Holes)", 'category' => 'Brakes', 'stock' => 80, 'price' => 0],
            ['sku' => 'BRK-012', 'name' => "28' Foot Brake Hub (40 Holes)", 'category' => 'Brakes', 'stock' => 80, 'price' => 0],

            // 21-22. SOLUTION
            ['sku' => 'SOL-001', 'name' => 'Solution - Big Box', 'category' => 'Solution', 'stock' => 20, 'price' => 0],
            ['sku' => 'SOL-002', 'name' => 'Solution - Small Box', 'category' => 'Solution', 'stock' => 20, 'price' => 0],

            // 23. FORK SUPPORT
            ['sku' => 'FSP-001', 'name' => 'Fork Support', 'category' => 'Fork Support', 'stock' => 80, 'price' => 0],

            // 24. BELLS
            ['sku' => 'BEL-001', 'name' => 'Horn Bell', 'category' => 'Bells', 'stock' => 60, 'price' => 0],
            ['sku' => 'BEL-002', 'name' => 'Ring Bell', 'category' => 'Bells', 'stock' => 60, 'price' => 0],

            // 25. LOCKS
            ['sku' => 'LCK-001', 'name' => 'Password Lock', 'category' => 'Locks', 'stock' => 60, 'price' => 0],
            ['sku' => 'LCK-002', 'name' => 'Key Lock', 'category' => 'Locks', 'stock' => 60, 'price' => 0],

            // 25. GREASE
            ['sku' => 'GRS-001', 'name' => 'Grease (10ltr)', 'category' => 'Grease', 'stock' => 10, 'price' => 0],

            // 26. HANDLE BAR
            ['sku' => 'HBR-001', 'name' => 'Mountain Handle Bar Full Set', 'category' => 'Handle Bars', 'stock' => 10, 'price' => 0],
            ['sku' => 'HBR-002', 'name' => 'Mountain Handle Bar Pipe', 'category' => 'Handle Bars', 'stock' => 40, 'price' => 0],
            ['sku' => 'HBR-003', 'name' => 'Mountain Handle Bar Neck', 'category' => 'Handle Bars', 'stock' => 40, 'price' => 0],
            ['sku' => 'HBR-004', 'name' => "28' Handle Bar", 'category' => 'Handle Bars', 'stock' => 10, 'price' => 0],

            // 27. SADDLES
            ['sku' => 'SDL-001', 'name' => 'Mountain Saddle', 'category' => 'Saddles', 'stock' => 40, 'price' => 0],
            ['sku' => 'SDL-002', 'name' => "28'/26' Saddle", 'category' => 'Saddles', 'stock' => 40, 'price' => 0],

            // 28. EXPANDABLE BOARDS
            ['sku' => 'EXP-001', 'name' => 'Mountain Expandable Board', 'category' => 'Expandable Boards', 'stock' => 20, 'price' => 0],
            ['sku' => 'EXP-002', 'name' => 'Mountain Expandable Board Set', 'category' => 'Expandable Boards', 'stock' => 20, 'price' => 0],

            // 29. FRAMES
            ['sku' => 'FRM-001', 'name' => "28' Frame", 'category' => 'Frames', 'stock' => 5, 'price' => 0],
            ['sku' => 'FRM-002', 'name' => "26' Frame", 'category' => 'Frames', 'stock' => 5, 'price' => 0],

            // 30. MUDGUARDS
            ['sku' => 'MUD-001', 'name' => 'Mountain Plastic Mudguard (3 Pieces)', 'category' => 'Mudguards', 'stock' => 95, 'price' => 0],
            ['sku' => 'MUD-002', 'name' => 'Mountain Plastic Mudguard (2 Pieces)', 'category' => 'Mudguards', 'stock' => 95, 'price' => 0],
            ['sku' => 'MUD-003', 'name' => "28' Mudguard", 'category' => 'Mudguards', 'stock' => 20, 'price' => 0],
            ['sku' => 'MUD-004', 'name' => "26' Ballon Mudguard", 'category' => 'Mudguards', 'stock' => 20, 'price' => 0],
            ['sku' => 'MUD-005', 'name' => "26' Buffalo Mudguard", 'category' => 'Mudguards', 'stock' => 20, 'price' => 0],

            // 31. CAPS
            ['sku' => 'CAP-001', 'name' => "28' Rear Cap", 'category' => 'Caps', 'stock' => 20, 'price' => 0],
            ['sku' => 'CAP-002', 'name' => "26' Ballon Rear Cap", 'category' => 'Caps', 'stock' => 20, 'price' => 0],
            ['sku' => 'CAP-003', 'name' => "28' Bottom Cap", 'category' => 'Caps', 'stock' => 20, 'price' => 0],
            ['sku' => 'CAP-004', 'name' => "26' Bottom Cap", 'category' => 'Caps', 'stock' => 20, 'price' => 0],
            ['sku' => 'CAP-005', 'name' => "26' Mountain Bottom Cap", 'category' => 'Caps', 'stock' => 20, 'price' => 0],
            ['sku' => 'CAP-006', 'name' => "28'/26' Ballon/Mountain Front Cap", 'category' => 'Caps', 'stock' => 26, 'price' => 0],
        ];

        foreach ($products as $product) {
            PosProduct::updateOrCreate(
                ['sku' => $product['sku']],
                [
                    'name' => $product['name'],
                    'category' => $product['category'],
                    'stock' => $product['stock'],
                    'price' => $product['price'],
                    'is_active' => true,
                ]
            );
        }
    }
}
