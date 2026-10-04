@extends('layouts.app')

@section('title', 'Medical Certificates — TT Dental Lab')
@section('header_title', 'Medical Certificates')

@push('styles')
<style>
    .mc-container {
        display: grid;
        grid-template-columns: 420px 1fr;
        gap: 20px;
        align-items: start;
    }
    @media (max-width: 980px) {
        .mc-container {
            grid-template-columns: 1fr;
        }
    }
    .mc-tile {
        background: #ffffff;
        border: 1px solid var(--card-border);
        border-radius: 12px;
        padding: 16px 18px;
        margin-bottom: 14px;
        transition: box-shadow 0.18s ease, border-color 0.18s ease;
    }
    .mc-tile:hover {
        border-color: #cbd5e1;
        box-shadow: 0 4px 12px rgba(0,0,0,0.04);
    }
    .mc-tile-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        margin-bottom: 10px;
        flex-wrap: wrap;
    }
    .mc-tile-date {
        font-size: 14.5px;
        font-weight: 800;
        color: var(--text-head);
    }
    .mc-tile-sub {
        font-size: 11.5px;
        color: var(--text-muted);
        margin-top: 1px;
    }
    .mc-reason-box {
        font-size: 13px;
        color: var(--text-body);
        background: #f8fafc;
        border-radius: 8px;
        padding: 10px 12px;
        margin-bottom: 12px;
        border-left: 3px solid #cbd5e1;
        line-height: 1.5;
    }
    .mc-admin-remark {
        display: flex;
        align-items: flex-start;
        gap: 8px;
        font-size: 12px;
        padding: 9px 12px;
        border-radius: 8px;
        margin-top: 10px;
        background: #eff6ff;
        border: 1px solid #bfdbfe;
        color: #1e40af;
    }
    .mc-admin-remark.rejected {
        background: #fef2f2;
        border-color: #fecaca;
        color: #991b1b;
    }
    .mc-admin-remark.approved {
        background: #f0fdf4;
        border-color: #bbf7d0;
        color: #166534;
    }
    .file-input-wrapper {
        position: relative;
        border: 2px dashed #cbd5e1;
        border-radius: 10px;
        padding: 16px;
        text-align: center;
        background: #f8fafc;
        cursor: pointer;
        transition: all 0.2s ease;
    }
    .file-input-wrapper:hover {
        border-color: var(--primary);
        background: var(--primary-light);
    }
    .file-input-wrapper input[type="file"] {
        position: absolute;
        inset: 0;
        opacity: 0;
        cursor: pointer;
        width: 100%;
        height: 100%;
    }
</style>
@endpush

@section('content')

{{-- Page Header --}}
<div class="page-header">
    <div>
        <div class="page-header-title">Medical Certificates</div>
        <div class="page-header-sub">Submit digital medical leave certificates (MC) and monitor real-time approval status</div>
    </div>
    <div class="page-header-actions">
        <a href="{{ route('staff.dashboard') }}" class="btn btn-outline btn-sm">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Back to Dashboard
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
            <div class="stat-sub">Submitted certificates</div>
        </div>
    </div>

    <div class="stat-card stat-amber">
        <div class="stat-icon">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" stroke-width="2"/><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6l4 2"/></svg>
        </div>
        <div class="stat-info">
            <div class="stat-label">Pending Review</div>
            <div class="stat-value">{{ $pendingMc }}</div>
            <div class="stat-sub">Awaiting admin review</div>
        </div>
    </div>

    <div class="stat-card stat-green">
        <div class="stat-icon">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        </div>
        <div class="stat-info">
            <div class="stat-label">Approved</div>
            <div class="stat-value">{{ $approvedMc }}</div>
            <div class="stat-sub">Validated medical leaves</div>
        </div>
    </div>

    <div class="stat-card stat-red">
        <div class="stat-icon">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        </div>
        <div class="stat-info">
            <div class="stat-label">Rejected</div>
            <div class="stat-value">{{ $rejectedMc }}</div>
            <div class="stat-sub">Unapproved requests</div>
        </div>
    </div>
</div>

{{-- Main Content: Submit Form + Records List --}}
<div class="mc-container">

    {{-- Left: Submission Form Card --}}
    <div class="card">
        <div class="card-header">
            <div>
                <div class="card-title">Submit Medical Certificate</div>
                <div class="card-subtitle">Upload doctor's medical leave note for absence verification</div>
            </div>
        </div>

        <form method="POST" action="{{ route('staff.mc.store') }}" enctype="multipart/form-data">
            @csrf

            {{-- Date --}}
            <div class="form-group">
                <label class="form-label" for="attendance_date">
                    Medical Leave Date <span class="req">*</span>
                </label>
                <input
                    type="date"
                    id="attendance_date"
                    name="attendance_date"
                    required
                    class="form-input"
                    value="{{ old('attendance_date', now()->format('Y-m-d')) }}"
                >
                <div class="form-hint">Date for which the medical certificate applies.</div>
            </div>

            {{-- Reason --}}
            <div class="form-group">
                <label class="form-label" for="reason">
                    Reason / Diagnosis <span class="req">*</span>
                </label>
                <textarea
                    id="reason"
                    name="reason"
                    rows="3"
                    required
                    class="form-textarea"
                    placeholder="e.g. High fever, dental extraction recovery, flu symptoms..."
                >{{ old('reason') }}</textarea>
                <div class="form-hint">Brief explanation of medical absence.</div>
            </div>

            {{-- Attachment --}}
            <div class="form-group">
                <label class="form-label" for="attachment">
                    Certificate Document (MC) <span class="req">*</span>
                </label>
                <div class="file-input-wrapper" id="fileWrapper">
                    <input
                        type="file"
                        id="attachment"
                        name="attachment"
                        accept=".pdf,.jpg,.jpeg,.png"
                        required
                        onchange="document.getElementById('fileNameDisplay').textContent = this.files[0] ? this.files[0].name : 'Choose file or drag & drop here';"
                    >
                    <svg width="28" height="28" fill="none" stroke="currentColor" style="color: var(--primary); margin: 0 auto 6px;" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/></svg>
                    <div id="fileNameDisplay" style="font-size: 13px; font-weight: 700; color: var(--text-head);">Choose file or drag & drop here</div>
                    <div style="font-size: 11px; color: var(--text-muted); margin-top: 3px;">PDF, PNG, JPG or JPEG (Max 2MB)</div>
                </div>
            </div>

            {{-- Submit Button --}}
            <button type="submit" class="btn btn-primary" style="width: 100%; justify-content: center; padding: 12px;">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                Submit Medical Certificate
            </button>
        </form>
    </div>

    {{-- Right: Submissions History Card --}}
    <div class="card">
        <div class="card-header">
            <div>
                <div class="card-title">My MC Submissions & History</div>
                <div class="card-subtitle">Showing {{ $records->count() }} submitted certificate{{ $records->count() === 1 ? '' : 's' }}</div>
            </div>
        </div>

        <div>
            @forelse ($records as $record)
                @php
                    $recDate = $record->attendance_date ? \Carbon\Carbon::parse($record->attendance_date) : null;
                    $status = strtolower($record->status ?? 'pending');
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

                <div class="mc-tile">
                    <div class="mc-tile-header">
                        <div>
                            <div class="mc-tile-date">
                                {{ $recDate ? $recDate->format('d F Y') : '—' }}
                                @if($recDate)
                                    <span style="font-weight: 500; font-size: 12px; color: var(--text-muted);">({{ $recDate->format('l') }})</span>
                                @endif
                            </div>
                            <div class="mc-tile-sub">
                                Submitted on {{ $record->created_at ? $record->created_at->format('d M Y, h:i A') : '—' }}
                            </div>
                        </div>

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
                    </div>

                    {{-- Reason --}}
                    <div class="mc-reason-box">
                        <strong>Reason:</strong> {{ $record->reason }}
                    </div>

                    {{-- Actions & Remarks --}}
                    <div style="display: flex; align-items: center; justify-content: space-between; gap: 10px; flex-wrap: wrap;">
                        @if($record->attachment_path)
                            <a href="{{ route('staff.mc.download', $record) }}" class="btn btn-outline btn-sm" target="_blank">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                View Attachment
                            </a>
                        @else
                            <span style="font-size: 11.5px; color: var(--text-faint);">No attachment</span>
                        @endif
                    </div>

                    {{-- Admin Remark --}}
                    @if($record->admin_remark)
                        <div class="mc-admin-remark {{ $status === 'approved' ? 'approved' : ($status === 'rejected' ? 'rejected' : '') }}">
                            <svg width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="flex-shrink: 0; margin-top: 1px;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"/></svg>
                            <div>
                                <strong>Admin Remark:</strong> {{ $record->admin_remark }}
                            </div>
                        </div>
                    @endif
                </div>
            @empty
                <div class="table-empty" style="padding: 48px 20px;">
                    <svg width="44" height="44" fill="none" stroke="currentColor" style="opacity: 0.35; margin: 0 auto 10px; display: block;" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    <div style="font-weight: 700; font-size: 14px; color: var(--text-head);">No medical certificates submitted yet</div>
                    <div style="font-size: 12.5px; color: var(--text-muted); max-width: 340px; margin: 4px auto 0;">
                        When you submit a medical leave certificate using the form on the left, it will appear here with live tracking.
                    </div>
                </div>
            @endforelse
        </div>
    </div>

</div>

@endsection
