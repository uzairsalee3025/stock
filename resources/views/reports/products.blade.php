@extends('layouts.app')
@section('title', 'Product Reports')

@section('content')
<div class="card p-3 mb-3">
    <form method="GET" class="row g-2 align-items-end">
        <div class="col-md-3">
            <label class="form-label small mb-1">Report</label>
            <select name="report" class="form-select form-select-sm" onchange="this.form.submit()">
                <option value="available" @selected($report==='available')>Available Stock</option>
                <option value="sold" @selected($report==='sold')>Sold Quantity</option>
                <option value="low_stock" @selected($report==='low_stock')>Low Stock</option>
            </select>
        </div>
        <div class="col-md-2"><button class="btn btn-sm btn-dark"><i class="bi bi-funnel"></i> Run</button></div>
    </form>
</div>

<div class="card p-0">
    <table class="table table-sm mb-0 align-middle">
        @if($report==='sold')
            <thead><tr><th>Product</th><th>Category</th><th>Total Sold</th><th>Available Stock</th></tr></thead>
            <tbody>
            @forelse($rows as $p)
                <tr><td>{{ $p->name }}</td><td>{{ $p->category->name ?? '—' }}</td><td>{{ (int) ($p->total_sold ?? 0) }}</td><td>{{ $p->quantity_stock }}</td></tr>
            @empty
                <tr><td colspan="4" class="text-center text-muted py-3">No products.</td></tr>
            @endforelse
            </tbody>
        @else {{-- available / low_stock --}}
            <thead><tr><th>Product</th><th>Category</th><th>Available Stock</th>@if($report==='low_stock')<th>Status</th>@endif</tr></thead>
            <tbody>
            @forelse($rows as $p)
                <tr>
                    <td>{{ $p->name }}</td>
                    <td>{{ $p->category->name ?? '—' }}</td>
                    <td><strong>{{ $p->quantity_stock }}</strong></td>
                    @if($report==='low_stock')<td><span class="badge bg-danger">Low</span></td>@endif
                </tr>
            @empty
                <tr><td colspan="4" class="text-center text-muted py-3">No products.</td></tr>
            @endforelse
            </tbody>
        @endif
    </table>
</div>
<div class="mt-2 text-muted small">Total records: {{ $rows->count() }}@if($report==='low_stock') · threshold ≤ {{ \App\Models\Product::LOW_STOCK_THRESHOLD }}@endif</div>
@endsection
