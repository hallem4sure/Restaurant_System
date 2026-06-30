<?php

namespace App\Http\Controllers\Admin;

use App\Contracts\Services\ReportServiceInterface;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Gate;

class ReportController extends Controller
{
    public function __construct(
        protected ReportServiceInterface $reportService
    ) {}

    public function index(Request $request)
    {
        Gate::authorize('view reports');

        $startDate = $request->input('start_date', Carbon::today()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->input('end_date', Carbon::today()->endOfMonth()->format('Y-m-d'));
        
        $activeTab = $request->input('tab', 'sales');

        $kpis = $this->reportService->getSummaryKPIs($startDate, $endDate);
        
        // We'll load the data for all tabs since we are caching them, it shouldn't be too heavy.
        $salesData = $this->reportService->getSalesData($startDate, $endDate, 'date');
        $revenueData = $this->reportService->getRevenueData($startDate, $endDate);
        $ordersData = $this->reportService->getOrdersData($startDate, $endDate);
        $menuData = $this->reportService->getMenuAnalytics($startDate, $endDate);
        $reservationsData = $this->reportService->getReservationsData($startDate, $endDate);
        $staffData = $this->reportService->getStaffPerformance($startDate, $endDate);

        return view('admin.reports.index', compact(
            'startDate',
            'endDate',
            'activeTab',
            'kpis',
            'salesData',
            'revenueData',
            'ordersData',
            'menuData',
            'reservationsData',
            'staffData'
        ));
    }

    public function export(Request $request, string $type)
    {
        Gate::authorize('view reports');

        $startDate = $request->input('start_date', Carbon::today()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->input('end_date', Carbon::today()->endOfMonth()->format('Y-m-d'));

        return $this->reportService->exportCsv($type, $startDate, $endDate);
    }
    
    public function print(Request $request)
    {
        Gate::authorize('view reports');

        $startDate = $request->input('start_date', Carbon::today()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->input('end_date', Carbon::today()->endOfMonth()->format('Y-m-d'));
        $activeTab = $request->input('tab', 'sales');
        
        $data = [];
        
        switch ($activeTab) {
            case 'sales':
                $data = $this->reportService->getSalesData($startDate, $endDate, 'date');
                break;
            case 'revenue':
                $data = $this->reportService->getRevenueData($startDate, $endDate);
                break;
            case 'orders':
                $data = $this->reportService->getOrdersData($startDate, $endDate);
                break;
            case 'menu':
                $data = $this->reportService->getMenuAnalytics($startDate, $endDate);
                break;
            case 'reservations':
                $data = $this->reportService->getReservationsData($startDate, $endDate);
                break;
            case 'staff':
                $data = $this->reportService->getStaffPerformance($startDate, $endDate);
                break;
        }

        return view('admin.reports.print', compact(
            'startDate',
            'endDate',
            'activeTab',
            'data'
        ));
    }
}
