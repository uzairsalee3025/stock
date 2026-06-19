@php $visit = $visit ?? null; @endphp
<div class="row g-3">
    <div class="col-md-4">
        <label class="form-label">Visit Date <span class="text-danger">*</span></label>
        <input type="date" name="visit_date" value="{{ old('visit_date', optional($visit)->visit_date?->format('Y-m-d') ?? now()->format('Y-m-d')) }}" class="form-control" required>
    </div>
    <div class="col-md-4">
        <label class="form-label">Doctor Name</label>
        <input type="text" name="doctor_name" value="{{ old('doctor_name', optional($visit)->doctor_name) }}" class="form-control">
    </div>
    <div class="col-md-4">
        <label class="form-label">Status <span class="text-danger">*</span></label>
        <select name="status" class="form-select" required>
            @foreach(['active','follow_up','completed','cancelled'] as $s)
                <option value="{{ $s }}" @selected(old('status', optional($visit)->status ?? 'active')===$s)>{{ ucwords(str_replace('_',' ',$s)) }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-6">
        <label class="form-label">Disease / Health Issue</label>
        <input type="text" name="disease" value="{{ old('disease', optional($visit)->disease) }}" class="form-control">
    </div>
    <div class="col-md-6">
        <label class="form-label">Follow-up Date</label>
        <input type="date" name="follow_up_date" value="{{ old('follow_up_date', optional($visit)->follow_up_date?->format('Y-m-d')) }}" class="form-control">
    </div>
    <div class="col-12">
        <label class="form-label">Notes</label>
        <textarea name="notes" rows="2" class="form-control">{{ old('notes', optional($visit)->notes) }}</textarea>
    </div>
    <div class="col-12">
        <label class="form-label">Prescription / Medicine Slip (image or PDF, max 5 MB)</label>
        <input type="file" name="prescription" accept=".jpg,.jpeg,.png,.webp,.pdf" class="form-control">
        @if($visit && $visit->prescription_path)
            <small class="text-muted">Current: <a href="{{ Storage::url($visit->prescription_path) }}" target="_blank">view slip</a> — uploading a new file replaces it.</small>
        @endif
    </div>
</div>
