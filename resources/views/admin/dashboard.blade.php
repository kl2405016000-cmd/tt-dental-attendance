@extends('layouts.app')
@section('title', 'Admin Dashboard — TT Dental Lab')
@section('header_title', 'Admin Dashboard')

@section('content')

{{-- Page Header --}}
<div class="page-header">
    <div>
        <div class="page-header-title">
            Good {{ now()->hour < 12 ? 'morning' : (now()->hour < 18 ? 'afternoon' : 'evening') }}, {{ explode(' ', auth()->user()->name)[0] }} 👋
        </div>
        <div class="page-header-sub">{{ now()->format('l, d F Y') }} · TT Dental Laboratory Attendance Overview</div>
    </div>
    <div class="page-header-actions">
        <a href="{{ route('employees.create') }}" class="btn btn-primary">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
            Add Employee
        </a>
        <a href="{{ route('reports.attendance') }}" class="btn btn-outline">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            Export Report
        </a>
    </div>
</div>

{{-- Primary Stat Cards --}}
<div class="stat-grid" style="grid-template-columns: repeat(4, 1fr);">

    <div class="stat-card stat-blue">
        <div class="stat-icon">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
        </div>
        <div class="stat-info">
            <div class="stat-label">Total Staff</div>
            <div class="stat-value">{{ $totalEmployees }}</div>
            <div class="stat-sub">Active employees</div>
        </div>
    </div>

    <div class="stat-card stat-green">
        <div class="stat-icon">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        </div>
        <div class="stat-info">
            <div class="stat-label">Present Today</div>
            <div class="stat-value">{{ $presentToday }}</div>
            <div class="stat-sub">{{ $totalEmployees ? round(($presentToday / $totalEmployees) * 100) : 0 }}% attendance rate</div>
        </div>
    </div>

    <div class="stat-card stat-red">
        <div class="stat-icon">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        </div>
        <div class="stat-info">
            <div class="stat-label">Absent Today</div>
            <div class="stat-value">{{ $absentToday }}</div>
            <div class="stat-sub">No clock-in recorded</div>
        </div>
    </div>

    <div class="stat-card stat-amber">
        <div class="stat-icon">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        </div>
        <div class="stat-info">
            <div class="stat-label">Late Arrivals</div>
            <div class="stat-value">{{ $lateToday }}</div>
            <div class="stat-sub">Clocked in after 09:00</div>
        </div>
    </div>

</div>

{{-- Secondary Stats Row --}}
<div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 12px; margin-bottom: 22px;">

    <div style="background: var(--purple-light); border: 1px solid rgba(124,58,237,0.15); border-radius: 12px; padding: 14px 16px; display: flex; align-items: center; gap: 12px;">
        <div style="width: 36px; height: 36px; background: var(--purple); border-radius: 9px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
            <svg width="17" height="17" fill="none" stroke="#fff" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        </div>
        <div>
            <div style="font-size: 10.5px; color: var(--purple); font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px;">On Break</div>
            <div style="font-size: 22px; font-weight: 900; color: #4c1d95; line-height: 1.2;">{{ $onBreak }}</div>
        </div>
    </div>

    <div style="background: var(--info-light); border: 1px solid var(--info-border); border-radius: 12px; padding: 14px 16px; display: flex; align-items: center; gap: 12px;">
        <div style="width: 36px; height: 36px; background: var(--info); border-radius: 9px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
            <svg width="17" height="17" fill="none" stroke="#fff" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
        </div>
        <div>
            <div style="font-size: 10.5px; color: var(--info); font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px;">On Leave</div>
            <div style="font-size: 22px; font-weight: 900; color: #075985; line-height: 1.2;">{{ $onLeave }}</div>
        </div>
    </div>

    <div style="background: #f0fdf4; border: 1px solid var(--success-border); border-radius: 12px; padding: 14px 16px; display: flex; align-items: center; gap: 12px;">
        <div style="width: 36px; height: 36px; background: var(--success); border-radius: 9px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
            <svg width="17" height="17" fill="none" stroke="#fff" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/></svg>
        </div>
        <div>
            <div style="font-size: 10.5px; color: var(--success); font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px;">Clock-ins</div>
            <div style="font-size: 22px; font-weight: 900; color: #14532d; line-height: 1.2;">{{ $totalClockIns }}</div>
        </div>
    </div>

    <div style="background: #fff7ed; border: 1px solid rgba(249,115,22,0.2); border-radius: 12px; padding: 14px 16px; display: flex; align-items: center; gap: 12px;">
        <div style="width: 36px; height: 36px; background: #f97316; border-radius: 9px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
            <svg width="17" height="17" fill="none" stroke="#fff" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
        </div>
        <div>
            <div style="font-size: 10.5px; color: #c2410c; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px;">Clock-outs</div>
            <div style="font-size: 22px; font-weight: 900; color: #7c2d12; line-height: 1.2;">{{ $totalClockOuts }}</div>
        </div>
    </div>

</div>

{{-- Main Content Grid --}}
<div style="display: grid; grid-template-columns: 1fr 320px; gap: 20px; align-items: start;">

    {{-- Recent Attendance Table --}}
    <div class="card" style="margin-bottom: 0;">
        <div class="card-header">
            <div>
                <div class="card-title">Today's Attendance</div>
                <div class="card-subtitle">Live records for {{ now()->format('d F Y') }}</div>
            </div>
            <a href="{{ route('reports.attendance') }}" class="btn btn-outline btn-sm">View all →</a>
        </div>

        <div class="table-wrap">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Employee</th>
                        <th>Clock In</th>
                        <th>Clock Out</th>
                        <th>Hours</th>
                        <th>Status</th>
                        <th>Method</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentAttendances as $att)
                        <tr>
                            <td>
                                <div style="display:flex; align-items:center; gap:9px;">
                                    <div style="width:32px; height:32px; border-radius:8px; background:linear-gradient(135deg,#4f46e5,#7c3aed); display:flex; align-items:center; justify-content:center; color:#fff; font-weight:800; font-size:12px; flex-shrink:0;">
                                        {{ strtoupper(substr($att->employee?->name ?? 'U', 0, 1)) }}
                                    </div>
                                    <div>
                                        <div style="font-weight:700; color:var(--text-head); font-size:13px;">{{ $att->employee?->name ?? 'Unknown' }}</div>
                                        <div style="font-size:11px; color:var(--text-faint);">{{ $att->employee?->employee_id ?? '-' }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="td-mono" style="font-size:13px;">{{ $att->clock_in ?? '—' }}</td>
                            <td class="td-mono" style="font-size:13px; color:var(--text-muted);">{{ $att->clock_out ?? '—' }}</td>
                            <td style="font-weight:600; font-size:13px;">{{ $att->working_hours }}</td>
                            <td>
                                @php
                                    $status = $att->status ?? 'Present';
                                    $cls = match(true) {
                                        str_contains($status, 'Present') => 'badge-green',
                                        str_contains($status, 'Break')   => 'badge-amber',
                                        str_contains($status, 'Leave')   => 'badge-cyan',
                                        default                           => 'badge-gray',
                                    };
                                @endphp
                                <span class="badge {{ $cls }}">{{ $status }}</span>
                            </td>
                            <td style="font-size:12px; color:var(--text-muted);">{{ $att->verification_method ?? '—' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="table-empty">
                                <svg width="36" height="36" fill="none" stroke="#cbd5e1" stroke-width="1.5" viewBox="0 0 24 24" style="margin-bottom:8px;"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg><br>
                                No attendance records for today yet.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Right Column --}}
    <div style="display: flex; flex-direction: column; gap: 16px;">

        {{-- Quick Actions --}}
        <div class="card" style="margin-bottom: 0;">
            <div class="card-title" style="margin-bottom: 14px;">Quick Actions</div>
            <div class="quick-list">
                <a href="{{ route('employees.index') }}" class="quick-item">
                    <div class="quick-item-icon" style="background:#eff6ff; color:#2563eb;">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    </div>
                    <div class="quick-item-text">
                        <strong>Employee Directory</strong>
                        <span>{{ $totalEmployees }} active staff</span>
                    </div>
                    <span class="quick-item-arrow">›</span>
                </a>

                <a href="{{ route('attendance.index') }}" class="quick-item">
                    <div class="quick-item-icon" style="background:#f0fdf4; color:#16a34a;">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                    </div>
                    <div class="quick-item-text">
                        <strong>Manual Attendance</strong>
                        <span>Override clock-in/out</span>
                    </div>
                    <span class="quick-item-arrow">›</span>
                </a>

                <a href="{{ route('admin.mc.index') }}" class="quick-item">
                    <div class="quick-item-icon" style="background:#fff7ed; color:#f97316;">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    </div>
                    <div class="quick-item-text">
                        <strong>MC Review</strong>
                        @if($pendingMc > 0)
                            <span style="color:#dc2626; font-weight:700;">{{ $pendingMc }} pending</span>
                        @else
                            <span>No pending MCs</span>
                        @endif
                    </div>
                    @if($pendingMc > 0)
                        <span class="badge badge-red">{{ $pendingMc }}</span>
                    @else
                        <span class="quick-item-arrow">›</span>
                    @endif
                </a>

                <a href="{{ route('face-attendance') }}" class="quick-item">
                    <div class="quick-item-icon" style="background:var(--purple-light); color:var(--purple);">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="8" r="4" stroke-width="2"/><path stroke-linecap="round" stroke-linejoin="round" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/></svg>
                    </div>
                    <div class="quick-item-text">
                        <strong>Face Registration</strong>
                        <span>Register biometrics</span>
                    </div>
                    <span class="quick-item-arrow">›</span>
                </a>

                <a href="{{ route('absences.index') }}" class="quick-item">
                    <div class="quick-item-icon" style="background:var(--danger-light); color:var(--danger);">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <div class="quick-item-text">
                        <strong>Absence Tracker</strong>
                        <span>{{ $absentToday }} absent today</span>
                    </div>
                    @if($absentToday > 0)
                        <span class="badge badge-red">{{ $absentToday }}</span>
                    @else
                        <span class="quick-item-arrow">›</span>
                    @endif
                </a>
            </div>
        </div>

        {{-- Attendance Rate --}}
        <div class="card" style="margin-bottom: 0;">
            <div class="card-title" style="margin-bottom: 14px;">Today's Rate</div>
            @php $rate = $totalEmployees ? round(($presentToday / $totalEmployees) * 100) : 0; @endphp

            <div style="text-align:center; margin-bottom:12px;">
                <div style="font-size:46px; font-weight:900; color:var(--text-head); letter-spacing:-2px; line-height:1;">{{ $rate }}%</div>
                <div style="font-size:12px; color:var(--text-muted); margin-top:4px;">Attendance rate</div>
            </div>

            <div style="background:#f1f5f9; border-radius:999px; height:8px; overflow:hidden; margin-bottom:10px;">
                <div style="height:100%; border-radius:999px; width:{{ $rate }}%;
                    background: linear-gradient(90deg, {{ $rate >= 80 ? '#16a34a, #22c55e' : ($rate >= 50 ? '#d97706, #fbbf24' : '#dc2626, #f87171') }});">
                </div>
            </div>

            <div style="display:flex; justify-content:space-between; font-size:11.5px; color:var(--text-muted);">
                <span>{{ $presentToday }} present</span>
                <span>{{ $totalEmployees }} total</span>
            </div>
        </div>

    </div>
</div>

@endsection

@push('styles')
<style>
@media (max-width: 1100px) {
    .stat-grid { grid-template-columns: repeat(2, 1fr) !important; }
}
@media (max-width: 900px) {
    .stat-grid { grid-template-columns: repeat(2, 1fr) !important; }
    div[style*="grid-template-columns: repeat(4, 1fr)"] { grid-template-columns: repeat(2, 1fr) !important; }
    div[style*="grid-template-columns: 1fr 320px"] { grid-template-columns: 1fr !important; }
}
@media (max-width: 600px) {
    .stat-grid { grid-template-columns: 1fr 1fr !important; }
    div[style*="grid-template-columns: repeat(4, 1fr)"] { grid-template-columns: 1fr 1fr !important; }
}
</style>
@endpush
