@extends('layouts.app')
@section('title', 'Ingredient Categories')
@section('actions')
    <a href="{{ route('ingredient-categories.create') }}" class="btn btn-primary"><i class="bi bi-plus-lg"></i> New Category</a>
@endsection

@section('content')
<div class="card p-0">
    <table class="table table-hover mb-0 align-middle">
        <thead><tr><th>Name</th><th>Ingredients</th><th>Notes</th><th></th></tr></thead>
        <tbody>
        @forelse($categories as $category)
            <tr>
                <td>{{ $category->name }}</td>
                <td>{{ $category->ingredients_count }}</td>
                <td class="text-muted small">{{ $category->notes ?? '—' }}</td>
                <td class="text-end">
                    <a href="{{ route('ingredient-categories.edit', $category) }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-pencil"></i></a>
                    <form method="POST" action="{{ route('ingredient-categories.destroy', $category) }}" class="d-inline" onsubmit="return confirm('Delete this category?')">
                        @csrf @method('DELETE')
                        <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                    </form>
                </td>
            </tr>
        @empty
            <tr><td colspan="4" class="text-center text-muted py-4">No categories yet.</td></tr>
        @endforelse
        </tbody>
    </table>
</div>
<div class="mt-3">{{ $categories->links() }}</div>
@endsection
