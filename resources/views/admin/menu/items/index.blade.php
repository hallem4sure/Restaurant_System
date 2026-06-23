@extends('layouts.app')

@section('page_title', 'Menu Items')

@section('main_content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Menu Items</h3>
        <div class="card-tools">
            <a href="{{ route('admin.menu-items.create') }}" class="btn btn-primary btn-sm">
                <i class="fas fa-plus"></i> Add Item
            </a>
        </div>
    </div>
    <div class="card-body table-responsive p-0">
        <table class="table table-hover text-nowrap">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Image</th>
                    <th>Category</th>
                    <th>Name</th>
                    <th>Price</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($items as $item)
                    <tr>
                        <td>{{ $item->id }}</td>
                        <td>
                            @if($item->images->count() > 0)
                                <img src="{{ asset('storage/' . $item->images->first()->image_path) }}" alt="{{ $item->name }}" width="50" height="50" class="img-thumbnail">
                            @else
                                <span class="text-muted">No image</span>
                            @endif
                        </td>
                        <td>{{ $item->category->name ?? 'N/A' }}</td>
                        <td>{{ $item->name }}</td>
                        <td>${{ number_format($item->price, 2) }}</td>
                        <td>
                            @if($item->is_available)
                                <span class="badge badge-success">Available</span>
                            @else
                                <span class="badge badge-danger">Unavailable</span>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('admin.menu-items.show', $item) }}" class="btn btn-sm btn-secondary"><i class="fas fa-eye"></i></a>
                            <a href="{{ route('admin.menu-items.edit', $item) }}" class="btn btn-sm btn-info"><i class="fas fa-edit"></i></a>
                            <form action="{{ route('admin.menu-items.destroy', $item) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center">No items found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="card-footer clearfix">
        {{ $items->links() }}
    </div>
</div>
@stop
