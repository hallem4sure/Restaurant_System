@php
    $days = ['monday','tuesday','wednesday','thursday','friday','saturday','sunday'];
@endphp

@extends('layouts.app')

@section('page_title', 'Create Offer')

@section('main_content')
<form action="{{ route('admin.offers.store') }}" method="POST">
@csrf

<div class="row">
    {{-- Left column: Core details --}}
    <div class="col-lg-8">
        <div class="card card-primary card-outline">
            <div class="card-header"><h3 class="card-title">Offer Details</h3></div>
            <div class="card-body">

                <div class="form-group">
                    <label for="name">Offer Name <span class="text-danger">*</span></label>
                    <input type="text" name="name" id="name"
                           class="form-control @error('name') is-invalid @enderror"
                           value="{{ old('name') }}" placeholder="e.g. Happy Hour 20%" required>
                    @error('name')<span class="invalid-feedback">{{ $message }}</span>@enderror
                </div>

                <div class="form-group">
                    <label for="description">Description</label>
                    <textarea name="description" id="description" rows="3"
                              class="form-control @error('description') is-invalid @enderror"
                              placeholder="Optional public-facing description">{{ old('description') }}</textarea>
                    @error('description')<span class="invalid-feedback">{{ $message }}</span>@enderror
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="type">Discount Type <span class="text-danger">*</span></label>
                            <select name="type" id="type" class="form-control @error('type') is-invalid @enderror" required>
                                <option value="percentage" {{ old('type') === 'percentage' ? 'selected' : '' }}>Percentage (%)</option>
                                <option value="fixed" {{ old('type') === 'fixed' ? 'selected' : '' }}>Fixed Amount</option>
                            </select>
                            @error('type')<span class="invalid-feedback">{{ $message }}</span>@enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="value">Discount Value <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="number" name="value" id="value" step="0.01" min="0.01"
                                       class="form-control @error('value') is-invalid @enderror"
                                       value="{{ old('value') }}" required>
                                <div class="input-group-append">
                                    <span class="input-group-text" id="value-suffix">%</span>
                                </div>
                            </div>
                            @error('value')<span class="invalid-feedback d-block">{{ $message }}</span>@enderror
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="min_order_amount">Min Order Amount</label>
                            <input type="number" name="min_order_amount" id="min_order_amount" step="0.01" min="0"
                                   class="form-control @error('min_order_amount') is-invalid @enderror"
                                   value="{{ old('min_order_amount') }}" placeholder="Leave empty for no minimum">
                            @error('min_order_amount')<span class="invalid-feedback">{{ $message }}</span>@enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="max_discount_amount">Max Discount Cap</label>
                            <input type="number" name="max_discount_amount" id="max_discount_amount" step="0.01" min="0.01"
                                   class="form-control @error('max_discount_amount') is-invalid @enderror"
                                   value="{{ old('max_discount_amount') }}" placeholder="Leave empty for no cap">
                            @error('max_discount_amount')<span class="invalid-feedback">{{ $message }}</span>@enderror
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="starts_at">Start Date <span class="text-danger">*</span></label>
                            <input type="datetime-local" name="starts_at" id="starts_at"
                                   class="form-control @error('starts_at') is-invalid @enderror"
                                   value="{{ old('starts_at') }}" required>
                            @error('starts_at')<span class="invalid-feedback">{{ $message }}</span>@enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="ends_at">End Date <span class="text-danger">*</span></label>
                            <input type="datetime-local" name="ends_at" id="ends_at"
                                   class="form-control @error('ends_at') is-invalid @enderror"
                                   value="{{ old('ends_at') }}" required>
                            @error('ends_at')<span class="invalid-feedback">{{ $message }}</span>@enderror
                        </div>
                    </div>
                </div>

            </div>
        </div>

        {{-- Applicable Days & Time --}}
        <div class="card card-outline card-secondary">
            <div class="card-header"><h3 class="card-title">Applicable Days & Time <small class="text-muted">(optional)</small></h3></div>
            <div class="card-body">
                <div class="form-group">
                    <label>Applicable Days</label>
                    <div class="row">
                        @foreach ($days as $day)
                        <div class="col-6 col-md-3">
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input"
                                       id="day_{{ $day }}" name="applicable_days[]" value="{{ $day }}"
                                       {{ in_array($day, old('applicable_days', [])) ? 'checked' : '' }}>
                                <label class="custom-control-label" for="day_{{ $day }}">{{ ucfirst($day) }}</label>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="applicable_from_time">From Time</label>
                            <input type="time" name="applicable_from_time" id="applicable_from_time"
                                   class="form-control @error('applicable_from_time') is-invalid @enderror"
                                   value="{{ old('applicable_from_time') }}">
                            @error('applicable_from_time')<span class="invalid-feedback">{{ $message }}</span>@enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="applicable_to_time">To Time</label>
                            <input type="time" name="applicable_to_time" id="applicable_to_time"
                                   class="form-control @error('applicable_to_time') is-invalid @enderror"
                                   value="{{ old('applicable_to_time') }}">
                            @error('applicable_to_time')<span class="invalid-feedback">{{ $message }}</span>@enderror
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Right column: Status + Menu Items --}}
    <div class="col-lg-4">
        <div class="card card-outline card-info">
            <div class="card-header"><h3 class="card-title">Status</h3></div>
            <div class="card-body">
                <div class="custom-control custom-switch">
                    <input type="hidden" name="is_active" value="0">
                    <input type="checkbox" class="custom-control-input" id="is_active"
                           name="is_active" value="1" {{ old('is_active', '1') == '1' ? 'checked' : '' }}>
                    <label class="custom-control-label" for="is_active">Active (offer is live)</label>
                </div>
            </div>
        </div>

        <div class="card card-outline card-warning">
            <div class="card-header"><h3 class="card-title">Applicable Menu Items <small class="text-muted">(optional)</small></h3></div>
            <div class="card-body p-2" style="max-height:400px;overflow-y:auto;">
                @forelse ($menuItemsByCategory as $category => $items)
                    <p class="text-muted font-weight-bold mb-1 px-2 pt-2"><small>{{ $category }}</small></p>
                    @foreach ($items as $item)
                    <div class="custom-control custom-checkbox px-4">
                        <input type="checkbox" class="custom-control-input"
                               id="item_{{ $item->id }}" name="menu_item_ids[]" value="{{ $item->id }}"
                               {{ in_array($item->id, old('menu_item_ids', [])) ? 'checked' : '' }}>
                        <label class="custom-control-label" for="item_{{ $item->id }}">
                            {{ $item->name }}
                            <small class="text-muted">({{ number_format($item->price, 2) }})</small>
                        </label>
                    </div>
                    @endforeach
                @empty
                    <p class="text-muted p-2 mb-0"><small>No menu items available. Leave unchecked to apply to all items.</small></p>
                @endforelse
            </div>
        </div>

        <div class="card-footer bg-transparent border-0 px-0">
            <button type="submit" class="btn btn-primary btn-block">
                <i class="fas fa-save mr-1"></i> Create Offer
            </button>
            <a href="{{ route('admin.offers.index') }}" class="btn btn-default btn-block">Cancel</a>
        </div>
    </div>
</div>
</form>
@stop

@section('custom_js')
<script>
    // Update the value suffix label based on type
    document.getElementById('type').addEventListener('change', function () {
        document.getElementById('value-suffix').textContent = this.value === 'percentage' ? '%' : 'off';
    });
</script>
@endsection
