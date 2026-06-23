@extends('layouts.app')

@section('page_title', 'Menu Sections')

@section('main_content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Menu Sections</h3>
        <div class="card-tools">
            <a href="{{ route('admin.menu-sections.create') }}" class="btn btn-primary btn-sm">
                <i class="fas fa-plus"></i> Add Section
            </a>
        </div>
    </div>
    <div class="card-body table-responsive p-0">
        <table class="table table-hover text-nowrap">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Sort Order</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($sections as $section)
                    <tr>
                        <td>{{ $section->id }}</td>
                        <td>{{ $section->name }}</td>
                        <td>{{ $section->sort_order }}</td>
                        <td>
                            @if($section->is_active)
                                <span class="badge badge-success">Active</span>
                            @else
                                <span class="badge badge-danger">Inactive</span>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('admin.menu-sections.show', $section) }}" class="btn btn-sm btn-secondary"><i class="fas fa-eye"></i></a>
                            <a href="{{ route('admin.menu-sections.edit', $section) }}" class="btn btn-sm btn-info"><i class="fas fa-edit"></i></a>
                            <form action="{{ route('admin.menu-sections.destroy', $section) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center">No sections found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="card-footer clearfix">
        {{ $sections->links() }}
    </div>
</div>
@stop
