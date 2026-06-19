@extends('layouts.app')
@section('title', 'Ingredient Usage')
@section('actions')
    <a href="{{ route('ingredient-usages.create') }}" class="btn btn-primary"><i class="bi bi-plus-lg"></i> Record Usage</a>
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
            <label class="form-label small mb-1">Ingredient</label>
            <select name="ingredient" class="form-select form-select-sm">
                <option value="">All</option>
                @foreach($ingredients as $i)<option value="{{ $i->id }}" @selected(request('ingredient')==$i->id)>{{ $i->name }}</option>@endforeach
            </select>
        </div>
        <div class="col-md-2"><label class="form-label small mb-1">From</label><input type="date" name="from" value="{{ request('from') }}" class="form-control form-control-sm"></div>
        <div class="col-md-2"><label class="form-label small mb-1">To</label><input type="date" name="to" value="{{ request('to') }}" class="form-control form-control-sm"></div>
        <div class="col-md-2">
            <button class="btn btn-sm btn-dark"><i class="bi bi-funnel"></i> Filter</button>
            <a href="{{ route('ingredient-usages.index') }}" class="btn btn-sm btn-outline-secondary">Reset</a>
        </div>
    </form>
</div>

<div class="card p-0">
    <table class="table table-hover mb-0 align-middle">
        <thead><tr><th>Date</th><th>Ingredient</th><th>Category</th><th>Quantity Used</th><th>Notes / Purpose</th><th></th></tr></thead>
        <tbody>
        @forelse($usages as $usage)
            <tr>
                <td>{{ $usage->usage_date->format('d M Y') }}</td>
                <td><a href="{{ route('ingredients.show', $usage->ingredient_id) }}">{{ $usage->ingredient->name ?? '—' }}</a></td>
                <td>{{ $usage->ingredient->category->name ?? '—' }}</td>
                <td>{{ rtrim(rtrim($usage->quantity,'0'),'.') }} {{ $usage->ingredient->unit ?? '' }}</td>
                <td class="small">{{ $usage->notes ?? '—' }}</td>
                <td class="text-end">
                    <form method="POST" action="{{ route('ingredient-usages.destroy', $usage) }}" onsubmit="return confirm('Remove this usage and return quantity to stock?')">
                        @csrf @method('DELETE')
                        <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                    </form>
                </td>
            </tr>
        @empty
            <tr><td colspan="6" class="text-center text-muted py-4">No usage records yet.</td></tr>
        @endforelse
        </tbody>
    </table>
</div>
<div class="mt-3">{{ $usages->links() }}</div>
@endsection
