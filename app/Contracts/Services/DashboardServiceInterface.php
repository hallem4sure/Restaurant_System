<?php

namespace App\Contracts\Services;

interface DashboardServiceInterface
{
    public function getStats(): array;
    public function getRevenueChart(): array;
    public function getOrdersByStatusChart(): array;
    public function getTopMenuItems(): array;
    public function getRecentOrders(): \Illuminate\Database\Eloquent\Collection;
    public function getRecentReservations(): \Illuminate\Database\Eloquent\Collection;
}
