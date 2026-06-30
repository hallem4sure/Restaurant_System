<?php

namespace App\Services;

use App\Contracts\Services\DashboardServiceInterface;
use App\Models\Bill;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Reservation;
use App\Models\RestaurantTable;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class DashboardService implements DashboardServiceInterface
{
    /**
     * Get all KPI statistics for the dashboard widgets.
     * Cache for 5 minutes.
     */
    public function getStats(): array
    {
        return Cache::remember('dashboard.stats', 300, function () {
            $today = Carbon::today();

            $todayRevenue = Bill::where('status', 'paid')
                ->whereDate('paid_at', $today)
                ->sum('total_amount');

            $ordersToday = Order::whereDate('created_at', $today)->count();

            $pendingOrders = Order::whereIn('status', ['pending', 'confirmed', 'preparing'])->count();

            $activeReservations = Reservation::whereIn('status', ['pending', 'confirmed'])
                ->whereDate('reserved_at', '>=', $today)
                ->count();

            $totalTables   = RestaurantTable::count();
            $occupiedTables = RestaurantTable::where('status', 'occupied')->count();
            $availableTables = RestaurantTable::where('status', 'available')->count();

            $pendingBills = Bill::where('status', 'pending')->count();

            $kitchenQueue = OrderItem::whereIn('kitchen_status', ['pending', 'preparing'])->count();

            return compact(
                'todayRevenue',
                'ordersToday',
                'pendingOrders',
                'activeReservations',
                'totalTables',
                'occupiedTables',
                'availableTables',
                'pendingBills',
                'kitchenQueue'
            );
        });
    }

    /**
     * Revenue for each of the past 7 days (labels + values).
     */
    public function getRevenueChart(): array
    {
        return Cache::remember('dashboard.revenue_chart', 600, function () {
            $days = collect(range(6, 0))->map(function ($daysAgo) {
                $date = Carbon::today()->subDays($daysAgo);
                $revenue = Bill::where('status', 'paid')
                    ->whereDate('paid_at', $date)
                    ->sum('total_amount');

                return [
                    'label'   => $date->format('D d'),
                    'revenue' => (float) $revenue,
                ];
            });

            return [
                'labels' => $days->pluck('label')->toArray(),
                'data'   => $days->pluck('revenue')->toArray(),
            ];
        });
    }

    /**
     * Order counts grouped by status.
     */
    public function getOrdersByStatusChart(): array
    {
        return Cache::remember('dashboard.orders_status_chart', 300, function () {
            $statuses = ['pending', 'confirmed', 'preparing', 'ready', 'completed', 'cancelled'];
            $counts = Order::select('status', DB::raw('count(*) as total'))
                ->whereIn('status', $statuses)
                ->groupBy('status')
                ->pluck('total', 'status')
                ->toArray();

            $data = [];
            $labels = [];
            foreach ($statuses as $status) {
                $labels[] = ucfirst($status);
                $data[]   = $counts[$status] ?? 0;
            }

            return compact('labels', 'data');
        });
    }

    /**
     * Top 5 best-selling menu items by total quantity sold.
     */
    public function getTopMenuItems(): array
    {
        return Cache::remember('dashboard.top_items', 600, function () {
            return OrderItem::select('menu_item_id', DB::raw('SUM(quantity) as total_sold'))
                ->with('menuItem:id,name')
                ->groupBy('menu_item_id')
                ->orderByDesc('total_sold')
                ->limit(5)
                ->get()
                ->map(fn($item) => [
                    'name'  => $item->menuItem->name ?? 'Unknown',
                    'total' => (int) $item->total_sold,
                ])
                ->toArray();
        });
    }

    /**
     * 8 most recent orders with waiter and table.
     */
    public function getRecentOrders(): \Illuminate\Database\Eloquent\Collection
    {
        return Order::with(['waiter:id,name', 'table:id,table_number'])
            ->latest()
            ->limit(8)
            ->get();
    }

    /**
     * 8 most recent reservations with table.
     */
    public function getRecentReservations(): \Illuminate\Database\Eloquent\Collection
    {
        return Reservation::with(['table:id,table_number'])
            ->latest()
            ->limit(8)
            ->get();
    }
}
