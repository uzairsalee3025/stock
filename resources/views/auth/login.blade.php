@extends('layouts.app')
@section('title', 'Login')

@section('content')
<div class="d-flex align-items-center justify-content-center" style="min-height:100vh;background:#1f2937;">
    <div class="card p-4 shadow" style="width:100%;max-width:400px;">
        <div class="text-center mb-3">
            <h4 class="mb-0"><i class="bi bi-heart-pulse text-danger"></i> Clinic &amp; Stock</h4>
            <small class="text-muted">Sign in to continue</small>
        </div>

        @if($errors->any())
            <div class="alert alert-danger py-2">{{ $errors->first() }}</div>
        @endif

        <form method="POST" action="{{ route('login') }}">
            @csrf
            <div class="mb-3">
                <label class="form-label">Email</label>
                <input type="email" name="email" value="{{ old('email') }}" class="form-control" required autofocus>
            </div>
            <div class="mb-3">
                <label class="form-label">Password</label>
                <input type="password" name="password" class="form-control" required>
            </div>
            <div class="form-check mb-3">
                <input type="checkbox" name="remember" class="form-check-input" id="remember">
                <label class="form-check-label" for="remember">Remember me</label>
            </div>
            <button class="btn btn-primary w-100">Sign In</button>
        </form>
    </div>
</div>
@endsection
