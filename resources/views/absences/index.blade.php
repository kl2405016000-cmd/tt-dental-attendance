@extends('layouts.app')

@section('title', 'Absence Management — TT Dental Lab')
@section('header_title', 'Absence Management')

@section('content')

{{-- Page Header --}}
<div class="page-header" style="margin-bottom: 16px;">
    <div>
        <div class="page-header-title">Absence Management</div>
        <div class="page-header-sub">
            Review company-wide employee absence reports, justifications, and daily attendance logs
        </div>
    </div>
    <div class="page-header-actions">
        <a href="{{ route('admin.dashboard') }}" class="btn btn-outline btn-sm">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Dashboard
        </a>
        <button type="button" class="btn btn-primary btn-sm" onclick="toggleAdminOverrideForm()">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Record Admin Override
        </button>
    </div>
</div>

@php
    $absentCount = $absentEmployees->count();
    $recordedCount = 0;
    foreach ($absentEmployees as $emp) {
        if (isset($absenceRecords[$emp->id])) {
            $recordedCount++;
        }
    }
    $unjustifiedCount = $absentCount - $recordedCount;
    $totalAllRecords = $allAbsences->count();
@endphp

{{-- Compact Stats Grid --}}
<div class="stat-grid" style="margin-bottom: 16px;">
    <div class="stat-card stat-purple" style="padding: 12px 16px;">
        <div class="stat-icon" style="width: 36px; height: 36px;">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
        </div>
        <div class="stat-info">
            <div class="stat-label">Total Records</div>
            <div class="stat-value" style="font-size: 22px;">{{ $totalAllRecords }}</div>
            <div class="stat-sub">All employee absence logs</div>
        </div>
    </div>

    <div class="stat-card stat-red" style="padding: 12px 16px;">
        <div class="stat-icon" style="width: 36px; height: 36px;">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        </div>
        <div class="stat-info">
            <div class="stat-label">Absent on Date</div>
            <div class="stat-value" style="font-size: 22px;">{{ $absentCount }}</div>
            <div class="stat-sub">No clock-in for {{ $date->format('d M') }}</div>
        </div>
    </div>

    <div class="stat-card stat-green" style="padding: 12px 16px;">
        <div class="stat-icon" style="width: 36px; height: 36px;">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        </div>
        <div class="stat-info">
            <div class="stat-label">Justified Absences</div>
            <div class="stat-value" style="font-size: 22px;">{{ $recordedCount }}</div>
            <div class="stat-sub">Reasons documented</div>
        </div>
    </div>

    <div class="stat-card stat-amber" style="padding: 12px 16px;">
        <div class="stat-icon" style="width: 36px; height: 36px;">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
        </div>
        <div class="stat-info">
            <div class="stat-label">Unjustified / Pending</div>
            <div class="stat-value" style="font-size: 22px;">{{ $unjustifiedCount }}</div>
            <div class="stat-sub">Awaiting reason report</div>
        </div>
    </div>
</div>

{{-- Secondary Collapsible: Admin Override / Manual Justification Form --}}
<div class="card" id="adminRecordSection" style="display: none; border-left: 4px solid var(--primary); padding: 18px 20px; border-radius: 14px; margin-bottom: 16px; background: #fafbff;">
    <div class="card-header" style="margin-bottom: 12px;">
        <div>
            <div class="card-title">Record Administrative Absence Override</div>
            <div class="card-subtitle">Assign an official reason or HR override on behalf of an employee</div>
        </div>
        <button type="button" class="btn btn-ghost btn-sm" onclick="toggleAdminOverrideForm()">✕ Close</button>
    </div>

    <form method="POST" action="{{ route('absences.store') }}" style="max-width: 650px;">
        @csrf

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom: 12px;">
            <div class="form-group" style="margin-bottom: 0;">
                <label class="form-label" for="absent_employee_select">
                    Employee <span class="req">*</span>
                </label>
                <select name="employee_id" id="absent_employee_select" required class="form-select">
                    <option value="">-- Choose Employee --</option>
                    @foreach($employees as $emp)
                        <option value="{{ $emp->id }}">
                            {{ $emp->employee_id }} — {{ $emp->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="form-group" style="margin-bottom: 0;">
                <label class="form-label" for="absence_date_input">
                    Absence Date <span class="req">*</span>
                </label>
                <input
                    type="date"
                    id="absence_date_input"
                    name="absence_date"
                    value="{{ $date->format('Y-m-d') }}"
                    required
                    class="form-input"
                >
            </div>
        </div>

        <div class="form-group" style="margin-bottom: 12px;">
            <label class="form-label" for="absence_reason">
                Reason for Absence <span class="req">*</span>
            </label>
            <select name="reason" id="absence_reason" required class="form-select">
                <option value="">-- Select Reason --</option>
                <option value="Sick Leave (Uncertified)">Sick Leave (Uncertified)</option>
                <option value="Emergency Leave">Emergency Leave</option>
                <option value="Annual Leave">Annual Leave</option>
                <option value="Personal Matter">Personal Matter</option>
                <option value="Family Matter">Family Matter</option>
                <option value="Unpaid Leave">Unpaid Leave</option>
                <option value="Medical Appointment">Medical Appointment</option>
                <option value="Hospitalization">Hospitalization</option>
                <option value="Other Reason">Other Reason</option>
            </select>
        </div>

        <div class="form-group" style="margin-bottom: 14px;">
            <label class="form-label" for="absence_remarks">
                Administrative Remarks (Optional)
            </label>
            <textarea
                name="remarks"
                id="absence_remarks"
                placeholder="Enter administrative notes, call-in record, or justification details..."
                rows="2"
                class="form-textarea"
            ></textarea>
        </div>

        <div style="display: flex; gap: 8px;">
            <button type="submit" class="btn btn-primary btn-sm">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                Save Record
            </button>
            <button type="button" class="btn btn-outline btn-sm" onclick="toggleAdminOverrideForm()">Cancel</button>
        </div>
    </form>
</div>

{{-- SECTION 1: ALL SUBMITTED ABSENCE RECORDS (COMPANY-WIDE) --}}
<div class="card" style="padding: 18px 20px; border-radius: 14px; margin-bottom: 18px;">
    <div class="card-header" style="margin-bottom: 14px;">
        <div>
            <div class="card-title">All Submitted Absence Records ({{ $allAbsences->count() }})</div>
            <div class="card-subtitle">Complete log of all absence justifications submitted by staff or recorded by administrators</div>
        </div>
    </div>

    <div class="table-wrap">
        @if($allAbsences->count() > 0)
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Employee</th>
                        <th>Department / Position</th>
                        <th>Reason for Absence</th>
                        <th>Remarks / Details</th>
                        <th>Submitted At</th>
                        <th style="text-align: right;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($allAbsences as $record)
                        @php
                            $d = $record->absence_date instanceof \DateTimeInterface
                                ? $record->absence_date
                                : \Carbon\Carbon::parse($record->absence_date);
                            $rLower = strtolower($record->reason ?? '');
                            $badgeClass = 'badge-gray';
                            if (str_contains($rLower, 'sick')) $badgeClass = 'badge-amber';
                            elseif (str_contains($rLower, 'emergency')) $badgeClass = 'badge-red';
                            elseif (str_contains($rLower, 'annual')) $badgeClass = 'badge-blue';
                            elseif (str_contains($rLower, 'medical')) $badgeClass = 'badge-green';
                            elseif (str_contains($rLower, 'hospital')) $badgeClass = 'badge-purple';
                        @endphp
                        <tr>
                            <td class="td-bold" style="white-space: nowrap;">
                                {{ $d->format('d M Y') }}
                                <div style="font-size: 11px; color: var(--text-muted); font-weight: 500;">{{ $d->format('l') }}</div>
                            </td>
                            <td>
                                @if($record->employee)
                                    <div style="font-weight: 700; color: var(--text-head);">{{ $record->employee->name }}</div>
                                    <div style="font-size: 11px; color: var(--primary); font-family: monospace;">{{ $record->employee->employee_id }}</div>
                                @else
                                    <span class="td-muted">Employee #{{ $record->employee_id }}</span>
                                @endif
                            </td>
                            <td class="td-muted">
                                <div>{{ $record->employee?->position ?? 'Staff' }}</div>
                                <div style="font-size: 11px; color: var(--text-faint);">{{ $record->employee?->department ?? 'Laboratory' }}</div>
                            </td>
                            <td>
                                <span class="badge {{ $badgeClass }}" style="font-size: 11.5px; padding: 3px 8px;">
                                    {{ $record->reason }}
                                </span>
                            </td>
                            <td>
                                @if($record->remarks)
                                    <div style="font-size: 12px; color: var(--text-body); max-width: 320px; line-height: 1.4;">
                                        {{ $record->remarks }}
                                    </div>
                                @else
                                    <span style="color: var(--text-faint); font-style: italic; font-size: 11.5px;">No remarks</span>
                                @endif
                            </td>
                            <td class="td-muted" style="white-space: nowrap; font-size: 11.5px;">
                                {{ $record->created_at ? $record->created_at->format('d M Y, h:i A') : 'Recorded' }}
                            </td>
                            <td style="text-align: right; white-space: nowrap;">
                                <form
                                    action="{{ route('absences.destroy', $record->id) }}"
                                    method="POST"
                                    style="display: inline-block;"
                                    onsubmit="return confirm('Remove absence record for {{ addslashes($record->employee?->name ?? 'this employee') }} on {{ $d->format('d M Y') }}?');"
                                >
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline btn-sm" style="color: var(--danger); border-color: rgba(220,38,38,0.3); padding: 4px 8px;" title="Remove this absence record">
                                        <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        Delete
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <div class="table-empty" style="padding: 30px;">
                <svg width="36" height="36" fill="none" stroke="#94a3b8" viewBox="0 0 24 24" style="margin: 0 auto 8px;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                <div style="font-weight: 700; color: var(--text-head); font-size: 13.5px;">No absence records logged in system</div>
                <div style="color: var(--text-muted); font-size: 12px; margin-top: 2px;">
                    Staff members can submit their absence reports from the Staff Portal, or administrators can record overrides above.
                </div>
            </div>
        @endif
    </div>
</div>

{{-- SECTION 2: DAILY ABSENCE INSPECTION (BY DATE) --}}
<div class="card" style="padding: 18px 20px; border-radius: 14px;">
    <div class="card-header" style="margin-bottom: 12px; flex-wrap: wrap;">
        <div>
            <div class="card-title">Daily Absence Inspection — {{ $date->format('d F Y (l)') }}</div>
            <div class="card-subtitle">Roster of employees who did not clock in on this date</div>
        </div>

        <form method="GET" action="{{ route('absences.index') }}" style="display: flex; gap: 8px; align-items: center; flex-wrap: wrap;">
            <input
                type="date"
                id="absence_inspect_date"
                name="date"
                value="{{ $date->format('Y-m-d') }}"
                class="form-input"
                style="height: 34px; padding: 4px 10px; font-size: 12.5px; width: 160px;"
            >
            <button type="submit" class="btn btn-outline btn-sm">
                Inspect Date
            </button>
            @if(!$date->isToday())
                <a href="{{ route('absences.index') }}" class="btn btn-ghost btn-sm">
                    Today
                </a>
            @endif
        </form>
    </div>

    <div class="table-wrap">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Employee ID</th>
                    <th>Name</th>
                    <th>Position / Dept</th>
                    <th>Attendance Status</th>
                    <th>Justification Status</th>
                    <th>Reason & Remarks</th>
                    <th style="text-align: right;">Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($absentEmployees as $employee)
                    <tr>
                        <td class="td-mono td-bold" style="color: var(--primary);">
                            {{ $employee->employee_id }}
                        </td>
                        <td class="td-bold">
                            {{ $employee->name }}
                        </td>
                        <td class="td-muted">
                            {{ $employee->position ?? 'Dental Staff' }}
                        </td>
                        <td>
                            <span class="badge badge-red">
                                NO CLOCK-IN
                            </span>
                        </td>
                        <td>
                            @if(isset($absenceRecords[$employee->id]))
                                <span class="badge badge-green">
                                    ✓ JUSTIFIED
                                </span>
                            @else
                                <span class="badge badge-amber">
                                    UNJUSTIFIED
                                </span>
                            @endif
                        </td>
                        <td>
                            @if(isset($absenceRecords[$employee->id]))
                                <div style="font-weight: 700; color: var(--text-head); font-size: 12.5px;">
                                    {{ $absenceRecords[$employee->id]->reason }}
                                </div>
                                @if($absenceRecords[$employee->id]->remarks)
                                    <div style="font-size: 11px; color: var(--text-muted); margin-top: 1px;">
                                        {{ $absenceRecords[$employee->id]->remarks }}
                                    </div>
                                @endif
                            @else
                                <span style="color: var(--text-faint); font-style: italic; font-size: 11.5px;">
                                    No reason submitted by staff
                                </span>
                            @endif
                        </td>
                        <td style="text-align: right; white-space: nowrap;">
                            @if(isset($absenceRecords[$employee->id]))
                                <form
                                    action="{{ route('absences.destroy', $absenceRecords[$employee->id]->id) }}"
                                    method="POST"
                                    style="display: inline-block;"
                                    onsubmit="return confirm('Remove absence justification for {{ addslashes($employee->name) }}?');"
                                >
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline btn-sm" style="color: var(--danger); border-color: rgba(220,38,38,0.3); padding: 3px 8px;">
                                        Remove
                                    </button>
                                </form>
                            @else
                                <button type="button" class="btn btn-outline btn-sm" onclick="openOverrideForEmployee('{{ $employee->id }}', '{{ $date->format('Y-m-d') }}')">
                                    Record Override
                                </button>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="table-empty" style="padding: 24px;">
                            <div style="display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 6px;">
                                <svg width="32" height="32" fill="none" stroke="currentColor" style="color: var(--success); opacity: 0.85;" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                <div style="font-weight: 700; font-size: 13.5px; color: var(--success);">All active employees are present on {{ $date->format('d M Y') }}!</div>
                                <div style="font-size: 11.5px; color: var(--text-muted);">
                                    Every active staff member has clocked in for this day.
                                </div>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<script>
function toggleAdminOverrideForm() {
    const sec = document.getElementById('adminRecordSection');
    if (sec.style.display === 'none' || sec.style.display === '') {
        sec.style.display = 'block';
        sec.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    } else {
        sec.style.display = 'none';
    }
}

function openOverrideForEmployee(empId, dateStr) {
    const sec = document.getElementById('adminRecordSection');
    sec.style.display = 'block';
    const select = document.getElementById('absent_employee_select');
    if (select) select.value = empId;
    const dateInput = document.getElementById('absence_date_input');
    if (dateInput && dateStr) dateInput.value = dateStr;
    sec.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
}
</script>

@endsection
