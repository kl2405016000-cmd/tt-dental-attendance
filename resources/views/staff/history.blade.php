@extends('layouts.app')

@section('title', 'My Attendance History — TT Dental Lab')
@section('header_title', 'My Attendance History')

@section('content')

{{-- Page Header --}}
<div class="page-header">
    <div>
        <div class="page-header-title">My Attendance History</div>
        <div class="page-header-sub">
            Review your historical clock-in times, break durations, and total logged working hours
            @if($employee)
                • <strong style="color: var(--text-head);">{{ $employee->employee_id }} — {{ $employee->name }}</strong>
            @endif
        </div>
    </div>
    <div class="page-header-actions">
        <a href="{{ route('staff.dashboard') }}" class="btn btn-outline btn-sm">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Back to Dashboard
        </a>
        <button type="button" class="btn btn-outline btn-sm" onclick="window.print()">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
            Print Summary
        </button>
    </div>
</div>

@php
    $totalDays = $attendances->count();
    $presentDays = $attendances->where('status', 'Present')->count();
    $mcDays = $attendances->filter(fn($a) => str_contains($a->status ?? '', 'MC') || str_contains($a->status ?? '', 'Leave'))->count();
    
    // Calculate total hours logged
    $totalLoggedMinutes = 0;
    foreach ($attendances as $att) {
        $totalLoggedMinutes += (int) ($att->net_work_minutes ?? 0);
    }
    $totalLoggedHours = sprintf('%02dh %02dm', intdiv($totalLoggedMinutes, 60), $totalLoggedMinutes % 60);
@endphp

{{-- Stats Row --}}
<div class="stat-grid">
    <div class="stat-card stat-blue">
        <div class="stat-icon">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
        </div>
        <div class="stat-info">
            <div class="stat-label">Total Logs</div>
            <div class="stat-value">{{ $totalDays }}</div>
            <div class="stat-sub">Logged attendance days</div>
        </div>
    </div>

    <div class="stat-card stat-green">
        <div class="stat-icon">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        </div>
        <div class="stat-info">
            <div class="stat-label">Present</div>
            <div class="stat-value">{{ $presentDays }}</div>
            <div class="stat-sub">Normal attendance days</div>
        </div>
    </div>

    <div class="stat-card stat-purple">
        <div class="stat-icon">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        </div>
        <div class="stat-info">
            <div class="stat-label">Total Hours</div>
            <div class="stat-value" style="font-size: 22px;">{{ $totalLoggedHours }}</div>
            <div class="stat-sub">Net logged working time</div>
        </div>
    </div>

    <div class="stat-card stat-amber">
        <div class="stat-icon">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
        </div>
        <div class="stat-info">
            <div class="stat-label">Medical Leave</div>
            <div class="stat-value">{{ $mcDays }}</div>
            <div class="stat-sub">Approved MC leaves</div>
        </div>
    </div>
</div>

{{-- Filter Card --}}
<div class="card" style="margin-bottom: 20px;">
    <div class="card-header" style="margin-bottom: 14px;">
        <div>
            <div class="card-title">Filter Attendance Logs</div>
            <div class="card-subtitle">Narrow down attendance records by date range or attendance status</div>
        </div>
    </div>

    <form method="GET" action="{{ route('staff.history') }}" style="display: flex; gap: 14px; flex-wrap: wrap; align-items: flex-end;">
        <div class="form-group" style="margin-bottom: 0; flex: 1; min-width: 170px;">
            <label class="form-label" for="date_from">From Date</label>
            <input type="date" id="date_from" name="date_from" class="form-input" value="{{ $dateFrom ?? '' }}">
        </div>

        <div class="form-group" style="margin-bottom: 0; flex: 1; min-width: 170px;">
            <label class="form-label" for="date_to">To Date</label>
            <input type="date" id="date_to" name="date_to" class="form-input" value="{{ $dateTo ?? '' }}">
        </div>

        <div class="form-group" style="margin-bottom: 0; flex: 1; min-width: 170px;">
            <label class="form-label" for="status">Status</label>
            <select id="status" name="status" class="form-select">
                <option value="">All Statuses</option>
                <option value="Present" {{ ($status ?? '') === 'Present' ? 'selected' : '' }}>Present</option>
                <option value="On Break" {{ ($status ?? '') === 'On Break' ? 'selected' : '' }}>On Break</option>
                <option value="On Leave (MC)" {{ ($status ?? '') === 'On Leave (MC)' ? 'selected' : '' }}>On Leave (MC)</option>
                <option value="Late" {{ ($status ?? '') === 'Late' ? 'selected' : '' }}>Late</option>
                <option value="Absent" {{ ($status ?? '') === 'Absent' ? 'selected' : '' }}>Absent</option>
            </select>
        </div>

        <div style="display: flex; gap: 8px;">
            <button type="submit" class="btn btn-primary">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/></svg>
                Apply Filter
            </button>
            @if(!empty($dateFrom) || !empty($dateTo) || !empty($status))
                <a href="{{ route('staff.history') }}" class="btn btn-outline" title="Clear Filters">
                    Reset
                </a>
            @endif
        </div>
    </form>
</div>

{{-- Records Table Card --}}
<div class="card">
    <div class="card-header">
        <div>
            <div class="card-title">Daily Attendance Records</div>
            <div class="card-subtitle">Showing {{ $attendances->count() }} record{{ $attendances->count() === 1 ? '' : 's' }} sorted by latest date</div>
        </div>
    </div>

    <div class="table-wrap">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Date & Day</th>
                    <th>Clock In</th>
                    <th>Break Start</th>
                    <th>Break End</th>
                    <th>Clock Out</th>
                    <th>Net Working Hours</th>
                    <th>Status</th>
                    <th>Method</th>
                </tr>
            </thead>
            <tbody>
                @forelse($attendances as $att)
                    @php
                        $parsedDate = $att->attendance_date ? \Carbon\Carbon::parse($att->attendance_date) : null;
                        
                        // Status badge style
                        $st = strtolower($att->status ?? 'present');
                        if (str_contains($st, 'present')) {
                            $badgeClass = 'badge-green';
                        } elseif (str_contains($st, 'break')) {
                            $badgeClass = 'badge-amber';
                        } elseif (str_contains($st, 'mc') || str_contains($st, 'leave')) {
                            $badgeClass = 'badge-purple';
                        } elseif (str_contains($st, 'late')) {
                            $badgeClass = 'badge-amber';
                        } elseif (str_contains($st, 'absent')) {
                            $badgeClass = 'badge-red';
                        } else {
                            $badgeClass = 'badge-gray';
                        }
                    @endphp
                    <tr>
                        {{-- Date & Day --}}
                        <td class="td-bold">
                            @if($parsedDate)
                                <div>{{ $parsedDate->format('d M Y') }}</div>
                                <div style="font-size: 11px; font-weight: 500; color: var(--text-muted);">{{ $parsedDate->format('l') }}</div>
                            @else
                                —
                            @endif
                        </td>

                        {{-- Clock In --}}
                        <td class="td-mono">
                            @if($att->clock_in)
                                <span style="display: inline-flex; align-items: center; gap: 5px; color: var(--success); font-weight: 700;">
                                    <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M11 16l-4-4m0 0l4-4m-4 4h14"/></svg>
                                    {{ \Carbon\Carbon::parse($att->clock_in)->format('h:i A') }}
                                </span>
                            @else
                                <span class="td-muted">—</span>
                            @endif
                        </td>

                        {{-- Break Start --}}
                        <td class="td-mono td-muted">
                            {{ $att->break_start ? \Carbon\Carbon::parse($att->break_start)->format('h:i A') : '—' }}
                        </td>

                        {{-- Break End --}}
                        <td class="td-mono td-muted">
                            {{ $att->break_end ? \Carbon\Carbon::parse($att->break_end)->format('h:i A') : '—' }}
                            @if($att->break_duration_minutes > 0)
                                <span class="badge badge-amber" style="font-size: 10px; margin-left: 4px;">{{ $att->break_duration_minutes }}m</span>
                            @endif
                        </td>

                        {{-- Clock Out --}}
                        <td class="td-mono">
                            @if($att->clock_out)
                                <span style="display: inline-flex; align-items: center; gap: 5px; color: var(--danger); font-weight: 700;">
                                    <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17 16l4-4m0 0l-4-4m4 4H7"/></svg>
                                    {{ \Carbon\Carbon::parse($att->clock_out)->format('h:i A') }}
                                </span>
                            @elseif($att->clock_in)
                                <span class="badge badge-blue">Active / On Duty</span>
                            @else
                                <span class="td-muted">—</span>
                            @endif
                        </td>

                        {{-- Working Hours --}}
                        <td class="td-bold">
                            @if($att->clock_in && $att->clock_out)
                                <span style="color: var(--primary);">{{ $att->working_hours }} hrs</span>
                            @elseif($att->clock_in)
                                <span style="font-size: 11.5px; color: var(--info); font-weight: 600;">In Progress</span>
                            @else
                                <span class="td-muted">—</span>
                            @endif
                        </td>

                        {{-- Status --}}
                        <td>
                            @if($att->late_minutes > 0)
                                <span class="badge badge-red" style="color:#ef4444; border-color:#fca5a5; background:#fef2f2;">Late – {{ $att->late_minutes }} minutes</span>
                            @else
                                <span class="badge {{ $badgeClass }}">
                                    {{ $att->status ?? 'Present' }}
                                </span>
                            @endif
                        </td>

                        {{-- Method --}}
                        <td>
                            @php $method = strtolower($att->verification_method ?? 'manual'); @endphp
                            @if(str_contains($method, 'face') || str_contains($method, 'biometric'))
                                <span class="badge badge-blue" title="Biometric Face Scan">
                                    <svg width="12" height="12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/></svg>
                                    Biometric
                                </span>
                            @elseif(str_contains($method, 'medical') || str_contains($method, 'mc'))
                                <span class="badge badge-purple">
                                    <svg width="12" height="12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                    MC Leave
                                </span>
                            @else
                                <span class="badge badge-gray">
                                    Manual
                                </span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="table-empty">
                            <div style="display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 8px;">
                                <svg width="40" height="40" fill="none" stroke="currentColor" style="opacity: 0.35;" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                                <div style="font-weight: 700; font-size: 14px; color: var(--text-head);">No attendance logs found</div>
                                <div style="font-size: 12.5px; color: var(--text-muted);">
                                    @if(!empty($dateFrom) || !empty($dateTo) || !empty($status))
                                        No attendance records match your current filter parameters. Try clearing the filters.
                                    @else
                                        Your daily attendance records will be listed here after you clock in.
                                    @endif
                                </div>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection
