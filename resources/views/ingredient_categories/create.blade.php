@extends('layouts.app')
@section('title', 'New Ingredient Category')

@section('content')
<form method="POST" action="{{ route('ingredient-categories.store') }}">
    @csrf
    <div class="card p-4 mb-3" style="max-width:600px;">
        <div class="mb-3">
            <label class="form-label">Category Name <span class="text-danger">*</span></label>
            <input type="text" name="name" value="{{ old('name') }}" class="form-control" required autofocus>
        </div>
        <div class="mb-3">
            <label class="form-label">Notes</label>
            <textarea name="notes" rows="2" class="form-control">{{ old('notes') }}</textarea>
        </div>
        <div>
            <button class="btn btn-primary"><i class="bi bi-save"></i> Save</button>
            <a href="{{ route('ingredient-categories.index') }}" class="btn btn-outline-secondary">Cancel</a>
        </div>
    </div>
</form>
@endsection
