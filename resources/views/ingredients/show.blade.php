@extends('layouts.app')
@section('title', 'Ingredient — '.$ingredient->name)
@section('actions')
    <a href="{{ route('ingredients.edit', $ingredient) }}" class="btn btn-outline-secondary"><i class="bi bi-pencil"></i> Edit</a>
    <form method="POST" action="{{ route('ingredients.destroy', $ingredient) }}" class="d-inline" onsubmit="return confirm('Delete ingredient and all its stock history?')">
        @csrf @method('DELETE')
        <button class="btn btn-outline-danger"><i class="bi bi-trash"></i> Delete</button>
    </form>
@endsection

@section('content')
@php use App\Models\Ingredient; @endphp
<div class="row g-3 mb-3">
    <div class="col-md-3"><div class="card p-3"><div class="text-muted small">Category</div><h5 class="mb-0">{{ $ingredient->category->name ?? '—' }}</h5></div></div>
    <div class="col-md-3"><div class="card p-3 {{ $ingredient->isLowStock() ? 'border border-danger' : '' }}"><div class="text-muted small">Total Available Quantity</div><h4 class="mb-0">{{ Ingredient::formatQty($availableTotal) }} <small class="text-muted">{{ $ingredient->unit }}</small> @if($ingredient->isLowStock())<span class="badge bg-danger">Low</span>@endif</h4></div></div>
    <div class="col-md-3"><div class="card p-3"><div class="text-muted small">Total Purchased</div><h4 class="mb-0">{{ Ingredient::formatQty($purchaseTotal) }} <small class="text-muted">{{ $ingredient->unit }}</small></h4></div></div>
    <div class="col-md-3"><div class="card p-3"><div class="text-muted small">Total Used</div><h4 class="mb-0">{{ Ingredient::formatQty($usedTotal) }} <small class="text-muted">{{ $ingredient->unit }}</small></h4></div></div>
</div>

<div class="row g-3">
    <div class="col-lg-5">
        <div class="card p-3 mb-3">
            <h6 class="border-bottom pb-2 text-success"><i class="bi bi-box-arrow-in-down"></i> Add Supplier Entry (Add Stock)</h6>
            <form method="POST" action="{{ route('ingredients.entries.store', $ingredient) }}" class="row g-2">
                @csrf
                <div class="col-12">
                    <label class="form-label small mb-1">Supplier Name *</label>
                    <input type="text" name="supplier_name" list="supplierlist" class="form-control form-control-sm" required>
                    <datalist id="supplierlist">
                        @foreach($supplierNames as $sn)<option value="{{ $sn }}">@endforeach
                    </datalist>
                </div>
                <div class="col-md-6">
                    <label class="form-label small mb-1">Quantity ({{ $ingredient->unit }}) *</label>
                    <input type="number" step="0.001" min="0.001" name="quantity" class="form-control form-control-sm" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label small mb-1">Price *</label>
                    <input type="number" step="0.01" min="0" name="price" value="0" class="form-control form-control-sm" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label small mb-1">Date *</label>
                    <input type="date" name="date" value="{{ now()->format('Y-m-d') }}" class="form-control form-control-sm" required>
                </div>
                <div class="col-12"><input type="text" name="notes" class="form-control form-control-sm" placeholder="Notes (optional)"></div>
                <div class="col-12"><button class="btn btn-sm btn-success"><i class="bi bi-plus-lg"></i> Add Stock</button></div>
            </form>
        </div>

        <div class="card p-3">
            <h6 class="border-bottom pb-2 text-warning"><i class="bi bi-box-arrow-up"></i> Quick Use / Deduct Stock</h6>
            <form method="POST" action="{{ route('ingredient-usages.store') }}" class="row g-2">
                @csrf
                <input type="hidden" name="ingredient_id" value="{{ $ingredient->id }}">
                <div class="col-md-6">
                    <label class="form-label small mb-1">Usage Qty ({{ $ingredient->unit }}) *</label>
                    <input type="number" step="0.001" min="0.001" name="quantity" class="form-control form-control-sm" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label small mb-1">Date *</label>
                    <input type="date" name="usage_date" value="{{ now()->format('Y-m-d') }}" class="form-control form-control-sm" required>
                </div>
                <div class="col-12"><input type="text" name="notes" class="form-control form-control-sm" placeholder="Notes / purpose (optional)"></div>
                <div class="col-12">
                    <button class="btn btn-sm btn-warning"><i class="bi bi-dash-lg"></i> Use Stock</button>
                    <a href="{{ route('ingredient-usages.create') }}" class="btn btn-sm btn-outline-secondary">Full Usage Page</a>
                </div>
            </form>
        </div>
    </div>

    <div class="col-lg-7">
        <div class="card p-3 mb-3">
            <h6 class="border-bottom pb-2">Supplier-wise Available Stock</h6>
            <table class="table table-sm mb-0">
                <thead><tr><th>Supplier</th><th>Total Quantity</th><th>Total Value</th></tr></thead>
                <tbody>
                @forelse($supplierBreakdown as $row)
                    <tr>
                        <td>{{ $row->supplier_name }}</td>
                        <td>{{ Ingredient::formatQty($row->total_quantity) }} {{ $ingredient->unit }}</td>
                        <td>{{ number_format($row->total_value, 2) }}</td>
                    </tr>
                @empty
                    <tr><td colspan="3" class="text-muted">No supplier entries yet.</td></tr>
                @endforelse
                </tbody>
                @if($supplierBreakdown->count())
                <tfoot><tr class="fw-bold"><td>Total</td><td>{{ Ingredient::formatQty($supplierTotalQuantity) }} {{ $ingredient->unit }}</td><td>{{ number_format($supplierTotalValue, 2) }}</td></tr></tfoot>
                @endif
            </table>
        </div>

        <div class="card p-3">
            <h6 class="border-bottom pb-2">Stock Movement History</h6>
            @php
                $movements = collect();
                foreach($ingredient->stockEntries as $e){ $movements->push(['date'=>$e->date,'type'=>'IN','qty'=>$e->quantity,'detail'=>'From '.$e->supplier_name.($e->price? ' @ '.number_format($e->price,2):''),'del'=>route('entries.destroy',$e)]); }
                foreach($ingredient->usages as $us){ $movements->push(['date'=>$us->usage_date,'type'=>'OUT','qty'=>$us->quantity,'detail'=>'Used'.($us->notes? ' — '.$us->notes:''),'del'=>route('ingredient-usages.destroy',$us)]); }
                $movements = $movements->sortByDesc('date');
            @endphp
            <table class="table table-sm mb-0 align-middle">
                <thead><tr><th>Date</th><th>Type</th><th>Qty</th><th>Detail</th><th></th></tr></thead>
                <tbody>
                @forelse($movements as $m)
                    <tr>
                        <td>{{ $m['date']->format('d M Y') }}</td>
                        <td><span class="badge {{ $m['type']==='IN'?'bg-success':'bg-warning text-dark' }}">{{ $m['type'] }}</span></td>
                        <td>{{ Ingredient::formatQty($m['qty']) }} {{ $ingredient->unit }}</td>
                        <td class="small">{{ $m['detail'] }}</td>
                        <td class="text-end">
                            <form method="POST" action="{{ $m['del'] }}" onsubmit="return confirm('Remove this entry and adjust quantity?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger py-0"><i class="bi bi-x"></i></button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="text-muted">No stock movements yet.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
