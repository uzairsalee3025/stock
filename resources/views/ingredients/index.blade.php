@extends('layouts.app')
@section('title', 'Ingredients')
@section('actions')
    <a href="{{ route('ingredients.create') }}" class="btn btn-primary"><i class="bi bi-plus-lg"></i> New Ingredient</a>
@endsection

@section('content')
<div class="card p-3 mb-3">
    <form method="GET" class="row g-2 align-items-end">
        <div class="col-md-3">
            <label class="form-label small mb-1">Search name</label>
            <input type="text" name="search" value="{{ request('search') }}" class="form-control form-control-sm">
        </div>
        <div class="col-md-2">
            <label class="form-label small mb-1">Category</label>
            <select name="category" class="form-select form-select-sm">
                <option value="">All</option>
                @foreach($categories as $c)
                    <option value="{{ $c->id }}" @selected(request('category')==$c->id)>{{ $c->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2">
            <label class="form-label small mb-1">Supplier</label>
            <input type="text" name="supplier" list="supplierlist" value="{{ request('supplier') }}" class="form-control form-control-sm">
            <datalist id="supplierlist">
                @foreach($supplierNames as $sn)<option value="{{ $sn }}">@endforeach
            </datalist>
        </div>
        <div class="col-md-2">
            <label class="form-label small mb-1">Entry Date</label>
            <input type="date" name="date" value="{{ request('date') }}" class="form-control form-control-sm">
        </div>
        <div class="col-md-2">
            <label class="form-label small mb-1">Stock</label>
            <select name="stock" class="form-select form-select-sm">
                <option value="">All</option>
                <option value="low" @selected(request('stock')==='low')>Low stock</option>
                <option value="out" @selected(request('stock')==='out')>Out of stock</option>
            </select>
        </div>
        <div class="col-md-1">
            <button class="btn btn-sm btn-dark w-100"><i class="bi bi-search"></i></button>
        </div>
    </form>
</div>

<div class="card p-0">
    <table class="table table-hover mb-0 align-middle">
        <thead><tr><th>Name</th><th>Category</th><th>Total Available Quantity</th><th>Unit</th><th></th></tr></thead>
        <tbody>
        @forelse($ingredients as $ing)
            <tr class="{{ $ing->isLowStock() ? 'table-warning' : '' }}">
                <td><a href="{{ route('ingredients.show', $ing) }}">{{ $ing->name }}</a></td>
                <td>{{ $ing->category->name ?? '—' }}</td>
                <td><strong>{{ rtrim(rtrim($ing->available_quantity,'0'),'.') }} {{ $ing->unit }}</strong> @if($ing->isLowStock())<span class="badge bg-danger">Low</span>@endif</td>
                <td>{{ $ing->unit }}</td>
                <td class="text-end">
                    <a href="{{ route('ingredients.show', $ing) }}" class="btn btn-sm btn-outline-primary"><i class="bi bi-eye"></i></a>
                    <a href="{{ route('ingredients.edit', $ing) }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-pencil"></i></a>
                </td>
            </tr>
        @empty
            <tr><td colspan="5" class="text-center text-muted py-4">No ingredients found.</td></tr>
        @endforelse
        </tbody>
    </table>
</div>
<div class="mt-3">{{ $ingredients->links() }}</div>
@endsection
