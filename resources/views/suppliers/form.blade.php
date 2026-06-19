@php $supplier = $supplier ?? null; @endphp
<div class="card p-4 mb-3" style="max-width:700px;">
    <div class="row g-3">
        <div class="col-md-6">
            <label class="form-label">Supplier / Vendor Name <span class="text-danger">*</span></label>
            <input type="text" name="name" value="{{ old('name', optional($supplier)->name) }}" class="form-control" required autofocus>
        </div>
        <div class="col-md-6">
            <label class="form-label">Phone</label>
            <input type="text" name="phone" value="{{ old('phone', optional($supplier)->phone) }}" class="form-control">
        </div>
        <div class="col-12">
            <label class="form-label">Address</label>
            <input type="text" name="address" value="{{ old('address', optional($supplier)->address) }}" class="form-control">
        </div>
        <div class="col-12">
            <label class="form-label">Notes</label>
            <textarea name="notes" rows="2" class="form-control">{{ old('notes', optional($supplier)->notes) }}</textarea>
        </div>
    </div>
</div>
