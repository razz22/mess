<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>All Members Report · {{ $monthLabel }} · {{ $mess->name }}</title>
    <link rel="stylesheet" href="{{ asset('assets/css/tabler.min.css') }}">
    <style>
        :root { --brand: #206bc4; }
        body { background: #f4f6fa; font-family: 'Inter', sans-serif; font-size: 13px; }

        .print-toolbar { background:#fff; border-bottom:1px solid #dee2e6; padding:10px 24px; display:flex; align-items:center; gap:10px; position:sticky; top:0; z-index:100; }
        .print-toolbar .spacer { flex:1; }

        .report-wrap { max-width:1200px; margin:24px auto; padding:0 16px 48px; }

        /* Header */
        .rpt-header { background:linear-gradient(135deg,#206bc4 0%,#1a55a0 100%); color:#fff; padding:28px 36px 24px; border-radius:12px 12px 0 0; position:relative; overflow:hidden; }
        .rpt-header::after  { content:''; position:absolute; right:-40px; top:-40px; width:200px; height:200px; border-radius:50%; background:rgba(255,255,255,.06); }
        .rpt-header::before { content:''; position:absolute; right:60px; bottom:-60px; width:140px; height:140px; border-radius:50%; background:rgba(255,255,255,.04); }
        .mess-logo { width:52px; height:52px; border-radius:10px; object-fit:cover; border:3px solid rgba(255,255,255,.3); flex-shrink:0; }
        .mess-logo-init { width:52px; height:52px; border-radius:10px; background:rgba(255,255,255,.2); display:flex; align-items:center; justify-content:center; font-size:24px; font-weight:700; border:3px solid rgba(255,255,255,.3); flex-shrink:0; }
        .mess-title { font-size:20px; font-weight:700; }
        .mess-meta  { font-size:12px; opacity:.8; margin-top:2px; }
        .month-badge { background:rgba(255,255,255,.18); border:1px solid rgba(255,255,255,.3); border-radius:20px; padding:4px 14px; font-size:13px; font-weight:600; display:inline-block; margin-top:8px; }
        .hdr-stats { display:flex; gap:16px; margin-top:16px; flex-wrap:wrap; position:relative; z-index:1; }
        .hdr-stat { background:rgba(255,255,255,.12); border:1px solid rgba(255,255,255,.2); border-radius:8px; padding:8px 16px; text-align:center; }
        .hdr-stat strong { display:block; font-size:18px; font-weight:700; }
        .hdr-stat span   { font-size:10px; opacity:.8; text-transform:uppercase; letter-spacing:.4px; }

        /* Card shell */
        .rpt-card { background:#fff; border-radius:12px; box-shadow:0 2px 16px rgba(0,0,0,.07); overflow:hidden; }

        /* Expense breakdown table */
        .breakdown-table { width:100%; border-collapse:collapse; font-size:12px; }
        .breakdown-table th { background:#f0f4ff; color:#206bc4; font-weight:700; font-size:10px; text-transform:uppercase; letter-spacing:.4px; padding:10px 12px; border-bottom:2px solid #d0dcf5; white-space:nowrap; }
        .breakdown-table th.cat-head { background:#e8f4fd; color:#1a6fa8; border-left:1px dashed #b8d9f0; }
        .breakdown-table td { padding:9px 12px; border-bottom:1px solid #f1f3f5; vertical-align:middle; }
        .breakdown-table tbody tr:last-child td { border-bottom:none; }
        .breakdown-table tbody tr:hover td { background:#f8fbff; }
        .breakdown-table .text-end { text-align:right; }
        .breakdown-table .text-center { text-align:center; }
        .breakdown-table tfoot td { background:#f0f4ff; font-weight:700; color:#206bc4; padding:10px 12px; border-top:2px solid #d0dcf5; }
        .breakdown-table .cat-cell { text-align:right; border-left:1px dashed #e3eaf5; color:#333; }
        .breakdown-table .cat-excl { text-align:right; border-left:1px dashed #e3eaf5; color:#adb5bd; font-style:italic; font-size:10px; }

        .due-pill   { display:inline-block; background:#fff0f0; color:#d63939; border:1px solid #f5c6cb; border-radius:20px; padding:2px 9px; font-weight:700; font-size:11px; }
        .extra-pill { display:inline-block; background:#f0fff4; color:#2fb344; border:1px solid #c3e6cb; border-radius:20px; padding:2px 9px; font-weight:700; font-size:11px; }
        .avatar-sm  { width:28px; height:28px; border-radius:50%; object-fit:cover; }
        .avatar-init { width:28px; height:28px; border-radius:50%; background:var(--brand); color:#fff; display:inline-flex; align-items:center; justify-content:center; font-weight:700; font-size:11px; }

        .section-head { font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:.5px; color:var(--brand); padding:16px 24px 8px; border-top:1px solid #f1f3f5; }

        .rpt-footer { background:#f8f9fa; border-top:1px solid #dee2e6; padding:12px 24px; display:flex; justify-content:space-between; font-size:11px; color:#6c757d; }

        @media print {
            body { background:#fff !important; }
            .print-toolbar { display:none !important; }
            .report-wrap { max-width:100%; margin:0; padding:0; }
            .rpt-card { box-shadow:none !important; border-radius:0 !important; }
            .rpt-header { border-radius:0 !important; -webkit-print-color-adjust:exact; print-color-adjust:exact; }
            .rpt-header::before, .rpt-header::after { display:none; }
            .breakdown-table th, .breakdown-table tfoot td, .hdr-stats { -webkit-print-color-adjust:exact; print-color-adjust:exact; }
        }
    </style>
</head>
<body>

<div class="print-toolbar d-print-none">
    <a href="{{ route('mess.report.monthly', ['mess'=>$mess->id,'month'=>$month,'year'=>$year]) }}"
        class="btn btn-sm btn-outline-secondary"><i class="ti ti-arrow-left me-1"></i>{{ __('Back') }}</a>
    <span class="fw-semibold text-muted" style="font-size:13px;">All Members · {{ $monthLabel }}</span>
    <span class="spacer"></span>
    <button onclick="window.print()" class="btn btn-sm btn-outline-primary"><i class="ti ti-printer me-1"></i>Print / PDF</button>
    <button onclick="downloadExcel()" class="btn btn-sm btn-success"><i class="ti ti-file-spreadsheet me-1"></i>{{ __('Excel') }}</button>
</div>

@php
    // Gather all distinct non-market expense categories for column headers
    $allExpensesColl = \App\Models\Expense::where('mess_id', $mess->id)
        ->whereMonth('expense_date', $month)->whereYear('expense_date', $year)
        ->where('is_market_expense', false)
        ->with('category')->get();

    $catColumns = $allExpensesColl->groupBy('category_id')
        ->map(fn($g) => ['id' => $g->first()->category_id, 'name' => $g->first()->category?->name ?? 'Uncategorized', 'total' => $g->sum('amount')])
        ->values();

    $allMemberIds = $members->pluck('user_id');
    $globalExclusions = \App\Models\MemberMonthlySummary::where(['mess_id'=>$mess->id,'month'=>$month,'year'=>$year])
        ->where('exclude_from_shared', true)->pluck('user_id')->toArray();
    $allCatExcl = \App\Models\MemberExpenseExclusion::where(['mess_id'=>$mess->id,'month'=>$month,'year'=>$year])
        ->get()->groupBy('user_id')->map(fn($g)=>$g->pluck('category_id')->toArray());

    // Per-category per-head
    $catPerHead = [];
    foreach($catColumns as $cat) {
        $paying = $allMemberIds->filter(function($id) use($globalExclusions,$allCatExcl,$cat) {
            if(in_array($id,$globalExclusions)) return false;
            return !in_array($cat['id'], $allCatExcl[$id] ?? []);
        })->count();
        $catPerHead[$cat['id']] = $paying > 0 ? $cat['total']/$paying : 0;
    }

    $totalMarketAll = $marketExpenses->sum('amount');
    $totalPayableAll = $summaries->sum('total_payable');
    $totalDepositAll = $summaries->sum('total_deposit');
    $totalDueAll   = $summaries->filter(fn($s)=>$s->due_amount>0)->sum('due_amount');
    $totalExtraAll = $summaries->filter(fn($s)=>$s->due_amount<0)->sum(fn($s)=>abs($s->due_amount));
@endphp

<div class="report-wrap">
<div class="rpt-card">

    {{-- Header --}}
    <div class="rpt-header">
        <div style="display:flex;align-items:flex-start;gap:16px;position:relative;z-index:1;">
            @if($mess->avatar)
            <img src="{{ asset('storage/'.$mess->avatar) }}" class="mess-logo" alt="">
            @else
            <div class="mess-logo-init">{{ strtoupper(substr($mess->name,0,1)) }}</div>
            @endif
            <div>
                <div class="mess-title">{{ $mess->name }}</div>
                @if($mess->address)<div class="mess-meta"><i class="ti ti-map-pin" style="font-size:11px;"></i> {{ $mess->address }}</div>@endif
                @if($mess->description)<div class="mess-meta" style="opacity:.65;">{{ $mess->description }}</div>@endif
                <div class="month-badge">📅 {{ $monthLabel }} — All Members Expense Report</div>
            </div>
        </div>
        <div class="hdr-stats">
            <div class="hdr-stat"><strong>{{ $members->count() }}</strong><span>{{ __('Members') }}</span></div>
            <div class="hdr-stat"><strong>৳{{ number_format($totalMarketAll,2) }}</strong><span>{{ __('Market Total') }}</span></div>
            <div class="hdr-stat"><strong>৳{{ number_format($catColumns->sum('total'),2) }}</strong><span>{{ __('Shared Expenses') }}</span></div>
            <div class="hdr-stat"><strong>৳{{ number_format($totalPayableAll,2) }}</strong><span>{{ __('Total Payable') }}</span></div>
            <div class="hdr-stat"><strong>৳{{ number_format($totalDepositAll,2) }}</strong><span>{{ __('Total Deposited') }}</span></div>
            <div class="hdr-stat" style="border-color:rgba(255,100,100,.4);background:rgba(255,80,80,.15);"><strong>৳{{ number_format($totalDueAll,2) }}</strong><span>{{ __('Total Due') }}</span></div>
            <div class="hdr-stat" style="border-color:rgba(80,220,120,.4);background:rgba(80,220,120,.12);"><strong>৳{{ number_format($totalExtraAll,2) }}</strong><span>{{ __('Total Extra') }}</span></div>
        </div>
    </div>

    {{-- Expense Breakdown Table --}}
    <div class="section-head">Expense Breakdown — All Members</div>
    <div style="overflow-x:auto;padding:0 0 24px;">
        <table class="breakdown-table" id="breakdown-table">
            <thead>
                <tr>
                    <th rowspan="2" style="width:180px;">#&nbsp; Member</th>
                    <th rowspan="2" class="text-center">Meal<br>Days</th>
                    <th rowspan="2" class="text-end">Meal<br>Cost</th>
                    @foreach($catColumns as $cat)
                    <th class="text-end cat-head">{{ $cat['name'] }}<br><span style="font-weight:400;opacity:.7;">÷ head</span></th>
                    @endforeach
                    <th rowspan="2" class="text-end">Total<br>Payable</th>
                    <th rowspan="2" class="text-end">{{ __('Deposited') }}</th>
                    <th rowspan="2" class="text-center">Due / Extra</th>
                    <th rowspan="2" class="text-center">Status</th>
                </tr>
                <tr>
                    {{-- Category totals sub-header --}}
                    @foreach($catColumns as $cat)
                    <th class="text-end cat-head" style="font-weight:400;color:#6c757d;font-size:9px;">
                        Total: ৳{{ number_format($cat['total'],0) }}<br>
                        /head: ৳{{ number_format($catPerHead[$cat['id']]??0,2) }}
                    </th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
            @foreach($members as $i => $m)
            @php
                $uid  = $m->user_id;
                $sm   = $summaries[$uid] ?? null;
                $due  = $sm ? $sm->due_amount : 0;
                $isGlobalExcl = in_array($uid, $globalExclusions);
                $memberCatExcl = $allCatExcl[$uid] ?? [];
            @endphp
            <tr>
                <td>
                    <div style="display:flex;align-items:center;gap:8px;">
                        <span style="color:#adb5bd;font-size:10px;width:16px;">{{ $i+1 }}</span>
                        @if($m->user->avatar)
                        <img src="{{ asset('storage/'.$m->user->avatar) }}" class="avatar-sm" alt="">
                        @else
                        <div class="avatar-init">{{ strtoupper(substr($m->user->name,0,1)) }}</div>
                        @endif
                        <div>
                            <div style="font-weight:600;">{{ $m->user->name }}</div>
                            <span class="badge bg-{{ $m->role==='owner'?'danger':($m->role==='manager'?'warning':'secondary') }}" style="font-size:9px;">{{ ucfirst($m->role) }}</span>
                        </div>
                    </div>
                </td>
                <td class="text-center" style="font-weight:600;">{{ $sm ? number_format($sm->total_meal_days,1) : '—' }}</td>
                <td class="text-end">{{ $sm ? '৳'.number_format($sm->meal_cost,2) : '—' }}</td>
                @foreach($catColumns as $cat)
                @php $isExcl = $isGlobalExcl || in_array($cat['id'], $memberCatExcl); @endphp
                <td class="{{ $isExcl ? 'cat-excl' : 'cat-cell' }}">
                    @if($isExcl) excl. @else ৳{{ number_format($catPerHead[$cat['id']]??0,2) }} @endif
                </td>
                @endforeach
                <td class="text-end" style="font-weight:700;">{{ $sm ? '৳'.number_format($sm->total_payable,2) : '—' }}</td>
                <td class="text-end" style="color:#2fb344;font-weight:600;">{{ $sm ? '৳'.number_format($sm->total_deposit,2) : '—' }}</td>
                <td class="text-center">
                    @if($sm)
                    <span class="{{ $due>0?'due-pill':'extra-pill' }}">{{ $due>0?'-':'+' }}৳{{ number_format(abs($due),2) }}</span>
                    @else <span style="color:#adb5bd;">—</span> @endif
                </td>
                <td class="text-center">
                    @if($sm)
                        @if($sm->status==='settled') <span class="badge bg-success">Settled</span>
                        @elseif($sm->status==='paid_out') <span class="badge bg-secondary">Paid Out</span>
                        @elseif($sm->status==='carried_forward') <span class="badge bg-info text-dark">Carried</span>
                        @elseif($due>0) <span class="badge bg-danger">Due</span>
                        @else <span class="badge bg-warning text-dark">Extra</span>
                        @endif
                    @else <span style="color:#adb5bd;font-size:10px;">No data</span> @endif
                </td>
            </tr>
            @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="2">Total</td>
                    <td class="text-end">৳{{ number_format($summaries->sum('meal_cost'),2) }}</td>
                    @foreach($catColumns as $cat)
                    <td class="text-end">৳{{ number_format($cat['total'],2) }}</td>
                    @endforeach
                    <td class="text-end">৳{{ number_format($totalPayableAll,2) }}</td>
                    <td class="text-end">৳{{ number_format($totalDepositAll,2) }}</td>
                    <td class="text-center">Due: ৳{{ number_format($totalDueAll,2) }}</td>
                    <td class="text-center">Extra: ৳{{ number_format($totalExtraAll,2) }}</td>
                </tr>
            </tfoot>
        </table>
    </div>

    <div class="rpt-footer">
        <span>Generated: {{ now()->format('d M Y, h:i A') }}</span>
        <span style="font-weight:600;">{{ $mess->name }} · {{ $monthLabel }}</span>
        <span>{{ __('Powered by Thaka Khawa') }}</span>
    </div>

</div>{{-- /rpt-card --}}
</div>{{-- /report-wrap --}}

<script src="{{ asset('assets/js/tabler.min.js') }}"></script>
<script>
// Excel download
function downloadExcel() {
    var wb = '<html><head><meta charset="UTF-8"><style>table{border-collapse:collapse;}td,th{border:1px solid #ccc;padding:6px 10px;font-size:12px;}th{background:#e8f0fe;font-weight:bold;}</style></head><body>';
    wb += '<h2>{{ addslashes($mess->name) }}</h2>';
    wb += '<p>{{ addslashes($mess->address ?? "") }}</p>';
    wb += '<p><strong>Period:</strong> {{ $monthLabel }}&nbsp;&nbsp;<strong>Generated:</strong> ' + new Date().toLocaleString() + '</p><br>';
    var tbl = document.getElementById('breakdown-table');
    if (tbl) wb += tbl.outerHTML;
    wb += '</body></html>';
    var blob = new Blob([wb], { type: 'application/vnd.ms-excel;charset=utf-8' });
    var url  = URL.createObjectURL(blob);
    var a    = document.createElement('a');
    a.href   = url;
    a.download = '{{ Str::slug($mess->name) }}-all-members-{{ $month }}-{{ $year }}.xls';
    a.click();
    URL.revokeObjectURL(url);
}
</script>
</body>
</html>
