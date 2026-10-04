@extends('layouts.app')

@section('title', 'Dashboard - TT Dental Lab')
@section('header_title', 'Dashboard')

@section('content')
<div class="page-heading">
    <div>
        <h1>Good {{ now()->hour < 12 ? 'morning' : (now()->hour < 18 ? 'afternoon' : 'evening') }}, Administrator 👋</h1>
        <p>Here is today's attendance overview for TT Dental Laboratory.</p>
    </div>
    <a class="btn btn-primary" href="{{ route('face-verification') }}">📷 Open Biometric Scanner</a>
</div>

<div class="stats-grid">
    <a class="stat-card" href="{{ route('employees.index') }}"><div class="stat-icon blue">👥</div><div><div class="stat-label">Total Employees</div><div class="stat-value">{{ $totalEmployees }}</div><div class="stat-note">Registered staff</div></div></a>
    <a class="stat-card" href="{{ route('attendance.index') }}"><div class="stat-icon green">✓</div><div><div class="stat-label">Present Today</div><div class="stat-value">{{ $presentToday }}</div><div class="stat-note">Clocked in today</div></div></a>
    <a class="stat-card" href="{{ route('absences.index') }}"><div class="stat-icon red">!</div><div><div class="stat-label">Absent Today</div><div class="stat-value">{{ $absentToday }}</div><div class="stat-note">No attendance record</div></div></a>
    <div class="stat-card"><div class="stat-icon amber">⏰</div><div><div class="stat-label">Late Today</div><div class="stat-value">{{ $lateToday }}</div><div class="stat-note">After 9:00 AM</div></div></div>
</div>

<div class="dashboard-grid">
    <section class="panel">
        <div class="panel-head"><div><h2>Today's Attendance</h2><p>{{ now()->format('d F Y') }}</p></div><a href="{{ route('reports.attendance') }}">View full report →</a></div>
        <div class="attendance-progress"><div><span>Attendance rate</span><strong>{{ $attendanceRate }}%</strong></div><div class="progress"><span style="width: {{ $attendanceRate }}%"></span></div></div>
        @if($recentAttendances->count())
        <div class="table-wrap"><table><thead><tr><th>Employee</th><th>Clock In</th><th>Clock Out</th><th>Method</th><th>Status</th></tr></thead><tbody>
        @foreach($recentAttendances as $attendance)
        <tr><td><strong>{{ $attendance->employee->name ?? 'Unknown' }}</strong><small>{{ $attendance->employee->employee_id ?? '-' }}</small></td><td>{{ $attendance->clock_in ?? '-' }}</td><td>{{ $attendance->clock_out ?? '-' }}</td><td>{{ $attendance->verification_method ?? '-' }}</td><td><span class="badge badge-success">{{ $attendance->status ?? 'Present' }}</span></td></tr>
        @endforeach
        </tbody></table></div>
        @else <div class="empty-state">No attendance has been recorded today.</div> @endif
    </section>

    <section class="panel quick-panel">
        <div class="panel-head"><div><h2>Quick Actions</h2><p>Common system operations</p></div></div>
        <a class="quick-action" href="{{ route('employees.create') }}"><span>＋</span><div><strong>Add Employee</strong><small>Register a new staff member</small></div><b>→</b></a>
        <a class="quick-action" href="{{ route('face-attendance') }}"><span>◉</span><div><strong>Register Face</strong><small>Set up biometric profile</small></div><b>→</b></a>
        <a class="quick-action" href="{{ route('reports.attendance') }}"><span>▤</span><div><strong>Attendance Report</strong><small>Review and export records</small></div><b>→</b></a>
        <a class="quick-action" href="{{ route('absences.index') }}"><span>!</span><div><strong>Manage Absences</strong><small>Record leave reasons</small></div><b>→</b></a>
    </section>
</div>
@endsection

@push('styles')
<style>
.page-heading{display:flex;justify-content:space-between;align-items:flex-end;gap:20px;margin-bottom:24px}.page-heading h1{font-size:26px;margin:0 0 6px}.page-heading p{margin:0;color:#64748b}.btn{display:inline-flex;align-items:center;justify-content:center;padding:11px 16px;border-radius:10px;text-decoration:none;font-weight:700;font-size:13px}.btn-primary{background:#2563eb;color:#fff;box-shadow:0 8px 18px rgba(37,99,235,.18)}.stats-grid{display:grid;grid-template-columns:repeat(4,1fr);gap:16px;margin-bottom:20px}.stat-card{background:#fff;border:1px solid #e2e8f0;border-radius:16px;padding:20px;display:flex;align-items:center;gap:14px;text-decoration:none;color:inherit;box-shadow:0 5px 20px rgba(15,23,42,.04)}.stat-icon{width:46px;height:46px;border-radius:13px;display:flex;align-items:center;justify-content:center;font-weight:800;font-size:20px}.stat-icon.blue{background:#eff6ff;color:#2563eb}.stat-icon.green{background:#ecfdf5;color:#16a34a}.stat-icon.red{background:#fef2f2;color:#dc2626}.stat-icon.amber{background:#fffbeb;color:#d97706}.stat-label{font-size:12px;color:#64748b;font-weight:700}.stat-value{font-size:27px;font-weight:800;margin-top:3px}.stat-note{font-size:11px;color:#94a3b8;margin-top:2px}.dashboard-grid{display:grid;grid-template-columns:1.65fr 1fr;gap:20px}.panel{background:#fff;border:1px solid #e2e8f0;border-radius:16px;padding:20px;box-shadow:0 5px 20px rgba(15,23,42,.04)}.panel-head{display:flex;justify-content:space-between;gap:15px;align-items:flex-start;margin-bottom:18px}.panel-head h2{font-size:17px;margin:0}.panel-head p{margin:5px 0 0;color:#94a3b8;font-size:12px}.panel-head a{font-size:12px;color:#2563eb;font-weight:700;text-decoration:none}.attendance-progress{background:#f8fafc;border-radius:12px;padding:14px;margin-bottom:16px}.attendance-progress>div:first-child{display:flex;justify-content:space-between;font-size:12px;color:#64748b;margin-bottom:9px}.attendance-progress strong{color:#0f172a}.progress{height:8px;background:#e2e8f0;border-radius:10px;overflow:hidden}.progress span{display:block;height:100%;background:#2563eb;border-radius:10px}.table-wrap{overflow:auto}table{width:100%;border-collapse:collapse;font-size:12px}th,td{text-align:left;padding:11px 8px;border-bottom:1px solid #f1f5f9;white-space:nowrap}th{font-size:10px;text-transform:uppercase;letter-spacing:.06em;color:#94a3b8}td small{display:block;color:#94a3b8;margin-top:3px}.badge{display:inline-block;padding:5px 8px;border-radius:999px;font-size:10px;font-weight:800}.badge-success{background:#dcfce7;color:#166534}.empty-state{text-align:center;color:#94a3b8;padding:30px 10px}.quick-action{display:flex;align-items:center;gap:12px;padding:13px 0;border-bottom:1px solid #f1f5f9;text-decoration:none;color:#0f172a}.quick-action:last-child{border-bottom:0}.quick-action>span{width:36px;height:36px;border-radius:10px;background:#eff6ff;color:#2563eb;display:flex;align-items:center;justify-content:center;font-weight:800}.quick-action div{flex:1}.quick-action strong,.quick-action small{display:block}.quick-action strong{font-size:12px}.quick-action small{font-size:10px;color:#94a3b8;margin-top:3px}.quick-action b{color:#94a3b8}@media(max-width:1000px){.stats-grid{grid-template-columns:repeat(2,1fr)}.dashboard-grid{grid-template-columns:1fr}}@media(max-width:600px){.page-heading{align-items:flex-start;flex-direction:column}.stats-grid{grid-template-columns:1fr}.page-heading h1{font-size:22px}}
</style>
@endpush
