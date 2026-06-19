@extends('layouts.app')
@section('title', 'New Patient')

@section('content')
<form method="POST" action="{{ route('patients.store') }}" enctype="multipart/form-data">
    @csrf
    <div class="card p-4 mb-3">
        <h6 class="border-bottom pb-2 mb-3">Patient Details</h6>
        <div class="row g-3">
            <div class="col-md-4">
                <label class="form-label">Serial Number</label>
                <input type="text" value="{{ $serial }}" class="form-control bg-light" readonly>
                <small class="text-muted">Auto-generated on save.</small>
            </div>
            <div class="col-md-5">
                <label class="form-label">Patient Name <span class="text-danger">*</span></label>
                <input type="text" name="name" value="{{ old('name') }}" class="form-control" required>
            </div>
            <div class="col-md-3">
                <label class="form-label">Phone</label>
                <input type="text" name="phone" value="{{ old('phone') }}" class="form-control">
            </div>
            <div class="col-md-2">
                <label class="form-label">Age</label>
                <input type="number" name="age" value="{{ old('age') }}" class="form-control" min="0" max="200">
            </div>
            <div class="col-md-3">
                <label class="form-label">Gender</label>
                <select name="gender" class="form-select">
                    <option value="">—</option>
                    @foreach(['male','female','other'] as $g)
                        <option value="{{ $g }}" @selected(old('gender')===$g)>{{ ucfirst($g) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-7">
                <label class="form-label">Address</label>
                <input type="text" name="address" value="{{ old('address') }}" class="form-control">
            </div>
        </div>
    </div>

    <div class="card p-4 mb-3">
        <h6 class="border-bottom pb-2 mb-3">First Visit</h6>
        @include('patients.partials.visit_fields')
    </div>

    <button class="btn btn-primary"><i class="bi bi-save"></i> Save Patient</button>
    <a href="{{ route('patients.index') }}" class="btn btn-outline-secondary">Cancel</a>
</form>
@endsection
