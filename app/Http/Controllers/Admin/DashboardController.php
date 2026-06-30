<?php

namespace App\Http\Controllers\Admin;

use App\Contracts\Services\DashboardServiceInterface;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __construct(
        protected DashboardServiceInterface $dashboardService
    ) {}

    /**
     * Display the admin dashboard.
     */
    public function index()
    {
        $stats = $this->dashboardService->getStats();
        $revenueChart = $this->dashboardService->getRevenueChart();
        $ordersStatusChart = $this->dashboardService->getOrdersByStatusChart();
        $topMenuItems = $this->dashboardService->getTopMenuItems();
        $recentOrders = $this->dashboardService->getRecentOrders();
        $recentReservations = $this->dashboardService->getRecentReservations();

        return view('admin.dashboard', compact(
            'stats',
            'revenueChart',
            'ordersStatusChart',
            'topMenuItems',
            'recentOrders',
            'recentReservations'
        ));
    }
}
