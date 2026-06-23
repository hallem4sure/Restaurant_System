<?php

namespace App\Services;

use App\Contracts\Services\TableServiceInterface;
use App\Models\RestaurantTable;

class TableService implements TableServiceInterface
{
    public function getAllTables()
    {
        return RestaurantTable::orderBy('table_number')->get();
    }

    public function createTable(array $data)
    {
        return RestaurantTable::create($data);
    }

    public function updateTable(int $id, array $data)
    {
        $table = RestaurantTable::findOrFail($id);
        $table->update($data);
        return $table;
    }

    public function deleteTable(int $id)
    {
        $table = RestaurantTable::findOrFail($id);
        $table->delete();
    }

    public function updateStatus(int $id, string $status)
    {
        $table = RestaurantTable::findOrFail($id);
        $table->update(['status' => $status]);
        return $table;
    }
}
