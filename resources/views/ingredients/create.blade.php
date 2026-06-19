@extends('layouts.app')
@section('title', 'New Ingredient')

@section('content')
<form method="POST" action="{{ route('ingredients.store') }}">
    @csrf
    <div class="card p-4 mb-3" style="max-width:820px;">
        <h6 class="border-bottom pb-2 mb-3">Ingredient + First Supplier Entry</h6>
        <p class="text-muted small">Adding an ingredient that already exists (same category &amp; name) simply adds this supplier's quantity to its total.</p>
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label">Category <span class="text-danger">*</span></label>
                <select name="ingredient_category_id" class="form-select" required>
                    <option value="">— select —</option>
                    @foreach($categories as $c)
                        <option value="{{ $c->id }}" @selected(old('ingredient_category_id')==$c->id)>{{ $c->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label">Ingredient Name <span class="text-danger">*</span></label>
                <input type="text" name="name" value="{{ old('name') }}" class="form-control" required>
            </div>
            <div class="col-md-4">
                <label class="form-label">Supplier Name <span class="text-danger">*</span></label>
                <input type="text" name="supplier_name" list="supplierlist" value="{{ old('supplier_name') }}" class="form-control" required>
                <datalist id="supplierlist">
                    @foreach($supplierNames as $sn)<option value="{{ $sn }}">@endforeach
                </datalist>
            </div>
            <div class="col-md-3">
                <label class="form-label">Quantity <span class="text-danger">*</span></label>
                <input type="number" step="0.001" min="0.001" name="quantity" value="{{ old('quantity') }}" class="form-control" required>
            </div>
            <div class="col-md-2">
                <label class="form-label">Unit <span class="text-danger">*</span></label>
                <input type="text" name="unit" list="unitlist" value="{{ old('unit', 'kg') }}" class="form-control" required>
                <datalist id="unitlist">
                    <option value="kg"><option value="gram"><option value="liter"><option value="ml"><option value="pcs">
                </datalist>
            </div>
            <div class="col-md-3">
                <label class="form-label">Price <span class="text-danger">*</span></label>
                <input type="number" step="0.01" min="0" name="price" value="{{ old('price') }}" class="form-control" required>
            </div>
            <div class="col-md-4">
                <label class="form-label">Date <span class="text-danger">*</span></label>
                <input type="date" name="date" value="{{ old('date', now()->format('Y-m-d')) }}" class="form-control" required>
            </div>
            <div class="col-12">
                <label class="form-label">Notes</label>
                <textarea name="notes" rows="2" class="form-control">{{ old('notes') }}</textarea>
            </div>
        </div>
    </div>
    <button class="btn btn-primary"><i class="bi bi-save"></i> Save Ingredient &amp; Stock</button>
    <a href="{{ route('ingredients.index') }}" class="btn btn-outline-secondary">Cancel</a>
</form>
@endsection
