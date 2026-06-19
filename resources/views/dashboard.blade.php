@extends('layouts.app')
@section('title', 'Dashboard')

@section('content')
@php $u = auth()->user(); @endphp

{{-- ---------------- Quick Links ---------------- --}}
<div class="card p-3 mb-4">
    <div class="d-flex flex-wrap gap-2">
        @if($u->canManagePatients())
            <a href="{{ route('patients.create') }}" class="btn btn-sm btn-primary"><i class="bi bi-person-plus"></i> Add Patient</a>
            <a href="{{ route('reports.patients') }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-file-earmark-text"></i> Patient Reports</a>
        @endif
        @if($u->canManageInventory())
            <a href="{{ route('ingredients.create') }}" class="btn btn-sm btn-success"><i class="bi bi-box-seam"></i> Add Ingredient</a>
            <a href="{{ route('ingredient-usages.create') }}" class="btn btn-sm btn-warning"><i class="bi bi-box-arrow-up"></i> Use Ingredient</a>
            <a href="{{ route('products.create') }}" class="btn btn-sm btn-success"><i class="bi bi-bag-plus"></i> Add Product</a>
            <a href="{{ route('product-sales.create') }}" class="btn btn-sm btn-warning"><i class="bi bi-cart-check"></i> Product Sale</a>
            <a href="{{ route('reports.ingredients') }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-file-earmark-bar-graph"></i> Ingredient Reports</a>
            <a href="{{ route('reports.products') }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-file-earmark-bar-graph"></i> Product Reports</a>
        @endif
    </div>
</div>

{{-- ---------------- Patient Summary ---------------- --}}
@if($u->canManagePatients())
<h5 class="mb-3"><i class="bi bi-people text-primary"></i> Patient Summary</h5>
<div class="row g-3 mb-3">
    <div class="col-md-3"><div class="card p-3"><div class="text-muted small">Total Patients</div><h3 class="mb-0">{{ $totalPatients }}</h3></div></div>
    <div class="col-md-3"><div class="card p-3"><div class="text-muted small">Today's Patients</div><h3 class="mb-0">{{ $todaysPatients }}</h3></div></div>
    <div class="col-md-3"><div class="card p-3"><div class="text-muted small">Upcoming Follow-ups</div><h3 class="mb-0">{{ $upcomingFollowUps->count() }}</h3></div></div>
    <div class="col-md-3"><div class="card p-3"><div class="text-muted small">Recent Visits</div><h3 class="mb-0">{{ $latestVisits->count() }}</h3></div></div>
</div>
<div class="row g-3 mb-4">
    <div class="col-lg-4">
        <div class="card p-3 h-100">
            <h6 class="border-bottom pb-2">Recent Patients</h6>
            <table class="table table-sm mb-0">
                <thead><tr><th>Serial</th><th>Name</th><th>Last Visit</th></tr></thead>
                <tbody>
                @forelse($recentPatients as $p)
                    <tr><td><a href="{{ route('patients.show',$p) }}">{{ $p->serial_number }}</a></td><td>{{ $p->name }}</td><td>{{ optional($p->latestVisit)->visit_date?->format('d M Y') ?? '—' }}</td></tr>
                @empty
                    <tr><td colspan="3" class="text-muted">No patients yet.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card p-3 h-100">
            <h6 class="border-bottom pb-2">Follow-up Patients</h6>
            <table class="table table-sm mb-0">
                <thead><tr><th>Patient</th><th>Doctor</th><th>Date</th></tr></thead>
                <tbody>
                @forelse($upcomingFollowUps as $v)
                    <tr><td><a href="{{ route('patients.show',$v->patient_id) }}">{{ $v->patient->name ?? '—' }}</a></td><td>{{ $v->doctor_name ?? '—' }}</td><td>{{ $v->follow_up_date?->format('d M Y') }}</td></tr>
                @empty
                    <tr><td colspan="3" class="text-muted">No upcoming follow-ups.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card p-3 h-100">
            <h6 class="border-bottom pb-2">Latest Patient Records</h6>
            <table class="table table-sm mb-0">
                <thead><tr><th>Patient</th><th>Visit</th><th>Status</th></tr></thead>
                <tbody>
                @forelse($latestVisits as $v)
                    <tr><td><a href="{{ route('patients.show',$v->patient_id) }}">{{ $v->patient->name ?? '—' }}</a></td><td>{{ $v->visit_date->format('d M Y') }}</td><td><span class="badge bg-info text-dark">{{ $v->statusLabel() }}</span></td></tr>
                @empty
                    <tr><td colspan="3" class="text-muted">No visits yet.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endif

{{-- ---------------- Ingredient Inventory Summary ---------------- --}}
@if($u->canManageInventory())
<h5 class="mb-3"><i class="bi bi-box-seam text-success"></i> Ingredient Inventory Summary</h5>
<div class="row g-3 mb-3">
    <div class="col-md-3"><div class="card p-3"><div class="text-muted small">Total Ingredients</div><h3 class="mb-0">{{ $totalIngredients }}</h3></div></div>
    <div class="col-md-3"><div class="card p-3"><div class="text-muted small">Ingredient Categories</div><h3 class="mb-0">{{ $totalIngredientCategories }}</h3></div></div>
    <div class="col-md-3"><div class="card p-3 {{ $lowStockIngredients->count() ? 'border border-danger' : '' }}"><div class="text-muted small">Low Stock Ingredients</div><h3 class="mb-0">{{ $lowStockIngredients->count() }}</h3></div></div>
    <div class="col-md-3"><div class="card p-3"><div class="text-muted small">Recent Stock Entries</div><h3 class="mb-0">{{ $recentStockEntries->count() }}</h3></div></div>
</div>
<div class="row g-3 mb-4">
    <div class="col-lg-4">
        <div class="card p-3 h-100">
            <h6 class="border-bottom pb-2 text-danger">Low Stock Ingredients</h6>
            <ul class="list-group list-group-flush">
                @forelse($lowStockIngredients as $i)
                    <li class="list-group-item d-flex justify-content-between px-0"><a href="{{ route('ingredients.show',$i) }}">{{ $i->name }}</a><span class="badge bg-danger">{{ rtrim(rtrim($i->available_quantity,'0'),'.') }} {{ $i->unit }}</span></li>
                @empty
                    <li class="list-group-item px-0 text-muted">All ingredients sufficiently stocked.</li>
                @endforelse
            </ul>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card p-3 h-100">
            <h6 class="border-bottom pb-2 text-success">Recent Stock Entries</h6>
            <ul class="list-group list-group-flush">
                @forelse($recentStockEntries as $e)
                    <li class="list-group-item px-0 small"><span class="badge bg-success">IN</span> {{ $e->ingredient->name ?? '—' }} ({{ rtrim(rtrim($e->quantity,'0'),'.') }}) <span class="text-muted">from {{ $e->supplier_name }} — {{ $e->date->format('d M') }}</span></li>
                @empty
                    <li class="list-group-item px-0 text-muted">No recent stock entries.</li>
                @endforelse
            </ul>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card p-3 h-100">
            <h6 class="border-bottom pb-2 text-warning">Recent Ingredient Usage</h6>
            <ul class="list-group list-group-flush">
                @forelse($recentUsages as $us)
                    <li class="list-group-item px-0 small"><span class="badge bg-warning text-dark">OUT</span> {{ $us->ingredient->name ?? '—' }} ({{ rtrim(rtrim($us->quantity,'0'),'.') }}) <span class="text-muted">— {{ $us->usage_date->format('d M') }}</span></li>
                @empty
                    <li class="list-group-item px-0 text-muted">No recent usage.</li>
                @endforelse
            </ul>
        </div>
    </div>
</div>

{{-- ---------------- Product Summary ---------------- --}}
<h5 class="mb-3"><i class="bi bi-bag text-success"></i> Product Summary</h5>
<div class="row g-3 mb-3">
    <div class="col-md-3"><div class="card p-3"><div class="text-muted small">Total Products</div><h3 class="mb-0">{{ $totalProducts }}</h3></div></div>
    <div class="col-md-3"><div class="card p-3"><div class="text-muted small">Product Categories</div><h3 class="mb-0">{{ $totalProductCategories }}</h3></div></div>
    <div class="col-md-3"><div class="card p-3 {{ $lowStockProducts->count() ? 'border border-danger' : '' }}"><div class="text-muted small">Low Stock Products</div><h3 class="mb-0">{{ $lowStockProducts->count() }}</h3></div></div>
    <div class="col-md-3"><div class="card p-3"><div class="text-muted small">Recent Sales</div><h3 class="mb-0">{{ $recentSales->count() }}</h3></div></div>
</div>
<div class="row g-3 mb-4">
    <div class="col-lg-4">
        <div class="card p-3 h-100">
            <h6 class="border-bottom pb-2 text-danger">Low Stock Products</h6>
            <ul class="list-group list-group-flush">
                @forelse($lowStockProducts as $p)
                    <li class="list-group-item d-flex justify-content-between px-0"><a href="{{ route('products.show',$p) }}">{{ $p->name }}</a><span class="badge bg-danger">{{ $p->quantity_stock }}</span></li>
                @empty
                    <li class="list-group-item px-0 text-muted">All products sufficiently stocked.</li>
                @endforelse
            </ul>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card p-3 h-100">
            <h6 class="border-bottom pb-2">Available Product Stock</h6>
            <ul class="list-group list-group-flush">
                @forelse($topStockProducts as $p)
                    <li class="list-group-item d-flex justify-content-between px-0"><a href="{{ route('products.show',$p) }}">{{ $p->name }}</a><span class="badge bg-secondary">{{ $p->quantity_stock }}</span></li>
                @empty
                    <li class="list-group-item px-0 text-muted">No products yet.</li>
                @endforelse
            </ul>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card p-3 h-100">
            <h6 class="border-bottom pb-2 text-warning">Recent Product Sales</h6>
            <ul class="list-group list-group-flush">
                @forelse($recentSales as $s)
                    <li class="list-group-item px-0 small"><span class="badge bg-warning text-dark">SOLD</span> {{ $s->product->name ?? '—' }} ({{ $s->sale_quantity }}) <span class="text-muted">— {{ $s->sale_date->format('d M') }}</span></li>
                @empty
                    <li class="list-group-item px-0 text-muted">No recent sales.</li>
                @endforelse
            </ul>
        </div>
    </div>
</div>
@endif
@endsection
