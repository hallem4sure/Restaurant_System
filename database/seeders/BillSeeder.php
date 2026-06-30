<?php

namespace Database\Seeders;

use App\Models\Bill;
use App\Models\Order;
use App\Models\User;
use Illuminate\Database\Seeder;

class BillSeeder extends Seeder
{
    public function run(): void
    {
        $cashier = User::whereHas('roles', fn($q) => $q->where('name', 'cashier'))->first()
                 ?? User::whereHas('roles', fn($q) => $q->where('name', 'admin'))->first()
                 ?? User::first();

        // Pick served/completed orders that don't already have a bill
        $billableOrders = Order::whereIn('status', ['served', 'completed'])
            ->whereDoesntHave('bill')
            ->take(3)
            ->get();

        if ($billableOrders->isEmpty()) {
            $this->command->info('BillSeeder: no billable orders found, skipping.');
            return;
        }

        foreach ($billableOrders as $index => $order) {
            $date = now()->format('Ymd');
            $sequence = Bill::where('bill_number', 'like', "BILL-{$date}-%")
                            ->orderBy('id', 'desc')
                            ->count() + 1;
            $billNumber = sprintf("BILL-%s-%04d", $date, $sequence);

            if ($index === 0) {
                // First bill: paid with cash
                Bill::create([
                    'bill_number'            => $billNumber,
                    'order_id'               => $order->id,
                    'cashier_id'             => $cashier->id,
                    'status'                 => 'paid',
                    'subtotal'               => $order->subtotal,
                    'discount_amount'        => $order->discount_amount,
                    'tax_amount'             => $order->tax_amount,
                    'service_charge_amount'  => $order->service_charge_amount,
                    'total_amount'           => $order->total_amount,
                    'payment_method'         => 'cash',
                    'amount_paid'            => $order->total_amount + 5.00,
                    'change_amount'          => 5.00,
                    'paid_at'                => now()->subMinutes(30),
                    'notes'                  => 'Demo paid bill (cash)',
                ]);
            } elseif ($index === 1) {
                // Second bill: paid with card
                Bill::create([
                    'bill_number'            => $billNumber,
                    'order_id'               => $order->id,
                    'cashier_id'             => $cashier->id,
                    'status'                 => 'paid',
                    'subtotal'               => $order->subtotal,
                    'discount_amount'        => $order->discount_amount,
                    'tax_amount'             => $order->tax_amount,
                    'service_charge_amount'  => $order->service_charge_amount,
                    'total_amount'           => $order->total_amount,
                    'payment_method'         => 'card',
                    'amount_paid'            => $order->total_amount,
                    'change_amount'          => 0,
                    'paid_at'                => now()->subMinutes(10),
                    'notes'                  => 'Demo paid bill (card)',
                ]);
            } else {
                // Third bill: pending (not yet paid)
                Bill::create([
                    'bill_number'            => $billNumber,
                    'order_id'               => $order->id,
                    'status'                 => 'pending',
                    'subtotal'               => $order->subtotal,
                    'discount_amount'        => $order->discount_amount,
                    'tax_amount'             => $order->tax_amount,
                    'service_charge_amount'  => $order->service_charge_amount,
                    'total_amount'           => $order->total_amount,
                ]);
            }
        }
    }
}
