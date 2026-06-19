@extends('layouts.app')
@section('title', 'Edit Supplier')

@section('content')
<form method="POST" action="{{ route('suppliers.update', $supplier) }}">
    @csrf @method('PUT')
    @include('suppliers.form', ['supplier' => $supplier])
    <button class="btn btn-primary"><i class="bi bi-save"></i> Update</button>
    <a href="{{ route('suppliers.index') }}" class="btn btn-outline-secondary">Cancel</a>
</form>
@endsection
