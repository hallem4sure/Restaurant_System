<?php

namespace App\Contracts\Services;

interface ReportServiceInterface
{
    public function getSummaryKPIs(string $startDate, string $endDate): array;
    public function getSalesData(string $startDate, string $endDate, string $groupBy): array;
    public function getRevenueData(string $startDate, string $endDate): array;
    public function getOrdersData(string $startDate, string $endDate): array;
    public function getMenuAnalytics(string $startDate, string $endDate): array;
    public function getReservationsData(string $startDate, string $endDate): array;
    public function getStaffPerformance(string $startDate, string $endDate): array;
    
    public function exportCsv(string $type, string $startDate, string $endDate): \Symfony\Component\HttpFoundation\StreamedResponse;
}
