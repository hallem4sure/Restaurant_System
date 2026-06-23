@extends('layouts.app')

@section('page_title', 'Tables Management')

@section('main_content')
<div class="row mb-3">
    <div class="col-12">
        <a href="{{ route('admin.tables.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Add New Table
        </a>
    </div>
</div>

<div class="row">
    @forelse ($tables as $table)
        <div class="col-lg-3 col-6">
            @php
                $bgClass = 'bg-info';
                switch($table->status) {
                    case 'available': $bgClass = 'bg-success'; break;
                    case 'occupied': $bgClass = 'bg-danger'; break;
                    case 'reserved': $bgClass = 'bg-warning'; break;
                    case 'maintenance': $bgClass = 'bg-secondary'; break;
                }
            @endphp
            <div class="small-box {{ $bgClass }}">
                <div class="inner">
                    <h3>{{ $table->table_number }}</h3>
                    <p>
                        Capacity: {{ $table->capacity }}<br>
                        Type: {{ $table->is_private ? 'Private' : 'Public' }}<br>
                        Status: {{ ucfirst($table->status) }}
                    </p>
                </div>
                <div class="icon">
                    <i class="fas {{ $table->is_private ? 'fa-user-secret' : 'fa-users' }}"></i>
                </div>
                <div class="small-box-footer">
                    <a href="{{ route('admin.tables.show', $table) }}" class="text-white mr-2" title="View"><i class="fas fa-eye"></i></a>
                    <a href="{{ route('admin.tables.edit', $table) }}" class="text-white mr-2" title="Edit"><i class="fas fa-edit"></i></a>
                    <form action="{{ route('admin.tables.destroy', $table) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this table?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-link p-0 text-white" title="Delete"><i class="fas fa-trash"></i></button>
                    </form>
                </div>
            </div>
        </div>
    @empty
        <div class="col-12">
            <div class="alert alert-info">
                No tables found. Please add some tables to the system.
            </div>
        </div>
    @endforelse
</div>
@stop
