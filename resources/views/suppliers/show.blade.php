@extends('layouts.app')
@section('title', 'Supplier — '.$supplier->name)
@section('actions')
    <a href="{{ route('suppliers.edit', $supplier) }}" class="btn btn-outline-secondary"><i class="bi bi-pencil"></i> Edit</a>
@endsection

@section('content')
<div class="card p-3 mb-3" style="max-width:600px;">
    <dl class="row mb-0 small">
        <dt class="col-4">Phone</dt><dd class="col-8">{{ $supplier->phone ?? '—' }}</dd>
        <dt class="col-4">Address</dt><dd class="col-8">{{ $supplier->address ?? '—' }}</dd>
        <dt class="col-4">Notes</dt><dd class="col-8">{{ $supplier->notes ?? '—' }}</dd>
    </dl>
</div>

<div class="card p-3">
    <h6 class="border-bottom pb-2">Supplier-wise Stock Entries</h6>
    <table class="table table-sm mb-0">
        <thead><tr><th>Date</th><th>Ingredient</th><th>Qty</th><th>Price</th><th>Total</th></tr></thead>
        <tbody>
        @forelse($entries as $e)
            <tr>
                <td>{{ $e->date->format('d M Y') }}</td>
                <td>{{ $e->ingredient->name ?? '—' }}</td>
                <td>{{ rtrim(rtrim($e->quantity,'0'),'.') }} {{ $e->ingredient->unit ?? '' }}</td>
                <td>{{ number_format($e->price, 2) }}</td>
                <td>{{ number_format($e->total_cost, 2) }}</td>
            </tr>
        @empty
            <tr><td colspan="5" class="text-muted">No stock entries from this supplier.</td></tr>
        @endforelse
        </tbody>
    </table>
</div>
@endsection
