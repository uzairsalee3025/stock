@extends('layouts.app')
@section('title', 'Record Ingredient Usage')

@section('content')
<form method="POST" action="{{ route('ingredient-usages.store') }}">
    @csrf
    <div class="card p-4 mb-3" style="max-width:720px;">
        <h6 class="border-bottom pb-2 mb-3">Use / Deduct Ingredient Stock</h6>
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label">Category <span class="text-danger">*</span></label>
                <select id="category" class="form-select" required>
                    <option value="">— select —</option>
                    @foreach($categories as $c)<option value="{{ $c->id }}">{{ $c->name }}</option>@endforeach
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label">Ingredient Name <span class="text-danger">*</span></label>
                <select name="ingredient_id" id="ingredient" class="form-select @error('ingredient_id') is-invalid @enderror" required>
                    <option value="">— select category first —</option>
                </select>
                @error('ingredient_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-6">
                <label class="form-label">Available Quantity</label>
                <input type="text" id="available" class="form-control bg-light" value="—" readonly>
            </div>
            <div class="col-md-6">
                <label class="form-label">Usage Quantity <span class="text-danger">*</span></label>
                <input type="number" step="0.001" min="0.001" name="quantity" value="{{ old('quantity') }}" class="form-control @error('quantity') is-invalid @enderror" required>
                @error('quantity')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-6">
                <label class="form-label">Usage Date <span class="text-danger">*</span></label>
                <input type="date" name="usage_date" value="{{ old('usage_date', now()->format('Y-m-d')) }}" class="form-control" required>
            </div>
            <div class="col-12">
                <label class="form-label">Notes / Purpose</label>
                <textarea name="notes" rows="2" class="form-control">{{ old('notes') }}</textarea>
            </div>
        </div>
    </div>
    <button class="btn btn-primary"><i class="bi bi-save"></i> Deduct Usage</button>
    <a href="{{ route('ingredient-usages.index') }}" class="btn btn-outline-secondary">Cancel</a>
</form>

<script>
    const INGREDIENTS = @json($ingredients);
    const catSel = document.getElementById('category');
    const ingSel = document.getElementById('ingredient');
    const avail = document.getElementById('available');

    function fmt(n){ return parseFloat(n).toString(); }

    catSel.addEventListener('change', function () {
        const cid = this.value;
        ingSel.innerHTML = '<option value="">— select —</option>';
        avail.value = '—';
        INGREDIENTS.filter(i => String(i.ingredient_category_id) === String(cid))
            .forEach(i => {
                const o = document.createElement('option');
                o.value = i.id;
                o.textContent = i.name;
                o.dataset.qty = i.available_quantity;
                o.dataset.unit = i.unit;
                ingSel.appendChild(o);
            });
    });

    ingSel.addEventListener('change', function () {
        const opt = this.selectedOptions[0];
        avail.value = opt && opt.dataset.qty !== undefined
            ? fmt(opt.dataset.qty) + ' ' + opt.dataset.unit
            : '—';
    });
</script>
@endsection
