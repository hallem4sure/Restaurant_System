@extends('layouts.app')

@section('page_title', 'Tags')

@section('breadcrumbs')
    @include('partials.breadcrumbs', ['crumbs' => [
        ['label' => 'Dashboard', 'url' => route('admin.dashboard')],
        ['label' => 'Menu Management', 'url' => '#'],
        ['label' => 'Tags'],
    ]])
@endsection

@section('main_content')
<div class="row mb-3">
    <div class="col-12 d-flex justify-content-between align-items-center">
        <p class="text-muted mb-0">Manage tags for menu items (e.g. Spicy, Vegan, New).</p>
        <a href="{{ route('admin.tags.create') }}" class="btn btn-primary">
            <i class="fas fa-plus mr-1"></i> Add Tag
        </a>
    </div>
</div>

@if ($tags->isEmpty())
    <div class="card">
        <div class="card-body text-center py-5">
            <i class="fas fa-tags fa-3x text-muted mb-3"></i>
            <h4 class="text-muted">No Tags Yet</h4>
            <p class="text-muted">Create your first tag to highlight specific features of your menu items.</p>
            <a href="{{ route('admin.tags.create') }}" class="btn btn-primary mt-2">
                <i class="fas fa-plus mr-1"></i> Create First Tag
            </a>
        </div>
    </div>
@else
<div class="card card-outline card-primary">
    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-tags mr-1"></i> All Tags</h3>
    </div>
    <div class="card-body p-0 table-responsive">
        <table class="table table-hover table-striped mb-0">
            <thead class="thead-light">
                <tr>
                    <th scope="col">ID</th>
                    <th scope="col">Name</th>
                    <th scope="col">Preview</th>
                    <th scope="col" class="text-center">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($tags as $tag)
                    <tr>
                        <td class="align-middle">{{ $tag->id }}</td>
                        <td class="align-middle"><strong>{{ $tag->name }}</strong></td>
                        <td class="align-middle">
                            @if($tag->color)
                                <span class="badge px-3 py-1" style="background-color: {{ $tag->color }}; color: #fff; font-size:0.9rem;">{{ $tag->name }}</span>
                            @else
                                <span class="text-muted">None</span>
                            @endif
                        </td>
                        <td class="align-middle text-center" style="white-space:nowrap;">
                            <a href="{{ route('admin.tags.edit', $tag) }}" class="btn btn-xs btn-warning" title="Edit Tag"><i class="fas fa-edit"></i></a>
                            <form action="{{ route('admin.tags.destroy', $tag) }}" method="POST" class="d-inline">
                                @csrf @method('DELETE')
                                <button type="button" class="btn btn-xs btn-danger" title="Delete Tag"
                                    data-confirm="Delete tag '{{ $tag->name }}'? This cannot be undone."
                                    data-confirm-title="Delete Tag"
                                    data-confirm-btn="Yes, delete it">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @if($tags->hasPages())
    <div class="card-footer pb-0">
        {{ $tags->links() }}
    </div>
    @endif
</div>
@endif
@stop
