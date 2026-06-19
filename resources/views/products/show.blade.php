@extends('layouts.app')
@section('title', 'Product — '.$product->name)
@section('actions')
    <a href="{{ route('product-sales.create') }}" class="btn btn-warning"><i class="bi bi-cart-check"></i> Sell Product</a>
    <a href="{{ route('products.edit', $product) }}" class="btn btn-outline-secondary"><i class="bi bi-pencil"></i> Edit</a>
    <form method="POST" action="{{ route('products.destroy', $product) }}" class="d-inline" onsubmit="return confirm('Delete product and all sale history?')">
        @csrf @method('DELETE')
        <button class="btn btn-outline-danger"><i class="bi bi-trash"></i> Delete</button>
    </form>
@endsection

@section('content')
<div class="row g-3 mb-3">
    <div class="col-md-4"><div class="card p-3"><div class="text-muted small">Category</div><h5 class="mb-0">{{ $product->category->name ?? '—' }}</h5></div></div>
    <div class="col-md-4"><div class="card p-3 {{ $product->isLowStock() ? 'border border-danger' : '' }}"><div class="text-muted small">Available Stock</div><h3 class="mb-0">{{ $product->quantity_stock }} @if($product->isLowStock())<span class="badge bg-danger">Low</span>@endif</h3></div></div>
    <div class="col-md-4"><div class="card p-3"><div class="text-muted small">Total Sold</div><h3 class="mb-0">{{ $product->sales->sum('sale_quantity') }}</h3></div></div>
</div>

<div class="card p-3">
    <h6 class="border-bottom pb-2">Sale / Deduction History</h6>
    <table class="table table-sm mb-0 align-middle">
        <thead><tr><th>Date</th><th>Sold Quantity</th><th>Notes</th><th></th></tr></thead>
        <tbody>
        @forelse($product->sales as $sale)
            <tr>
                <td>{{ $sale->sale_date->format('d M Y') }}</td>
                <td>{{ $sale->sale_quantity }}</td>
                <td class="small">{{ $sale->notes ?? '—' }}</td>
                <td class="text-end">
                    <form method="POST" action="{{ route('product-sales.destroy', $sale) }}" onsubmit="return confirm('Remove this sale and return stock?')">
                        @csrf @method('DELETE')
                        <button class="btn btn-sm btn-outline-danger py-0"><i class="bi bi-x"></i></button>
                    </form>
                </td>
            </tr>
        @empty
            <tr><td colspan="4" class="text-muted">No sales recorded yet.</td></tr>
        @endforelse
        </tbody>
    </table>
</div>
@endsection
