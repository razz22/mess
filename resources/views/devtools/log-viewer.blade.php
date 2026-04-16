<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Laravel Log Viewer</title>
<style>
  *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
  body { font-family: 'Segoe UI', system-ui, sans-serif; background: #0f1117; color: #e2e8f0; min-height: 100vh; }

  .header { background: #1a1d27; border-bottom: 1px solid #2d3148; padding: 14px 20px; display: flex; align-items: center; flex-wrap: wrap; gap: 12px; }
  .header h1 { font-size: 16px; font-weight: 700; color: #a78bfa; white-space: nowrap; }
  .header h1 span { color: #64748b; font-weight: 400; font-size: 13px; margin-left: 8px; }

  .toolbar { display: flex; gap: 8px; align-items: center; flex-wrap: wrap; margin-left: auto; }
  .toolbar input, .toolbar select { background: #252836; border: 1px solid #3d4163; color: #e2e8f0; border-radius: 6px; padding: 6px 10px; font-size: 13px; outline: none; }
  .toolbar input:focus, .toolbar select:focus { border-color: #a78bfa; }
  .toolbar input[type="text"] { width: 200px; }
  .btn { padding: 6px 14px; border-radius: 6px; font-size: 13px; font-weight: 600; cursor: pointer; border: none; text-decoration: none; display: inline-flex; align-items: center; gap: 5px; }
  .btn-primary { background: #7c3aed; color: #fff; }
  .btn-primary:hover { background: #6d28d9; }
  .btn-outline { background: transparent; border: 1px solid #3d4163; color: #94a3b8; }
  .btn-outline:hover { border-color: #a78bfa; color: #a78bfa; }
  .btn-danger { background: #991b1b; color: #fff; }
  .btn-danger:hover { background: #7f1d1d; }

  .meta { background: #1a1d27; padding: 10px 20px; font-size: 12px; color: #64748b; border-bottom: 1px solid #2d3148; display: flex; gap: 20px; flex-wrap: wrap; align-items: center; }
  .meta b { color: #94a3b8; }
  .badge { display: inline-block; padding: 2px 8px; border-radius: 999px; font-size: 11px; font-weight: 700; }
  .badge-token { background: #78350f; color: #fcd34d; }
  .badge-admin { background: #1e3a5f; color: #93c5fd; }

  .log-wrap { padding: 16px 20px; overflow-x: auto; }
  pre#log { font-family: 'Cascadia Code', 'Fira Code', 'Consolas', monospace; font-size: 12.5px; line-height: 1.6; white-space: pre-wrap; word-break: break-word; }

  /* Colorize log levels */
  .line-error   { color: #f87171; }
  .line-warning { color: #fbbf24; }
  .line-info    { color: #60a5fa; }
  .line-debug   { color: #94a3b8; }
  .line-trace   { color: #6b7280; }
  .line-date    { color: #a78bfa; }
  .line-msg     { color: #e2e8f0; }

  .empty { text-align: center; padding: 60px 20px; color: #4b5563; font-size: 14px; }

  /* Highlight search term */
  mark { background: #854d0e; color: #fde68a; border-radius: 2px; padding: 0 2px; }

  /* Scroll to bottom button */
  #scrollBtn { position: fixed; bottom: 24px; right: 24px; background: #7c3aed; color: #fff; border: none; border-radius: 50%; width: 44px; height: 44px; font-size: 20px; cursor: pointer; box-shadow: 0 4px 12px rgba(0,0,0,0.4); display: flex; align-items: center; justify-content: center; }
  #scrollBtn:hover { background: #6d28d9; }

  /* Stats bar */
  .stats { display: flex; gap: 16px; padding: 8px 20px; background: #13151e; border-bottom: 1px solid #2d3148; font-size: 12px; flex-wrap: wrap; }
  .stat { display: flex; align-items: center; gap: 5px; }
  .dot { width: 8px; height: 8px; border-radius: 50%; }
  .dot-err { background: #f87171; }
  .dot-warn { background: #fbbf24; }
  .dot-info { background: #60a5fa; }
</style>
</head>
<body>

<div class="header">
  <h1>&#128203; Laravel Log Viewer <span>{{ $logFile }}</span></h1>
  <div class="toolbar">
    <form method="GET" action="{{ url('/get-logs') }}" style="display:contents">
      @if(request('token'))
      <input type="hidden" name="token" value="{{ request('token') }}">
      @endif
      <input type="text" name="filter" placeholder="Filter keyword..." value="{{ $filter }}">
      <select name="lines">
        @foreach([50,100,200,500,1000] as $n)
        <option value="{{ $n }}" {{ $lines == $n ? 'selected' : '' }}>Last {{ $n }} lines</option>
        @endforeach
      </select>
      <button type="submit" class="btn btn-primary">&#128269; Apply</button>
    </form>
    <a href="{{ url('/get-logs') }}?{{ request('token') ? 'token='.request('token').'&' : '' }}download=1" class="btn btn-outline">&#8681; Download</a>
    <button onclick="clearLog()" class="btn btn-danger" title="Clear log file">&#128465; Clear</button>
  </div>
</div>

<div class="meta">
  <span><b>File size:</b> {{ $fileSize }}</span>
  <span><b>Showing:</b> last {{ $lines }} lines{{ $filter ? ' matching "'.e($filter).'"' : '' }}</span>
  @if($tokenMode)
    <span class="badge badge-token">&#128273; Token access</span>
  @else
    <span class="badge badge-admin">&#128100; Super admin</span>
  @endif
  <span style="margin-left:auto;color:#4b5563;font-size:11px">{{ now()->format('d M Y, H:i:s') }}</span>
</div>

@php
// Count log levels
$errCount  = substr_count(strtolower($logText), '.error');
$warnCount = substr_count(strtolower($logText), '.warning');
$infoCount = substr_count(strtolower($logText), '.info');
@endphp
<div class="stats">
  <div class="stat"><span class="dot dot-err"></span> <b style="color:#f87171">{{ $errCount }}</b>&nbsp;errors</div>
  <div class="stat"><span class="dot dot-warn"></span> <b style="color:#fbbf24">{{ $warnCount }}</b>&nbsp;warnings</div>
  <div class="stat"><span class="dot dot-info"></span> <b style="color:#60a5fa">{{ $infoCount }}</b>&nbsp;info</div>
  <div class="stat" style="margin-left:auto;color:#4b5563">Press <kbd style="background:#252836;padding:1px 5px;border-radius:3px;font-size:11px">Ctrl+F</kbd> to search in-page</div>
</div>

<div class="log-wrap">
@if(trim($logText) === '' || trim($logText) === '[No log file found at '.$logFile.']')
  <div class="empty">
    <div style="font-size:40px;margin-bottom:12px">&#128203;</div>
    <div>{{ trim($logText) ?: 'Log file is empty.' }}</div>
  </div>
@else
  <pre id="log"></pre>
@endif
</div>

<button id="scrollBtn" onclick="window.scrollTo({top:document.body.scrollHeight,behavior:'smooth'})" title="Scroll to bottom">&#8595;</button>

<form id="clearForm" method="POST" action="{{ url('/get-logs/clear') }}" style="display:none">
  @csrf
</form>

<script>
const RAW = @json($logText);
const FILTER = @json($filter);

function colorLine(line) {
    const lo = line.toLowerCase();
    if (/^\[?\d{4}-\d{2}-\d{2}/.test(line)) {
        // Date stamp line — parse level
        if (lo.includes('.error') || lo.includes('error:'))   return '<span class="line-error">'   + esc(line) + '</span>';
        if (lo.includes('.warning') || lo.includes('warn:'))  return '<span class="line-warning">' + esc(line) + '</span>';
        if (lo.includes('.info') || lo.includes('info:'))     return '<span class="line-info">'    + esc(line) + '</span>';
        if (lo.includes('.debug') || lo.includes('debug:'))   return '<span class="line-debug">'   + esc(line) + '</span>';
        return '<span class="line-date">' + esc(line) + '</span>';
    }
    if (lo.includes('stack trace') || lo.startsWith('#') || lo.startsWith('  #')) {
        return '<span class="line-trace">' + esc(line) + '</span>';
    }
    return '<span class="line-msg">' + esc(line) + '</span>';
}

function esc(s) {
    return s.replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;');
}

function highlight(html) {
    if (!FILTER) return html;
    const re = new RegExp('(' + FILTER.replace(/[.*+?^${}()|[\]\\]/g,'\\$&') + ')', 'gi');
    return html.replace(re, '<mark>$1</mark>');
}

const el = document.getElementById('log');
if (el) {
    const lines = RAW.split('\n');
    const html  = lines.map(l => highlight(colorLine(l))).join('\n');
    el.innerHTML = html;
    // Auto-scroll to bottom
    window.scrollTo(0, document.body.scrollHeight);
}

function clearLog() {
    if (!confirm('Clear the entire log file? This cannot be undone.')) return;
    document.getElementById('clearForm').submit();
}
</script>
</body>
</html>
