@extends('layouts.app')

@section('page_title', 'Menu Categories')

@section('main_content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Menu Categories</h3>
        <div class="card-tools">
            <a href="{{ route('admin.menu-categories.create') }}" class="btn btn-primary btn-sm">
                <i class="fas fa-plus"></i> Add Category
            </a>
        </div>
    </div>
    <div class="card-body table-responsive p-0">
        <table class="table table-hover text-nowrap">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Section</th>
                    <th>Name</th>
                    <th>Sort Order</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($categories as $category)
                    <tr>
                        <td>{{ $category->id }}</td>
                        <td>{{ $category->section->name ?? 'N/A' }}</td>
                        <td>{{ $category->name }}</td>
                        <td>{{ $category->sort_order }}</td>
                        <td>
                            @if($category->is_active)
                                <span class="badge badge-success">Active</span>
                            @else
                                <span class="badge badge-danger">Inactive</span>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('admin.menu-categories.show', $category) }}" class="btn btn-sm btn-secondary"><i class="fas fa-eye"></i></a>
                            <a href="{{ route('admin.menu-categories.edit', $category) }}" class="btn btn-sm btn-info"><i class="fas fa-edit"></i></a>
                            <form action="{{ route('admin.menu-categories.destroy', $category) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center">No categories found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="card-footer clearfix">
        {{ $categories->links() }}
    </div>
</div>
@stop
