@extends('layouts.app')

@section('page_title', 'Reports')

@section('custom_css')
<style>
    .trend-up { color: #28a745; }
    .trend-down { color: #dc3545; }
    .trend-neutral { color: #6c757d; }
    @media print {
        .no-print { display: none !important; }
    }
</style>
@stop

@section('main_content')
    <div class="row mb-3 no-print">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body pb-0">
                    <form action="{{ route('admin.reports.index') }}" method="GET" class="form-inline mb-3">
                        <input type="hidden" name="tab" value="{{ $activeTab }}">
                        
                        <div class="form-group mr-3">
                            <label for="start_date" class="mr-2">Start Date:</label>
                            <input type="date" name="start_date" id="start_date" class="form-control" value="{{ $startDate }}" required>
                        </div>
                        
                        <div class="form-group mr-3">
                            <label for="end_date" class="mr-2">End Date:</label>
                            <input type="date" name="end_date" id="end_date" class="form-control" value="{{ $endDate }}" required>
                        </div>
                        
                        <button type="submit" class="btn btn-primary mr-2"><i class="fas fa-filter"></i> Apply Filter</button>
                        <a href="{{ route('admin.reports.index') }}" class="btn btn-default">Reset</a>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @php
        $sym = setting('billing.currency_symbol', '$');
        $pos = setting('billing.currency_position', 'before');
        $formatCurrency = function($amount) use ($sym, $pos) {
            return $pos === 'before' ? $sym . number_format($amount, 2) : number_format($amount, 2) . $sym;
        };
    @endphp

    <!-- Summary KPIs -->
    <div class="row">
        <div class="col-lg-6 col-6">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3>{{ $formatCurrency($kpis['revenue']['current']) }}</h3>
                    <p>Total Revenue (Selected Period)</p>
                    <small>
                        @if($kpis['revenue']['trend'] > 0)
                            <i class="fas fa-arrow-up text-white"></i> {{ $kpis['revenue']['trend'] }}% vs prev period
                        @elseif($kpis['revenue']['trend'] < 0)
                            <i class="fas fa-arrow-down text-white"></i> {{ abs($kpis['revenue']['trend']) }}% vs prev period
                        @else
                            <i class="fas fa-minus text-white"></i> Same as prev period
                        @endif
                    </small>
                </div>
                <div class="icon">
                    <i class="fas fa-dollar-sign"></i>
                </div>
            </div>
        </div>

        <div class="col-lg-6 col-6">
            <div class="small-box bg-info">
                <div class="inner">
                    <h3>{{ $kpis['orders']['current'] }}</h3>
                    <p>Total Orders (Selected Period)</p>
                    <small>
                        @if($kpis['orders']['trend'] > 0)
                            <i class="fas fa-arrow-up text-white"></i> {{ $kpis['orders']['trend'] }}% vs prev period
                        @elseif($kpis['orders']['trend'] < 0)
                            <i class="fas fa-arrow-down text-white"></i> {{ abs($kpis['orders']['trend']) }}% vs prev period
                        @else
                            <i class="fas fa-minus text-white"></i> Same as prev period
                        @endif
                    </small>
                </div>
                <div class="icon">
                    <i class="fas fa-shopping-cart"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Tabs -->
    <div class="card card-primary card-outline card-outline-tabs">
        <div class="card-header p-0 border-bottom-0">
            <div class="d-flex justify-content-between align-items-center pr-3">
                <ul class="nav nav-tabs" id="custom-tabs-four-tab" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link {{ $activeTab == 'sales' ? 'active' : '' }}" href="{{ request()->fullUrlWithQuery(['tab' => 'sales']) }}">Sales</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ $activeTab == 'revenue' ? 'active' : '' }}" href="{{ request()->fullUrlWithQuery(['tab' => 'revenue']) }}">Revenue Breakdown</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ $activeTab == 'orders' ? 'active' : '' }}" href="{{ request()->fullUrlWithQuery(['tab' => 'orders']) }}">Orders</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ $activeTab == 'menu' ? 'active' : '' }}" href="{{ request()->fullUrlWithQuery(['tab' => 'menu']) }}">Menu Analytics</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ $activeTab == 'reservations' ? 'active' : '' }}" href="{{ request()->fullUrlWithQuery(['tab' => 'reservations']) }}">Reservations</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ $activeTab == 'staff' ? 'active' : '' }}" href="{{ request()->fullUrlWithQuery(['tab' => 'staff']) }}">Staff Performance</a>
                    </li>
                </ul>
                <div class="btn-group no-print">
                    <a href="{{ route('admin.reports.export', ['type' => $activeTab, 'start_date' => $startDate, 'end_date' => $endDate]) }}" class="btn btn-sm btn-success">
                        <i class="fas fa-file-csv"></i> Export CSV
                    </a>
                    <a href="{{ route('admin.reports.print', ['tab' => $activeTab, 'start_date' => $startDate, 'end_date' => $endDate]) }}" target="_blank" class="btn btn-sm btn-secondary">
                        <i class="fas fa-print"></i> Print
                    </a>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="tab-content" id="custom-tabs-four-tabContent">
                
                <!-- SALES TAB -->
                @if($activeTab == 'sales')
                    <div class="tab-pane fade show active" role="tabpanel">
                        @if(empty($salesData['raw']))
                            <div class="alert alert-info"><i class="fas fa-info-circle"></i> No sales data found for the selected period.</div>
                        @else
                            <canvas id="salesChart" style="height: 300px; max-height: 300px; max-width: 100%;"></canvas>
                            <table class="table table-striped mt-4">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Total Orders</th>
                                        <th>Revenue</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($salesData['raw'] as $row)
                                    <tr>
                                        <td>{{ $row['label'] }}</td>
                                        <td>{{ $row['count'] }}</td>
                                        <td>{{ $formatCurrency($row['total']) }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @endif
                    </div>
                @endif

                <!-- REVENUE TAB -->
                @if($activeTab == 'revenue')
                    <div class="tab-pane fade show active" role="tabpanel">
                        <div class="row">
                            <div class="col-md-6">
                                <table class="table table-bordered table-striped">
                                    <tbody>
                                        <tr>
                                            <th>Gross Revenue</th>
                                            <td>{{ $formatCurrency($revenueData['gross_revenue']) }}</td>
                                        </tr>
                                        <tr>
                                            <th>Total Discounts</th>
                                            <td class="text-danger">- {{ $formatCurrency($revenueData['total_discount']) }}</td>
                                        </tr>
                                        <tr>
                                            <th>Tax Collected</th>
                                            <td>{{ $formatCurrency($revenueData['tax_collected']) }}</td>
                                        </tr>
                                        <tr>
                                            <th>Service Charges</th>
                                            <td>{{ $formatCurrency($revenueData['service_charges']) }}</td>
                                        </tr>
                                        <tr class="bg-success text-white">
                                            <th>Net Revenue</th>
                                            <th>{{ $formatCurrency($revenueData['net_revenue']) }}</th>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- ORDERS TAB -->
                @if($activeTab == 'orders')
                    <div class="tab-pane fade show active" role="tabpanel">
                        @if($ordersData['total_orders'] == 0)
                            <div class="alert alert-info"><i class="fas fa-info-circle"></i> No orders found for the selected period.</div>
                        @else
                            <div class="row">
                                <div class="col-md-6">
                                    <h5>Average Order Value: <strong>{{ $formatCurrency($ordersData['avg_value']) }}</strong></h5>
                                    <h5 class="mt-3">Orders by Status</h5>
                                    <canvas id="ordersStatusChart" style="height: 250px; max-height: 250px; max-width: 100%;"></canvas>
                                </div>
                                <div class="col-md-6">
                                    <h5 class="mt-3">Orders by Type</h5>
                                    <canvas id="ordersTypeChart" style="height: 250px; max-height: 250px; max-width: 100%;"></canvas>
                                </div>
                            </div>
                        @endif
                    </div>
                @endif

                <!-- MENU ANALYTICS TAB -->
                @if($activeTab == 'menu')
                    <div class="tab-pane fade show active" role="tabpanel">
                        @if($menuData['all_items']->isEmpty())
                            <div class="alert alert-info"><i class="fas fa-info-circle"></i> No menu items sold in the selected period.</div>
                        @else
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="card card-success card-outline">
                                        <div class="card-header"><h3 class="card-title">Best Selling Items</h3></div>
                                        <div class="card-body p-0">
                                            <table class="table table-sm">
                                                <thead><tr><th>Item</th><th>Qty</th><th>Revenue</th></tr></thead>
                                                <tbody>
                                                    @foreach($menuData['best_selling'] as $item)
                                                    <tr>
                                                        <td>{{ $item->menuItem->name ?? 'Unknown' }}</td>
                                                        <td>{{ $item->total_quantity }}</td>
                                                        <td>{{ $formatCurrency($item->total_revenue) }}</td>
                                                    </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="card card-danger card-outline">
                                        <div class="card-header"><h3 class="card-title">Worst Selling Items</h3></div>
                                        <div class="card-body p-0">
                                            <table class="table table-sm">
                                                <thead><tr><th>Item</th><th>Qty</th><th>Revenue</th></tr></thead>
                                                <tbody>
                                                    @foreach($menuData['worst_selling'] as $item)
                                                    <tr>
                                                        <td>{{ $item->menuItem->name ?? 'Unknown' }}</td>
                                                        <td>{{ $item->total_quantity }}</td>
                                                        <td>{{ $formatCurrency($item->total_revenue) }}</td>
                                                    </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row mt-4">
                                <div class="col-md-12">
                                    <h5>Category Performance</h5>
                                    <table class="table table-striped table-bordered">
                                        <thead>
                                            <tr>
                                                <th>Category</th>
                                                <th>Items Sold</th>
                                                <th>Revenue</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($menuData['by_category'] as $cat => $data)
                                            <tr>
                                                <td>{{ $cat }}</td>
                                                <td>{{ $data['quantity'] }}</td>
                                                <td>{{ $formatCurrency($data['revenue']) }}</td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        @endif
                    </div>
                @endif

                <!-- RESERVATIONS TAB -->
                @if($activeTab == 'reservations')
                    <div class="tab-pane fade show active" role="tabpanel">
                        @if(empty($reservationsData['by_status']))
                            <div class="alert alert-info"><i class="fas fa-info-circle"></i> No reservations found for the selected period.</div>
                        @else
                            <div class="row">
                                <div class="col-md-6">
                                    <h5>Reservations by Status</h5>
                                    <canvas id="reservationsStatusChart" style="height: 250px; max-height: 250px; max-width: 100%;"></canvas>
                                </div>
                                <div class="col-md-6">
                                    <h5>Reservations per Day</h5>
                                    <table class="table table-striped mt-3">
                                        <thead>
                                            <tr><th>Date</th><th>Count</th></tr>
                                        </thead>
                                        <tbody>
                                            @for($i=0; $i < count($reservationsData['by_day']['labels']); $i++)
                                                <tr>
                                                    <td>{{ $reservationsData['by_day']['labels'][$i] }}</td>
                                                    <td>{{ $reservationsData['by_day']['counts'][$i] }}</td>
                                                </tr>
                                            @endfor
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        @endif
                    </div>
                @endif

                <!-- STAFF TAB -->
                @if($activeTab == 'staff')
                    <div class="tab-pane fade show active" role="tabpanel">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="card card-primary card-outline">
                                    <div class="card-header"><h3 class="card-title">Orders Handled per Waiter</h3></div>
                                    <div class="card-body p-0">
                                        @if($staffData['waiters']->isEmpty())
                                            <p class="p-3 text-muted">No data available.</p>
                                        @else
                                        <table class="table table-striped">
                                            <thead><tr><th>Staff Name</th><th>Orders</th><th>Revenue</th></tr></thead>
                                            <tbody>
                                                @foreach($staffData['waiters'] as $w)
                                                <tr>
                                                    <td>{{ $w->waiter->name ?? 'Unknown' }}</td>
                                                    <td>{{ $w->total_orders }}</td>
                                                    <td>{{ $formatCurrency($w->total_revenue) }}</td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card card-info card-outline">
                                    <div class="card-header"><h3 class="card-title">Bills Processed per Cashier</h3></div>
                                    <div class="card-body p-0">
                                        @if($staffData['cashiers']->isEmpty())
                                            <p class="p-3 text-muted">No data available.</p>
                                        @else
                                        <table class="table table-striped">
                                            <thead><tr><th>Staff Name</th><th>Bills</th><th>Revenue</th></tr></thead>
                                            <tbody>
                                                @foreach($staffData['cashiers'] as $c)
                                                <tr>
                                                    <td>{{ $c->cashier->name ?? 'Unknown' }}</td>
                                                    <td>{{ $c->total_bills }}</td>
                                                    <td>{{ $formatCurrency($c->total_revenue) }}</td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
                
            </div>
        </div>
    </div>
@stop

@section('custom_js')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const activeTab = "{{ $activeTab }}";

    if (activeTab === 'sales' && document.getElementById('salesChart')) {
        new Chart(document.getElementById('salesChart').getContext('2d'), {
            type: 'bar',
            data: {
                labels: {!! json_encode($salesData['labels'] ?? []) !!},
                datasets: [{
                    label: 'Revenue',
                    data: {!! json_encode($salesData['totals'] ?? []) !!},
                    backgroundColor: 'rgba(40, 167, 69, 0.7)',
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: { y: { beginAtZero: true } }
            }
        });
    }

    if (activeTab === 'orders' && document.getElementById('ordersStatusChart')) {
        const statuses = {!! json_encode($ordersData['by_status'] ?? []) !!};
        new Chart(document.getElementById('ordersStatusChart').getContext('2d'), {
            type: 'pie',
            data: {
                labels: Object.keys(statuses),
                datasets: [{
                    data: Object.values(statuses),
                    backgroundColor: ['#ffc107', '#17a2b8', '#007bff', '#28a745', '#dc3545', '#6c757d']
                }]
            },
            options: { responsive: true, maintainAspectRatio: false }
        });

        const types = {!! json_encode($ordersData['by_type'] ?? []) !!};
        new Chart(document.getElementById('ordersTypeChart').getContext('2d'), {
            type: 'pie',
            data: {
                labels: Object.keys(types),
                datasets: [{
                    data: Object.values(types),
                    backgroundColor: ['#6f42c1', '#fd7e14', '#20c997']
                }]
            },
            options: { responsive: true, maintainAspectRatio: false }
        });
    }

    if (activeTab === 'reservations' && document.getElementById('reservationsStatusChart')) {
        const rStatuses = {!! json_encode($reservationsData['by_status'] ?? []) !!};
        new Chart(document.getElementById('reservationsStatusChart').getContext('2d'), {
            type: 'pie',
            data: {
                labels: Object.keys(rStatuses),
                datasets: [{
                    data: Object.values(rStatuses),
                    backgroundColor: ['#ffc107', '#007bff', '#28a745', '#dc3545', '#6c757d']
                }]
            },
            options: { responsive: true, maintainAspectRatio: false }
        });
    }
});
</script>
@stop
