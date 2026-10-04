@extends('layouts.app')
@section('title', 'Manual Attendance — TT Dental Lab')
@section('header_title', 'Manual Attendance')

@section('content')

<div class="page-header">
    <div>
        <div class="page-header-title">Manual Attendance</div>
        <div class="page-header-sub">Administrative clock-in/out override for employees · {{ now()->format('d F Y') }}</div>
    </div>
</div>

{{-- Control Card --}}
<div class="card">
    <div class="card-header">
        <div>
            <div class="card-title">Record Attendance</div>
            <div class="card-subtitle">Select an active employee and apply clock-in or clock-out on their behalf.</div>
        </div>
    </div>

    <div style="max-width: 580px;">
        <div class="form-group">
            <label class="form-label">Select Active Employee</label>
            <select id="employee_id" class="form-select">
                <option value="">— Choose Employee —</option>
                @foreach($employees as $employee)
                    <option value="{{ $employee->id }}">
                        {{ $employee->employee_id }} — {{ $employee->name }}
                        @if($employee->position) ({{ $employee->position }}) @endif
                    </option>
                @endforeach
            </select>
        </div>

        <div style="display: flex; gap: 10px; flex-wrap: wrap;">
            <form action="{{ route('attendance.clockIn') }}" method="POST">
                @csrf
                <input type="hidden" name="employee_id" id="clock_in_employee">
                <button type="submit" id="btnClockIn" class="btn btn-success" disabled>
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/></svg>
                    Manual Clock In
                </button>
            </form>

            <form action="{{ route('attendance.clockOut') }}" method="POST">
                @csrf
                <input type="hidden" name="employee_id" id="clock_out_employee">
                <button type="submit" id="btnClockOut" class="btn btn-danger" disabled>
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                    Manual Clock Out
                </button>
            </form>
        </div>
    </div>
</div>

{{-- Today's Attendance Table --}}
<div class="card">
    <div class="card-header">
        <div>
            <div class="card-title">Today's Attendance Logs</div>
            <div class="card-subtitle">Live records for {{ now()->format('d F Y') }}</div>
        </div>
        <span class="badge badge-blue">{{ $attendances->count() }} records</span>
    </div>

    <div class="table-wrap">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Employee</th>
                    <th>Clock In</th>
                    <th>Break</th>
                    <th>Clock Out</th>
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
                        <td class="td-mono" style="color:var(--success); font-weight:700;">{{ $att->clock_in ?? '—' }}</td>
                        <td style="font-size:12.5px; color:var(--text-muted);">
                            @if($att->break_start)
                                {{ $att->break_start }} → {{ $att->break_end ?? '…' }}
                            @else
                                —
                            @endif
                        </td>
                        <td class="td-mono" style="color:var(--danger); font-weight:700;">{{ $att->clock_out ?? '—' }}</td>
                        <td style="font-weight:700; color:var(--text-head);">{{ $att->working_hours }}</td>
                        <td>
                            @if($att->late_minutes > 0)
                                <span class="badge badge-red" style="color:#ef4444; border-color:#fca5a5; background:#fef2f2;">Late – {{ $att->late_minutes }} minutes</span>
                            @else
                                @php
                                    $s = $att->status ?? 'Present';
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
                        <td colspan="7" class="table-empty">
                            No attendance records logged today yet.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection

@push('scripts')
<script>
    const sel = document.getElementById('employee_id');
    const ciE = document.getElementById('clock_in_employee');
    const coE = document.getElementById('clock_out_employee');
    const ci  = document.getElementById('btnClockIn');
    const co  = document.getElementById('btnClockOut');

    sel.addEventListener('change', function () {
        ciE.value = this.value;
        coE.value = this.value;
        const has = Boolean(this.value);
        ci.disabled = !has;
        co.disabled = !has;
    });
</script>
@endpush
