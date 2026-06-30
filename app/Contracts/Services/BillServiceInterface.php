<?php

namespace App\Contracts\Services;

use App\Models\Bill;
use Illuminate\Pagination\LengthAwarePaginator;

interface BillServiceInterface
{
    public function getAllBills(int $perPage = 15): LengthAwarePaginator;
    public function findBill(int $id): Bill;
    public function generateBill(int $orderId): Bill;
    public function processPayment(int $billId, array $paymentData): Bill;
    public function cancelBill(int $billId): Bill;
}
