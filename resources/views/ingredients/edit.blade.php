@extends('layouts.app')
@section('title', 'Edit Ingredient')

@section('content')
<form method="POST" action="{{ route('ingredients.update', $ingredient) }}">
    @csrf @method('PUT')
    <div class="card p-4 mb-3" style="max-width:720px;">
        <h6 class="border-bottom pb-2 mb-3">Ingredient Details</h6>
        <p class="text-muted small">To change stock quantity, add or remove supplier entries / usages — quantity is calculated automatically.</p>
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label">Category <span class="text-danger">*</span></label>
                <select name="ingredient_category_id" class="form-select" required>
                    @foreach($categories as $c)
                        <option value="{{ $c->id }}" @selected(old('ingredient_category_id', $ingredient->ingredient_category_id)==$c->id)>{{ $c->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label">Ingredient Name <span class="text-danger">*</span></label>
                <input type="text" name="name" value="{{ old('name', $ingredient->name) }}" class="form-control" required>
            </div>
            <div class="col-md-4">
                <label class="form-label">Unit <span class="text-danger">*</span></label>
                <input type="text" name="unit" list="unitlist" value="{{ old('unit', $ingredient->unit) }}" class="form-control" required>
                <datalist id="unitlist">
                    <option value="kg"><option value="gram"><option value="liter"><option value="ml"><option value="pcs">
                </datalist>
            </div>
            <div class="col-md-4">
                <label class="form-label">Low Stock Threshold</label>
                <input type="number" step="0.001" min="0" name="low_stock_threshold" value="{{ old('low_stock_threshold', $ingredient->low_stock_threshold) }}" class="form-control">
            </div>
            <div class="col-md-4">
                <label class="form-label">Available Quantity</label>
                <input type="text" value="{{ rtrim(rtrim($ingredient->available_quantity,'0'),'.') }} {{ $ingredient->unit }}" class="form-control bg-light" readonly>
            </div>
            <div class="col-12">
                <label class="form-label">Notes</label>
                <textarea name="notes" rows="2" class="form-control">{{ old('notes', $ingredient->notes) }}</textarea>
            </div>
        </div>
    </div>
    <button class="btn btn-primary"><i class="bi bi-save"></i> Update</button>
    <a href="{{ route('ingredients.show', $ingredient) }}" class="btn btn-outline-secondary">Cancel</a>
</form>
@endsection
