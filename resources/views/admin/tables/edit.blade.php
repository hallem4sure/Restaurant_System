@extends('layouts.app')

@section('page_title', 'Edit Table')

@section('main_content')
<div class="card card-info">
    <div class="card-header">
        <h3 class="card-title">Edit Table: {{ $table->table_number }}</h3>
    </div>
    <form action="{{ route('admin.tables.update', $table) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="card-body">
            <div class="form-group">
                <label for="table_number">Table Number / Identifier</label>
                <input type="text" class="form-control @error('table_number') is-invalid @enderror" id="table_number" name="table_number" value="{{ old('table_number', $table->table_number) }}" required>
                @error('table_number')
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>
            
            <div class="form-group">
                <label for="capacity">Capacity (Persons)</label>
                <input type="number" class="form-control @error('capacity') is-invalid @enderror" id="capacity" name="capacity" value="{{ old('capacity', $table->capacity) }}" min="1" max="20" required>
                @error('capacity')
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="status">Status</label>
                <select class="form-control @error('status') is-invalid @enderror" id="status" name="status" required>
                    <option value="available" {{ old('status', $table->status) == 'available' ? 'selected' : '' }}>Available</option>
                    <option value="occupied" {{ old('status', $table->status) == 'occupied' ? 'selected' : '' }}>Occupied</option>
                    <option value="reserved" {{ old('status', $table->status) == 'reserved' ? 'selected' : '' }}>Reserved</option>
                    <option value="maintenance" {{ old('status', $table->status) == 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                </select>
                @error('status')
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <div class="custom-control custom-switch">
                    <input type="hidden" name="is_private" value="0">
                    <input type="checkbox" class="custom-control-input" id="is_private" name="is_private" value="1" {{ old('is_private', $table->is_private) ? 'checked' : '' }}>
                    <label class="custom-control-label" for="is_private">Private Table / VIP</label>
                </div>
            </div>
        </div>
        <div class="card-footer">
            <button type="submit" class="btn btn-info">Update Table</button>
            <a href="{{ route('admin.tables.index') }}" class="btn btn-default">Cancel</a>
        </div>
    </form>
</div>
@stop
