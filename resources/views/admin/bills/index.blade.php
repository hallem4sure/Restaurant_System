@extends('layouts.app')

@section('page_title', 'Bills / POS')

@section('breadcrumbs')
    @include('partials.breadcrumbs', ['crumbs' => [
        ['label' => 'Dashboard', 'url' => route('admin.dashboard')],
        ['label' => 'Bills / POS'],
    ]])
@endsection

@section('main_content')
<div class="row mb-3">
    <div class="col-12 d-flex justify-content-between align-items-center">
        <p class="text-muted mb-0">Manage and track all billing transactions.</p>
        @can('create', \App\Models\Bill::class)
        <button type="button" class="btn btn-success" data-toggle="modal" data-target="#generateBillModal">
            <i class="fas fa-plus mr-1"></i> Generate Bill
        </button>
        @endcan
    </div>
</div>

{{-- Stats Row --}}
<div class="row mb-3">
    <div class="col-md-4">
        <div class="info-box bg-warning">
            <span class="info-box-icon"><i class="fas fa-clock"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Pending Bills</span>
                <span class="info-box-number">{{ $bills->where('status', 'pending')->count() }}</span>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="info-box bg-success">
            <span class="info-box-icon"><i class="fas fa-check-circle"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Paid Today</span>
                <span class="info-box-number">{{ $bills->where('status', 'paid')->where('paid_at', '>=', now()->startOfDay())->count() }}</span>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="info-box bg-danger">
            <span class="info-box-icon"><i class="fas fa-ban"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Cancelled</span>
                <span class="info-box-number">{{ $bills->where('status', 'cancelled')->count() }}</span>
            </div>
        </div>
    </div>
</div>

@if ($bills->isEmpty())
    <div class="card">
        <div class="card-body text-center py-5">
            <i class="fas fa-file-invoice-dollar fa-3x text-muted mb-3"></i>
            <h4 class="text-muted">No Bills Generated Yet</h4>
            <p class="text-muted">Generate your first bill from a completed order to start the POS workflow.</p>
            @can('create', \App\Models\Bill::class)
            <button type="button" class="btn btn-success mt-2" data-toggle="modal" data-target="#generateBillModal">
                <i class="fas fa-plus mr-1"></i> Generate First Bill
            </button>
            @endcan
        </div>
    </div>
@else
<div class="card card-outline card-primary">
    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-file-invoice-dollar mr-1"></i> All Bills</h3>
    </div>
    <div class="card-body p-0 table-responsive">
        <table class="table table-hover table-striped mb-0">
            <thead class="thead-light">
                <tr>
                    <th scope="col">Bill #</th>
                    <th scope="col">Order #</th>
                    <th scope="col">Date</th>
                    <th scope="col">Subtotal</th>
                    <th scope="col">Tax</th>
                    <th scope="col">Total</th>
                    <th scope="col">Payment</th>
                    <th scope="col">Status</th>
                    <th scope="col" class="text-center">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($bills as $bill)
                <tr>
                    <td><strong>{{ $bill->bill_number }}</strong></td>
                    <td>
                        @if ($bill->order)
                            <a href="{{ route('admin.orders.show', $bill->order) }}">{{ $bill->order->order_number }}</a>
                        @else
                            <span class="text-muted">—</span>
                        @endif
                    </td>
                    <td>{{ $bill->created_at->format('d M Y') }}<br>
                        <small class="text-muted">{{ $bill->created_at->format('H:i') }}</small>
                    </td>
                    <td>{{ number_format($bill->subtotal, 2) }}</td>
                    <td>{{ number_format($bill->tax_amount, 2) }}</td>
                    <td><strong>{{ setting('billing.currency_symbol', '$') }}{{ number_format($bill->total_amount, 2) }}</strong></td>
                    <td>
                        @if($bill->payment_method)
                            @php
                                $pmIcons = ['cash'=>'fa-money-bill-wave','card'=>'fa-credit-card','digital_wallet'=>'fa-mobile-alt'];
                                $pmIcon = $pmIcons[$bill->payment_method] ?? 'fa-question';
                            @endphp
                            <i class="fas {{ $pmIcon }} mr-1"></i>{{ ucfirst(str_replace('_',' ',$bill->payment_method)) }}
                        @else
                            <span class="text-muted">—</span>
                        @endif
                    </td>
                    <td>
                        @php
                            $statusColors = ['pending'=>'warning','paid'=>'success','cancelled'=>'danger'];
                            $color = $statusColors[$bill->status] ?? 'secondary';
                        @endphp
                        <span class="badge badge-{{ $color }}">{{ ucfirst($bill->status) }}</span>
                    </td>
                    <td class="text-center" style="white-space:nowrap;">
                        <a href="{{ route('admin.bills.show', $bill) }}" class="btn btn-xs btn-info" title="View Invoice"><i class="fas fa-eye"></i></a>
                        @if($bill->isPending())
                            @can('processPayment', $bill)
                            <a href="{{ route('admin.bills.edit', $bill) }}" class="btn btn-xs btn-success" title="Process Payment"><i class="fas fa-cash-register"></i></a>
                            @endcan
                            @can('delete', $bill)
                            <form action="{{ route('admin.bills.destroy', $bill) }}" method="POST" class="d-inline">
                                @csrf @method('DELETE')
                                <button type="button" class="btn btn-xs btn-danger" title="Cancel Bill"
                                    data-confirm="Cancel bill {{ $bill->bill_number }}? This cannot be undone."
                                    data-confirm-title="Cancel Bill"
                                    data-confirm-icon="warning"
                                    data-confirm-btn="Yes, cancel it">
                                    <i class="fas fa-ban"></i>
                                </button>
                            </form>
                            @endcan
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @if($bills->hasPages())
    <div class="card-footer pb-0">
        {{ $bills->links() }}
    </div>
    @endif
</div>
@endif

{{-- Generate Bill Modal --}}
@can('create', \App\Models\Bill::class)
<div class="modal fade" id="generateBillModal" tabindex="-1" role="dialog" aria-labelledby="generateBillModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="generateBillModalLabel"><i class="fas fa-file-invoice mr-1"></i> Generate New Bill</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('admin.bills.store') }}" method="POST" data-loading>
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label for="order_id">Select Order <span class="text-danger">*</span></label>
                        <select name="order_id" id="order_id" class="form-control @error('order_id') is-invalid @enderror" required>
                            <option value="">— Select a billable order —</option>
                            @foreach(\App\Models\Order::whereIn('status',['served','ready','completed'])->whereDoesntHave('bill')->orderByDesc('created_at')->get() as $order)
                                <option value="{{ $order->id }}">
                                    {{ $order->order_number }} — Table {{ $order->table->table_number ?? 'N/A' }} — {{ number_format($order->total_amount,2) }} ({{ ucfirst($order->status) }})
                                </option>
                            @endforeach
                        </select>
                        @error('order_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success"><i class="fas fa-file-invoice-dollar mr-1"></i> Generate Bill</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endcan
@stop
