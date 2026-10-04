@extends('layouts.app')

@section('title', 'MC Review & Approval — TT Dental Lab')
@section('header_title', 'Medical Certificate Review')

@push('styles')
<style>
    .mc-admin-card {
        background: #ffffff;
        border: 1px solid var(--card-border);
        border-radius: 14px;
        padding: 20px;
        margin-bottom: 16px;
        box-shadow: var(--card-shadow);
        transition: border-color 0.2s ease, box-shadow 0.2s ease;
    }
    .mc-admin-card:hover {
        border-color: #cbd5e1;
        box-shadow: var(--card-shadow-md);
    }
    .mc-card-top {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 16px;
        margin-bottom: 14px;
        flex-wrap: wrap;
    }
    .mc-emp-avatar {
        width: 44px;
        height: 44px;
        border-radius: 12px;
        background: #eef2ff;
        color: #4f46e5;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 800;
        font-size: 16px;
        flex-shrink: 0;
    }
    .mc-reason-quote {
        background: #f8fafc;
        border-radius: 10px;
        border-left: 3px solid var(--primary);
        padding: 12px 14px;
        font-size: 13px;
        color: var(--text-body);
        margin-bottom: 14px;
        line-height: 1.5;
    }
    .mc-action-bar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 14px;
        padding-top: 14px;
        border-top: 1px solid #f1f5f9;
        flex-wrap: wrap;
    }
    .filter-tabs {
        display: flex;
        gap: 8px;
        margin-bottom: 20px;
        flex-wrap: wrap;
    }
    .filter-tab {
        padding: 8px 16px;
        border-radius: 999px;
        font-size: 12.5px;
        font-weight: 700;
        cursor: pointer;
        border: 1px solid var(--card-border);
        background: #ffffff;
        color: var(--text-muted);
        transition: all 0.18s ease;
    }
    .filter-tab:hover {
        background: #f1f5f9;
        color: var(--text-head);
    }
    .filter-tab.active {
        background: var(--primary);
        color: #ffffff;
        border-color: var(--primary);
        box-shadow: 0 2px 8px var(--primary-glow);
    }
</style>
@endpush

@section('content')

{{-- Page Header --}}
<div class="page-header">
    <div>
        <div class="page-header-title">Medical Certificate Management</div>
        <div class="page-header-sub">Review employee submitted medical certificates, verify attachments, and record approval decisions</div>
    </div>
    <div class="page-header-actions">
        <a href="{{ route('admin.dashboard') }}" class="btn btn-outline btn-sm">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Dashboard
        </a>
    </div>
</div>

@php
    $totalMc = $records->count();
    $pendingMc = $records->where('status', 'pending')->count();
    $approvedMc = $records->where('status', 'approved')->count();
    $rejectedMc = $records->where('status', 'rejected')->count();
@endphp

{{-- Stats Row --}}
<div class="stat-grid">
    <div class="stat-card stat-blue">
        <div class="stat-icon">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
        </div>
        <div class="stat-info">
            <div class="stat-label">Total Submissions</div>
            <div class="stat-value">{{ $totalMc }}</div>
            <div class="stat-sub">All employee MCs</div>
        </div>
    </div>

    <div class="stat-card stat-amber">
        <div class="stat-icon">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" stroke-width="2"/><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6l4 2"/></svg>
        </div>
        <div class="stat-info">
            <div class="stat-label">Pending Review</div>
            <div class="stat-value">{{ $pendingMc }}</div>
            <div class="stat-sub">Requires verification</div>
        </div>
    </div>

    <div class="stat-card stat-green">
        <div class="stat-icon">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        </div>
        <div class="stat-info">
            <div class="stat-label">Approved</div>
            <div class="stat-value">{{ $approvedMc }}</div>
            <div class="stat-sub">Valid medical leaves</div>
        </div>
    </div>

    <div class="stat-card stat-red">
        <div class="stat-icon">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        </div>
        <div class="stat-info">
            <div class="stat-label">Rejected</div>
            <div class="stat-value">{{ $rejectedMc }}</div>
            <div class="stat-sub">Declined requests</div>
        </div>
    </div>
</div>

{{-- Interactive Filter Tabs --}}
<div class="filter-tabs">
    <button type="button" class="filter-tab active" onclick="filterMc('all', this)">
        All Requests ({{ $totalMc }})
    </button>
    <button type="button" class="filter-tab" onclick="filterMc('pending', this)">
        Pending ({{ $pendingMc }})
    </button>
    <button type="button" class="filter-tab" onclick="filterMc('approved', this)">
        Approved ({{ $approvedMc }})
    </button>
    <button type="button" class="filter-tab" onclick="filterMc('rejected', this)">
        Rejected ({{ $rejectedMc }})
    </button>
</div>

{{-- MC Cards List --}}
<div id="mcList">
    @forelse ($records as $record)
        @php
            $status = strtolower($record->status ?? 'pending');
            $recDate = $record->attendance_date ? \Carbon\Carbon::parse($record->attendance_date) : null;
            $empName = $record->employee?->name ?? 'Unknown Employee';
            $empId = $record->employee?->employee_id ?? 'EMP';
            $initial = strtoupper(substr($empName, 0, 1));
            
            if ($status === 'approved') {
                $badgeClass = 'badge-green';
                $statusText = 'Approved';
            } elseif ($status === 'rejected') {
                $badgeClass = 'badge-red';
                $statusText = 'Rejected';
            } else {
                $badgeClass = 'badge-amber';
                $statusText = 'Pending Review';
            }
        @endphp

        <div class="mc-admin-card" data-status="{{ $status }}">
            {{-- Top Row --}}
            <div class="mc-card-top">
                <div style="display: flex; align-items: center; gap: 14px;">
                    <div class="mc-emp-avatar">{{ $initial }}</div>
                    <div>
                        <div style="font-size: 15px; font-weight: 800; color: var(--text-head);">
                            {{ $empName }}
                            <span style="font-size: 12px; font-weight: 600; color: var(--text-muted); margin-left: 6px;">
                                ({{ $empId }})
                            </span>
                        </div>
                        <div style="font-size: 12px; color: var(--text-muted); margin-top: 2px;">
                            {{ $record->employee?->position ?? 'Dental Staff' }} •
                            Leave Date: <strong style="color: var(--text-head);">{{ $recDate ? $recDate->format('d M Y (l)') : '—' }}</strong>
                        </div>
                    </div>
                </div>

                <div style="display: flex; align-items: center; gap: 10px;">
                    <span class="badge {{ $badgeClass }}">
                        @if($status === 'approved')
                            <svg width="12" height="12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                        @elseif($status === 'rejected')
                            <svg width="12" height="12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                        @else
                            <svg width="12" height="12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" stroke-width="2.5"/><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6l4 2"/></svg>
                        @endif
                        {{ $statusText }}
                    </span>
                    <span style="font-size: 11.5px; color: var(--text-faint);">
                        Submitted {{ $record->created_at ? $record->created_at->diffForHumans() : '' }}
                    </span>
                </div>
            </div>

            {{-- Reason Block --}}
            <div class="mc-reason-quote">
                <strong>Medical Reason / Diagnosis:</strong> {{ $record->reason }}
            </div>

            {{-- Document Attachment & Action Bar --}}
            <div class="mc-action-bar">
                <div>
                    @if($record->attachment_path)
                        <a href="{{ route('admin.mc.download', $record) }}" class="btn btn-outline btn-sm" target="_blank">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            View / Download Certificate
                        </a>
                    @else
                        <span style="font-size: 12px; color: var(--text-faint); font-style: italic;">No attachment uploaded</span>
                    @endif
                </div>

                {{-- Pending Actions --}}
                @if($status === 'pending')
                    <div style="display: flex; align-items: center; gap: 8px; flex-wrap: wrap;">
                        {{-- Approve Form --}}
                        <form method="POST" action="{{ route('admin.mc.approve', $record) }}" style="display: inline;">
                            @csrf
                            <button type="submit" class="btn btn-success btn-sm">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                Approve MC
                            </button>
                        </form>

                        {{-- Reject Form --}}
                        <form method="POST" action="{{ route('admin.mc.reject', $record) }}" style="display: flex; gap: 6px; align-items: center;">
                            @csrf
                            <input
                                type="text"
                                name="admin_remark"
                                placeholder="Rejection remark (optional)"
                                class="form-input"
                                style="padding: 6px 10px; font-size: 12px; width: 200px;"
                            >
                            <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to reject this MC?');">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                                Reject
                            </button>
                        </form>
                    </div>
                @else
                    {{-- Decision Notes --}}
                    @if($record->admin_remark)
                        <div style="font-size: 12px; color: var(--text-muted); display: flex; align-items: center; gap: 6px;">
                            <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <span><strong>Admin Remark:</strong> {{ $record->admin_remark }}</span>
                        </div>
                    @endif
                @endif
            </div>
        </div>
    @empty
        <div class="card table-empty" style="padding: 50px 20px;">
            <svg width="44" height="44" fill="none" stroke="currentColor" style="opacity: 0.35; margin: 0 auto 10px; display: block;" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            <div style="font-weight: 700; font-size: 14px; color: var(--text-head);">No medical certificate submissions</div>
            <div style="font-size: 12.5px; color: var(--text-muted); margin-top: 4px;">Staff submitted MC applications will be listed here for administrative review.</div>
        </div>
    @endforelse
</div>

<script>
    function filterMc(status, btn) {
        document.querySelectorAll('.filter-tab').forEach(b => b.classList.remove('active'));
        btn.classList.add('active');

        const cards = document.querySelectorAll('.mc-admin-card');
        cards.forEach(card => {
            if (status === 'all' || card.dataset.status === status) {
                card.style.display = 'block';
            } else {
                card.style.display = 'none';
            }
        });
    }
</script>

@endsection
