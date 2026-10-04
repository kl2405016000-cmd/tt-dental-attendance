@extends('layouts.app')
@section('title', 'Attendance Reports — TT Dental Lab')
@section('header_title', 'Attendance Reports')

@section('content')

<div class="page-header">
    <div>
        <div class="page-header-title">Attendance Reports</div>
        <div class="page-header-sub">Filter, review, export and print attendance records</div>
    </div>
    <div class="page-header-actions">
        <a href="{{ route('reports.attendance.export', ['date_from' => $dateFrom, 'date_to' => $dateTo, 'employee_id' => $employeeId]) }}"
           class="btn btn-success">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
            Export CSV
        </a>
        <button type="button" class="btn btn-outline" onclick="window.print()">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
            Print
        </button>
    </div>
</div>

{{-- Filter Card --}}
<div class="card no-print">
    <div class="card-header">
        <div>
            <div class="card-title">Search & Filter</div>
            <div class="card-subtitle">Narrow down records by date range or employee</div>
        </div>
    </div>

    <form action="{{ route('reports.attendance') }}" method="GET">
        <div style="display: grid; grid-template-columns: 1fr 1fr 1.5fr auto; gap: 14px; align-items: end;">
            <div class="form-group" style="margin-bottom:0;">
                <label class="form-label">From Date</label>
                <input type="date" name="date_from" class="form-input" value="{{ $dateFrom }}">
            </div>
            <div class="form-group" style="margin-bottom:0;">
                <label class="form-label">To Date</label>
                <input type="date" name="date_to" class="form-input" value="{{ $dateTo }}">
            </div>
            <div class="form-group" style="margin-bottom:0;">
                <label class="form-label">Employee</label>
                <select name="employee_id" class="form-select">
                    <option value="">All Employees</option>
                    @foreach($employees as $employee)
                        <option value="{{ $employee->id }}" {{ $employeeId == $employee->id ? 'selected' : '' }}>
                            {{ $employee->employee_id }} — {{ $employee->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="btn btn-primary" style="align-self:flex-end;">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                Search
            </button>
        </div>
    </form>
</div>

{{-- Results --}}
<div class="card">
    <div class="card-header">
        <div>
            <div class="card-title">Attendance Records</div>
            <div class="card-subtitle">
                {{ $dateFrom }} to {{ $dateTo }}
                @if($employeeId)
                    · Filtered by employee
                @endif
            </div>
        </div>
        <span class="badge badge-blue">{{ $attendances->count() }} records</span>
    </div>

    <div class="table-wrap">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Employee</th>
                    <th>Date</th>
                    <th>Clock In</th>
                    <th>Clock Out</th>
                    <th>Break</th>
                    <th>Working Hrs</th>
                    <th>Status</th>
                    <th>Method</th>
                </tr>
            </thead>
            <tbody>
                @forelse($attendances as $att)
                    <tr>
                        <td>
                            <div style="font-weight:700; color:var(--text-head);">{{ $att->employee->name ?? 'Unknown' }}</div>
                            <div style="font-size:11.5px; color:var(--primary); font-weight:600;">{{ $att->employee->employee_id ?? '—' }}</div>
                        </td>
                        <td style="font-weight:600; white-space:nowrap;">{{ $att->attendance_date instanceof \DateTimeInterface ? $att->attendance_date->format('d M Y') : $att->attendance_date }}</td>
                        <td class="td-mono" style="color:var(--success); font-weight:700;">{{ $att->clock_in ?? '—' }}</td>
                        <td class="td-mono" style="color:var(--danger); font-weight:700;">{{ $att->clock_out ?? '—' }}</td>
                        <td style="font-size:12.5px; color:var(--text-muted);">{{ $att->break_duration }}</td>
                        <td style="font-weight:700;">{{ $att->working_hours }}</td>
                        <td>
                            @if($att->late_minutes > 0)
                                <span class="badge badge-red" style="color:#ef4444; border-color:#fca5a5; background:#fef2f2;">Late – {{ $att->late_minutes }} minutes</span>
                            @else
                                @php
                                    $s  = $att->status ?? 'Present';
                                    $bc = match(true) {
                                        str_contains($s,'Present') => 'badge-green',
                                        str_contains($s,'Break')   => 'badge-amber',
                                        str_contains($s,'Leave')   => 'badge-cyan',
                                        default                    => 'badge-gray',
                                    };
                                @endphp
                                <span class="badge {{ $bc }}">{{ $s }}</span>
                            @endif
                        </td>
                        <td style="font-size:12px; color:var(--text-muted);">{{ $att->verification_method ?? '—' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="table-empty">No records found for the selected filters.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection

@push('styles')
<style>
    @media print {
        .sidebar, .topbar, .no-print, .app-footer, .page-header-actions { display: none !important; }
        .main-wrapper { margin-left: 0 !important; }
        .content-area { padding: 0 !important; }
        body { background: white !important; }
        .card { box-shadow: none !important; border: 1px solid #ddd !important; }
    }
    @media (max-width: 900px) {
        form > div[style*="grid-template-columns: 1fr 1fr 1.5fr auto"] {
            grid-template-columns: 1fr 1fr !important;
        }
    }
</style>
@endpush
