@extends('layouts.app')
@section('title', 'New Supplier')

@section('content')
<form method="POST" action="{{ route('suppliers.store') }}">
    @csrf
    @include('suppliers.form', ['supplier' => null])
    <button class="btn btn-primary"><i class="bi bi-save"></i> Save</button>
    <a href="{{ route('suppliers.index') }}" class="btn btn-outline-secondary">Cancel</a>
</form>
@endsection
