<?php

namespace App\Contracts\Services;

interface TableServiceInterface
{
    public function getAllTables();
    public function createTable(array $data);
    public function updateTable(int $id, array $data);
    public function deleteTable(int $id);
    public function updateStatus(int $id, string $status);
}
