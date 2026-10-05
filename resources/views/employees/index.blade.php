@extends('layouts.app')
@section('title', 'Employees — TT Dental Lab')
@section('header_title', 'Employee Management')

@section('content')

<div class="page-header">
    <div>
        <div class="page-header-title">Employee Directory</div>
        <div class="page-header-sub">Manage all staff registered in the biometric attendance system</div>
    </div>
    <div class="page-header-actions">
        <a href="{{ route('employees.create') }}" class="btn btn-primary">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
            Add Employee
        </a>
        <a href="{{ route('face-attendance') }}" class="btn btn-outline">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="8" r="4" stroke-width="2"/><path stroke-linecap="round" stroke-linejoin="round" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/></svg>
            Face Registration
        </a>
    </div>
</div>

{{-- Stats --}}
<div class="stat-grid" style="grid-template-columns: repeat(3, 1fr); margin-bottom: 22px;">
    <div class="stat-card stat-blue">
        <div class="stat-icon">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
        </div>
        <div class="stat-info">
            <div class="stat-label">Total Employees</div>
            <div class="stat-value">{{ $employees->count() }}</div>
            <div class="stat-sub">Registered in system</div>
        </div>
    </div>
    <div class="stat-card stat-green">
        <div class="stat-icon">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        </div>
        <div class="stat-info">
            <div class="stat-label">Active</div>
            <div class="stat-value">{{ $employees->where('status', true)->count() }}</div>
            <div class="stat-sub">Currently active staff</div>
        </div>
    </div>
    <div class="stat-card stat-purple">
        <div class="stat-icon">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="8" r="4" stroke-width="2"/><path stroke-linecap="round" stroke-linejoin="round" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/></svg>
        </div>
        <div class="stat-info">
            <div class="stat-label">Biometric</div>
            <div class="stat-value">{{ $employees->filter(fn($e) => $e->has_face_registered)->count() }}</div>
            <div class="stat-sub">Face registered</div>
        </div>
    </div>
</div>

{{-- Employees Table --}}
<div class="card">
    <div class="card-header">
        <div>
            <div class="card-title">All Employees</div>
            <div class="card-subtitle">{{ $employees->count() }} employee(s) registered in the system</div>
        </div>
    </div>

    <div class="table-wrap">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Employee</th>
                    <th>Position / Dept</th>
                    <th>Contact</th>
                    <th>Biometric</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($employees as $employee)
                    <tr>
                        <td>
                            <div style="display:flex; align-items:center; gap:10px;">
                                <a href="{{ route('employees.show', $employee) }}" style="width:38px; height:38px; border-radius:10px; overflow:hidden; flex-shrink:0; background:linear-gradient(135deg,#4f46e5,#7c3aed); display:block;">
                                    <img src="{{ $employee->profile_photo_url }}" alt="{{ $employee->name }}" style="width:100%; height:100%; object-fit:cover;" onerror="this.style.display='none'; this.parentElement.style.display='flex'; this.parentElement.style.alignItems='center'; this.parentElement.style.justifyContent='center'; this.parentElement.style.color='white'; this.parentElement.style.fontWeight='800'; this.parentElement.style.fontSize='14px'; this.parentElement.textContent='{{ strtoupper(substr($employee->name, 0, 1)) }}';">
                                </a>
                                <div>
                                    <a href="{{ route('employees.show', $employee) }}" style="font-weight:700; color:var(--text-head); font-size:13.5px; text-decoration:none; display:inline-block;" onmouseover="this.style.color='var(--primary)'" onmouseout="this.style.color='var(--text-head)'">{{ $employee->name }}</a>
                                    <div style="font-size:11.5px; color:var(--primary); font-weight:600;">{{ $employee->employee_id }}</div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div style="font-size:13px; font-weight:600; color:var(--text-body);">{{ $employee->position ?? '—' }}</div>
                            <div style="font-size:11.5px; color:var(--text-muted);">{{ $employee->department ?? '' }}</div>
                        </td>
                        <td>
                            <div style="font-size:12.5px; color:var(--text-body);">{{ $employee->email ?? '—' }}</div>
                            <div style="font-size:12px; color:var(--text-muted);">{{ $employee->phone ?? '' }}</div>
                        </td>
                        <td>
                            @if($employee->has_face_registered)
                                <span class="badge badge-green">
                                    <svg width="10" height="10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                    Registered
                                </span>
                            @else
                                <span class="badge badge-amber">Not registered</span>
                            @endif
                        </td>
                        <td>
                            <span class="badge {{ $employee->status ? 'badge-green' : 'badge-red' }}">
                                {{ $employee->status ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                        <td>
                            <div style="display:flex; align-items:center; gap:6px; flex-wrap:wrap;">
                                <a href="{{ route('employees.show', $employee) }}" class="btn btn-outline btn-sm" title="View Biodata Profile">
                                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    View
                                </a>

                                <a href="{{ route('employees.edit', $employee->id) }}" class="btn btn-outline btn-sm">
                                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    Edit
                                </a>

                                <a href="{{ route('face-attendance') }}?employee_id={{ $employee->id }}" class="btn btn-sm" style="background:var(--purple-light); color:var(--purple); border:1px solid rgba(124,58,237,0.2);">
                                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="8" r="4" stroke-width="2"/><path stroke-linecap="round" stroke-linejoin="round" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/></svg>
                                    Face
                                </a>

                                <form action="{{ route('employees.toggle-status', $employee->id) }}" method="POST" style="display:inline;">
                                    @csrf @method('PATCH')
                                    <button type="submit" class="btn btn-sm btn-outline"
                                        title="{{ $employee->status ? 'Deactivate' : 'Activate' }}">
                                        @if($employee->status)
                                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10 9v6m4-6v6m7-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        @else
                                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/><path stroke-linecap="round" stroke-linejoin="round" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        @endif
                                        {{ $employee->status ? 'Deactivate' : 'Activate' }}
                                    </button>
                                </form>

                                <form action="{{ route('employees.destroy', $employee->id) }}" method="POST" style="display:inline;"
                                    onsubmit="return confirm('Delete {{ addslashes($employee->name) }}? All related attendance records will also be deleted. This cannot be undone.');">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger">
                                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        Delete
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="table-empty">
                            <svg width="40" height="40" fill="none" stroke="#cbd5e1" stroke-width="1.5" viewBox="0 0 24 24" style="margin-bottom:10px;"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            <br>No employees registered yet.
                            <br><a href="{{ route('employees.create') }}" class="btn btn-primary" style="margin-top:12px; display:inline-flex;">Add First Employee</a>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection
