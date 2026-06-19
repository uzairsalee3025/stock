@extends('layouts.app')
@section('title', 'Suppliers / Vendors')
@section('actions')
    <a href="{{ route('suppliers.create') }}" class="btn btn-primary"><i class="bi bi-plus-lg"></i> New Supplier</a>
@endsection

@section('content')
<div class="card p-3 mb-3">
    <form method="GET" class="row g-2">
        <div class="col-md-4">
            <input type="text" name="search" value="{{ request('search') }}" class="form-control form-control-sm" placeholder="Search name or phone">
        </div>
        <div class="col-md-3">
            <button class="btn btn-sm btn-dark"><i class="bi bi-search"></i> Search</button>
            <a href="{{ route('suppliers.index') }}" class="btn btn-sm btn-outline-secondary">Reset</a>
        </div>
    </form>
</div>
<div class="card p-0">
    <table class="table table-hover mb-0 align-middle">
        <thead><tr><th>Name</th><th>Phone</th><th>Address</th><th>Stock Entries</th><th></th></tr></thead>
        <tbody>
        @forelse($suppliers as $supplier)
            <tr>
                <td><a href="{{ route('suppliers.show', $supplier) }}">{{ $supplier->name }}</a></td>
                <td>{{ $supplier->phone ?? '—' }}</td>
                <td class="small">{{ $supplier->address ?? '—' }}</td>
                <td>{{ $supplier->entries_count }}</td>
                <td class="text-end">
                    <a href="{{ route('suppliers.edit', $supplier) }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-pencil"></i></a>
                    <form method="POST" action="{{ route('suppliers.destroy', $supplier) }}" class="d-inline" onsubmit="return confirm('Delete this supplier?')">
                        @csrf @method('DELETE')
                        <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                    </form>
                </td>
            </tr>
        @empty
            <tr><td colspan="5" class="text-center text-muted py-4">No suppliers yet.</td></tr>
        @endforelse
        </tbody>
    </table>
</div>
<div class="mt-3">{{ $suppliers->links() }}</div>
@endsection
