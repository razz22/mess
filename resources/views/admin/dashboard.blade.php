<?php $page = "admin-dashboard" ?>
@extends('layout.mainlayout')
@section('content')
<div class="page-wrapper">
    <div class="content">

        <div class="page-header">
            <div class="page-title">
                <h4 class="fw-bold"><i class="ti ti-shield-check me-2 text-danger"></i>Super Admin Dashboard</h4>
                <h6 class="text-muted">System-wide overview &mdash; {{ now()->format('d F Y') }}</h6>
            </div>
        </div>

        @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show py-2">{{ session('success') }}<button class="btn-close" data-bs-dismiss="alert"></button></div>
        @endif
        @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show py-2">{{ session('error') }}<button class="btn-close" data-bs-dismiss="alert"></button></div>
        @endif

        {{-- Stat Cards --}}
        <div class="row g-3 mb-4">
            <div class="col-6 col-md-3">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body d-flex align-items-center gap-3">
                        <div class="rounded-3 p-2 bg-primary bg-opacity-10 text-primary fs-2"><i class="ti ti-users"></i></div>
                        <div>
                            <div class="small text-muted">Total Users</div>
                            <div class="fw-bold fs-4">{{ number_format($stats['total_users']) }}</div>
                            <div class="small text-success">{{ $stats['active_users'] }} active</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body d-flex align-items-center gap-3">
                        <div class="rounded-3 p-2 bg-success bg-opacity-10 text-success fs-2"><i class="ti ti-building-community"></i></div>
                        <div>
                            <div class="small text-muted">Total Messes</div>
                            <div class="fw-bold fs-4">{{ number_format($stats['total_messes']) }}</div>
                            <div class="small text-success">+{{ $stats['new_messes_this_month'] }} this month</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body d-flex align-items-center gap-3">
                        <div class="rounded-3 p-2 bg-info bg-opacity-10 text-info fs-2"><i class="ti ti-id-badge"></i></div>
                        <div>
                            <div class="small text-muted">Active Members</div>
                            <div class="fw-bold fs-4">{{ number_format($stats['total_members']) }}</div>
                            <div class="small text-muted">Across all messes</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body d-flex align-items-center gap-3">
                        <div class="rounded-3 p-2 bg-warning bg-opacity-10 text-warning fs-2"><i class="ti ti-cash"></i></div>
                        <div>
                            <div class="small text-muted">Total Rent Collected</div>
                            <div class="fw-bold fs-4">৳{{ number_format($stats['total_rent_collected'], 0) }}</div>
                            <div class="small text-muted">Advance: ৳{{ number_format($stats['total_advance_held'], 0) }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Charts Row --}}
        <div class="row g-3 mb-4">
            <div class="col-md-7">
                <div class="card h-100 shadow-sm">
                    <div class="card-header"><h6 class="mb-0">User Registrations — Last 6 Months</h6></div>
                    <div class="card-body">
                        <canvas id="userGrowthChart" height="120"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-md-5">
                <div class="card h-100 shadow-sm">
                    <div class="card-header"><h6 class="mb-0">Quick Stats</h6></div>
                    <div class="card-body">
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item d-flex justify-content-between px-0">
                                <span class="text-muted">New users this month</span>
                                <span class="badge bg-primary">{{ $stats['new_users_this_month'] }}</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between px-0">
                                <span class="text-muted">New messes this month</span>
                                <span class="badge bg-success">{{ $stats['new_messes_this_month'] }}</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between px-0">
                                <span class="text-muted">Inactive / Banned users</span>
                                <span class="badge bg-danger">{{ $stats['total_users'] - $stats['active_users'] }}</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between px-0">
                                <span class="text-muted">Advance deposits held</span>
                                <span class="badge bg-warning text-dark">৳{{ number_format($stats['total_advance_held'], 0) }}</span>
                            </li>
                        </ul>
                        <div class="mt-3 d-flex gap-2">
                            <a href="{{ route('admin.users') }}" class="btn btn-sm btn-outline-primary w-50">Manage Users</a>
                            <a href="{{ route('admin.messes') }}" class="btn btn-sm btn-outline-success w-50">Manage Messes</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Recent Users + Recent Messes --}}
        <div class="row g-3">
            <div class="col-md-6">
                <div class="card shadow-sm">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h6 class="mb-0">Recent Users</h6>
                        <a href="{{ route('admin.users') }}" class="btn btn-xs btn-outline-primary">View All</a>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0 small">
                            <thead class="table-light">
                                <tr><th>User</th><th>Messes</th><th>Status</th><th>Joined</th></tr>
                            </thead>
                            <tbody>
                                @foreach($recentUsers as $u)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            @if($u->avatar)
                                            <img src="{{ asset('storage/'.$u->avatar) }}" class="rounded-circle" style="width:28px;height:28px;object-fit:cover;">
                                            @else
                                            <span class="rounded-circle bg-secondary text-white d-inline-flex align-items-center justify-content-center fw-semibold" style="width:28px;height:28px;font-size:11px;">{{ strtoupper(substr($u->name,0,1)) }}</span>
                                            @endif
                                            <div>
                                                <div class="fw-semibold">{{ $u->name }}</div>
                                                <div class="text-muted" style="font-size:11px;">{{ $u->email }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>{{ $u->mess_members_count }}</td>
                                    <td><span class="badge {{ $u->is_active ? 'bg-success' : 'bg-danger' }}">{{ $u->is_active ? 'Active' : 'Banned' }}</span></td>
                                    <td class="text-muted">{{ $u->created_at->format('d M Y') }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card shadow-sm">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h6 class="mb-0">Recent Messes</h6>
                        <a href="{{ route('admin.messes') }}" class="btn btn-xs btn-outline-success">View All</a>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0 small">
                            <thead class="table-light">
                                <tr><th>Mess</th><th>Owner</th><th>Members</th><th>Created</th></tr>
                            </thead>
                            <tbody>
                                @foreach($recentMesses as $m)
                                <tr>
                                    <td class="fw-semibold">
                                        <a href="{{ route('admin.mess.show', $m->id) }}" class="text-decoration-none">{{ $m->name }}</a>
                                    </td>
                                    <td class="text-muted">{{ $m->owner->name }}</td>
                                    <td>{{ $m->active_members_count }}</td>
                                    <td class="text-muted">{{ $m->created_at->format('d M Y') }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    var ctx = document.getElementById('userGrowthChart');
    if (!ctx) return;
    var labels = @json(collect($userGrowth)->pluck('month'));
    var data   = @json(collect($userGrowth)->pluck('count'));
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: 'New Users',
                data: data,
                backgroundColor: 'rgba(99,102,241,0.7)',
                borderRadius: 4,
            }]
        },
        options: {
            responsive: true,
            plugins: { legend: { display: false } },
            scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } }
        }
    });
});
</script>
@endsection
