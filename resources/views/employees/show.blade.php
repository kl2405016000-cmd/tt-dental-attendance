@extends('layouts.app')
@section('title', $employee->name . ' — Staff Biodata Profile')
@section('header_title', 'Staff Biodata Profile')

@section('content')
<div style="max-width: 1000px; margin: 0 auto;">

    {{-- Top Header / Profile Card --}}
    <div class="card" style="margin-bottom: 24px;">
        <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 16px;">
            <div style="display: flex; align-items: center; gap: 20px;">
                <div style="width: 80px; height: 80px; border-radius: 16px; overflow: hidden; border: 3px solid #e2e8f0; background: linear-gradient(135deg,#4f46e5,#7c3aed); flex-shrink: 0; box-shadow: 0 4px 12px rgba(0,0,0,0.08);">
                    <img src="{{ $employee->profile_photo_url }}" alt="{{ $employee->name }}" style="width: 100%; height: 100%; object-fit: cover;" onerror="this.style.display='none'; this.parentElement.style.display='flex'; this.parentElement.style.alignItems='center'; this.parentElement.style.justifyContent='center'; this.parentElement.style.color='white'; this.parentElement.style.fontWeight='800'; this.parentElement.style.fontSize='28px'; this.parentElement.textContent='{{ strtoupper(substr($employee->name, 0, 1)) }}';">
                </div>
                <div>
                    <div style="display: flex; align-items: center; gap: 10px; flex-wrap: wrap;">
                        <h2 style="font-size: 22px; font-weight: 800; color: var(--text-head); margin: 0;">{{ $employee->name }}</h2>
                        <span class="badge {{ $employee->status ? 'badge-green' : 'badge-red' }}">
                            {{ $employee->status ? 'Active Staff' : 'Inactive' }}
                        </span>
                        @if($employee->has_face_registered)
                            <span class="badge badge-green">
                                <svg width="10" height="10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                Face Registered
                            </span>
                        @else
                            <span class="badge badge-amber">Face Not Registered</span>
                        @endif
                    </div>
                    <div style="display: flex; align-items: center; gap: 12px; margin-top: 6px; font-size: 13.5px; color: var(--text-muted); flex-wrap: wrap;">
                        <span style="font-weight: 700; color: var(--primary);">ID: {{ $employee->employee_id }}</span>
                        <span>•</span>
                        <span>{{ $employee->position ?? 'No Position Set' }}</span>
                        <span>•</span>
                        <span>{{ $employee->department ?? 'No Department' }}</span>
                    </div>
                </div>
            </div>

            <div style="display: flex; align-items: center; gap: 10px; flex-wrap: wrap;">
                <a href="{{ route('employees.edit', $employee) }}" class="btn btn-primary">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                    Edit Biodata
                </a>
                <a href="{{ route('face-attendance') }}?employee_id={{ $employee->id }}" class="btn" style="background: var(--purple-light); color: var(--purple); border: 1px solid rgba(124,58,237,0.25);">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="8" r="4" stroke-width="2"/><path stroke-linecap="round" stroke-linejoin="round" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/></svg>
                    Face Registration
                </a>
                <a href="{{ route('employees.index') }}" class="btn btn-outline">← All Employees</a>
            </div>
        </div>
    </div>

    {{-- Main 4 Sections Grid --}}
    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 24px;">

        {{-- 1. PERSONAL INFORMATION --}}
        <div class="card" style="height: 100%;">
            <div class="card-header" style="padding-bottom: 12px; border-bottom: 1px solid #f1f5f9;">
                <div style="display: flex; align-items: center; gap: 8px;">
                    <div style="width: 28px; height: 28px; border-radius: 8px; background: #e0f2fe; color: #0284c7; display: flex; align-items: center; justify-content: center;">
                        <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    </div>
                    <div style="font-weight: 800; font-size: 14.5px; color: var(--text-head);">1. Personal Information</div>
                </div>
            </div>

            <div style="padding-top: 14px; display: flex; flex-direction: column; gap: 12px;">
                <div style="display: flex; justify-content: space-between; align-items: flex-start; padding-bottom: 8px; border-bottom: 1px dashed #f1f5f9;">
                    <span style="font-size: 12.5px; font-weight: 600; color: var(--text-muted);">Employee ID</span>
                    <span style="font-size: 13.5px; font-weight: 700; color: var(--primary);">{{ $employee->employee_id }}</span>
                </div>
                <div style="display: flex; justify-content: space-between; align-items: flex-start; padding-bottom: 8px; border-bottom: 1px dashed #f1f5f9;">
                    <span style="font-size: 12.5px; font-weight: 600; color: var(--text-muted);">Full Name</span>
                    <span style="font-size: 13.5px; font-weight: 700; color: var(--text-head);">{{ $employee->name }}</span>
                </div>
                <div style="display: flex; justify-content: space-between; align-items: flex-start; padding-bottom: 8px; border-bottom: 1px dashed #f1f5f9;">
                    <span style="font-size: 12.5px; font-weight: 600; color: var(--text-muted);">IC / Passport Number</span>
                    <span style="font-size: 13.5px; font-weight: 600; color: var(--text-body);">{{ $employee->ic_passport_number ?: '—' }}</span>
                </div>
                <div style="display: flex; justify-content: space-between; align-items: flex-start; padding-bottom: 8px; border-bottom: 1px dashed #f1f5f9;">
                    <span style="font-size: 12.5px; font-weight: 600; color: var(--text-muted);">Date of Birth</span>
                    <span style="font-size: 13.5px; font-weight: 600; color: var(--text-body);">
                        {{ $employee->date_of_birth ? ($employee->date_of_birth instanceof \DateTimeInterface ? $employee->date_of_birth->format('d M Y') : \Carbon\Carbon::parse($employee->date_of_birth)->format('d M Y')) : '—' }}
                    </span>
                </div>
                <div style="display: flex; justify-content: space-between; align-items: flex-start; padding-bottom: 8px; border-bottom: 1px dashed #f1f5f9;">
                    <span style="font-size: 12.5px; font-weight: 600; color: var(--text-muted);">Gender</span>
                    <span style="font-size: 13.5px; font-weight: 600; color: var(--text-body);">{{ $employee->gender ?: '—' }}</span>
                </div>
                <div style="display: flex; justify-content: space-between; align-items: flex-start; padding-bottom: 8px; border-bottom: 1px dashed #f1f5f9;">
                    <span style="font-size: 12.5px; font-weight: 600; color: var(--text-muted);">Email Address</span>
                    <span style="font-size: 13.5px; font-weight: 600; color: var(--text-body);">{{ $employee->email ?: '—' }}</span>
                </div>
                <div style="display: flex; justify-content: space-between; align-items: flex-start; padding-bottom: 8px; border-bottom: 1px dashed #f1f5f9;">
                    <span style="font-size: 12.5px; font-weight: 600; color: var(--text-muted);">Phone Number</span>
                    <span style="font-size: 13.5px; font-weight: 600; color: var(--text-body);">{{ $employee->phone ?: '—' }}</span>
                </div>
                <div style="display: flex; flex-direction: column; gap: 4px;">
                    <span style="font-size: 12.5px; font-weight: 600; color: var(--text-muted);">Home Address</span>
                    <span style="font-size: 13px; font-weight: 500; color: var(--text-body); line-height: 1.5; background: #f8fafc; padding: 8px 10px; border-radius: 6px; border: 1px solid #e2e8f0;">
                        {{ $employee->address ?: 'No home address provided.' }}
                    </span>
                </div>
            </div>
        </div>

        {{-- 2. EMPLOYMENT INFORMATION --}}
        <div class="card" style="height: 100%;">
            <div class="card-header" style="padding-bottom: 12px; border-bottom: 1px solid #f1f5f9;">
                <div style="display: flex; align-items: center; gap: 8px;">
                    <div style="width: 28px; height: 28px; border-radius: 8px; background: #ede9fe; color: #7c3aed; display: flex; align-items: center; justify-content: center;">
                        <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                    </div>
                    <div style="font-weight: 800; font-size: 14.5px; color: var(--text-head);">2. Employment Information</div>
                </div>
            </div>

            <div style="padding-top: 14px; display: flex; flex-direction: column; gap: 12px;">
                <div style="display: flex; justify-content: space-between; align-items: flex-start; padding-bottom: 8px; border-bottom: 1px dashed #f1f5f9;">
                    <span style="font-size: 12.5px; font-weight: 600; color: var(--text-muted);">Position / Role</span>
                    <span style="font-size: 13.5px; font-weight: 700; color: var(--text-head);">{{ $employee->position ?: '—' }}</span>
                </div>
                <div style="display: flex; justify-content: space-between; align-items: flex-start; padding-bottom: 8px; border-bottom: 1px dashed #f1f5f9;">
                    <span style="font-size: 12.5px; font-weight: 600; color: var(--text-muted);">Department</span>
                    <span style="font-size: 13.5px; font-weight: 600; color: var(--text-body);">{{ $employee->department ?: '—' }}</span>
                </div>
                <div style="display: flex; justify-content: space-between; align-items: flex-start; padding-bottom: 8px; border-bottom: 1px dashed #f1f5f9;">
                    <span style="font-size: 12.5px; font-weight: 600; color: var(--text-muted);">Employment Status</span>
                    <span style="font-size: 13px; font-weight: 700; color: #0284c7; background: #e0f2fe; padding: 2px 10px; border-radius: 9999px;">
                        {{ $employee->employment_status ?: 'Full-Time' }}
                    </span>
                </div>
                <div style="display: flex; justify-content: space-between; align-items: flex-start; padding-bottom: 8px; border-bottom: 1px dashed #f1f5f9;">
                    <span style="font-size: 12.5px; font-weight: 600; color: var(--text-muted);">Date Joined</span>
                    <span style="font-size: 13.5px; font-weight: 600; color: var(--text-body);">
                        {{ $employee->date_joined ? ($employee->date_joined instanceof \DateTimeInterface ? $employee->date_joined->format('d M Y') : \Carbon\Carbon::parse($employee->date_joined)->format('d M Y')) : '—' }}
                    </span>
                </div>
                <div style="display: flex; justify-content: space-between; align-items: flex-start; padding-bottom: 8px; border-bottom: 1px dashed #f1f5f9;">
                    <span style="font-size: 12.5px; font-weight: 600; color: var(--text-muted);">Active Status</span>
                    <span class="badge {{ $employee->status ? 'badge-green' : 'badge-red' }}">
                        {{ $employee->status ? 'Active in System' : 'Inactive' }}
                    </span>
                </div>
            </div>
        </div>

        {{-- 3. EMERGENCY CONTACT --}}
        <div class="card" style="height: 100%;">
            <div class="card-header" style="padding-bottom: 12px; border-bottom: 1px solid #f1f5f9;">
                <div style="display: flex; align-items: center; gap: 8px;">
                    <div style="width: 28px; height: 28px; border-radius: 8px; background: #fee2e2; color: #dc2626; display: flex; align-items: center; justify-content: center;">
                        <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                    </div>
                    <div style="font-weight: 800; font-size: 14.5px; color: var(--text-head);">3. Emergency Contact</div>
                </div>
            </div>

            <div style="padding-top: 14px; display: flex; flex-direction: column; gap: 12px;">
                <div style="display: flex; justify-content: space-between; align-items: flex-start; padding-bottom: 8px; border-bottom: 1px dashed #f1f5f9;">
                    <span style="font-size: 12.5px; font-weight: 600; color: var(--text-muted);">Contact Name</span>
                    <span style="font-size: 13.5px; font-weight: 700; color: var(--text-head);">
                        {{ $employee->emergency_contact_name ?: '—' }}
                    </span>
                </div>
                <div style="display: flex; justify-content: space-between; align-items: flex-start; padding-bottom: 8px; border-bottom: 1px dashed #f1f5f9;">
                    <span style="font-size: 12.5px; font-weight: 600; color: var(--text-muted);">Relationship</span>
                    <span style="font-size: 13.5px; font-weight: 600; color: var(--text-body);">
                        {{ $employee->emergency_contact_relationship ?: '—' }}
                    </span>
                </div>
                <div style="display: flex; justify-content: space-between; align-items: flex-start; padding-bottom: 8px; border-bottom: 1px dashed #f1f5f9;">
                    <span style="font-size: 12.5px; font-weight: 600; color: var(--text-muted);">Contact Phone</span>
                    <span style="font-size: 13.5px; font-weight: 600; color: var(--primary);">
                        {{ $employee->emergency_contact_phone ?: '—' }}
                    </span>
                </div>
                <div style="display: flex; flex-direction: column; gap: 4px;">
                    <span style="font-size: 12.5px; font-weight: 600; color: var(--text-muted);">Contact Address</span>
                    <span style="font-size: 13px; font-weight: 500; color: var(--text-body); line-height: 1.5; background: #f8fafc; padding: 8px 10px; border-radius: 6px; border: 1px solid #e2e8f0;">
                        {{ $employee->emergency_contact_address ?: 'No emergency address provided.' }}
                    </span>
                </div>
            </div>
        </div>

        {{-- 4. SYSTEM INFORMATION --}}
        <div class="card" style="height: 100%;">
            <div class="card-header" style="padding-bottom: 12px; border-bottom: 1px solid #f1f5f9;">
                <div style="display: flex; align-items: center; gap: 8px;">
                    <div style="width: 28px; height: 28px; border-radius: 8px; background: #dcfce7; color: #16a34a; display: flex; align-items: center; justify-content: center;">
                        <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z"/></svg>
                    </div>
                    <div style="font-weight: 800; font-size: 14.5px; color: var(--text-head);">4. System Information</div>
                </div>
            </div>

            <div style="padding-top: 14px; display: flex; flex-direction: column; gap: 12px;">
                <div style="display: flex; justify-content: space-between; align-items: flex-start; padding-bottom: 8px; border-bottom: 1px dashed #f1f5f9;">
                    <span style="font-size: 12.5px; font-weight: 600; color: var(--text-muted);">Login Email</span>
                    <span style="font-size: 13.5px; font-weight: 600; color: var(--text-head);">
                        {{ $employee->user ? $employee->user->email : 'No linked login account' }}
                    </span>
                </div>
                <div style="display: flex; justify-content: space-between; align-items: flex-start; padding-bottom: 8px; border-bottom: 1px dashed #f1f5f9;">
                    <span style="font-size: 12.5px; font-weight: 600; color: var(--text-muted);">Account Role</span>
                    <span style="font-size: 13px; font-weight: 700; color: #7c3aed; background: #ede9fe; padding: 2px 10px; border-radius: 9999px;">
                        {{ $employee->user ? ucfirst($employee->user->role) : 'N/A' }}
                    </span>
                </div>
                <div style="display: flex; justify-content: space-between; align-items: flex-start; padding-bottom: 8px; border-bottom: 1px dashed #f1f5f9;">
                    <span style="font-size: 12.5px; font-weight: 600; color: var(--text-muted);">Face Registration Status</span>
                    @if($employee->has_face_registered)
                        <span class="badge badge-green">✓ Registered</span>
                    @else
                        <span class="badge badge-amber">Not Registered</span>
                    @endif
                </div>
                <div style="display: flex; justify-content: space-between; align-items: flex-start; padding-bottom: 8px; border-bottom: 1px dashed #f1f5f9;">
                    <span style="font-size: 12.5px; font-weight: 600; color: var(--text-muted);">Account Status</span>
                    <span class="badge {{ $employee->status ? 'badge-green' : 'badge-red' }}">
                        {{ $employee->status ? 'Active' : 'Inactive' }}
                    </span>
                </div>
                <div style="margin-top: 6px; padding: 10px 12px; border-radius: 8px; background: #f8fafc; border: 1px solid #e2e8f0; font-size: 12px; color: var(--text-muted);">
                    <div style="display: flex; align-items: center; gap: 6px; font-weight: 600; color: var(--text-body); margin-bottom: 2px;">
                        <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        Security &amp; Credentials
                    </div>
                    Password is encrypted and protected. To change credentials, use the <a href="{{ route('employees.edit', $employee) }}" style="color: var(--primary); font-weight: 600; text-decoration: underline;">Edit Biodata</a> form.
                </div>
            </div>
        </div>

    </div>

    {{-- Recent Attendance Activity --}}
    <div class="card">
        <div class="card-header">
            <div>
                <div class="card-title">Recent Attendance Records</div>
                <div class="card-subtitle">Last 10 attendance sessions recorded for {{ $employee->name }}</div>
            </div>
        </div>

        <div class="table-wrap">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Clock In</th>
                        <th>Break</th>
                        <th>Clock Out</th>
                        <th>Total Hours</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($employee->attendances as $att)
                        <tr>
                            <td style="font-weight: 600;">{{ \Carbon\Carbon::parse($att->attendance_date)->format('d M Y (D)') }}</td>
                            <td>{{ $att->clock_in ? \Carbon\Carbon::parse($att->clock_in)->format('h:i A') : '—' }}</td>
                            <td>
                                @if($att->break_start)
                                    {{ \Carbon\Carbon::parse($att->break_start)->format('h:i A') }}
                                    @if($att->break_end)
                                        – {{ \Carbon\Carbon::parse($att->break_end)->format('h:i A') }}
                                        <span style="font-size: 11px; color: var(--text-muted);">({{ $att->break_duration_minutes ?? 0 }}m)</span>
                                    @endif
                                @else
                                    —
                                @endif
                            </td>
                            <td>{{ $att->clock_out ? \Carbon\Carbon::parse($att->clock_out)->format('h:i A') : '—' }}</td>
                            <td>
                                @if($att->total_work_minutes)
                                    {{ sprintf('%.1f', $att->total_work_minutes / 60) }}h
                                @else
                                    —
                                @endif
                            </td>
                            <td>
                                <span class="badge {{ $att->status === 'Present' ? 'badge-green' : ($att->status === 'Late' ? 'badge-amber' : 'badge-red') }}">
                                    {{ $att->status }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="table-empty" style="padding: 24px; text-align: center; color: var(--text-muted);">
                                No attendance records found for this employee yet.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
