@extends('layouts.app')

@section('page_title', 'Create Tag')

@section('main_content')
<div class="card card-primary">
    <div class="card-header">
        <h3 class="card-title">New Tag</h3>
    </div>
    <form action="{{ route('admin.tags.store') }}" method="POST">
        @csrf
        <div class="card-body">
            <div class="form-group">
                <label for="name">Name</label>
                <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required>
                @error('name')
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>
            <div class="form-group">
                <label for="color">Color (Hex Code, optional)</label>
                <input type="text" class="form-control @error('color') is-invalid @enderror" id="color" name="color" value="{{ old('color') }}" placeholder="#FF0000">
                @error('color')
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>
        </div>
        <div class="card-footer">
            <button type="submit" class="btn btn-primary">Save</button>
            <a href="{{ route('admin.tags.index') }}" class="btn btn-default">Cancel</a>
        </div>
    </form>
</div>
@stop
