@extends('layouts.app')

@section('page_title', 'Tags')

@section('main_content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Menu Item Tags</h3>
        <div class="card-tools">
            <a href="{{ route('admin.tags.create') }}" class="btn btn-primary btn-sm">
                <i class="fas fa-plus"></i> Add Tag
            </a>
        </div>
    </div>
    <div class="card-body table-responsive p-0">
        <table class="table table-hover text-nowrap">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Color</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($tags as $tag)
                    <tr>
                        <td>{{ $tag->id }}</td>
                        <td>{{ $tag->name }}</td>
                        <td>
                            @if($tag->color)
                                <span class="badge" style="background-color: {{ $tag->color }}; color: #fff;">{{ $tag->color }}</span>
                            @else
                                <span class="text-muted">None</span>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('admin.tags.edit', $tag) }}" class="btn btn-sm btn-info"><i class="fas fa-edit"></i></a>
                            <form action="{{ route('admin.tags.destroy', $tag) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-center">No tags found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="card-footer clearfix">
        {{ $tags->links() }}
    </div>
</div>
@stop
