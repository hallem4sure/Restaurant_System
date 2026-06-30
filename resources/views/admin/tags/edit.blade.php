@extends('layouts.app')

@section('page_title', 'Edit Tag')

@section('main_content')
<div class="card card-info">
    <div class="card-header">
        <h3 class="card-title">Edit Tag: {{ $tag->name }}</h3>
    </div>
    <form action="{{ route('admin.tags.update', $tag) }}" method="POST" data-loading>
        @csrf
        @method('PUT')
        <div class="card-body">
            <div class="form-group">
                <label for="name">Name</label>
                <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $tag->name) }}" required>
                @error('name')
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>
            <div class="form-group">
                <label for="color">Color (Hex Code, optional)</label>
                <input type="text" class="form-control @error('color') is-invalid @enderror" id="color" name="color" value="{{ old('color', $tag->color) }}" placeholder="#FF0000">
                @error('color')
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>
        </div>
        <div class="card-footer">
            <button type="submit" class="btn btn-info">Update</button>
            <a href="{{ route('admin.tags.index') }}" class="btn btn-default">Cancel</a>
        </div>
    </form>
</div>
@stop
