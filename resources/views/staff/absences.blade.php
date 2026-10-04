@extends('layouts.app')

@section('title', 'My Absences & Report Absence — TT Dental Lab')
@section('header_title', 'Report Absence')

@push('styles')
<style>
    .absence-container {
        display: grid;
        grid-template-columns: 380px 1fr;
        gap: 18px;
        align-items: start;
    }
    @media (max-width: 980px) {
        .absence-container {
            grid-template-columns: 1fr;
        }
    }
    .absence-tile {
        background: #ffffff;
        border: 1px solid var(--card-border);
        border-radius: 12px;
        padding: 14px 16px;
        margin-bottom: 12px;
        transition: box-shadow 0.18s ease, border-color 0.18s ease;
    }
    .absence-tile:hover {
        border-color: #cbd5e1;
        box-shadow: 0 4px 12px rgba(0,0,0,0.04);
    }
    .absence-tile-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
        margin-bottom: 8px;
        flex-wrap: wrap;
    }
    .absence-tile-date {
        font-size: 14px;
        font-weight: 800;
        color: var(--text-head);
    }
    .absence-tile-sub {
        font-size: 11px;
        color: var(--text-muted);
        margin-top: 1px;
    }
    .absence-reason-box {
        font-size: 12.5px;
        color: var(--text-body);
        background: #f8fafc;
        border-radius: 8px;
        padding: 8px 12px;
        margin-bottom: 8px;
        border-left: 3px solid #6366f1;
        line-height: 1.45;
    }
    .badge-reason-sick { background: #fef3c7; color: #b45309; }
    .badge-reason-emergency { background: #fee2e2; color: #b91c1c; }
    .badge-reason-annual { background: #e0f2fe; color: #0369a1; }
    .badge-reason-personal { background: #eff6ff; color: #1d4ed8; }
    .badge-reason-family { background: #ede9fe; color: #6d28d9; }
    .badge-reason-unpaid { background: #f1f5f9; color: #475569; }
    .badge-reason-medical { background: #dcfce7; color: #15803d; }
    .badge-reason-hospital { background: #fce7f3; color: #be185d; }
    .badge-reason-other { background: #f3f4f6; color: #374151; }
</style>
@endpush

@section('content')

{{-- Page Header --}}
<div class="page-header" style="margin-bottom: 16px;">
    <div>
        <div class="page-header-title">Report Absence</div>
        <div class="page-header-sub">
            Notify management of your absence reasons, personal matters, or emergency leaves
            @if($employee)
                • <strong style="color: var(--text-head);">{{ $employee->employee_id }} — {{ $employee->name }}</strong>
            @endif
        </div>
    </div>
    <div class="page-header-actions">
        <a href="{{ route('staff.dashboard') }}" class="btn btn-outline btn-sm">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Dashboard
        </a>
        <a href="{{ route('staff.mc.index') }}" class="btn btn-outline btn-sm">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            Submit Medical Certificate (MC)
        </a>
    </div>
</div>

@php
    $totalAbsences = $records->count();
    $thisMonthAbsences = $records->filter(function($item) {
        $d = $item->absence_date instanceof \DateTimeInterface
            ? $item->absence_date
            : \Carbon\Carbon::parse($item->absence_date);
        return $d->isCurrentMonth();
    })->count();
@endphp

{{-- Compact Stats Row --}}
<div class="stat-grid" style="margin-bottom: 16px;">
    <div class="stat-card stat-blue" style="padding: 12px 16px;">
        <div class="stat-icon" style="width: 36px; height: 36px;">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
        </div>
        <div class="stat-info">
            <div class="stat-label">Total Reported</div>
            <div class="stat-value" style="font-size: 22px;">{{ $totalAbsences }}</div>
            <div class="stat-sub">Your submitted records</div>
        </div>
    </div>

    <div class="stat-card stat-amber" style="padding: 12px 16px;">
        <div class="stat-icon" style="width: 36px; height: 36px;">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        </div>
        <div class="stat-info">
            <div class="stat-label">This Month</div>
            <div class="stat-value" style="font-size: 22px;">{{ $thisMonthAbsences }}</div>
            <div class="stat-sub">Logged in {{ now()->format('F Y') }}</div>
        </div>
    </div>

    <div class="stat-card stat-green" style="padding: 12px 16px;">
        <div class="stat-icon" style="width: 36px; height: 36px;">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        </div>
        <div class="stat-info">
            <div class="stat-label">Documentation</div>
            <div class="stat-value" style="font-size: 22px;">100%</div>
            <div class="stat-sub">Official self-reporting</div>
        </div>
    </div>
</div>

<div class="absence-container">

    {{-- Form Column: Report Absence --}}
    <div class="card" style="padding: 18px 20px; border-radius: 14px;">
        <div class="card-header" style="margin-bottom: 14px;">
            <div>
                <div class="card-title">Submit Absence Notice</div>
                <div class="card-subtitle">Notify laboratory administrators of your absence</div>
            </div>
        </div>

        @if(!$employee)
            <div class="alert-box alert-error" style="margin-bottom: 14px;">
                Your account is not yet linked to an employee profile. Please contact an administrator.
            </div>
        @else
            <form action="{{ route('staff.absences.store') }}" method="POST">
                @csrf

                <!-- Absence Date -->
                <div class="form-group" style="margin-bottom: 12px;">
                    <label class="form-label" for="absence_date">
                        Absence Date <span class="req">*</span>
                    </label>
                    <input
                        type="date"
                        id="absence_date"
                        name="absence_date"
                        value="{{ old('absence_date', now()->format('Y-m-d')) }}"
                        required
                        class="form-input"
                    >
                    <div class="form-hint">Select the day you are unable to attend work.</div>
                </div>

                <!-- Reason for Absence -->
                <div class="form-group" style="margin-bottom: 12px;">
                    <label class="form-label" for="absence_reason">
                        Reason for Absence <span class="req">*</span>
                    </label>
                    <select id="absence_reason" name="reason" required class="form-select">
                        <option value="">-- Choose Absence Reason --</option>
                        <option value="Sick Leave (Uncertified)" {{ old('reason') == 'Sick Leave (Uncertified)' ? 'selected' : '' }}>Sick Leave (Uncertified)</option>
                        <option value="Emergency Leave" {{ old('reason') == 'Emergency Leave' ? 'selected' : '' }}>Emergency Leave</option>
                        <option value="Annual Leave" {{ old('reason') == 'Annual Leave' ? 'selected' : '' }}>Annual Leave</option>
                        <option value="Personal Matter" {{ old('reason') == 'Personal Matter' ? 'selected' : '' }}>Personal Matter</option>
                        <option value="Family Matter" {{ old('reason') == 'Family Matter' ? 'selected' : '' }}>Family Matter</option>
                        <option value="Unpaid Leave" {{ old('reason') == 'Unpaid Leave' ? 'selected' : '' }}>Unpaid Leave</option>
                        <option value="Medical Appointment" {{ old('reason') == 'Medical Appointment' ? 'selected' : '' }}>Medical Appointment</option>
                        <option value="Hospitalization" {{ old('reason') == 'Hospitalization' ? 'selected' : '' }}>Hospitalization</option>
                        <option value="Other Reason" {{ old('reason') == 'Other Reason' ? 'selected' : '' }}>Other Reason</option>
                    </select>
                </div>

                <!-- Optional Remarks -->
                <div class="form-group" style="margin-bottom: 14px;">
                    <label class="form-label" for="absence_remarks">
                        Optional Remarks / Details
                    </label>
                    <textarea
                        id="absence_remarks"
                        name="remarks"
                        rows="3"
                        class="form-textarea"
                        placeholder="Provide any additional details or handover notes..."
                    >{{ old('remarks') }}</textarea>
                </div>

                <!-- MC Tip Notice -->
                <div style="background: #eff6ff; border: 1px solid #bfdbfe; border-radius: 8px; padding: 9px 11px; margin-bottom: 14px; font-size: 11.5px; color: #1e40af; display: flex; align-items: flex-start; gap: 7px;">
                    <svg width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="flex-shrink:0; margin-top:1px;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span>Have a doctor's clinic note? Please submit through the <a href="{{ route('staff.mc.index') }}" style="color: #2563eb; font-weight: 700; text-decoration: underline;">Medical Certificate page</a> instead.</span>
                </div>

                <!-- Submit Button -->
                <button type="submit" class="btn btn-primary" style="width: 100%; justify-content: center; padding: 10px 14px;">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                    Submit Absence Report
                </button>
            </form>
        @endif
    </div>

    {{-- Records Column: My Absence Records --}}
    <div class="card" style="padding: 18px 20px; border-radius: 14px;">
        <div class="card-header" style="margin-bottom: 14px;">
            <div>
                <div class="card-title">My Absence Records ({{ $records->count() }})</div>
                <div class="card-subtitle">Your documented absence history and logged justifications</div>
            </div>
        </div>

        @if($records->count() > 0)
            @foreach($records as $rec)
                @php
                    $d = $rec->absence_date instanceof \DateTimeInterface
                        ? $rec->absence_date
                        : \Carbon\Carbon::parse($rec->absence_date);
                    $isPast = $d->isPast() && !$d->isToday();
                    $isToday = $d->isToday();

                    $rLower = strtolower($rec->reason ?? '');
                    $badgeStyle = 'badge-reason-other';
                    if (str_contains($rLower, 'sick')) $badgeStyle = 'badge-reason-sick';
                    elseif (str_contains($rLower, 'emergency')) $badgeStyle = 'badge-reason-emergency';
                    elseif (str_contains($rLower, 'annual')) $badgeStyle = 'badge-reason-annual';
                    elseif (str_contains($rLower, 'personal')) $badgeStyle = 'badge-reason-personal';
                    elseif (str_contains($rLower, 'family')) $badgeStyle = 'badge-reason-family';
                    elseif (str_contains($rLower, 'unpaid')) $badgeStyle = 'badge-reason-unpaid';
                    elseif (str_contains($rLower, 'medical')) $badgeStyle = 'badge-reason-medical';
                    elseif (str_contains($rLower, 'hospital')) $badgeStyle = 'badge-reason-hospital';
                @endphp
                <div class="absence-tile">
                    <div class="absence-tile-header">
                        <div>
                            <div class="absence-tile-date">
                                {{ $d->format('d F Y') }} ({{ $d->format('l') }})
                                @if($isToday)
                                    <span style="font-size: 10px; font-weight: 800; background: #eff6ff; color: #2563eb; padding: 1px 6px; border-radius: 4px; margin-left: 4px;">TODAY</span>
                                @elseif(!$isPast)
                                    <span style="font-size: 10px; font-weight: 800; background: #f0fdf4; color: #15803d; padding: 1px 6px; border-radius: 4px; margin-left: 4px;">UPCOMING</span>
                                @endif
                            </div>
                            <div class="absence-tile-sub">Submitted: {{ $rec->created_at ? $rec->created_at->format('d M Y, h:i A') : 'Recorded' }}</div>
                        </div>

                        <div style="display: flex; align-items: center; gap: 8px;">
                            <span class="badge {{ $badgeStyle }}" style="font-size: 11px; padding: 3px 8px; border-radius: 6px;">
                                {{ $rec->reason }}
                            </span>

                            <form action="{{ route('staff.absences.destroy', $rec->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to remove this absence notice for {{ $d->format('d M Y') }}?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-ghost btn-sm" style="color: var(--danger); padding: 4px 6px;" title="Remove this absence record">
                                    <svg width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </form>
                        </div>
                    </div>

                    @if($rec->remarks)
                        <div class="absence-reason-box">
                            <strong style="color: var(--text-head); display: block; font-size: 11.5px; margin-bottom: 2px;">Remarks:</strong>
                            {{ $rec->remarks }}
                        </div>
                    @endif

                    <div style="display: flex; align-items: center; justify-content: space-between; font-size: 11.5px; color: var(--text-muted); margin-top: 4px;">
                        <span style="display: inline-flex; align-items: center; gap: 4px; color: var(--success); font-weight: 700;">
                            <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                            Self-Reported & Documented
                        </span>
                        <span>Record #{{ $rec->id }}</span>
                    </div>
                </div>
            @endforeach
        @else
            <div class="table-empty" style="padding: 36px 16px;">
                <svg width="40" height="40" fill="none" stroke="#94a3b8" viewBox="0 0 24 24" style="margin: 0 auto 10px;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                <div style="font-weight: 700; color: var(--text-head); font-size: 14px;">No absence records submitted yet</div>
                <div style="color: var(--text-muted); font-size: 12.5px; margin-top: 3px;">
                    Use the form on the left to submit an absence notice if you are unable to attend work.
                </div>
            </div>
        @endif
    </div>

</div>

@endsection
