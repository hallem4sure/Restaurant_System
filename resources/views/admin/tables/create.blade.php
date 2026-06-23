@extends('layouts.app')

@section('page_title', 'Add New Table')

@section('main_content')
<div class="card card-primary">
    <div class="card-header">
        <h3 class="card-title">New Dining Table</h3>
    </div>
    <form action="{{ route('admin.tables.store') }}" method="POST">
        @csrf
        <div class="card-body">
            <div class="form-group">
                <label for="table_number">Table Number / Identifier</label>
                <input type="text" class="form-control @error('table_number') is-invalid @enderror" id="table_number" name="table_number" value="{{ old('table_number') }}" required placeholder="e.g. T1, Balcony-1">
                @error('table_number')
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>
            
            <div class="form-group">
                <label for="capacity">Capacity (Persons)</label>
                <input type="number" class="form-control @error('capacity') is-invalid @enderror" id="capacity" name="capacity" value="{{ old('capacity', 2) }}" min="1" max="20" required>
                @error('capacity')
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="status">Initial Status</label>
                <select class="form-control @error('status') is-invalid @enderror" id="status" name="status" required>
                    <option value="available" {{ old('status') == 'available' ? 'selected' : '' }}>Available</option>
                    <option value="occupied" {{ old('status') == 'occupied' ? 'selected' : '' }}>Occupied</option>
                    <option value="reserved" {{ old('status') == 'reserved' ? 'selected' : '' }}>Reserved</option>
                    <option value="maintenance" {{ old('status') == 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                </select>
                @error('status')
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <div class="custom-control custom-switch">
                    <input type="hidden" name="is_private" value="0">
                    <input type="checkbox" class="custom-control-input" id="is_private" name="is_private" value="1" {{ old('is_private') ? 'checked' : '' }}>
                    <label class="custom-control-label" for="is_private">Private Table / VIP</label>
                </div>
            </div>
        </div>
        <div class="card-footer">
            <button type="submit" class="btn btn-primary">Save Table</button>
            <a href="{{ route('admin.tables.index') }}" class="btn btn-default">Cancel</a>
        </div>
    </form>
</div>
@stop
