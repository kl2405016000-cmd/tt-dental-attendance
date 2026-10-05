@extends('layouts.app')
@section('title', 'My Dashboard — TT Dental Laboratory')
@section('header_title', 'Staff Dashboard')

@section('content')

@php
    $statusLabel = 'Not Started';
    $statusClass = 'status-not-started';
    $statusDotColor = '#94a3b8';
    $statusBg = 'rgba(148, 163, 184, 0.15)';
    $statusBorder = 'rgba(148, 163, 184, 0.3)';

    if ($attendance) {
        if ($attendance->break_start && !$attendance->break_end) {
            $statusLabel = 'On Break';
            $statusClass = 'status-break';
            $statusDotColor = '#f59e0b';
            $statusBg = 'rgba(245, 158, 11, 0.15)';
            $statusBorder = 'rgba(245, 158, 11, 0.35)';
        } elseif ($attendance->clock_out) {
            $statusLabel = 'Day Complete';
            $statusClass = 'status-complete';
            $statusDotColor = '#3b82f6';
            $statusBg = 'rgba(59, 130, 246, 0.15)';
            $statusBorder = 'rgba(59, 130, 246, 0.35)';
        } elseif ($attendance->clock_in) {
            $statusLabel = 'Working Active';
            $statusClass = 'status-working';
            $statusDotColor = '#10b981';
            $statusBg = 'rgba(16, 185, 129, 0.15)';
            $statusBorder = 'rgba(16, 185, 129, 0.35)';
        }
    }

    $canClockIn    = !$attendance || !$attendance->clock_in;
    $canClockOut   = $attendance && $attendance->clock_in && !$attendance->clock_out && !($attendance->break_start && !$attendance->break_end);
    $canStartBreak = $attendance && $attendance->clock_in && !$attendance->clock_out && !($attendance->break_start && !$attendance->break_end);
    $canEndBreak   = $attendance && $attendance->break_start && !$attendance->break_end;

    $historyList = isset($recentAttendances) ? $recentAttendances : ($employee ? $employee->attendances()->orderByDesc('attendance_date')->take(7)->get() : collect());
@endphp

{{-- ============================================================
     1. COMPACT WELCOME HERO BANNER (DENTAL THEMED VISUAL)
============================================================ --}}
<div class="welcome-hero-banner">
    <div class="hero-bg-visual" style="background-image: url('{{ asset('images/dental-hero-banner.jpg') }}');"></div>
    <div class="hero-gradient-overlay"></div>
    <div class="hero-decorative-orb hero-orb-1"></div>
    <div class="hero-decorative-orb hero-orb-2"></div>

    <div class="hero-content">
        <div class="hero-left">
            <div class="hero-greeting-pill">
                <span class="greeting-sun-icon">
                    @if(now()->hour < 12)
                        ☀️
                    @elseif(now()->hour < 18)
                        🌤️
                    @else
                        🌙
                    @endif
                </span>
                <span>Good {{ now()->hour < 12 ? 'morning' : (now()->hour < 18 ? 'afternoon' : 'evening') }}, Dental Professional</span>
            </div>

            <h1 class="hero-title">
                Welcome Back, <span class="hero-name-gradient">{{ $employee?->name ?? auth()->user()->name }}</span>
            </h1>

            <div class="hero-meta-badges">
                <span class="hero-badge">
                    <svg width="12" height="12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    {{ $employee?->employee_id ? 'ID: ' . $employee->employee_id : 'Staff' }}
                </span>
                <span class="hero-badge">
                    <svg width="12" height="12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                    {{ $employee?->position ?? 'Dental Technician' }}
                </span>
                <span class="hero-badge">
                    <svg width="12" height="12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                    {{ $employee?->department ?? 'Laboratory' }}
                </span>
            </div>
        </div>

        <div class="hero-right">
            <div class="hero-date-display">
                <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                <span>{{ now()->format('D, d M Y') }}</span>
            </div>

            <div class="hero-status-pill" style="background: {{ $statusBg }}; border-color: {{ $statusBorder }};">
                <span class="hero-status-dot" style="background: {{ $statusDotColor }};"></span>
                <span class="hero-status-text">{{ $statusLabel }}</span>
            </div>
        </div>
    </div>
</div>

{{-- ============================================================
     2. COMPACT ATTENDANCE SUMMARY CARDS
============================================================ --}}
<div class="attendance-summary-grid">

    <!-- Card 1: Clock In -->
    <div class="summary-card card-clock-in">
        <div class="summary-card-inner">
            <div class="summary-header">
                <div class="summary-icon-box">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.3" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
                    </svg>
                </div>
                <span class="summary-tag">Clock In</span>
            </div>
            <div class="summary-body">
                <div class="summary-value">
                    {{ $attendance?->clock_in ? \Carbon\Carbon::parse($attendance->clock_in)->format('h:i A') : '—:—' }}
                </div>
                <div class="summary-status">
                    @if($attendance?->clock_in)
                        @if($attendance->late_minutes > 0)
                            <span class="status-pill-sub pill-late">
                                <span class="dot-sm bg-late"></span>
                                Late {{ $attendance->late_minutes }}m
                            </span>
                        @else
                            <span class="status-pill-sub pill-ontime">
                                <span class="dot-sm bg-ontime"></span>
                                On Time
                            </span>
                        @endif
                    @else
                        <span class="status-pill-sub pill-pending">Pending Scan</span>
                    @endif
                </div>
            </div>
        </div>
        <div class="card-accent-bar bar-emerald"></div>
    </div>

    <!-- Card 2: Clock Out -->
    <div class="summary-card card-clock-out">
        <div class="summary-card-inner">
            <div class="summary-header">
                <div class="summary-icon-box">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.3" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                    </svg>
                </div>
                <span class="summary-tag">Clock Out</span>
            </div>
            <div class="summary-body">
                <div class="summary-value">
                    {{ $attendance?->clock_out ? \Carbon\Carbon::parse($attendance->clock_out)->format('h:i A') : '—:—' }}
                </div>
                <div class="summary-status">
                    @if($attendance?->clock_out)
                        <span class="status-pill-sub pill-completed">
                            <span class="dot-sm bg-blue"></span>
                            Day Concluded
                        </span>
                    @elseif($attendance?->clock_in)
                        <span class="status-pill-sub pill-working">
                            <span class="dot-sm bg-green-pulse"></span>
                            In Progress
                        </span>
                    @else
                        <span class="status-pill-sub pill-pending">Shift Not Started</span>
                    @endif
                </div>
            </div>
        </div>
        <div class="card-accent-bar bar-rose"></div>
    </div>

    <!-- Card 3: Start Break -->
    <div class="summary-card card-start-break">
        <div class="summary-card-inner">
            <div class="summary-header">
                <div class="summary-icon-box">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.3" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <span class="summary-tag">Start Break</span>
            </div>
            <div class="summary-body">
                <div class="summary-value">
                    {{ $attendance?->break_start ? \Carbon\Carbon::parse($attendance->break_start)->format('h:i A') : '—:—' }}
                </div>
                <div class="summary-status">
                    @if($attendance?->break_start && !$attendance?->break_end)
                        <span class="status-pill-sub pill-active-break">
                            <span class="dot-sm bg-amber-pulse"></span>
                            On Break
                        </span>
                    @elseif($attendance?->break_start && $attendance?->break_end)
                        <span class="status-pill-sub pill-finished-break">Completed</span>
                    @else
                        <span class="status-pill-sub pill-pending">Not Taken</span>
                    @endif
                </div>
            </div>
        </div>
        <div class="card-accent-bar bar-amber"></div>
    </div>

    <!-- Card 4: End Break -->
    <div class="summary-card card-end-break">
        <div class="summary-card-inner">
            <div class="summary-header">
                <div class="summary-icon-box">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.3" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <span class="summary-tag">End Break</span>
            </div>
            <div class="summary-body">
                <div class="summary-value">
                    {{ $attendance?->break_end ? \Carbon\Carbon::parse($attendance->break_end)->format('h:i A') : '—:—' }}
                </div>
                <div class="summary-status">
                    @if($attendance?->break_duration)
                        <span class="status-pill-sub pill-duration">
                            ⏱️ {{ $attendance->break_duration }} taken
                        </span>
                    @else
                        <span class="status-pill-sub pill-pending">00:00 Duration</span>
                    @endif
                </div>
            </div>
        </div>
        <div class="card-accent-bar bar-indigo"></div>
    </div>

</div>

{{-- ============================================================
     3. TODAY'S ATTENDANCE SECTION & QUICK ATTENDANCE ACTIONS
============================================================ --}}
<div class="dashboard-two-col-grid">

    <!-- Today's Attendance Overview Card -->
    <div class="saas-card today-attendance-card">
        <div class="saas-card-header">
            <div>
                <h2 class="saas-card-title">Today's Attendance</h2>
                <p class="saas-card-subtitle">Real-time status and logged activity</p>
            </div>
            <span class="live-indicator-badge">
                <span class="beacon-dot"></span>
                Live
            </span>
        </div>

        <div class="today-overview-tiles">
            <!-- Attendance Status -->
            <div class="overview-tile tile-status">
                <div class="tile-icon-circle">
                    <svg width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                </div>
                <div class="tile-details">
                    <span class="tile-label">Status</span>
                    <strong class="tile-value" style="color: {{ $statusDotColor }};">
                        {{ $statusLabel }}
                    </strong>
                </div>
            </div>

            <!-- Total Working Hours -->
            <div class="overview-tile tile-hours">
                <div class="tile-icon-circle">
                    <svg width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <div class="tile-details">
                    <span class="tile-label">Working Hours</span>
                    <strong class="tile-value">{{ gmdate('H\h i\m', $todayMinutes * 60) }}</strong>
                </div>
            </div>

            <!-- Shift Times -->
            <div class="overview-tile tile-shift">
                <div class="tile-icon-circle">
                    <svg width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                </div>
                <div class="tile-details">
                    <span class="tile-label">Shift Times</span>
                    <strong class="tile-value">
                        {{ $attendance?->clock_in ? \Carbon\Carbon::parse($attendance->clock_in)->format('h:i A') : '—' }}
                        <span style="color: #94a3b8; font-weight: 500;">→</span>
                        {{ $attendance?->clock_out ? \Carbon\Carbon::parse($attendance->clock_out)->format('h:i A') : 'Pending' }}
                    </strong>
                </div>
            </div>

            <!-- Total Break Time -->
            <div class="overview-tile tile-break">
                <div class="tile-icon-circle">
                    <svg width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/></svg>
                </div>
                <div class="tile-details">
                    <span class="tile-label">Break Time</span>
                    <strong class="tile-value">{{ $attendance?->break_duration ?? '00:00' }}</strong>
                </div>
            </div>
        </div>

        <!-- Shift Progression Visual Line -->
        <div class="shift-progress-wrapper">
            <div class="shift-progress-header">
                <span class="progress-title">Shift Milestones</span>
                <span class="progress-info">
                    @if($attendance?->clock_out)
                        100% Complete
                    @elseif($attendance?->clock_in)
                        In Progress
                    @else
                        Pending In
                    @endif
                </span>
            </div>
            <div class="milestones-track">
                <div class="milestone-node {{ $attendance?->clock_in ? 'node-done' : 'node-pending' }}">
                    <div class="node-dot"></div>
                    <span class="node-title">Clock In</span>
                    <span class="node-time">{{ $attendance?->clock_in ? \Carbon\Carbon::parse($attendance->clock_in)->format('h:i A') : '—' }}</span>
                </div>
                <div class="milestone-line {{ $attendance?->break_start ? 'line-done' : '' }}"></div>

                <div class="milestone-node {{ $attendance?->break_start ? 'node-done' : 'node-pending' }}">
                    <div class="node-dot"></div>
                    <span class="node-title">Break</span>
                    <span class="node-time">{{ $attendance?->break_start ? \Carbon\Carbon::parse($attendance->break_start)->format('h:i A') : '—' }}</span>
                </div>
                <div class="milestone-line {{ $attendance?->break_end ? 'line-done' : '' }}"></div>

                <div class="milestone-node {{ $attendance?->break_end ? 'node-done' : 'node-pending' }}">
                    <div class="node-dot"></div>
                    <span class="node-title">Resume</span>
                    <span class="node-time">{{ $attendance?->break_end ? \Carbon\Carbon::parse($attendance->break_end)->format('h:i A') : '—' }}</span>
                </div>
                <div class="milestone-line {{ $attendance?->clock_out ? 'line-done' : '' }}"></div>

                <div class="milestone-node {{ $attendance?->clock_out ? 'node-done' : 'node-pending' }}">
                    <div class="node-dot"></div>
                    <span class="node-title">Clock Out</span>
                    <span class="node-time">{{ $attendance?->clock_out ? \Carbon\Carbon::parse($attendance->clock_out)->format('h:i A') : '—' }}</span>
                </div>
            </div>
        </div>

    </div>

    <!-- Attendance Actions & Geolocation Section -->
    <div class="saas-card actions-container-card">
        <div class="saas-card-header">
            <div>
                <h2 class="saas-card-title">Attendance Actions</h2>
                <p class="saas-card-subtitle">Active buttons reflect your current status</p>
            </div>
        </div>

        <div class="modern-action-grid">

            <!-- Clock In Action -->
            <a href="{{ route('face-verification') }}" class="action-btn-link" {{ $canClockIn ? '' : 'onclick="event.preventDefault();"' }}>
                <button type="button" class="modern-action-btn btn-action-in" {{ $canClockIn ? '' : 'disabled' }}>
                    <div class="action-btn-icon-wrap">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/></svg>
                    </div>
                    <div class="action-btn-content">
                        <span class="btn-main-label">{{ $attendance && $attendance->clock_in ? 'Clocked In' : 'Clock In' }}</span>
                        <span class="btn-sub-label">{{ $canClockIn ? 'Record arrival' : 'Recorded today' }}</span>
                    </div>
                </button>
            </a>

            <!-- Clock Out Action -->
            <a href="{{ route('face-verification') }}" class="action-btn-link" {{ $canClockOut ? '' : 'onclick="event.preventDefault();"' }}>
                <button type="button" class="modern-action-btn btn-action-out" {{ $canClockOut ? '' : 'disabled' }}>
                    <div class="action-btn-icon-wrap">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                    </div>
                    <div class="action-btn-content">
                        <span class="btn-main-label">{{ $attendance?->clock_out ? 'Clocked Out' : 'Clock Out' }}</span>
                        <span class="btn-sub-label">{{ $canClockOut ? 'End workday' : ($attendance?->clock_out ? 'Shift done' : 'Awaiting in') }}</span>
                    </div>
                </button>
            </a>

            <!-- Start Break Action -->
            <a href="{{ route('face-verification') }}" class="action-btn-link" {{ $canStartBreak ? '' : 'onclick="event.preventDefault();"' }}>
                <button type="button" class="modern-action-btn btn-action-brk" {{ $canStartBreak ? '' : 'disabled' }}>
                    <div class="action-btn-icon-wrap">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <div class="action-btn-content">
                        <span class="btn-main-label">{{ $canEndBreak ? 'On Break' : 'Start Break' }}</span>
                        <span class="btn-sub-label">{{ $canStartBreak ? 'Take pause' : ($canEndBreak ? 'Resting' : 'While working') }}</span>
                    </div>
                </button>
            </a>

            <!-- End Break Action -->
            <a href="{{ route('face-verification') }}" class="action-btn-link" {{ $canEndBreak ? '' : 'onclick="event.preventDefault();"' }}>
                <button type="button" class="modern-action-btn btn-action-end" {{ $canEndBreak ? '' : 'disabled' }}>
                    <div class="action-btn-icon-wrap">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <div class="action-btn-content">
                        <span class="btn-main-label">End Break</span>
                        <span class="btn-sub-label">{{ $canEndBreak ? 'Resume shift' : 'During break' }}</span>
                    </div>
                </button>
            </a>

        </div>

        @if(!$employee)
            <div class="warning-callout">
                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                <span>Account not yet linked to an employee profile. Please contact TT Dental admin.</span>
            </div>
        @endif

        <div id="locationMsg" class="location-warning-box" style="display:none;">
            <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
            <span>Verifying laboratory geofence boundary...</span>
        </div>

    </div>

</div>

{{-- ============================================================
     4. COMPACT QUICK ACTIONS & SHORTCUT CARDS
============================================================ --}}
<div class="quick-shortcuts-section">
    <div class="section-title-wrap">
        <h2 class="saas-card-title">Quick Actions & Portal Shortcuts</h2>
    </div>

    <div class="quick-shortcuts-grid">

        <!-- History Card -->
        <a href="{{ route('staff.history') }}" class="shortcut-card card-blue-glow">
            <div class="shortcut-icon-circle bg-blue-tint">
                <svg width="20" height="20" fill="none" stroke="#2563eb" stroke-width="2.2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div class="shortcut-meta">
                <strong class="shortcut-name">Attendance History</strong>
                <span class="shortcut-desc">View full logs, work hours, and past dates</span>
            </div>
            <div class="shortcut-arrow">
                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
            </div>
        </a>

        <!-- MC Submission Card -->
        <a href="{{ route('staff.mc.index') }}" class="shortcut-card card-green-glow">
            <div class="shortcut-icon-circle bg-green-tint">
                <svg width="20" height="20" fill="none" stroke="#16a34a" stroke-width="2.2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
            </div>
            <div class="shortcut-meta">
                <strong class="shortcut-name">Medical Certificate (MC)</strong>
                <span class="shortcut-desc">Submit doctor notes and track approvals</span>
            </div>
            <div class="shortcut-arrow">
                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
            </div>
        </a>

        <!-- Report Absence Card -->
        <a href="{{ route('staff.absences.index') }}" class="shortcut-card card-amber-glow">
            <div class="shortcut-icon-circle bg-amber-tint">
                <svg width="20" height="20" fill="none" stroke="#d97706" stroke-width="2.2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/><path stroke-linecap="round" stroke-linejoin="round" d="M10 14l2 2 4-4"/>
                </svg>
            </div>
            <div class="shortcut-meta">
                <strong class="shortcut-name">Report Absence</strong>
                <span class="shortcut-desc">Notify reasons for missed workdays</span>
            </div>
            <div class="shortcut-arrow">
                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
            </div>
        </a>

        <!-- Biometric Kiosk Link -->
        <a href="{{ route('face-verification') }}" target="_blank" class="shortcut-card card-purple-glow">
            <div class="shortcut-icon-circle bg-purple-tint">
                <svg width="20" height="20" fill="none" stroke="#7c3aed" stroke-width="2.2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                    <circle cx="12" cy="13" r="3" stroke-width="2"/>
                </svg>
            </div>
            <div class="shortcut-meta">
                <div style="display:flex; align-items:center; gap:6px;">
                    <strong class="shortcut-name">Face Scan Kiosk</strong>
                    <span class="ext-badge">↗</span>
                </div>
                <span class="shortcut-desc">Open biometric kiosk terminal for rapid facial scan</span>
            </div>
            <div class="shortcut-arrow">
                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
            </div>
        </a>

    </div>
</div>

{{-- ============================================================
     5. COMPACT RECENT ATTENDANCE HISTORY TABLE
============================================================ --}}
<div class="saas-card recent-history-card">
    <div class="saas-card-header history-header">
        <div>
            <h2 class="saas-card-title">Recent Attendance History</h2>
            <p class="saas-card-subtitle">Your latest logged entries and verified attendance records</p>
        </div>
        <a href="{{ route('staff.history') }}" class="btn-view-all">
            <span>View Full History</span>
            <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
        </a>
    </div>

    <div class="history-table-container">
        @if($historyList && $historyList->count() > 0)
            <table class="saas-table">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Clock In</th>
                        <th>Clock Out</th>
                        <th>Break Duration</th>
                        <th>Working Hours</th>
                        <th>Status</th>
                        <th>Method</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($historyList as $item)
                        @php
                            $dateObj = $item->attendance_date instanceof \DateTimeInterface
                                ? $item->attendance_date
                                : \Carbon\Carbon::parse($item->attendance_date);
                            $isToday = $dateObj->isToday();
                        @endphp
                        <tr>
                            <td>
                                <div class="td-date-cell">
                                    <strong class="date-main">{{ $dateObj->format('d M Y') }}</strong>
                                    <span class="date-day">{{ $dateObj->format('l') }} @if($isToday)<span class="pill-today">Today</span>@endif</span>
                                </div>
                            </td>
                            <td>
                                @if($item->clock_in)
                                    <div class="time-stamp-box">
                                        <span class="time-text">{{ \Carbon\Carbon::parse($item->clock_in)->format('h:i A') }}</span>
                                        @if(($item->late_minutes ?? 0) > 0)
                                            <span class="micro-badge badge-late">Late {{ $item->late_minutes }}m</span>
                                        @else
                                            <span class="micro-badge badge-ontime">On Time</span>
                                        @endif
                                    </div>
                                @else
                                    <span class="time-empty">—</span>
                                @endif
                            </td>
                            <td>
                                @if($item->clock_out)
                                    <span class="time-text">{{ \Carbon\Carbon::parse($item->clock_out)->format('h:i A') }}</span>
                                @else
                                    <span class="time-empty">{{ $isToday ? 'In Progress' : '—' }}</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge-neutral">
                                    {{ $item->break_duration ?? '00:00' }}
                                </span>
                            </td>
                            <td>
                                <span class="work-hours-text">
                                    {{ $item->working_hours ?? '00:00' }}
                                </span>
                            </td>
                            <td>
                                @php
                                    $st = strtolower($item->status ?? 'present');
                                    $badgeColor = 'badge-green';
                                    if (str_contains($st, 'late')) $badgeColor = 'badge-amber';
                                    elseif (str_contains($st, 'absent')) $badgeColor = 'badge-red';
                                    elseif (str_contains($st, 'mc') || str_contains($st, 'leave')) $badgeColor = 'badge-blue';
                                @endphp
                                <span class="badge {{ $badgeColor }}">
                                    {{ $item->status ?? 'Present' }}
                                </span>
                            </td>
                            <td>
                                <span class="method-tag">
                                    @if(str_contains(strtolower($item->verification_method ?? ''), 'face') || str_contains(strtolower($item->verification_method ?? ''), 'biometric'))
                                        <svg width="12" height="12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="12" r="9" stroke-width="2"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 10h.01M15 10h.01M9.5 15a3.5 3.5 0 005 0"/></svg>
                                        Face Scan
                                    @else
                                        <svg width="12" height="12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                                        {{ $item->verification_method ?? 'Manual' }}
                                    @endif
                                </span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <div class="empty-history-state">
                <div class="empty-icon-wrap">
                    <svg width="28" height="28" fill="none" stroke="#94a3b8" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                    </svg>
                </div>
                <h3>No attendance logs recorded yet</h3>
                <p>Use the attendance actions above or the Face Scan Kiosk to record your first log.</p>
            </div>
        @endif
    </div>
</div>

@endsection

@push('styles')
<style>
/* ============================================================
   STAFF DASHBOARD COMPACT MODERN SAAS STYLES
============================================================ */

/* ---- Compact Welcome Hero Banner ---- */
.welcome-hero-banner {
    position: relative;
    border-radius: 16px;
    overflow: hidden;
    margin-bottom: 14px;
    padding: 18px 22px;
    background: #090e24;
    box-shadow: 0 8px 24px rgba(15, 23, 42, 0.12);
    border: 1px solid rgba(255, 255, 255, 0.1);
}

.hero-bg-visual {
    position: absolute;
    inset: 0;
    background-size: cover;
    background-position: center right;
    opacity: 0.28;
    filter: saturate(1.2) contrast(1.1);
}

.hero-gradient-overlay {
    position: absolute;
    inset: 0;
    background: linear-gradient(100deg, rgba(8, 12, 32, 0.96) 0%, rgba(15, 23, 42, 0.9) 45%, rgba(67, 56, 202, 0.75) 100%);
}

.hero-decorative-orb {
    position: absolute;
    border-radius: 50%;
    filter: blur(50px);
    pointer-events: none;
}
.hero-orb-1 {
    width: 220px; height: 220px;
    background: radial-gradient(circle, rgba(124, 58, 237, 0.4) 0%, transparent 70%);
    top: -50px; right: 10%;
}
.hero-orb-2 {
    width: 180px; height: 180px;
    background: radial-gradient(circle, rgba(59, 130, 246, 0.3) 0%, transparent 70%);
    bottom: -30px; right: 28%;
}

.hero-content {
    position: relative;
    z-index: 2;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 16px;
    flex-wrap: wrap;
}

.hero-left {
    max-width: 600px;
}

.hero-greeting-pill {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 3px 10px;
    border-radius: 999px;
    background: rgba(255, 255, 255, 0.1);
    border: 1px solid rgba(255, 255, 255, 0.16);
    backdrop-filter: blur(8px);
    -webkit-backdrop-filter: blur(8px);
    font-size: 10.5px;
    font-weight: 700;
    color: #e2e8f0;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin-bottom: 6px;
}

.greeting-sun-icon {
    font-size: 11.5px;
}

.hero-title {
    font-size: 20px;
    font-weight: 800;
    color: #ffffff;
    letter-spacing: -0.4px;
    line-height: 1.25;
    margin-bottom: 8px;
}

.hero-name-gradient {
    background: linear-gradient(135deg, #a5b4fc 0%, #c084fc 50%, #ffffff 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
}

.hero-meta-badges {
    display: flex;
    align-items: center;
    gap: 6px;
    flex-wrap: wrap;
}

.hero-badge {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    padding: 3px 9px;
    border-radius: 6px;
    background: rgba(255, 255, 255, 0.08);
    border: 1px solid rgba(255, 255, 255, 0.12);
    font-size: 11px;
    font-weight: 600;
    color: #cbd5e1;
}

.hero-badge svg {
    color: #93c5fd;
}

.hero-right {
    display: flex;
    flex-direction: column;
    align-items: flex-end;
    gap: 8px;
}

.hero-date-display {
    display: flex;
    align-items: center;
    gap: 6px;
    font-size: 12px;
    font-weight: 600;
    color: rgba(255, 255, 255, 0.85);
}

.hero-date-display svg {
    color: #818cf8;
}

.hero-status-pill {
    display: inline-flex;
    align-items: center;
    gap: 7px;
    padding: 6px 14px;
    border-radius: 999px;
    border: 1.5px solid;
    backdrop-filter: blur(10px);
    -webkit-backdrop-filter: blur(10px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
}

.hero-status-dot {
    width: 8px;
    height: 8px;
    border-radius: 50%;
    animation: statusBeaconPulse 2s infinite ease-in-out;
}

@keyframes statusBeaconPulse {
    0%, 100% { transform: scale(0.9); opacity: 0.8; }
    50% { transform: scale(1.25); opacity: 1; box-shadow: 0 0 10px currentColor; }
}

.hero-status-text {
    font-size: 12.5px;
    font-weight: 800;
    letter-spacing: 0.2px;
    color: #ffffff;
}

/* ---- Compact 4 Summary Cards Grid ---- */
.attendance-summary-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 12px;
    margin-bottom: 14px;
}

.summary-card {
    background: #ffffff;
    border-radius: 13px;
    border: 1px solid #e2e8f0;
    box-shadow: 0 1px 4px rgba(0, 0, 0, 0.04);
    position: relative;
    overflow: hidden;
    transition: transform 0.18s ease, box-shadow 0.18s ease;
}

.summary-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 18px rgba(0, 0, 0, 0.07);
}

.summary-card-inner {
    padding: 12px 14px 10px;
}

.summary-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 8px;
}

.summary-icon-box {
    width: 34px;
    height: 34px;
    border-radius: 9px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

.summary-icon-box svg {
    width: 17px;
    height: 17px;
}

.summary-tag {
    font-size: 11px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    color: #64748b;
}

.summary-value {
    font-size: 20px;
    font-weight: 900;
    color: #0f172a;
    letter-spacing: -0.4px;
    line-height: 1.2;
    margin-bottom: 4px;
}

.summary-status {
    min-height: 20px;
    display: flex;
    align-items: center;
}

.status-pill-sub {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    font-size: 11px;
    font-weight: 700;
    padding: 2px 7px;
    border-radius: 5px;
}

.pill-ontime { background: #dcfce7; color: #15803d; }
.pill-late   { background: #fee2e2; color: #b91c1c; }
.pill-completed { background: #dbeafe; color: #1e40af; }
.pill-working   { background: #dcfce7; color: #15803d; }
.pill-active-break { background: #fef3c7; color: #b45309; }
.pill-finished-break { background: #f1f5f9; color: #475569; }
.pill-duration { background: #e0e7ff; color: #4338ca; }
.pill-pending  { background: #f8fafc; color: #94a3b8; font-weight: 500; font-size: 10.5px; }

.dot-sm {
    width: 5px;
    height: 5px;
    border-radius: 50%;
}
.bg-ontime { background: #16a34a; }
.bg-late   { background: #dc2626; }
.bg-blue   { background: #2563eb; }
.bg-green-pulse { background: #16a34a; animation: pulseDotSm 1.5s infinite; }
.bg-amber-pulse { background: #d97706; animation: pulseDotSm 1.5s infinite; }

@keyframes pulseDotSm {
    0%, 100% { opacity: 0.6; transform: scale(0.9); }
    50% { opacity: 1; transform: scale(1.2); }
}

.card-accent-bar {
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    height: 3px;
}

/* Card Accent Themes */
.card-clock-in .summary-icon-box { background: #ecfdf5; color: #059669; }
.bar-emerald { background: linear-gradient(90deg, #10b981, #059669); }

.card-clock-out .summary-icon-box { background: #fff1f2; color: #e11d48; }
.bar-rose { background: linear-gradient(90deg, #f43f5e, #be123c); }

.card-start-break .summary-icon-box { background: #fffbeb; color: #d97706; }
.bar-amber { background: linear-gradient(90deg, #f59e0b, #d97706); }

.card-end-break .summary-icon-box { background: #eff6ff; color: #2563eb; }
.bar-indigo { background: linear-gradient(90deg, #3b82f6, #6366f1); }

/* ---- Two-Column Main Content Grid ---- */
.dashboard-two-col-grid {
    display: grid;
    grid-template-columns: 1.15fr 1fr;
    gap: 14px;
    margin-bottom: 14px;
}

/* Common SaaS Card */
.saas-card {
    background: #ffffff;
    border-radius: 14px;
    border: 1px solid #e2e8f0;
    box-shadow: 0 1px 6px rgba(0, 0, 0, 0.04);
    padding: 16px 18px;
}

.saas-card-header {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 10px;
    margin-bottom: 12px;
}

.saas-card-title {
    font-size: 15px;
    font-weight: 800;
    color: #0f172a;
    letter-spacing: -0.2px;
    line-height: 1.2;
}

.saas-card-subtitle {
    font-size: 11.5px;
    color: #64748b;
    margin-top: 2px;
    font-weight: 500;
}

.live-indicator-badge {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    padding: 3px 8px;
    border-radius: 999px;
    background: #f0fdf4;
    border: 1px solid #bbf7d0;
    color: #15803d;
    font-size: 10.5px;
    font-weight: 700;
}

.beacon-dot {
    width: 5px;
    height: 5px;
    border-radius: 50%;
    background: #16a34a;
    box-shadow: 0 0 5px #16a34a;
}

/* Today's Overview Tiles */
.today-overview-tiles {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 8px;
    margin-bottom: 12px;
}

.overview-tile {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 9px 12px;
    border-radius: 10px;
    background: #f8fafc;
    border: 1px solid #edf2f7;
    transition: all 0.15s ease;
}

.overview-tile:hover {
    background: #f1f5f9;
    border-color: #e2e8f0;
}

.tile-icon-circle {
    width: 32px;
    height: 32px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

.tile-status .tile-icon-circle { background: #e0f2fe; color: #0284c7; }
.tile-hours .tile-icon-circle { background: #ede9fe; color: #7c3aed; }
.tile-shift .tile-icon-circle { background: #fef3c7; color: #d97706; }
.tile-break .tile-icon-circle { background: #dcfce7; color: #16a34a; }

.tile-details {
    display: flex;
    flex-direction: column;
}

.tile-label {
    font-size: 10px;
    font-weight: 700;
    color: #64748b;
    text-transform: uppercase;
    letter-spacing: 0.3px;
}

.tile-value {
    font-size: 14px;
    font-weight: 800;
    color: #0f172a;
    letter-spacing: -0.2px;
    margin-top: 1px;
}

/* Shift Milestone Track */
.shift-progress-wrapper {
    background: #fafbfd;
    border: 1px solid #eef2f6;
    border-radius: 10px;
    padding: 10px 14px;
}

.shift-progress-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 8px;
}

.progress-title {
    font-size: 11px;
    font-weight: 700;
    color: #334155;
    text-transform: uppercase;
    letter-spacing: 0.4px;
}

.progress-info {
    font-size: 10.5px;
    font-weight: 700;
    color: #4f46e5;
}

.milestones-track {
    display: flex;
    align-items: center;
    justify-content: space-between;
    position: relative;
    padding: 0 6px;
}

.milestone-node {
    display: flex;
    flex-direction: column;
    align-items: center;
    z-index: 2;
    position: relative;
}

.node-dot {
    width: 11px;
    height: 11px;
    border-radius: 50%;
    border: 2px solid #ffffff;
    box-shadow: 0 0 0 1.5px #cbd5e1;
    background: #ffffff;
    transition: all 0.2s ease;
}

.node-done .node-dot {
    background: #4f46e5;
    box-shadow: 0 0 0 1.5px #4f46e5;
}

.node-title {
    font-size: 10px;
    font-weight: 700;
    color: #475569;
    margin-top: 4px;
}

.node-time {
    font-size: 9.5px;
    font-weight: 600;
    color: #94a3b8;
}

.node-done .node-title {
    color: #0f172a;
}

.milestone-line {
    flex: 1;
    height: 2px;
    background: #e2e8f0;
    margin: 0 -3px;
    margin-bottom: 18px;
    z-index: 1;
}

.milestone-line.line-done {
    background: #4f46e5;
}

/* Modern Attendance Action Buttons */
.modern-action-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 8px;
}

.action-btn-link {
    display: contents;
    text-decoration: none;
}

.modern-action-btn {
    width: 100%;
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 10px 12px;
    border-radius: 10px;
    border: 1.5px solid transparent;
    cursor: pointer;
    font-family: inherit;
    transition: all 0.18s ease;
    text-align: left;
}

.modern-action-btn:disabled {
    opacity: 0.42;
    cursor: not-allowed;
    pointer-events: none;
    filter: grayscale(0.2);
}

.action-btn-icon-wrap {
    width: 34px;
    height: 34px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

.action-btn-icon-wrap svg {
    width: 18px;
    height: 18px;
}

.action-btn-content {
    display: flex;
    flex-direction: column;
}

.btn-main-label {
    font-size: 13px;
    font-weight: 800;
    letter-spacing: -0.2px;
    line-height: 1.2;
}

.btn-sub-label {
    font-size: 10.5px;
    font-weight: 500;
    margin-top: 1px;
}

/* Action Button Themes */
.btn-action-in {
    background: #f0fdf4;
    border-color: #86efac;
    color: #15803d;
}
.btn-action-in .action-btn-icon-wrap {
    background: #16a34a;
    color: #ffffff;
}
.btn-action-in:not(:disabled):hover {
    background: #dcfce7;
    transform: translateY(-1px);
    box-shadow: 0 4px 14px rgba(22, 163, 74, 0.2);
}

.btn-action-out {
    background: #fff1f2;
    border-color: #fda4af;
    color: #be123c;
}
.btn-action-out .action-btn-icon-wrap {
    background: #e11d48;
    color: #ffffff;
}
.btn-action-out:not(:disabled):hover {
    background: #ffe4e6;
    transform: translateY(-1px);
    box-shadow: 0 4px 14px rgba(225, 29, 72, 0.2);
}

.btn-action-brk {
    background: #fffbeb;
    border-color: #fcd34d;
    color: #b45309;
}
.btn-action-brk .action-btn-icon-wrap {
    background: #d97706;
    color: #ffffff;
}
.btn-action-brk:not(:disabled):hover {
    background: #fef3c7;
    transform: translateY(-1px);
    box-shadow: 0 4px 14px rgba(217, 119, 6, 0.2);
}

.btn-action-end {
    background: #eff6ff;
    border-color: #93c5fd;
    color: #1d4ed8;
}
.btn-action-end .action-btn-icon-wrap {
    background: #2563eb;
    color: #ffffff;
}
.btn-action-end:not(:disabled):hover {
    background: #dbeafe;
    transform: translateY(-1px);
    box-shadow: 0 4px 14px rgba(37, 99, 235, 0.2);
}

/* Callout Alerts */
.warning-callout {
    margin-top: 10px;
    padding: 9px 12px;
    background: #fffbeb;
    border: 1px solid #fde68a;
    border-radius: 10px;
    font-size: 11.5px;
    color: #92400e;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 8px;
}

.location-warning-box {
    margin-top: 10px;
    padding: 9px 12px;
    background: #fef2f2;
    border: 1px solid #fecaca;
    border-radius: 10px;
    font-size: 11.5px;
    color: #991b1b;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 8px;
}

/* ---- Quick Shortcuts Section ---- */
.quick-shortcuts-section {
    margin-bottom: 14px;
}

.section-title-wrap {
    margin-bottom: 8px;
}

.quick-shortcuts-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 12px;
}

.shortcut-card {
    background: #ffffff;
    border-radius: 12px;
    border: 1px solid #e2e8f0;
    padding: 12px 14px;
    display: flex;
    align-items: center;
    gap: 10px;
    text-decoration: none;
    box-shadow: 0 1px 4px rgba(0, 0, 0, 0.04);
    transition: all 0.18s ease;
}

.shortcut-card:hover {
    transform: translateY(-2px);
}

.card-blue-glow:hover {
    border-color: #93c5fd;
    box-shadow: 0 6px 18px rgba(37, 99, 235, 0.1);
}

.card-green-glow:hover {
    border-color: #86efac;
    box-shadow: 0 6px 18px rgba(22, 163, 74, 0.1);
}

.card-amber-glow:hover {
    border-color: #fcd34d;
    box-shadow: 0 6px 18px rgba(217, 119, 6, 0.12);
}

.card-purple-glow:hover {
    border-color: #c4b5fd;
    box-shadow: 0 6px 18px rgba(124, 58, 237, 0.12);
}

.shortcut-icon-circle {
    width: 38px;
    height: 38px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

.bg-blue-tint   { background: #eff6ff; }
.bg-green-tint  { background: #f0fdf4; }
.bg-amber-tint  { background: #fffbeb; }
.bg-purple-tint { background: #f5f3ff; }

.shortcut-meta {
    flex: 1;
    overflow: hidden;
}

.shortcut-name {
    font-size: 13px;
    font-weight: 800;
    color: #0f172a;
    display: block;
    letter-spacing: -0.2px;
}

.shortcut-desc {
    font-size: 11px;
    color: #64748b;
    margin-top: 2px;
    display: block;
    line-height: 1.3;
}

.ext-badge {
    font-size: 9.5px;
    font-weight: 700;
    padding: 1px 5px;
    border-radius: 999px;
    background: #ede9fe;
    color: #7c3aed;
}

.shortcut-arrow {
    color: #cbd5e1;
    display: flex;
    align-items: center;
    transition: transform 0.18s ease, color 0.18s ease;
}

.shortcut-card:hover .shortcut-arrow {
    color: #4f46e5;
    transform: translateX(3px);
}

/* ---- Recent History Card ---- */
.recent-history-card {
    padding: 16px 18px;
    border-radius: 14px;
}

.history-header {
    margin-bottom: 12px;
}

.btn-view-all {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 5px 11px;
    border-radius: 7px;
    background: #f8fafc;
    border: 1.5px solid #e2e8f0;
    color: #475569;
    font-size: 11.5px;
    font-weight: 700;
    text-decoration: none;
    transition: all 0.18s ease;
}

.btn-view-all:hover {
    background: #ffffff;
    border-color: #6366f1;
    color: #4f46e5;
    box-shadow: 0 2px 8px rgba(99, 102, 241, 0.1);
}

.history-table-container {
    overflow-x: auto;
    border-radius: 10px;
    border: 1px solid #f1f5f9;
}

.saas-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 12.5px;
    text-align: left;
}

.saas-table thead th {
    background: #f8fafc;
    padding: 9px 12px;
    font-size: 10.5px;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    color: #64748b;
    border-bottom: 1px solid #e2e8f0;
    white-space: nowrap;
}

.saas-table tbody td {
    padding: 10px 12px;
    color: #334155;
    border-bottom: 1px solid #f1f5f9;
    vertical-align: middle;
}

.saas-table tbody tr:last-child td {
    border-bottom: none;
}

.saas-table tbody tr:hover {
    background: #fafbfd;
}

.td-date-cell {
    display: flex;
    flex-direction: column;
}

.date-main {
    font-size: 12.5px;
    font-weight: 800;
    color: #0f172a;
}

.date-day {
    font-size: 10.5px;
    color: #94a3b8;
    margin-top: 1px;
    display: flex;
    align-items: center;
    gap: 5px;
}

.pill-today {
    font-size: 9px;
    font-weight: 800;
    padding: 1px 4px;
    border-radius: 4px;
    background: #eff6ff;
    color: #2563eb;
    text-transform: uppercase;
}

.time-stamp-box {
    display: flex;
    align-items: center;
    gap: 6px;
}

.time-text {
    font-weight: 700;
    color: #1e293b;
    font-size: 12.5px;
}

.time-empty {
    color: #94a3b8;
    font-style: italic;
    font-size: 12px;
}

.micro-badge {
    font-size: 9.5px;
    font-weight: 700;
    padding: 1px 5px;
    border-radius: 4px;
}

.badge-ontime { background: #dcfce7; color: #16a34a; }
.badge-late   { background: #fee2e2; color: #dc2626; }

.badge-neutral {
    display: inline-block;
    padding: 2px 7px;
    border-radius: 5px;
    background: #f1f5f9;
    color: #475569;
    font-weight: 700;
    font-size: 11px;
}

.work-hours-text {
    font-weight: 800;
    color: #4f46e5;
    font-size: 12.5px;
}

.method-tag {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    font-size: 11px;
    color: #64748b;
    font-weight: 600;
}

/* Empty state */
.empty-history-state {
    padding: 28px 16px;
    text-align: center;
}

.empty-icon-wrap {
    width: 48px;
    height: 48px;
    border-radius: 12px;
    background: #f1f5f9;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 10px;
}

.empty-history-state h3 {
    font-size: 14px;
    font-weight: 800;
    color: #334155;
    margin-bottom: 3px;
}

.empty-history-state p {
    font-size: 12px;
    color: #94a3b8;
}

/* Responsive Rules */
@media (max-width: 1200px) {
    .attendance-summary-grid {
        grid-template-columns: repeat(2, 1fr);
    }
    .dashboard-two-col-grid {
        grid-template-columns: 1fr;
    }
    .quick-shortcuts-grid {
        grid-template-columns: 1fr;
    }
}

@media (max-width: 768px) {
    .welcome-hero-banner {
        padding: 16px 16px;
    }
    .hero-title {
        font-size: 18px;
    }
    .hero-right {
        align-items: flex-start;
        width: 100%;
        flex-direction: row;
        justify-content: space-between;
    }
    .attendance-summary-grid {
        grid-template-columns: 1fr 1fr;
    }
    .today-overview-tiles {
        grid-template-columns: 1fr;
    }
    .modern-action-grid {
        grid-template-columns: 1fr;
    }
}

@media (max-width: 500px) {
    .attendance-summary-grid {
        grid-template-columns: 1fr;
    }
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const allowedLocations = @json(\App\Services\GeolocationService::getActiveLocations());

    function getDistance(lat1, lon1, lat2, lon2) {
        const R = 6371e3;
        const p1 = lat1 * Math.PI/180;
        const p2 = lat2 * Math.PI/180;
        const dp = (lat2-lat1) * Math.PI/180;
        const dl = (lon2-lon1) * Math.PI/180;
        const a = Math.sin(dp/2) * Math.sin(dp/2) +
                  Math.cos(p1) * Math.cos(p2) *
                  Math.sin(dl/2) * Math.sin(dl/2);
        const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
        return R * c;
    }

    if (allowedLocations && allowedLocations.length > 0) {
        const actionBtns = document.querySelectorAll('.modern-action-btn');
        const msgDiv = document.getElementById('locationMsg');
        const msgSpan = msgDiv ? msgDiv.querySelector('span') : null;
        
        if (actionBtns.length > 0 && msgDiv && msgSpan) {
            // Temporarily store disabled states until location is verified
            const originalStates = Array.from(actionBtns).map(btn => btn.disabled);
            actionBtns.forEach(btn => btn.disabled = true);
            msgDiv.style.display = 'flex';

            if ("geolocation" in navigator) {
                navigator.geolocation.getCurrentPosition(function(position) {
                    const userLat = position.coords.latitude;
                    const userLng = position.coords.longitude;
                    let validLocation = null;
                    let nearestLocation = null;
                    let minDistance = Infinity;

                    for (const loc of allowedLocations) {
                        const dist = getDistance(userLat, userLng, loc.lat, loc.lng);
                        if (dist <= loc.radius && !validLocation) {
                            validLocation = { ...loc, distance: dist };
                        }
                        if (dist < minDistance) {
                            minDistance = dist;
                            nearestLocation = { ...loc, distance: dist };
                        }
                    }

                    if (validLocation) {
                        // Valid location within approved boundary
                        msgDiv.style.display = 'none';
                        actionBtns.forEach((btn, idx) => { btn.disabled = originalStates[idx]; });
                    } else {
                        const nearestDist = Math.round(minDistance);
                        const nearestRad = nearestLocation ? Math.round(nearestLocation.radius) : 500;
                        const nearestName = nearestLocation ? nearestLocation.name : 'Workplace';
                        msgSpan.innerText = "Outside Allowed Location: You are outside all approved attendance locations (Nearest: " + nearestName + ", " + nearestDist + "m away, max allowed: " + nearestRad + "m). Attendance actions are restricted.";
                    }
                }, function(error) {
                    if (error.code === 1) {
                        msgSpan.innerText = "Location permission denied. Please allow location access in your browser to perform attendance.";
                    } else if (error.code === 2) {
                        msgSpan.innerText = "Location unavailable. Please ensure device GPS is turned on.";
                    } else if (error.code === 3) {
                        msgSpan.innerText = "Location check timed out. Please refresh.";
                    } else {
                        msgSpan.innerText = "Location cannot be verified. Attendance restricted.";
                    }
                }, { enableHighAccuracy: true, timeout: 10000, maximumAge: 0 });
            } else {
                msgSpan.innerText = "Geolocation is not supported by your browser.";
            }
        }
    }
});
</script>
@endpush
