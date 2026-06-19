<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') — {{ config('app.name') }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        body { background:#f4f6f9; }
        .sidebar { width:250px; min-height:100vh; background:#1f2937; }
        .sidebar a { color:#cbd5e1; text-decoration:none; padding:.6rem 1rem; display:flex; align-items:center; gap:.6rem; border-radius:.4rem; margin:.1rem .5rem; }
        .sidebar a:hover, .sidebar a.active { background:#374151; color:#fff; }
        .sidebar .nav-section { color:#6b7280; font-size:.72rem; text-transform:uppercase; letter-spacing:.05em; padding:.8rem 1rem .2rem; }
        .content { flex:1; min-width:0; }
        .card { border:none; box-shadow:0 1px 3px rgba(0,0,0,.08); }
        .table thead th { background:#f8f9fa; }
    </style>
</head>
<body>
@auth
<div class="d-flex">
    <nav class="sidebar text-white d-flex flex-column flex-shrink-0">
        <div class="p-3 border-bottom border-secondary">
            <h5 class="mb-0 text-white"><i class="bi bi-heart-pulse"></i> Clinic & Stock</h5>
            <small class="text-secondary">{{ auth()->user()->roleLabel() }}</small>
        </div>
        @php $u = auth()->user(); @endphp
        <div class="flex-grow-1 py-2">
            <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'active' : '' }}"><i class="bi bi-speedometer2"></i> Dashboard</a>

            @if($u->canManagePatients())
                <div class="nav-section">Patients</div>
                <a href="{{ route('patients.index') }}" class="{{ request()->routeIs('patients.*','visits.*') ? 'active' : '' }}"><i class="bi bi-people"></i> Patients</a>
                <a href="{{ route('reports.patients') }}" class="{{ request()->routeIs('reports.patients') ? 'active' : '' }}"><i class="bi bi-file-earmark-text"></i> Patient Reports</a>
            @endif

            @if($u->canManageInventory())
                <div class="nav-section">Ingredient Inventory</div>
                <a href="{{ route('ingredients.index') }}" class="{{ request()->routeIs('ingredients.*','entries.*') ? 'active' : '' }}"><i class="bi bi-box-seam"></i> Ingredients</a>
                <a href="{{ route('ingredient-categories.index') }}" class="{{ request()->routeIs('ingredient-categories.*') ? 'active' : '' }}"><i class="bi bi-tags"></i> Categories</a>
                <a href="{{ route('suppliers.index') }}" class="{{ request()->routeIs('suppliers.*') ? 'active' : '' }}"><i class="bi bi-truck"></i> Suppliers / Supplier Entries</a>
                <a href="{{ route('ingredient-usages.index') }}" class="{{ request()->routeIs('ingredient-usages.*') ? 'active' : '' }}"><i class="bi bi-box-arrow-up"></i> Ingredient Usage</a>
                <a href="{{ route('reports.ingredients') }}" class="{{ request()->routeIs('reports.ingredients') ? 'active' : '' }}"><i class="bi bi-file-earmark-bar-graph"></i> Ingredient Reports</a>

                <div class="nav-section">Products</div>
                <a href="{{ route('products.index') }}" class="{{ request()->routeIs('products.*') ? 'active' : '' }}"><i class="bi bi-bag"></i> Products</a>
                <a href="{{ route('product-categories.index') }}" class="{{ request()->routeIs('product-categories.*') ? 'active' : '' }}"><i class="bi bi-tags"></i> Product Categories</a>
                <a href="{{ route('product-sales.index') }}" class="{{ request()->routeIs('product-sales.*') ? 'active' : '' }}"><i class="bi bi-cart-check"></i> Product Sales</a>
                <a href="{{ route('reports.products') }}" class="{{ request()->routeIs('reports.products') ? 'active' : '' }}"><i class="bi bi-file-earmark-bar-graph"></i> Product Reports</a>
            @endif
        </div>
        <div class="p-3 border-top border-secondary">
            <div class="small text-secondary mb-2">{{ $u->name }}</div>
            <a href="{{ route('profile.edit') }}" class="btn btn-sm btn-outline-light w-100 mb-2 {{ request()->routeIs('profile.*') ? 'active' : '' }}"><i class="bi bi-person-circle"></i> Profile</a>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button class="btn btn-sm btn-outline-light w-100"><i class="bi bi-box-arrow-right"></i> Logout</button>
            </form>
        </div>
    </nav>

    <div class="content">
        <div class="bg-white border-bottom px-4 py-3 d-flex justify-content-between align-items-center">
            <h4 class="mb-0">@yield('title', 'Dashboard')</h4>
            <div>@yield('actions')</div>
        </div>
        <div class="p-4">
            @include('partials.flash')
            @yield('content')
        </div>
    </div>
</div>
@else
    @yield('content')
@endauth

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
