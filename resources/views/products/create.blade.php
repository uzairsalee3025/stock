@extends('layouts.app')
@section('title', 'Add Product')

@section('content')
<form method="POST" action="{{ route('products.store') }}">
    @csrf
    <div class="card p-4 mb-3" style="max-width:640px;">
        <h6 class="border-bottom pb-2 mb-3">Add Product Stock</h6>
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label">Category <span class="text-danger">*</span></label>
                <select name="product_category_id" class="form-select" required>
                    <option value="">— select —</option>
                    @foreach($categories as $c)
                        <option value="{{ $c->id }}" @selected(old('product_category_id')==$c->id)>{{ $c->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label">Product Name <span class="text-danger">*</span></label>
                <input type="text" name="name" value="{{ old('name') }}" class="form-control" required>
            </div>
            <div class="col-md-6">
                <label class="form-label">Quantity Stock <span class="text-danger">*</span></label>
                <input type="number" min="0" name="quantity_stock" value="{{ old('quantity_stock') }}" class="form-control" required>
            </div>
        </div>
    </div>
    <button class="btn btn-primary"><i class="bi bi-save"></i> Save Product</button>
    <a href="{{ route('products.index') }}" class="btn btn-outline-secondary">Cancel</a>
</form>
@endsection
