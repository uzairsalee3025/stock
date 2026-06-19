@extends('layouts.app')
@section('title', 'Sell Product')

@section('content')
<form method="POST" action="{{ route('product-sales.store') }}">
    @csrf
    <div class="card p-4 mb-3" style="max-width:680px;">
        <h6 class="border-bottom pb-2 mb-3">Sell Product / Deduct Stock</h6>
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label">Category <span class="text-danger">*</span></label>
                <select id="category" class="form-select" required>
                    <option value="">— select —</option>
                    @foreach($categories as $c)<option value="{{ $c->id }}">{{ $c->name }}</option>@endforeach
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label">Product Name <span class="text-danger">*</span></label>
                <select name="product_id" id="product" class="form-select @error('product_id') is-invalid @enderror" required>
                    <option value="">— select category first —</option>
                </select>
                @error('product_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-6">
                <label class="form-label">Available Stock</label>
                <input type="text" id="available" class="form-control bg-light" value="—" readonly>
            </div>
            <div class="col-md-6">
                <label class="form-label">Sale Quantity <span class="text-danger">*</span></label>
                <input type="number" min="1" name="sale_quantity" value="{{ old('sale_quantity') }}" class="form-control @error('sale_quantity') is-invalid @enderror" required>
                @error('sale_quantity')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-12">
                <label class="form-label">Notes <span class="text-muted">(optional)</span></label>
                <textarea name="notes" rows="2" class="form-control">{{ old('notes') }}</textarea>
            </div>
        </div>
    </div>
    <button class="btn btn-primary"><i class="bi bi-cart-check"></i> Record Sale</button>
    <a href="{{ route('product-sales.index') }}" class="btn btn-outline-secondary">Cancel</a>
</form>

<script>
    const PRODUCTS = @json($products);
    const catSel = document.getElementById('category');
    const prodSel = document.getElementById('product');
    const avail = document.getElementById('available');

    catSel.addEventListener('change', function () {
        const cid = this.value;
        prodSel.innerHTML = '<option value="">— select —</option>';
        avail.value = '—';
        PRODUCTS.filter(p => String(p.product_category_id) === String(cid))
            .forEach(p => {
                const o = document.createElement('option');
                o.value = p.id;
                o.textContent = p.name;
                o.dataset.qty = p.quantity_stock;
                prodSel.appendChild(o);
            });
    });

    prodSel.addEventListener('change', function () {
        const opt = this.selectedOptions[0];
        avail.value = opt && opt.dataset.qty !== undefined ? opt.dataset.qty : '—';
    });
</script>
@endsection
