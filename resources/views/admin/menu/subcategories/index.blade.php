@extends('layouts.app')

@section('page_title', 'Menu Subcategories')

@section('main_content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Menu Subcategories</h3>
        <div class="card-tools">
            <a href="{{ route('admin.menu-subcategories.create') }}" class="btn btn-primary btn-sm">
                <i class="fas fa-plus"></i> Add Subcategory
            </a>
        </div>
    </div>
    <div class="card-body table-responsive p-0">
        <table class="table table-hover text-nowrap">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Category</th>
                    <th>Name</th>
                    <th>Sort Order</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($subcategories as $subcategory)
                    <tr>
                        <td>{{ $subcategory->id }}</td>
                        <td>{{ $subcategory->category->name ?? 'N/A' }}</td>
                        <td>{{ $subcategory->name }}</td>
                        <td>{{ $subcategory->sort_order }}</td>
                        <td>
                            @if($subcategory->is_active)
                                <span class="badge badge-success">Active</span>
                            @else
                                <span class="badge badge-danger">Inactive</span>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('admin.menu-subcategories.show', $subcategory) }}" class="btn btn-sm btn-secondary"><i class="fas fa-eye"></i></a>
                            <a href="{{ route('admin.menu-subcategories.edit', $subcategory) }}" class="btn btn-sm btn-info"><i class="fas fa-edit"></i></a>
                            <form action="{{ route('admin.menu-subcategories.destroy', $subcategory) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center">No subcategories found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="card-footer clearfix">
        {{ $subcategories->links() }}
    </div>
</div>
@stop
