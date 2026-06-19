@extends('layouts.app')
@section('title', 'Ingredient Reports')

@section('content')
<div class="card p-3 mb-3">
    <form method="GET" class="row g-2 align-items-end">
        <div class="col-md-3">
            <label class="form-label small mb-1">Report</label>
            <select name="report" class="form-select form-select-sm" onchange="this.form.submit()">
                <option value="available" @selected($report==='available')>Available Stock</option>
                <option value="supplier_wise" @selected($report==='supplier_wise')>Supplier-wise</option>
                <option value="purchase" @selected($report==='purchase')>Purchase Report</option>
                <option value="usage" @selected($report==='usage')>Usage Report</option>
                <option value="low_stock" @selected($report==='low_stock')>Low Stock</option>
            </select>
        </div>
        @if(in_array($report, ['supplier_wise','purchase','usage']))
            <div class="col-md-3"><label class="form-label small mb-1">From</label><input type="date" name="from" value="{{ $from }}" class="form-control form-control-sm"></div>
            <div class="col-md-3"><label class="form-label small mb-1">To</label><input type="date" name="to" value="{{ $to }}" class="form-control form-control-sm"></div>
        @endif
        @if($report==='supplier_wise')
            <div class="col-md-3">
                <label class="form-label small mb-1">Supplier</label>
                <input type="text" name="supplier" list="supplierlist" value="{{ $supplier }}" class="form-control form-control-sm">
                <datalist id="supplierlist">
                    @foreach($supplierNames as $sn)<option value="{{ $sn }}">@endforeach
                </datalist>
            </div>
        @endif
        <div class="col-md-2"><button class="btn btn-sm btn-dark"><i class="bi bi-funnel"></i> Run</button></div>
    </form>
</div>

<div class="card p-0">
    <table class="table table-sm mb-0 align-middle">
        @if($report==='available' || $report==='low_stock')
            <thead><tr><th>Ingredient</th><th>Category</th><th>Available</th><th>Unit</th><th>Low Threshold</th><th>Status</th></tr></thead>
            <tbody>
            @forelse($rows as $i)
                <tr><td>{{ $i->name }}</td><td>{{ $i->category->name ?? '—' }}</td><td>{{ rtrim(rtrim($i->available_quantity,'0'),'.') }}</td><td>{{ $i->unit }}</td><td>{{ rtrim(rtrim($i->low_stock_threshold,'0'),'.') }}</td><td>{!! $i->isLowStock() ? '<span class="badge bg-danger">Low</span>' : '<span class="badge bg-success">OK</span>' !!}</td></tr>
            @empty
                <tr><td colspan="6" class="text-center text-muted py-3">No ingredients.</td></tr>
            @endforelse
            </tbody>
        @elseif($report==='usage')
            <thead><tr><th>Date</th><th>Ingredient</th><th>Qty</th><th>Notes / Purpose</th></tr></thead>
            <tbody>
            @forelse($rows as $r)
                <tr><td>{{ $r->usage_date->format('d M Y') }}</td><td>{{ $r->ingredient->name ?? '—' }}</td><td>{{ rtrim(rtrim($r->quantity,'0'),'.') }} {{ $r->ingredient->unit ?? '' }}</td><td class="small">{{ $r->notes ?? '—' }}</td></tr>
            @empty
                <tr><td colspan="4" class="text-center text-muted py-3">No usage records in this range.</td></tr>
            @endforelse
            </tbody>
        @else {{-- purchase / supplier_wise --}}
            <thead><tr><th>Date</th><th>Ingredient</th><th>Supplier</th><th>Qty</th><th>Price</th><th>Total</th></tr></thead>
            <tbody>
            @forelse($rows as $r)
                <tr><td>{{ $r->date->format('d M Y') }}</td><td>{{ $r->ingredient->name ?? '—' }}</td><td>{{ $r->supplier_name }}</td><td>{{ rtrim(rtrim($r->quantity,'0'),'.') }} {{ $r->ingredient->unit ?? '' }}</td><td>{{ number_format($r->price,2) }}</td><td>{{ number_format($r->total_cost,2) }}</td></tr>
            @empty
                <tr><td colspan="6" class="text-center text-muted py-3">No stock entries in this range.</td></tr>
            @endforelse
            </tbody>
        @endif
    </table>
</div>
<div class="mt-2 text-muted small">Total records: {{ $rows->count() }}</div>
@endsection
