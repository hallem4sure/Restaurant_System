<?php

namespace App\Services;

use App\Contracts\Services\BillServiceInterface;
use App\Models\Bill;
use App\Models\Order;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Str;

class BillService implements BillServiceInterface
{
    public function getAllBills(int $perPage = 15): LengthAwarePaginator
    {
        return Bill::with(['order', 'cashier'])->orderByDesc('created_at')->paginate($perPage);
    }

    public function findBill(int $id): Bill
    {
        return Bill::with(['order.items.menuItem', 'cashier'])->findOrFail($id);
    }

    public function generateBill(int $orderId): Bill
    {
        $order = Order::findOrFail($orderId);

        // Check if bill already exists
        if ($order->bill) {
            return $order->bill;
        }

        // Generate unique bill number (e.g. BILL-YYYYMMDD-XXXX)
        $date = now()->format('Ymd');
        $lastBill = Bill::where('bill_number', 'like', "BILL-{$date}-%")->orderBy('id', 'desc')->first();
        $sequence = $lastBill ? ((int) substr($lastBill->bill_number, -4)) + 1 : 1;
        $billNumber = sprintf("BILL-%s-%04d", $date, $sequence);

        $bill = Bill::create([
            'bill_number' => $billNumber,
            'order_id' => $order->id,
            'status' => 'pending',
            'subtotal' => $order->subtotal,
            'discount_amount' => $order->discount_amount,
            'tax_amount' => $order->tax_amount,
            'service_charge_amount' => $order->service_charge_amount,
            'total_amount' => $order->total_amount,
        ]);

        return $bill;
    }

    public function processPayment(int $billId, array $paymentData): Bill
    {
        $bill = Bill::findOrFail($billId);

        $amountPaid = $paymentData['amount_paid'];
        $changeAmount = $amountPaid > $bill->total_amount ? $amountPaid - $bill->total_amount : 0;

        $bill->update([
            'status' => 'paid',
            'cashier_id' => auth()->id(),
            'payment_method' => $paymentData['payment_method'],
            'amount_paid' => $amountPaid,
            'change_amount' => $changeAmount,
            'paid_at' => now(),
            'notes' => $paymentData['notes'] ?? null,
        ]);

        // Automatically mark the order as completed
        $bill->order->update(['status' => 'completed']);
        
        // Also free up the table if there is one
        if ($bill->order->table) {
            $bill->order->table->update(['status' => 'available']);
        }

        return $bill;
    }

    public function cancelBill(int $billId): Bill
    {
        $bill = Bill::findOrFail($billId);
        
        $bill->update([
            'status' => 'cancelled',
            'notes' => 'Bill cancelled by ' . auth()->user()->name,
        ]);

        return $bill;
    }
}
