@extends('layouts.app')

@section('page_title', 'Kitchen Dashboard')

@section('breadcrumbs')
    @include('partials.breadcrumbs', ['crumbs' => [
        ['label' => 'Dashboard', 'url' => route('admin.dashboard')],
        ['label' => 'Kitchen Dashboard'],
    ]])
@endsection

@section('custom_css')
<style>
    /* KDS Dark/High Contrast Styles */
    .kds-board {
        background-color: #1a1a1a;
        min-height: calc(100vh - 120px);
        padding: 15px;
        margin: -15px;
        border-radius: 5px;
        overflow-x: auto;
        white-space: nowrap;
    }
    .kds-order-card {
        display: inline-block;
        vertical-align: top;
        width: 350px;
        margin-right: 15px;
        background-color: #2c2c2c;
        color: #ffffff;
        border-radius: 8px;
        border: 2px solid #444;
        white-space: normal;
        box-shadow: 0 4px 6px rgba(0,0,0,0.3);
    }
    .kds-header {
        padding: 15px;
        border-bottom: 2px solid #444;
        background-color: #333;
        border-radius: 6px 6px 0 0;
    }
    .kds-header h4 {
        margin: 0 0 10px 0;
        font-weight: bold;
        font-size: 1.25rem;
    }
    .kds-meta {
        display: flex;
        justify-content: space-between;
        align-items: center;
        font-size: 0.95rem;
    }
    .kds-timer {
        font-family: monospace;
        font-size: 1.2rem;
        font-weight: bold;
        padding: 4px 8px;
        border-radius: 4px;
    }
    .timer-green { background-color: #28a745; color: white; }
    .timer-yellow { background-color: #ffc107; color: black; }
    .timer-red { background-color: #dc3545; color: white; }
    
    .kds-body {
        padding: 0;
    }
    .kds-item {
        padding: 12px 15px;
        border-bottom: 1px solid #444;
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
    }
    .kds-item:last-child {
        border-bottom: none;
    }
    .kds-qty {
        font-weight: bold;
        font-size: 1.2rem;
        margin-right: 10px;
        color: #17a2b8;
    }
    .kds-item-details {
        flex-grow: 1;
    }
    .kds-item-name {
        font-size: 1.1rem;
        font-weight: bold;
    }
    .kds-notes {
        color: #ffc107;
        font-size: 0.9rem;
        margin-top: 4px;
        background: rgba(255,193,7,0.1);
        padding: 4px;
        border-radius: 4px;
        border-left: 3px solid #ffc107;
    }
    .kds-item-action {
        min-width: 100px;
        text-align: right;
    }
    .kds-footer {
        padding: 15px;
        background-color: #333;
        border-top: 2px solid #444;
        border-radius: 0 0 6px 6px;
    }
    
    /* Disable invalid transitions visually */
    option:disabled {
        color: #888;
        background: #eee;
    }
</style>
@endsection

@section('main_content')
<div class="kds-board">
    @forelse($activeOrders as $order)
        <div class="kds-order-card" data-created-at="{{ $order->created_at->toIso8601String() }}">
            <div class="kds-header">
                <h4>
                    #{{ $order->order_number }}
                    @if($order->table)
                        <span class="float-right text-info">Table {{ $order->table->table_number }}</span>
                    @else
                        <span class="float-right text-secondary">{{ ucfirst(str_replace('_', ' ', $order->type)) }}</span>
                    @endif
                </h4>
                <div class="kds-meta">
                    <span class="badge badge-{{ $order->status === 'preparing' ? 'warning' : 'light' }}" style="font-size:1rem;">
                        {{ strtoupper($order->status) }}
                    </span>
                    <span class="kds-timer timer-green" id="timer-{{ $order->id }}">00:00</span>
                </div>
            </div>
            
            <div class="kds-body">
                @foreach($order->items as $item)
                    <div class="kds-item">
                        <div class="kds-qty">{{ $item->quantity }}x</div>
                        <div class="kds-item-details">
                            <div class="kds-item-name">{{ $item->menuItem->name }}</div>
                            @if($item->special_instructions)
                                <div class="kds-notes">
                                    <i class="fas fa-exclamation-triangle"></i> {{ $item->special_instructions }}
                                </div>
                            @endif
                        </div>
                        <div class="kds-item-action">
                            <form action="{{ route('admin.kitchen.item.update-status', $item) }}" method="POST">
                                @csrf
                                @method('PATCH')
                                <select name="status" class="form-control form-control-sm" onchange="this.form.submit()" 
                                    style="background-color: {{ $item->kitchen_status === 'ready' ? '#d4edda' : ($item->kitchen_status === 'preparing' ? '#fff3cd' : '#fff') }}; color: #333;">
                                    <option value="pending" {{ $item->kitchen_status === 'pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="preparing" {{ $item->kitchen_status === 'preparing' ? 'selected' : '' }}>Preparing</option>
                                    <option value="ready" {{ $item->kitchen_status === 'ready' ? 'selected' : '' }} 
                                        {{ $item->kitchen_status === 'pending' ? 'disabled' : '' }}>Ready</option>
                                </select>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>
            
            <div class="kds-footer">
                <form action="{{ route('admin.kitchen.order.update-status', $order) }}" method="POST">
                    @csrf
                    @method('PATCH')
                    @php
                        $hasPendingOrPreparing = $order->items->whereIn('kitchen_status', ['pending', 'preparing'])->count() > 0;
                    @endphp
                    <div class="input-group">
                        <select name="status" class="form-control" style="background-color: #fff; color: #333;">
                            <option value="pending" {{ $order->status === 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="preparing" {{ $order->status === 'preparing' ? 'selected' : '' }}>Preparing</option>
                            <option value="ready" {{ $order->status === 'ready' ? 'selected' : '' }}
                                {{ $order->status === 'pending' ? 'disabled' : '' }}
                                {{ $hasPendingOrPreparing ? 'disabled' : '' }}>Ready</option>
                        </select>
                        <span class="input-group-append">
                            <button type="submit" class="btn btn-primary" {{ ($order->status === 'pending' && !$hasPendingOrPreparing) ? '' : '' }}>Update</button>
                        </span>
                    </div>
                    @if($hasPendingOrPreparing && $order->status !== 'ready')
                        <small class="text-muted mt-1 d-block">Cannot mark order as ready until all items are ready.</small>
                    @endif
                </form>
            </div>
        </div>
    @empty
        <div class="text-center w-100" style="margin-top: 100px; color: #888;">
            <i class="fas fa-check-circle fa-4x mb-3"></i>
            <h2>All Caught Up!</h2>
            <p>There are no active orders in the kitchen.</p>
        </div>
    @endforelse
</div>
@endsection

@section('custom_js')
<script>
    function updateTimers() {
        const cards = document.querySelectorAll('.kds-order-card');
        const now = new Date();
        
        cards.forEach(card => {
            const createdAtStr = card.getAttribute('data-created-at');
            if (!createdAtStr) return;
            
            const createdAt = new Date(createdAtStr);
            const diffMs = now - createdAt;
            
            const diffMins = Math.floor(diffMs / 60000);
            const diffSecs = Math.floor((diffMs % 60000) / 1000);
            
            const timerEl = card.querySelector('.kds-timer');
            if (!timerEl) return;
            
            timerEl.innerText = String(diffMins).padStart(2, '0') + ':' + String(diffSecs).padStart(2, '0');
            
            // Remove existing color classes
            timerEl.classList.remove('timer-green', 'timer-yellow', 'timer-red');
            
            // Apply new color class based on elapsed time
            if (diffMins < 10) {
                timerEl.classList.add('timer-green');
            } else if (diffMins >= 10 && diffMins < 20) {
                timerEl.classList.add('timer-yellow');
            } else {
                timerEl.classList.add('timer-red');
            }
        });
    }

    // Update timers every second
    setInterval(updateTimers, 1000);
    // Initial call
    updateTimers();
</script>
@endsection
