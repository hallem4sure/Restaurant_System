@extends('layouts.app')

@section('page_title', 'Create Menu Item')

@section('main_content')
<div class="card card-primary">
    <div class="card-header">
        <h3 class="card-title">New Item</h3>
    </div>
    <form action="{{ route('admin.menu-items.store') }}" method="POST" data-loading enctype="multipart/form-data">
        @csrf
        <div class="card-body row">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="name">Name</label>
                    <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required>
                    @error('name') <span class="invalid-feedback">{{ $message }}</span> @enderror
                </div>
                
                <div class="form-group">
                    <label for="category_id">Category</label>
                    <select class="form-control @error('category_id') is-invalid @enderror" id="category_id" name="category_id" required>
                        <option value="">Select Category</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                        @endforeach
                    </select>
                    @error('category_id') <span class="invalid-feedback">{{ $message }}</span> @enderror
                </div>
                
                <div class="form-group">
                    <label for="subcategory_id">Subcategory (Optional)</label>
                    <select class="form-control @error('subcategory_id') is-invalid @enderror" id="subcategory_id" name="subcategory_id">
                        <option value="">Select Subcategory</option>
                        @foreach($subcategories as $subcategory)
                            <option value="{{ $subcategory->id }}" {{ old('subcategory_id') == $subcategory->id ? 'selected' : '' }}>{{ $subcategory->name }}</option>
                        @endforeach
                    </select>
                    @error('subcategory_id') <span class="invalid-feedback">{{ $message }}</span> @enderror
                </div>

                <div class="form-group">
                    <label for="price">Price</label>
                    <input type="number" step="0.01" class="form-control @error('price') is-invalid @enderror" id="price" name="price" value="{{ old('price') }}" required>
                    @error('price') <span class="invalid-feedback">{{ $message }}</span> @enderror
                </div>

                <div class="form-group">
                    <label>Tags</label>
                    <div class="select2-purple">
                        <select class="select2" multiple="multiple" data-placeholder="Select a Tag" data-dropdown-css-class="select2-purple" style="width: 100%;" name="tags[]">
                            @foreach($tags as $tag)
                                <option value="{{ $tag->id }}" {{ (is_array(old('tags')) && in_array($tag->id, old('tags'))) ? 'selected' : '' }}>{{ $tag->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="form-group">
                    <label for="description">Description</label>
                    <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="4">{{ old('description') }}</textarea>
                    @error('description') <span class="invalid-feedback">{{ $message }}</span> @enderror
                </div>

                <div class="form-group">
                    <label for="images">Images</label>
                    <div class="input-group">
                        <div class="custom-file">
                            <input type="file" class="custom-file-input @error('images.*') is-invalid @enderror" id="images" name="images[]" multiple accept="image/*">
                            <label class="custom-file-label" for="images">Choose files</label>
                        </div>
                    </div>
                    @error('images.*') <span class="text-danger small">{{ $message }}</span> @enderror
                </div>

                <div class="form-group">
                    <label for="sort_order">Sort Order</label>
                    <input type="number" class="form-control @error('sort_order') is-invalid @enderror" id="sort_order" name="sort_order" value="{{ old('sort_order', 0) }}">
                    @error('sort_order') <span class="invalid-feedback">{{ $message }}</span> @enderror
                </div>

                <div class="form-group">
                    <div class="custom-control custom-switch">
                        <input type="hidden" name="is_available" value="0">
                        <input type="checkbox" class="custom-control-input" id="is_available" name="is_available" value="1" {{ old('is_available', true) ? 'checked' : '' }}>
                        <label class="custom-control-label" for="is_available">Available for Order</label>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-footer">
            <button type="submit" class="btn btn-primary">Save</button>
            <a href="{{ route('admin.menu-items.index') }}" class="btn btn-default">Cancel</a>
        </div>
    </form>
</div>
@stop

@section('custom_js')
<script>
    $(function () {
        // Initialize Select2
        $('.select2').select2()
        
        // Custom file input logic
        bsCustomFileInput.init();
    })
</script>
@stop
