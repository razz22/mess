<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $targetMember->user->name }} — {{ $monthLabel }} Report · {{ $mess->name }}</title>
    <link rel="stylesheet" href="{{ asset('assets/css/tabler.min.css') }}">
    <style>
        :root { --brand: #206bc4; }
        body { background: #f4f6fa; font-family: 'Inter', sans-serif; }

        /* ── Screen toolbar ── */
        .print-toolbar { background: #fff; border-bottom: 1px solid #dee2e6; padding: 10px 24px; display: flex; align-items: center; gap: 10px; position: sticky; top: 0; z-index: 100; }
        .print-toolbar .spacer { flex: 1; }

        /* ── Report card ── */
        .report-wrap { max-width: 860px; margin: 28px auto; padding: 0 16px 40px; }
        .report-card { background: #fff; border-radius: 12px; box-shadow: 0 2px 16px rgba(0,0,0,.08); overflow: hidden; }

        /* ── Header ── */
        .rpt-header { background: linear-gradient(135deg, var(--brand) 0%, #1a55a0 100%); color: #fff; padding: 32px 36px 24px; position: relative; overflow: hidden; }
        .rpt-header::after { content: ''; position: absolute; right: -40px; top: -40px; width: 200px; height: 200px; border-radius: 50%; background: rgba(255,255,255,.07); }
        .rpt-header::before { content: ''; position: absolute; right: 60px; bottom: -60px; width: 140px; height: 140px; border-radius: 50%; background: rgba(255,255,255,.05); }
        .mess-logo { width: 56px; height: 56px; border-radius: 10px; object-fit: cover; border: 3px solid rgba(255,255,255,.3); }
        .mess-logo-placeholder { width: 56px; height: 56px; border-radius: 10px; background: rgba(255,255,255,.2); display: flex; align-items: center; justify-content: center; font-size: 26px; font-weight: 700; color: #fff; border: 3px solid rgba(255,255,255,.3); flex-shrink: 0; }
        .mess-title { font-size: 22px; font-weight: 700; letter-spacing: -.3px; }
        .mess-meta { font-size: 12px; opacity: .8; margin-top: 2px; }
        .rpt-month-badge { background: rgba(255,255,255,.18); border: 1px solid rgba(255,255,255,.3); border-radius: 20px; padding: 4px 14px; font-size: 13px; font-weight: 600; display: inline-block; margin-top: 10px; }
        .member-pill { background: rgba(255,255,255,.15); border: 1px solid rgba(255,255,255,.25); border-radius: 8px; padding: 8px 14px; margin-top: 14px; display: inline-flex; align-items: center; gap: 10px; }
        .member-avatar { width: 38px; height: 38px; border-radius: 50%; object-fit: cover; border: 2px solid rgba(255,255,255,.4); }
        .member-avatar-init { width: 38px; height: 38px; border-radius: 50%; background: rgba(255,255,255,.25); display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 16px; color: #fff; border: 2px solid rgba(255,255,255,.4); }
        .member-name { font-weight: 600; font-size: 15px; }
        .member-role { font-size: 11px; opacity: .75; }

        /* ── Summary strip ── */
        .summary-strip { display: grid; grid-template-columns: repeat(4, 1fr); border-bottom: 1px solid #e9ecef; }
        .sum-box { padding: 16px 20px; border-right: 1px solid #e9ecef; text-align: center; }
        .sum-box:last-child { border-right: none; }
        .sum-label { font-size: 11px; color: #6c757d; text-transform: uppercase; letter-spacing: .5px; font-weight: 600; }
        .sum-value { font-size: 20px; font-weight: 700; margin-top: 4px; }
        .sum-value.danger { color: #d63939; }
        .sum-value.success { color: #2fb344; }
        .sum-value.primary { color: var(--brand); }
        .sum-value.warning { color: #f76707; }

        /* ── Section ── */
        .rpt-section { padding: 24px 36px; border-bottom: 1px solid #f1f3f5; }
        .rpt-section:last-child { border-bottom: none; }
        .section-title { font-size: 13px; font-weight: 700; text-transform: uppercase; letter-spacing: .6px; color: var(--brand); margin-bottom: 14px; display: flex; align-items: center; gap-6px; }
        .section-title i { font-size: 16px; }

        /* ── Tables ── */
        .rpt-table { width: 100%; border-collapse: collapse; font-size: 13px; }
        .rpt-table th { background: #f8f9fa; font-weight: 600; font-size: 11px; text-transform: uppercase; letter-spacing: .4px; color: #6c757d; padding: 8px 12px; text-align: left; border-bottom: 2px solid #dee2e6; }
        .rpt-table td { padding: 8px 12px; border-bottom: 1px solid #f1f3f5; vertical-align: top; }
        .rpt-table tr:last-child td { border-bottom: none; }
        .rpt-table .text-end { text-align: right; }
        .rpt-table .text-center { text-align: center; }
        .sub-row td { background: #fafbfc; font-size: 12px; color: #555; }
        .sub-row td:first-child { padding-left: 28px; }
        .cat-total td { font-weight: 600; background: #f0f4ff; color: var(--brand); }

        /* ── Meal grid ── */
        .meal-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(54px, 1fr)); gap: 6px; }
        .meal-day { border: 1px solid #dee2e6; border-radius: 6px; padding: 4px 2px; text-align: center; font-size: 11px; }
        .meal-day .day-num { font-weight: 700; font-size: 13px; color: #333; }
        .meal-day .day-off { color: #adb5bd; font-size: 10px; }
        .meal-day .meal-tag { display: inline-block; border-radius: 3px; padding: 1px 4px; font-size: 9px; font-weight: 600; margin-top: 2px; }
        .meal-day.has-meal { border-color: #c3e6cb; background: #f6fff8; }
        .meal-day.all-off { background: #fff5f5; border-color: #f5c6cb; }

        /* ── Deposit table ── */
        .deposit-tag { display: inline-block; background: #d1f5d3; color: #155724; font-size: 10px; padding: 2px 8px; border-radius: 20px; font-weight: 600; }

        /* ── Balance box ── */
        .balance-box { border-radius: 10px; padding: 18px 24px; margin-top: 6px; }
        .balance-box.due { background: #fff5f5; border: 1px solid #f5c6cb; }
        .balance-box.extra { background: #f6fff8; border: 1px solid #c3e6cb; }
        .balance-row { display: flex; justify-content: space-between; padding: 5px 0; font-size: 13px; border-bottom: 1px dashed #e9ecef; }
        .balance-row:last-child { border-bottom: none; font-weight: 700; font-size: 15px; }

        /* ── Footer ── */
        .rpt-footer { background: #f8f9fa; border-top: 1px solid #dee2e6; padding: 14px 36px; font-size: 11px; color: #6c757d; display: flex; justify-content: space-between; align-items: center; }

        /* ── Print ── */
        @media print {
            body { background: #fff !important; }
            .print-toolbar { display: none !important; }
            .report-wrap { max-width: 100%; margin: 0; padding: 0; }
            .report-card { box-shadow: none !important; border-radius: 0 !important; }
            .rpt-header { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
            .summary-strip { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
            .rpt-table th { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
            .cat-total td { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
            .meal-day.has-meal { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
            .meal-day.all-off { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
            .balance-box { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
        }
    </style>
</head>
<body>

{{-- Screen toolbar --}}
<div class="print-toolbar d-print-none">
    <a href="{{ route('mess.report.monthly', ['mess' => $mess->id, 'month' => $month, 'year' => $year]) }}"
        class="btn btn-sm btn-outline-secondary">
        <i class="ti ti-arrow-left me-1"></i>Back
    </a>
    <span class="fw-semibold text-muted" style="font-size:13px;">
        {{ $targetMember->user->name }} — {{ $monthLabel }}
    </span>
    <span class="spacer"></span>
    <button onclick="window.print()" class="btn btn-sm btn-outline-primary">
        <i class="ti ti-printer me-1"></i>Print / Save PDF
    </button>
    <button onclick="downloadExcel()" class="btn btn-sm btn-success">
        <i class="ti ti-file-spreadsheet me-1"></i>Download Excel
    </button>
</div>

<div class="report-wrap">
<div class="report-card">

    {{-- ── Header ── --}}
    <div class="rpt-header">
        <div style="display:flex;align-items:flex-start;gap:16px;position:relative;z-index:1;">
            @if($mess->avatar)
            <img src="{{ asset('storage/'.$mess->avatar) }}" class="mess-logo" alt="">
            @else
            <div class="mess-logo-placeholder">{{ strtoupper(substr($mess->name,0,1)) }}</div>
            @endif
            <div>
                <div class="mess-title">{{ $mess->name }}</div>
                @if($mess->address)<div class="mess-meta"><i class="ti ti-map-pin" style="font-size:11px;"></i> {{ $mess->address }}</div>@endif
                <div class="rpt-month-badge">📅 {{ $monthLabel }} — Monthly Statement</div>
            </div>
        </div>
        <div class="member-pill" style="position:relative;z-index:1;">
            @if($targetMember->user->avatar)
            <img src="{{ asset('storage/'.$targetMember->user->avatar) }}" class="member-avatar" alt="">
            @else
            <div class="member-avatar-init">{{ strtoupper(substr($targetMember->user->name,0,1)) }}</div>
            @endif
            <div>
                <div class="member-name">{{ $targetMember->user->name }}</div>
                <div class="member-role">{{ ucfirst($targetMember->role) }} · Joined {{ $targetMember->created_at->format('d M Y') }}</div>
            </div>
        </div>
    </div>

    {{-- ── Summary strip ── --}}
    @php
        $sm = $summary;
        $dueAmt = $sm ? $sm->due_amount : 0;
    @endphp
    <div class="summary-strip">
        <div class="sum-box">
            <div class="sum-label">Meal Days</div>
            <div class="sum-value primary">{{ $sm ? number_format($sm->total_meal_days,1) : '—' }}</div>
        </div>
        <div class="sum-box">
            <div class="sum-label">Total Payable</div>
            <div class="sum-value warning">৳{{ $sm ? number_format($sm->total_payable,2) : '0.00' }}</div>
        </div>
        <div class="sum-box">
            <div class="sum-label">Total Deposit</div>
            <div class="sum-value success">৳{{ $sm ? number_format($sm->total_deposit,2) : '0.00' }}</div>
        </div>
        <div class="sum-box">
            <div class="sum-label">{{ $dueAmt > 0 ? 'Due Amount' : 'Extra Balance' }}</div>
            <div class="sum-value {{ $dueAmt > 0 ? 'danger' : 'success' }}">
                {{ $dueAmt > 0 ? '-' : '+' }}৳{{ number_format(abs($dueAmt),2) }}
            </div>
        </div>
    </div>

    {{-- ── Meal Attendance ── --}}
    <div class="rpt-section">
        <div class="section-title"><i class="ti ti-calendar-stats"></i>&nbsp; Meal Attendance</div>
        @if(empty($attendanceByDate))
        <p class="text-muted small mb-0">No meal attendance recorded this month.</p>
        @else
        <table class="rpt-table" id="meal-table">
            <thead>
                <tr>
                    <th>{{ __('Date') }}</th>
                    @foreach($mealTypes as $mt)
                    <th class="text-center">{{ $mt->name }}</th>
                    @endforeach
                    <th class="text-center">Total</th>
                </tr>
            </thead>
            <tbody>
                @php $grandMealFull = 0; $grandMealHalf = 0; @endphp
                @foreach($attendanceByDate as $dateStr => $typeMap)
                @php
                    $d = \Carbon\Carbon::parse($dateStr);
                    $rowFull = 0; $rowHalf = 0;
                    foreach($typeMap as $att) { $rowFull += (int)$att->full_qty; $rowHalf += (int)$att->half_qty; }
                    $grandMealFull += $rowFull; $grandMealHalf += $rowHalf;
                @endphp
                <tr>
                    <td>
                        <span style="font-weight:600;">{{ $d->format('d') }}</span>
                        <span style="color:#6c757d;font-size:11px;"> {{ $d->format('D, M Y') }}</span>
                    </td>
                    @foreach($mealTypes as $mt)
                    @php $att = $typeMap[$mt->name] ?? null; @endphp
                    <td class="text-center">
                        @if($att && ($att->full_qty > 0 || $att->half_qty > 0))
                            @if($att->full_qty > 0)<span class="meal-tag" style="background:#d1f5d3;color:#155724;">{{ $att->full_qty }}F</span>@endif
                            @if($att->half_qty > 0)<span class="meal-tag" style="background:#fff3cd;color:#856404;">{{ $att->half_qty }}H</span>@endif
                        @elseif($att)
                            <span style="color:#adb5bd;font-size:11px;">Off</span>
                        @else
                            <span style="color:#dee2e6;">—</span>
                        @endif
                    </td>
                    @endforeach
                    <td class="text-center" style="font-weight:600;font-size:12px;">
                        @if($rowFull > 0)<span class="meal-tag" style="background:#cfe2ff;color:#0a58ca;">{{ $rowFull }}F</span>@endif
                        @if($rowHalf > 0)<span class="meal-tag" style="background:#fff3cd;color:#856404;">{{ $rowHalf }}H</span>@endif
                    </td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr style="background:#f0f4ff;font-weight:700;">
                    <td>Total</td>
                    @foreach($mealTypes as $mt)
                    @php
                        $mF = collect($attendanceByDate)->sum(fn($tm) => isset($tm[$mt->name]) ? (int)$tm[$mt->name]->full_qty : 0);
                        $mH = collect($attendanceByDate)->sum(fn($tm) => isset($tm[$mt->name]) ? (int)$tm[$mt->name]->half_qty : 0);
                    @endphp
                    <td class="text-center" style="font-size:12px;">
                        @if($mF > 0)<span class="meal-tag" style="background:#d1f5d3;color:#155724;">{{ $mF }}F</span>@endif
                        @if($mH > 0)<span class="meal-tag" style="background:#fff3cd;color:#856404;">{{ $mH }}H</span>@endif
                    </td>
                    @endforeach
                    <td class="text-center" style="font-size:13px;">
                        @if($grandMealFull > 0)<span class="meal-tag" style="background:#cfe2ff;color:#0a58ca;">{{ $grandMealFull }}F</span>@endif
                        @if($grandMealHalf > 0)<span class="meal-tag" style="background:#fff3cd;color:#856404;">{{ $grandMealHalf }}H</span>@endif
                    </td>
                </tr>
            </tfoot>
        </table>
        @endif
    </div>

    {{-- ── Meal Cost (Market) ── --}}
    <div class="rpt-section">
        <div class="section-title"><i class="ti ti-shopping-cart"></i>&nbsp; Meal Cost (Market Expenses)</div>
        @if($marketExpenses->isEmpty())
        <p class="text-muted small mb-0">No market expenses recorded this month.</p>
        @else
        <table class="rpt-table" id="market-table">
            <thead>
                <tr>
                    <th>{{ __('Date') }}</th>
                    <th>Title</th>
                    <th>Paid By</th>
                    <th class="text-end">Amount</th>
                </tr>
            </thead>
            <tbody>
                @foreach($marketExpenses as $exp)
                <tr>
                    <td style="white-space:nowrap;color:#6c757d;font-size:12px;">{{ $exp->expense_date->format('d M') }}</td>
                    <td>{{ $exp->title }}</td>
                    <td style="font-size:12px;color:#6c757d;">{{ $exp->member?->name ?? '—' }}</td>
                    <td class="text-end" style="font-weight:600;">৳{{ number_format($exp->amount,2) }}</td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr style="background:#f0f4ff;font-weight:700;">
                    <td colspan="3">Total Market Expenses</td>
                    <td class="text-end">৳{{ number_format($marketExpenses->sum('amount'),2) }}</td>
                </tr>
                <tr style="font-size:12px;color:#555;">
                    <td colspan="3">Your Meal Cost ({{ $sm ? number_format($sm->total_meal_days,1) : 0 }} meal days)</td>
                    <td class="text-end" style="font-weight:600;color:var(--brand);">৳{{ $sm ? number_format($sm->meal_cost,2) : '0.00' }}</td>
                </tr>
            </tfoot>
        </table>
        @endif
    </div>

    {{-- ── Shared Expenses ── --}}
    <div class="rpt-section">
        <div class="section-title"><i class="ti ti-receipt-2"></i>&nbsp; Shared Expenses</div>
        @if(empty($expenseLines))
        <p class="text-muted small mb-0">No shared expenses applicable for this member.</p>
        @else
        <table class="rpt-table" id="expense-table">
            <thead>
                <tr>
                    <th>Category / Item</th>
                    <th class="text-end">Category Total</th>
                    <th class="text-end">Your Share</th>
                </tr>
            </thead>
            <tbody>
                @foreach($expenseLines as $line)
                {{-- Category header row --}}
                <tr class="cat-total">
                    <td style="font-weight:700;">{{ $line['category'] }}</td>
                    <td class="text-end">৳{{ number_format($line['total'],2) }}</td>
                    <td class="text-end">৳{{ number_format($line['per_head'],2) }}</td>
                </tr>
                {{-- Individual items --}}
                @foreach($line['items'] as $item)
                <tr class="sub-row">
                    <td>
                        <span style="color:#adb5bd;margin-right:6px;">└</span>
                        {{ $item['title'] }}
                        <span style="color:#adb5bd;font-size:10px;margin-left:6px;">{{ $item['date'] }}</span>
                    </td>
                    <td class="text-end">৳{{ number_format($item['amount'],2) }}</td>
                    <td class="text-end" style="color:#6c757d;">—</td>
                </tr>
                @endforeach
                @endforeach
            </tbody>
            <tfoot>
                <tr style="background:#f0f4ff;font-weight:700;">
                    <td colspan="2">Your Total Expense Share</td>
                    <td class="text-end">৳{{ $sm ? number_format($sm->total_expenses,2) : number_format(array_sum(array_column($expenseLines,'per_head')),2) }}</td>
                </tr>
            </tfoot>
        </table>
        @endif
    </div>

    {{-- ── Deposits ── --}}
    <div class="rpt-section">
        <div class="section-title"><i class="ti ti-cash"></i>&nbsp; Deposits</div>
        @if($deposits->isEmpty())
        <p class="text-muted small mb-0">No deposits recorded this month.</p>
        @else
        <table class="rpt-table" id="deposit-table">
            <thead>
                <tr>
                    <th>{{ __('Date') }}</th>
                    <th>{{ __('Note') }}</th>
                    <th class="text-end">Amount</th>
                </tr>
            </thead>
            <tbody>
                @foreach($deposits as $dep)
                <tr>
                    <td style="white-space:nowrap;color:#6c757d;font-size:12px;">{{ $dep->created_at->format('d M Y') }}</td>
                    <td>{{ $dep->note ?? '—' }}</td>
                    <td class="text-end"><span class="deposit-tag">+৳{{ number_format($dep->amount,2) }}</span></td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr style="background:#f6fff8;font-weight:700;color:#155724;">
                    <td colspan="2">Total Deposits</td>
                    <td class="text-end">৳{{ number_format($deposits->sum('amount'),2) }}</td>
                </tr>
            </tfoot>
        </table>
        @endif
    </div>

    {{-- ── Balance Summary ── --}}
    <div class="rpt-section">
        <div class="section-title"><i class="ti ti-calculator"></i>&nbsp; Balance Summary</div>
        <div class="balance-box {{ $dueAmt > 0 ? 'due' : 'extra' }}">
            <div class="balance-row"><span>Meal Cost</span><span>৳{{ $sm ? number_format($sm->meal_cost,2) : '0.00' }}</span></div>
            <div class="balance-row"><span>Shared Expense</span><span>৳{{ $sm ? number_format($sm->total_expenses,2) : '0.00' }}</span></div>
            <div class="balance-row" style="font-weight:600;"><span>Total Payable</span><span>৳{{ $sm ? number_format($sm->total_payable,2) : '0.00' }}</span></div>
            <div class="balance-row"><span>Total Deposit</span><span style="color:#2fb344;">৳{{ $sm ? number_format($sm->total_deposit,2) : '0.00' }}</span></div>
            @if($sm && $sm->carry_forward_in > 0)
            <div class="balance-row"><span>Carry Forward (In)</span><span style="color:#2fb344;">+৳{{ number_format($sm->carry_forward_in,2) }}</span></div>
            @endif
            <div class="balance-row">
                <span style="font-size:16px;">{{ $dueAmt > 0 ? '⚠ Due Amount' : '✓ Extra Balance' }}</span>
                <span style="color:{{ $dueAmt > 0 ? '#d63939' : '#2fb344' }};font-size:18px;">
                    {{ $dueAmt > 0 ? '-' : '+' }}৳{{ number_format(abs($dueAmt),2) }}
                </span>
            </div>
        </div>
    </div>

    {{-- ── Footer ── --}}
    <div class="rpt-footer d-print-flex">
        <span>Generated: {{ now()->format('d M Y, h:i A') }}</span>
        <span style="font-weight:600;">{{ $mess->name }} · {{ $monthLabel }}</span>
        <span>Powered by Thaka Khawa</span>
    </div>

</div>
</div>

<script src="{{ asset('assets/js/tabler.min.js') }}"></script>
<script>
function downloadExcel() {
    var sheets = [
        { id: 'meal-table',    name: 'Meal Attendance' },
        { id: 'market-table',  name: 'Meal Cost' },
        { id: 'expense-table', name: 'Shared Expenses' },
        { id: 'deposit-table', name: 'Deposits' },
    ];

    // Build simple single-sheet HTML export (avoids XML namespace issues with Blade)
    var wb = '<html><head><meta charset="UTF-8"></head><body>';

    // Cover info
    wb += '<table id="cover_sheet"><tr><td><b>{{ addslashes($mess->name) }}</b></td></tr>';
    wb += '<tr><td>{{ addslashes($mess->address ?? '') }}</td></tr>';
    wb += '<tr><td>Member: {{ addslashes($targetMember->user->name) }}</td></tr>';
    wb += '<tr><td>Period: {{ $monthLabel }}</td></tr>';
    wb += '<tr><td>Role: {{ ucfirst($targetMember->role) }}</td></tr>';
    wb += '<tr><td></td></tr>';
    wb += '<tr><td>Meal Cost</td><td>৳{{ $sm ? number_format($sm->meal_cost,2) : "0.00" }}</td></tr>';
    wb += '<tr><td>Shared Expense</td><td>৳{{ $sm ? number_format($sm->total_expenses,2) : "0.00" }}</td></tr>';
    wb += '<tr><td>Total Payable</td><td>৳{{ $sm ? number_format($sm->total_payable,2) : "0.00" }}</td></tr>';
    wb += '<tr><td>Total Deposit</td><td>৳{{ $sm ? number_format($sm->total_deposit,2) : "0.00" }}</td></tr>';
    wb += '<tr><td>{{ $dueAmt > 0 ? "Due Amount" : "Extra Balance" }}</td><td>{{ ($dueAmt > 0 ? "-" : "+") }}৳{{ number_format(abs($dueAmt),2) }}</td></tr>';
    wb += '</table>';

    sheets.forEach(function(s) {
        var tbl = document.getElementById(s.id);
        if (tbl) wb += '<table id="' + s.id + '_sheet">' + tbl.innerHTML + '</table>';
    });

    wb += '</body></html>';

    var blob = new Blob([wb], { type: 'application/vnd.ms-excel;charset=utf-8' });
    var url  = URL.createObjectURL(blob);
    var a    = document.createElement('a');
    a.href   = url;
    a.download = '{{ Str::slug($mess->name) }}-{{ Str::slug($targetMember->user->name) }}-{{ $month }}-{{ $year }}.xls';
    a.click();
    URL.revokeObjectURL(url);
}
</script>
</body>
</html>
