@extends('layout.mainlayout')
@section('content')
<div class="page-wrapper">
<div class="content">

<div class="page-header mb-4">
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
        <div>
            <h4 class="fw-bold mb-1 d-flex align-items-center gap-2">
                <span class="d-flex align-items-center justify-content-center rounded-circle bg-primary text-white" style="width:36px;height:36px;">
                    <i class="ti ti-headset fs-5"></i>
                </span>
                {{ __('Support Tickets') }}
            </h4>
            <p class="text-muted mb-0 small">{{ __('Manage support requests from mess users.') }}</p>
        </div>
        @if($totalUnread > 0)
        <span class="badge bg-danger px-3 py-2" style="font-size:13px;">
            <i class="ti ti-bell me-1"></i>{{ $totalUnread }} {{ __('unread') }}
        </span>
        @endif
    </div>
</div>

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show d-flex align-items-center gap-2 mb-4" role="alert">
    <i class="ti ti-circle-check fs-5 flex-shrink-0"></i><div>{{ session('success') }}</div>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

{{-- Filters --}}
<div class="card border-0 shadow-sm mb-4" style="border-radius:12px;">
    <div class="card-body p-3">
        <form method="GET" class="d-flex align-items-center gap-3 flex-wrap">
            <div class="flex-grow-1" style="min-width:200px;">
                <input type="text" name="search" class="form-control" placeholder="{{ __('Search by mess, user, token, or subject...') }}" value="{{ request('search') }}">
            </div>
            <select name="status" class="form-select" style="width:auto;">
                <option value="">{{ __('All Status') }}</option>
                <option value="open"    {{ request('status')==='open'    ? 'selected' : '' }}>{{ __('Open') }}</option>
                <option value="closed"  {{ request('status')==='closed'  ? 'selected' : '' }}>{{ __('Closed') }}</option>
                <option value="expired" {{ request('status')==='expired' ? 'selected' : '' }}>{{ __('Expired') }}</option>
            </select>
            <button type="submit" class="btn btn-primary">{{ __('Filter') }}</button>
            @if(request('search') || request('status'))
            <a href="{{ route('admin.support.index') }}" class="btn btn-outline-secondary">{{ __('Clear') }}</a>
            @endif
        </form>
    </div>
</div>

{{-- Tickets Table --}}
<div class="card border-0 shadow-sm" style="border-radius:14px;overflow:hidden;">
    <div class="card-body p-0">
        @if($tokens->isEmpty())
        <div class="text-center text-muted py-5">
            <span class="d-inline-flex align-items-center justify-content-center rounded-circle bg-primary-subtle mb-3" style="width:64px;height:64px;">
                <i class="ti ti-ticket text-primary fs-2"></i>
            </span>
            <h6 class="fw-normal">{{ __('No support tickets yet') }}</h6>
            <p class="text-muted small mb-0">{{ __('Tickets submitted by mess users will appear here.') }}</p>
        </div>
        @else
        <div class="table-responsive">
            <table class="table table-hover mb-0 align-middle">
                <thead style="background:#f8f9fb;">
                    <tr>
                        <th class="px-4 py-3 fw-semibold small text-uppercase text-muted" style="font-size:11px;">{{ __('Token') }}</th>
                        <th class="py-3 fw-semibold small text-uppercase text-muted" style="font-size:11px;">{{ __('Subject') }}</th>
                        <th class="py-3 fw-semibold small text-uppercase text-muted" style="font-size:11px;">{{ __('From') }}</th>
                        <th class="py-3 fw-semibold small text-uppercase text-muted" style="font-size:11px;">{{ __('Role') }}</th>
                        <th class="py-3 fw-semibold small text-uppercase text-muted" style="font-size:11px;">{{ __('Status') }}</th>
                        <th class="py-3 fw-semibold small text-uppercase text-muted" style="font-size:11px;">{{ __('Messages') }}</th>
                        <th class="py-3 fw-semibold small text-uppercase text-muted" style="font-size:11px;">{{ __('Created') }}</th>
                        <th class="py-3 pe-4 fw-semibold small text-uppercase text-muted" style="font-size:11px;">{{ __('Action') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($tokens as $ticket)
                    @php
                        $isExpired = $ticket->isExpired();
                        $badgeClass = match($ticket->status) {
                            'open'    => $isExpired ? 'bg-warning-subtle text-warning border-warning-subtle' : 'bg-success-subtle text-success border-success-subtle',
                            'closed'  => 'bg-secondary-subtle text-secondary border-secondary-subtle',
                            'expired' => 'bg-warning-subtle text-warning border-warning-subtle',
                            default   => 'bg-secondary-subtle text-secondary border-secondary-subtle',
                        };
                        $statusLabel = $ticket->status === 'open' && !$isExpired ? __('Open') : ($ticket->status === 'closed' ? __('Closed') : __('Expired'));
                        $member = \App\Models\MessMember::where('mess_id',$ticket->mess_id)->where('user_id',$ticket->user_id)->first();
                        $role = $member ? ucfirst($member->role ?? 'member') : 'Owner';
                    @endphp
                    <tr>
                        <td class="px-4">
                            <code class="text-primary fw-semibold">{{ $ticket->token }}</code>
                        </td>
                        <td>
                            <div class="fw-semibold text-truncate" style="max-width:220px;">{{ $ticket->subject }}</div>
                            @if($ticket->unread_count > 0)
                            <span class="badge bg-danger mt-1" style="font-size:10px;">{{ $ticket->unread_count }} {{ __('unread') }}</span>
                            @endif
                        </td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <div class="d-flex align-items-center justify-content-center rounded-circle bg-primary text-white" style="width:30px;height:30px;font-size:12px;font-weight:700;flex-shrink:0;">
                                    {{ strtoupper(substr($ticket->user->name ?? '?', 0, 1)) }}
                                </div>
                                <div>
                                    <div class="fw-semibold small">{{ $ticket->user->name ?? '-' }}</div>
                                    <div class="text-muted" style="font-size:11px;">{{ $ticket->mess->name ?? '-' }}</div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <span class="badge bg-info-subtle text-info border border-info-subtle" style="font-size:11px;">{{ $role }}</span>
                        </td>
                        <td>
                            <span class="badge border {{ $badgeClass }}" style="font-size:11px;">{{ $statusLabel }}</span>
                        </td>
                        <td>
                            <span class="small text-muted">{{ $ticket->messages->count() }} {{ __('msgs') }}</span>
                        </td>
                        <td>
                            <span class="small text-muted">{{ $ticket->created_at->format('d M Y') }}<br>{{ $ticket->created_at->format('h:i A') }}</span>
                        </td>
                        <td class="pe-4">
                            <a href="{{ route('admin.support.show', $ticket) }}" class="btn btn-sm btn-outline-primary">
                                {{ __('Open') }}
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @if($tokens->hasPages())
        <div class="px-4 py-3 border-top">
            {{ $tokens->links() }}
        </div>
        @endif
        @endif
    </div>
</div>

</div>
</div>
@endsection
