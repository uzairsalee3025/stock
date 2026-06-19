@extends('layouts.app')
@section('title', 'Patient Records')
@section('actions')
    <a href="{{ route('patients.create') }}" class="btn btn-primary"><i class="bi bi-plus-lg"></i> New Patient</a>
@endsection

@section('content')
<div class="card p-3 mb-3">
    <form method="GET" class="row g-2 align-items-end">
        <div class="col-md-3">
            <label class="form-label small mb-1">Search (serial / name / phone)</label>
            <input type="text" name="search" value="{{ request('search') }}" class="form-control form-control-sm">
        </div>
        <div class="col-md-2">
            <label class="form-label small mb-1">Doctor</label>
            <input type="text" name="doctor" value="{{ request('doctor') }}" class="form-control form-control-sm">
        </div>
        <div class="col-md-2">
            <label class="form-label small mb-1">Visit Date</label>
            <input type="date" name="date" value="{{ request('date') }}" class="form-control form-control-sm">
        </div>
        <div class="col-md-2">
            <label class="form-label small mb-1">Status</label>
            <select name="status" class="form-select form-select-sm">
                <option value="">All</option>
                @foreach(['active','follow_up','completed','cancelled'] as $s)
                    <option value="{{ $s }}" @selected(request('status')===$s)>{{ ucwords(str_replace('_',' ',$s)) }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-3">
            <button class="btn btn-sm btn-dark"><i class="bi bi-search"></i> Filter</button>
            <a href="{{ route('patients.index') }}" class="btn btn-sm btn-outline-secondary">Reset</a>
        </div>
    </form>
</div>

<div class="card p-0">
    <table class="table table-hover mb-0 align-middle">
        <thead>
            <tr><th>Serial No.</th><th>Name</th><th>Phone</th><th>Age</th><th>Last Visit</th><th>Doctor</th><th>Status</th><th></th></tr>
        </thead>
        <tbody>
        @forelse($patients as $patient)
            @php $lv = $patient->latestVisit; @endphp
            <tr>
                <td><span class="badge bg-secondary">{{ $patient->serial_number }}</span></td>
                <td>{{ $patient->name }}</td>
                <td>{{ $patient->phone ?? '—' }}</td>
                <td>{{ $patient->age ?? '—' }}</td>
                <td>{{ optional($lv)->visit_date?->format('d M Y') ?? '—' }}</td>
                <td>{{ optional($lv)->doctor_name ?? '—' }}</td>
                <td>@if($lv)<span class="badge bg-info text-dark">{{ $lv->statusLabel() }}</span>@else — @endif</td>
                <td class="text-end">
                    <a href="{{ route('patients.show', $patient) }}" class="btn btn-sm btn-outline-primary"><i class="bi bi-eye"></i></a>
                    <a href="{{ route('patients.edit', $patient) }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-pencil"></i></a>
                </td>
            </tr>
        @empty
            <tr><td colspan="8" class="text-center text-muted py-4">No patients found.</td></tr>
        @endforelse
        </tbody>
    </table>
</div>
<div class="mt-3">{{ $patients->links() }}</div>
@endsection
