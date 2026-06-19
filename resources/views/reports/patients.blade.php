@extends('layouts.app')
@section('title', 'Patient Reports')

@section('content')
<div class="card p-3 mb-3">
    <form method="GET" class="row g-2 align-items-end">
        <div class="col-md-3">
            <label class="form-label small mb-1">Report</label>
            <select name="report" class="form-select form-select-sm" onchange="this.form.submit()">
                <option value="date_wise" @selected($report==='date_wise')>Date-wise Patients</option>
                <option value="serial" @selected($report==='serial')>Serial Number Search</option>
                <option value="follow_up" @selected($report==='follow_up')>Follow-up Patients</option>
                <option value="prescription" @selected($report==='prescription')>Prescription Uploads</option>
            </select>
        </div>
        @if($report==='serial')
            <div class="col-md-3">
                <label class="form-label small mb-1">Serial Number</label>
                <input type="text" name="serial" value="{{ request('serial') }}" class="form-control form-control-sm">
            </div>
        @else
            <div class="col-md-3">
                <label class="form-label small mb-1">From</label>
                <input type="date" name="from" value="{{ $from }}" class="form-control form-control-sm">
            </div>
            <div class="col-md-3">
                <label class="form-label small mb-1">To</label>
                <input type="date" name="to" value="{{ $to }}" class="form-control form-control-sm">
            </div>
        @endif
        <div class="col-md-2"><button class="btn btn-sm btn-dark"><i class="bi bi-funnel"></i> Run</button></div>
    </form>
</div>

<div class="card p-0">
    <table class="table table-sm mb-0 align-middle">
        @if($report==='serial')
            <thead><tr><th>Serial</th><th>Name</th><th>Phone</th><th>Visits</th></tr></thead>
            <tbody>
            @forelse($rows as $p)
                <tr><td>{{ $p->serial_number }}</td><td><a href="{{ route('patients.show',$p) }}">{{ $p->name }}</a></td><td>{{ $p->phone ?? '—' }}</td><td>{{ $p->visits->count() }}</td></tr>
            @empty
                <tr><td colspan="4" class="text-center text-muted py-3">No matching patients.</td></tr>
            @endforelse
            </tbody>
        @elseif($report==='follow_up')
            <thead><tr><th>Patient</th><th>Doctor</th><th>Visit Date</th><th>Follow-up Date</th><th>Status</th></tr></thead>
            <tbody>
            @forelse($rows as $v)
                <tr><td><a href="{{ route('patients.show',$v->patient_id) }}">{{ $v->patient->name ?? '—' }}</a></td><td>{{ $v->doctor_name ?? '—' }}</td><td>{{ $v->visit_date->format('d M Y') }}</td><td>{{ $v->follow_up_date->format('d M Y') }}</td><td>{{ $v->statusLabel() }}</td></tr>
            @empty
                <tr><td colspan="5" class="text-center text-muted py-3">No follow-ups in this range.</td></tr>
            @endforelse
            </tbody>
        @elseif($report==='prescription')
            <thead><tr><th>Patient</th><th>Visit Date</th><th>Doctor</th><th>Slip</th></tr></thead>
            <tbody>
            @forelse($rows as $v)
                <tr><td><a href="{{ route('patients.show',$v->patient_id) }}">{{ $v->patient->name ?? '—' }}</a></td><td>{{ $v->visit_date->format('d M Y') }}</td><td>{{ $v->doctor_name ?? '—' }}</td><td><a href="{{ Storage::url($v->prescription_path) }}" target="_blank">View</a></td></tr>
            @empty
                <tr><td colspan="4" class="text-center text-muted py-3">No prescription uploads in this range.</td></tr>
            @endforelse
            </tbody>
        @else
            <thead><tr><th>Patient</th><th>Serial</th><th>Visit Date</th><th>Doctor</th><th>Disease</th><th>Status</th></tr></thead>
            <tbody>
            @forelse($rows as $v)
                <tr><td><a href="{{ route('patients.show',$v->patient_id) }}">{{ $v->patient->name ?? '—' }}</a></td><td>{{ $v->patient->serial_number ?? '—' }}</td><td>{{ $v->visit_date->format('d M Y') }}</td><td>{{ $v->doctor_name ?? '—' }}</td><td>{{ $v->disease ?? '—' }}</td><td>{{ $v->statusLabel() }}</td></tr>
            @empty
                <tr><td colspan="6" class="text-center text-muted py-3">No visits in this range.</td></tr>
            @endforelse
            </tbody>
        @endif
    </table>
</div>
<div class="mt-2 text-muted small">Total records: {{ $rows->count() }}</div>
@endsection
