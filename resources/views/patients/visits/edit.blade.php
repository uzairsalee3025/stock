@extends('layouts.app')
@section('title', 'Edit Visit')

@section('content')
<form method="POST" action="{{ route('visits.update', $visit) }}" enctype="multipart/form-data">
    @csrf @method('PUT')
    <div class="card p-4 mb-3">
        <h6 class="border-bottom pb-2 mb-3">Visit for {{ $visit->patient->name }} ({{ $visit->patient->serial_number }})</h6>
        @include('patients.partials.visit_fields', ['visit' => $visit])
    </div>
    <button class="btn btn-primary"><i class="bi bi-save"></i> Update Visit</button>
    <a href="{{ route('patients.show', $visit->patient_id) }}" class="btn btn-outline-secondary">Cancel</a>
</form>
@endsection
