@extends('layouts.app')
@section('title', 'Product Sales')
@section('actions')
    <a href="{{ route('product-sales.create') }}" class="btn btn-primary"><i class="bi bi-cart-check"></i> Sell Product</a>
@endsection

@section('content')
<div class="card p-3 mb-3">
    <form method="GET" class="row g-2 align-items-end">
        <div class="col-md-3">
            <label class="form-label small mb-1">Category</label>
            <select name="category" class="form-select form-select-sm">
                <option value="">All</option>
                @foreach($categories as $c)<option value="{{ $c->id }}" @selected(request('category')==$c->id)>{{ $c->name }}</option>@endforeach
            </select>
        </div>
        <div class="col-md-3">
            <label class="form-label small mb-1">Product</label>
            <select name="product" class="form-select form-select-sm">
                <option value="">All</option>
                @foreach($products as $p)<option value="{{ $p->id }}" @selected(request('product')==$p->id)>{{ $p->name }}</option>@endforeach
            </select>
        </div>
        <div class="col-md-2"><label class="form-label small mb-1">From</label><input type="date" name="from" value="{{ request('from') }}" class="form-control form-control-sm"></div>
        <div class="col-md-2"><label class="form-label small mb-1">To</label><input type="date" name="to" value="{{ request('to') }}" class="form-control form-control-sm"></div>
        <div class="col-md-2">
            <button class="btn btn-sm btn-dark"><i class="bi bi-funnel"></i> Filter</button>
            <a href="{{ route('product-sales.index') }}" class="btn btn-sm btn-outline-secondary">Reset</a>
        </div>
    </form>
</div>

<div class="card p-0">
    <table class="table table-hover mb-0 align-middle">
        <thead><tr><th>Date</th><th>Product</th><th>Category</th><th>Sold Quantity</th><th>Notes</th><th></th></tr></thead>
        <tbody>
        @forelse($sales as $sale)
            <tr>
                <td>{{ $sale->sale_date->format('d M Y') }}</td>
                <td><a href="{{ route('products.show', $sale->product_id) }}">{{ $sale->product->name ?? '—' }}</a></td>
                <td>{{ $sale->product->category->name ?? '—' }}</td>
                <td>{{ $sale->sale_quantity }}</td>
                <td class="small">{{ $sale->notes ?? '—' }}</td>
                <td class="text-end">
                    <form method="POST" action="{{ route('product-sales.destroy', $sale) }}" onsubmit="return confirm('Remove this sale and return stock?')">
                        @csrf @method('DELETE')
                        <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                    </form>
                </td>
            </tr>
        @empty
            <tr><td colspan="6" class="text-center text-muted py-4">No sales recorded yet.</td></tr>
        @endforelse
        </tbody>
    </table>
</div>
<div class="mt-3">{{ $sales->links() }}</div>
@endsection
