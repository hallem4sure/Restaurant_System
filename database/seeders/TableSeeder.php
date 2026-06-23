<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\RestaurantTable;

class TableSeeder extends Seeder
{
    public function run(): void
    {
        $tables = [
            // Public small tables
            ['table_number' => 'T1', 'capacity' => 2, 'status' => 'available', 'is_private' => false],
            ['table_number' => 'T2', 'capacity' => 2, 'status' => 'available', 'is_private' => false],
            ['table_number' => 'T3', 'capacity' => 2, 'status' => 'occupied', 'is_private' => false],
            
            // Public medium tables
            ['table_number' => 'T4', 'capacity' => 4, 'status' => 'available', 'is_private' => false],
            ['table_number' => 'T5', 'capacity' => 4, 'status' => 'reserved', 'is_private' => false],
            ['table_number' => 'T6', 'capacity' => 4, 'status' => 'available', 'is_private' => false],
            
            // Public large tables
            ['table_number' => 'T7', 'capacity' => 6, 'status' => 'available', 'is_private' => false],
            ['table_number' => 'T8', 'capacity' => 8, 'status' => 'maintenance', 'is_private' => false],
            
            // Private tables
            ['table_number' => 'VIP-1', 'capacity' => 4, 'status' => 'available', 'is_private' => true],
            ['table_number' => 'VIP-2', 'capacity' => 10, 'status' => 'reserved', 'is_private' => true],
        ];

        foreach ($tables as $table) {
            RestaurantTable::updateOrCreate(
                ['table_number' => $table['table_number']],
                $table
            );
        }
    }
}
