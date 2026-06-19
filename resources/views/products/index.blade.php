@extends('layouts.app')
@section('title', 'Products')
@section('actions')
    <a href="{{ route('products.create') }}" class="btn btn-primary"><i class="bi bi-plus-lg"></i> Add Product</a>
@endsection

@section('content')
<div class="card p-3 mb-3">
    <form method="GET" class="row g-2 align-items-end">
        <div class="col-md-4">
            <label class="form-label small mb-1">Search name</label>
            <input type="text" name="search" value="{{ request('search') }}" class="form-control form-control-sm">
        </div>
        <div class="col-md-3">
            <label class="form-label small mb-1">Category</label>
            <select name="category" class="form-select form-select-sm">
                <option value="">All</option>
                @foreach($categories as $c)
                    <option value="{{ $c->id }}" @selected(request('category')==$c->id)>{{ $c->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-3">
            <label class="form-label small mb-1">Stock</label>
            <select name="stock" class="form-select form-select-sm">
                <option value="">All</option>
                <option value="low" @selected(request('stock')==='low')>Low stock</option>
                <option value="out" @selected(request('stock')==='out')>Out of stock</option>
            </select>
        </div>
        <div class="col-md-2">
            <button class="btn btn-sm btn-dark"><i class="bi bi-search"></i> Filter</button>
            <a href="{{ route('products.index') }}" class="btn btn-sm btn-outline-secondary">Reset</a>
        </div>
    </form>
</div>

<div class="card p-0">
    <table class="table table-hover mb-0 align-middle">
        <thead><tr><th>Category</th><th>Product Name</th><th>Available Stock</th><th>Created Date</th><th class="text-end">Actions</th></tr></thead>
        <tbody>
        @forelse($products as $product)
            <tr class="{{ $product->isLowStock() ? 'table-warning' : '' }}">
                <td>{{ $product->category->name ?? '—' }}</td>
                <td><a href="{{ route('products.show', $product) }}">{{ $product->name }}</a></td>
                <td><strong>{{ $product->quantity_stock }}</strong> @if($product->isLowStock())<span class="badge bg-danger">Low</span>@endif</td>
                <td>{{ $product->created_at->format('d M Y') }}</td>
                <td class="text-end">
                    <a href="{{ route('products.show', $product) }}" class="btn btn-sm btn-outline-primary"><i class="bi bi-eye"></i></a>
                    <a href="{{ route('products.edit', $product) }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-pencil"></i></a>
                    <form method="POST" action="{{ route('products.destroy', $product) }}" class="d-inline" onsubmit="return confirm('Delete this product?')">
                        @csrf @method('DELETE')
                        <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                    </form>
                </td>
            </tr>
        @empty
            <tr><td colspan="5" class="text-center text-muted py-4">No products found.</td></tr>
        @endforelse
        </tbody>
    </table>
</div>
<div class="mt-3">{{ $products->links() }}</div>
@endsection
