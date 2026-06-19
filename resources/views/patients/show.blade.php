@extends('layouts.app')
@section('title', 'Patient — '.$patient->serial_number)
@section('actions')
    <a href="{{ route('patients.edit', $patient) }}" class="btn btn-outline-secondary"><i class="bi bi-pencil"></i> Edit</a>
    <form method="POST" action="{{ route('patients.destroy', $patient) }}" class="d-inline" onsubmit="return confirm('Delete this patient and all visits?')">
        @csrf @method('DELETE')
        <button class="btn btn-outline-danger"><i class="bi bi-trash"></i> Delete</button>
    </form>
@endsection

@section('content')
<div class="row g-3">
    <div class="col-lg-4">
        <div class="card p-3">
            <h5>{{ $patient->name }}</h5>
            <span class="badge bg-secondary mb-3">{{ $patient->serial_number }}</span>
            <dl class="row mb-0 small">
                <dt class="col-5">Phone</dt><dd class="col-7">{{ $patient->phone ?? '—' }}</dd>
                <dt class="col-5">Age</dt><dd class="col-7">{{ $patient->age ?? '—' }}</dd>
                <dt class="col-5">Gender</dt><dd class="col-7">{{ $patient->gender ? ucfirst($patient->gender) : '—' }}</dd>
                <dt class="col-5">Address</dt><dd class="col-7">{{ $patient->address ?? '—' }}</dd>
                <dt class="col-5">Total Visits</dt><dd class="col-7">{{ $patient->visits->count() }}</dd>
            </dl>
        </div>
    </div>

    <div class="col-lg-8">
        <div class="card p-3 mb-3">
            <h6 class="border-bottom pb-2 mb-3"><i class="bi bi-plus-circle"></i> Add Repeat Visit</h6>
            <form method="POST" action="{{ route('patients.visits.store', $patient) }}" enctype="multipart/form-data">
                @csrf
                @include('patients.partials.visit_fields', ['visit' => null])
                <button class="btn btn-primary mt-3"><i class="bi bi-save"></i> Add Visit</button>
            </form>
        </div>

        <div class="card p-3">
            <h6 class="border-bottom pb-2 mb-3">Visit History</h6>
            @forelse($patient->visits as $visit)
                <div class="border rounded p-3 mb-2">
                    <div class="d-flex justify-content-between">
                        <div>
                            <strong>{{ $visit->visit_date->format('d M Y') }}</strong>
                            <span class="badge bg-info text-dark ms-2">{{ $visit->statusLabel() }}</span>
                        </div>
                        <div>
                            <a href="{{ route('visits.edit', $visit) }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-pencil"></i></a>
                            <form method="POST" action="{{ route('visits.destroy', $visit) }}" class="d-inline" onsubmit="return confirm('Remove this visit?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                            </form>
                        </div>
                    </div>
                    <div class="small mt-2">
                        <div><strong>Doctor:</strong> {{ $visit->doctor_name ?? '—' }}</div>
                        <div><strong>Disease:</strong> {{ $visit->disease ?? '—' }}</div>
                        @if($visit->follow_up_date)<div><strong>Follow-up:</strong> {{ $visit->follow_up_date->format('d M Y') }}</div>@endif
                        @if($visit->notes)<div><strong>Notes:</strong> {{ $visit->notes }}</div>@endif
                        @if($visit->prescription_path)
                            <div class="mt-1"><a href="{{ Storage::url($visit->prescription_path) }}" target="_blank" class="btn btn-sm btn-outline-primary"><i class="bi bi-file-earmark-medical"></i> View Prescription Slip</a></div>
                        @endif
                    </div>
                </div>
            @empty
                <p class="text-muted mb-0">No visits recorded.</p>
            @endforelse
        </div>
    </div>
</div>
@endsection
