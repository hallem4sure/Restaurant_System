@php
    $days = ['monday','tuesday','wednesday','thursday','friday','saturday','sunday'];
    $activeDays = $offer->applicable_days ?? [];
@endphp

@extends('layouts.app')

@section('page_title', 'Edit Offer: ' . $offer->name)

@section('main_content')
<form action="{{ route('admin.offers.update', $offer) }}" method="POST">
@csrf @method('PUT')

<div class="row">
    {{-- Left column --}}
    <div class="col-lg-8">
        <div class="card card-info card-outline">
            <div class="card-header"><h3 class="card-title">Offer Details</h3></div>
            <div class="card-body">

                <div class="form-group">
                    <label for="name">Offer Name <span class="text-danger">*</span></label>
                    <input type="text" name="name" id="name"
                           class="form-control @error('name') is-invalid @enderror"
                           value="{{ old('name', $offer->name) }}" required>
                    @error('name')<span class="invalid-feedback">{{ $message }}</span>@enderror
                </div>

                <div class="form-group">
                    <label for="description">Description</label>
                    <textarea name="description" id="description" rows="3"
                              class="form-control @error('description') is-invalid @enderror">{{ old('description', $offer->description) }}</textarea>
                    @error('description')<span class="invalid-feedback">{{ $message }}</span>@enderror
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="type">Discount Type <span class="text-danger">*</span></label>
                            <select name="type" id="type" class="form-control @error('type') is-invalid @enderror" required>
                                <option value="percentage" {{ old('type', $offer->type) === 'percentage' ? 'selected' : '' }}>Percentage (%)</option>
                                <option value="fixed" {{ old('type', $offer->type) === 'fixed' ? 'selected' : '' }}>Fixed Amount</option>
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
                                       value="{{ old('value', $offer->value) }}" required>
                                <div class="input-group-append">
                                    <span class="input-group-text" id="value-suffix">
                                        {{ $offer->type === 'percentage' ? '%' : 'off' }}
                                    </span>
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
                                   value="{{ old('min_order_amount', $offer->min_order_amount) }}">
                            @error('min_order_amount')<span class="invalid-feedback">{{ $message }}</span>@enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="max_discount_amount">Max Discount Cap</label>
                            <input type="number" name="max_discount_amount" id="max_discount_amount" step="0.01" min="0.01"
                                   class="form-control @error('max_discount_amount') is-invalid @enderror"
                                   value="{{ old('max_discount_amount', $offer->max_discount_amount) }}">
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
                                   value="{{ old('starts_at', $offer->starts_at->format('Y-m-d\TH:i')) }}" required>
                            @error('starts_at')<span class="invalid-feedback">{{ $message }}</span>@enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="ends_at">End Date <span class="text-danger">*</span></label>
                            <input type="datetime-local" name="ends_at" id="ends_at"
                                   class="form-control @error('ends_at') is-invalid @enderror"
                                   value="{{ old('ends_at', $offer->ends_at->format('Y-m-d\TH:i')) }}" required>
                            @error('ends_at')<span class="invalid-feedback">{{ $message }}</span>@enderror
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <div class="card card-outline card-secondary">
            <div class="card-header"><h3 class="card-title">Applicable Days & Time</h3></div>
            <div class="card-body">
                <div class="form-group">
                    <label>Applicable Days</label>
                    <div class="row">
                        @foreach ($days as $day)
                        <div class="col-6 col-md-3">
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input"
                                       id="day_{{ $day }}" name="applicable_days[]" value="{{ $day }}"
                                       {{ in_array($day, old('applicable_days', $activeDays)) ? 'checked' : '' }}>
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
                                   value="{{ old('applicable_from_time', $offer->applicable_from_time) }}">
                            @error('applicable_from_time')<span class="invalid-feedback">{{ $message }}</span>@enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="applicable_to_time">To Time</label>
                            <input type="time" name="applicable_to_time" id="applicable_to_time"
                                   class="form-control @error('applicable_to_time') is-invalid @enderror"
                                   value="{{ old('applicable_to_time', $offer->applicable_to_time) }}">
                            @error('applicable_to_time')<span class="invalid-feedback">{{ $message }}</span>@enderror
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Right column --}}
    <div class="col-lg-4">
        <div class="card card-outline card-info">
            <div class="card-header"><h3 class="card-title">Status</h3></div>
            <div class="card-body">
                <div class="custom-control custom-switch">
                    <input type="hidden" name="is_active" value="0">
                    <input type="checkbox" class="custom-control-input" id="is_active"
                           name="is_active" value="1" {{ old('is_active', $offer->is_active) ? 'checked' : '' }}>
                    <label class="custom-control-label" for="is_active">Active</label>
                </div>
            </div>
        </div>

        <div class="card card-outline card-warning">
            <div class="card-header"><h3 class="card-title">Applicable Menu Items</h3></div>
            <div class="card-body p-2" style="max-height:400px;overflow-y:auto;">
                @forelse ($menuItemsByCategory as $category => $items)
                    <p class="text-muted font-weight-bold mb-1 px-2 pt-2"><small>{{ $category }}</small></p>
                    @foreach ($items as $item)
                    <div class="custom-control custom-checkbox px-4">
                        <input type="checkbox" class="custom-control-input"
                               id="item_{{ $item->id }}" name="menu_item_ids[]" value="{{ $item->id }}"
                               {{ in_array($item->id, old('menu_item_ids', $selectedIds)) ? 'checked' : '' }}>
                        <label class="custom-control-label" for="item_{{ $item->id }}">
                            {{ $item->name }}
                            <small class="text-muted">({{ number_format($item->price, 2) }})</small>
                        </label>
                    </div>
                    @endforeach
                @empty
                    <p class="text-muted p-2 mb-0"><small>No menu items available.</small></p>
                @endforelse
            </div>
        </div>

        <div class="bg-transparent px-0">
            <button type="submit" class="btn btn-info btn-block">
                <i class="fas fa-save mr-1"></i> Update Offer
            </button>
            <a href="{{ route('admin.offers.index') }}" class="btn btn-default btn-block">Cancel</a>
        </div>
    </div>
</div>
</form>
@stop

@section('custom_js')
<script>
    document.getElementById('type').addEventListener('change', function () {
        document.getElementById('value-suffix').textContent = this.value === 'percentage' ? '%' : 'off';
    });
</script>
@endsection
