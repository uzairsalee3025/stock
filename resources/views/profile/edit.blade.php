@extends('layouts.app')
@section('title', 'My Profile')

@section('content')
<div class="row g-3">
    <div class="col-lg-4">
        <div class="card p-3">
            <h6 class="border-bottom pb-2">Account</h6>
            <dl class="row mb-0 small">
                <dt class="col-4">Name</dt><dd class="col-8">{{ $user->name }}</dd>
                <dt class="col-4">Email</dt><dd class="col-8">{{ $user->email }}</dd>
                <dt class="col-4">Role</dt><dd class="col-8">{{ $user->roleLabel() }}</dd>
            </dl>
        </div>
    </div>

    <div class="col-lg-6">
        <div class="card p-4">
            <h6 class="border-bottom pb-2 mb-3"><i class="bi bi-shield-lock"></i> Change Password</h6>
            <form method="POST" action="{{ route('profile.password') }}">
                @csrf
                <div class="mb-3">
                    <label class="form-label">Current Password <span class="text-danger">*</span></label>
                    <input type="password" name="current_password" class="form-control @error('current_password') is-invalid @enderror" required>
                    @error('current_password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="mb-3">
                    <label class="form-label">New Password <span class="text-danger">*</span></label>
                    <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" required>
                    @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    <small class="text-muted">Minimum 8 characters.</small>
                </div>
                <div class="mb-3">
                    <label class="form-label">Confirm New Password <span class="text-danger">*</span></label>
                    <input type="password" name="password_confirmation" class="form-control" required>
                </div>
                <button class="btn btn-primary"><i class="bi bi-save"></i> Update Password</button>
            </form>
        </div>
    </div>
</div>
@endsection
