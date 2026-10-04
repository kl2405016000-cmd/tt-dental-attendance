@extends('layouts.app')

@section('title', 'System Settings — TT Dental Lab')
@section('header_title', 'System Settings')

@push('styles')
<style>
    .settings-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
        margin-bottom: 20px;
    }
    @media (max-width: 900px) {
        .settings-grid {
            grid-template-columns: 1fr;
        }
    }
</style>
@endpush

@section('content')

<div class="page-header">
    <div>
        <div class="page-header-title">System Settings & Configuration</div>
        <div class="page-header-sub">Manage laboratory operations, attendance policies, geolocation rules, and kiosk settings</div>
    </div>
    <div class="page-header-actions">
        <a href="{{ route('admin.dashboard') }}" class="btn btn-outline btn-sm">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Dashboard
        </a>
    </div>
</div>

@if(session('success'))
<div style="padding:15px; background:#dcfce7; color:#166534; border-radius:10px; border:1px solid #bbf7d0; margin-bottom:20px; font-weight:600; font-size:14px; display:flex; align-items:center; gap:10px;">
    <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
    {{ session('success') }}
</div>
@endif

<div class="settings-grid">
    {{-- General Settings --}}
    <div class="card">
        <form action="{{ route('settings.update') }}" method="POST">
            @csrf
            <div class="card-header" style="border-bottom:1px solid #f1f5f9; padding-bottom:15px; margin-bottom:15px;">
                <div>
                    <div class="card-title">General Settings</div>
                    <div class="card-subtitle">Global company details</div>
                </div>
            </div>
            <div class="form-group">
                <label class="form-label">Company Name</label>
                <input type="text" name="company_name" class="form-input" value="{{ $settings['company_name'] ?? 'TT Dental Laboratory' }}" required>
            </div>
            <button class="btn btn-primary" type="submit">Save General Settings</button>
        </form>
    </div>

    {{-- Working Hours & Late Threshold --}}
    <div class="card">
        <form action="{{ route('settings.update') }}" method="POST">
            @csrf
            <div class="card-header" style="border-bottom:1px solid #f1f5f9; padding-bottom:15px; margin-bottom:15px;">
                <div>
                    <div class="card-title">Working Hours & Late Threshold</div>
                    <div class="card-subtitle">Configure shift start time and grace periods</div>
                </div>
            </div>
            <div class="form-group">
                <label class="form-label">Standard Work Shift Start (HH:MM)</label>
                <input type="time" name="work_shift_start" class="form-input" value="{{ $settings['work_shift_start'] ?? '08:30' }}" required>
            </div>
            <div class="form-group">
                <label class="form-label">Late Threshold (Grace Period in Minutes)</label>
                <input type="number" name="grace_period" class="form-input" value="{{ $settings['grace_period'] ?? '30' }}" required>
            </div>
            <button class="btn btn-primary" type="submit">Save Working Hours</button>
        </form>
    </div>

    {{-- Workplace Location & Geofence --}}
    <div class="card">
        <form action="{{ route('settings.update') }}" method="POST">
            @csrf
            <div class="card-header" style="border-bottom:1px solid #f1f5f9; padding-bottom:15px; margin-bottom:15px;">
                <div>
                    <div class="card-title">Workplace Location & Geofence</div>
                    <div class="card-subtitle">Restrict staff attendance actions by location</div>
                </div>
            </div>
            <div class="form-group">
                <label class="form-label">Workplace Latitude</label>
                <input type="text" name="workplace_lat" class="form-input" value="{{ $settings['workplace_lat'] ?? '3.1390' }}" required>
            </div>
            <div class="form-group">
                <label class="form-label">Workplace Longitude</label>
                <input type="text" name="workplace_lng" class="form-input" value="{{ $settings['workplace_lng'] ?? '101.6869' }}" required>
            </div>
            <div class="form-group">
                <label class="form-label">Allowed Radius (Meters)</label>
                <input type="number" name="workplace_radius" class="form-input" value="{{ $settings['workplace_radius'] ?? '100' }}" required>
            </div>
            <button class="btn btn-primary" type="submit">Save Geofence Settings</button>
        </form>
    </div>

    {{-- Attendance Settings --}}
    <div class="card">
        <form action="{{ route('settings.update') }}" method="POST">
            @csrf
            <div class="card-header" style="border-bottom:1px solid #f1f5f9; padding-bottom:15px; margin-bottom:15px;">
                <div>
                    <div class="card-title">Attendance Settings</div>
                    <div class="card-subtitle">Other attendance-related policies</div>
                </div>
            </div>
            <div class="form-group">
                <label class="form-label">Daily Working Hours</label>
                <input type="text" name="daily_working_hours" class="form-input" value="{{ $settings['daily_working_hours'] ?? '8.0' }}" required>
            </div>
            <div class="form-group">
                <label class="form-label">Standard Lunch Break</label>
                <input type="text" name="lunch_break" class="form-input" value="{{ $settings['lunch_break'] ?? '60 minutes (12:00 PM – 01:00 PM)' }}" required>
            </div>
            <button class="btn btn-primary" type="submit">Save Attendance Settings</button>
        </form>
    </div>

    {{-- Face Recognition Settings --}}
    <div class="card">
        <form action="{{ route('settings.update') }}" method="POST">
            @csrf
            <div class="card-header" style="border-bottom:1px solid #f1f5f9; padding-bottom:15px; margin-bottom:15px;">
                <div>
                    <div class="card-title">Face Recognition Settings</div>
                    <div class="card-subtitle">Biometric threshold parameters</div>
                </div>
            </div>
            <div class="form-group">
                <label class="form-label">Match Distance Threshold (Lower is stricter)</label>
                <input type="text" name="face_match_threshold" class="form-input" value="{{ $settings['face_match_threshold'] ?? '0.55' }}" required>
            </div>
            <button class="btn btn-primary" type="submit">Save Face Settings</button>
        </form>
    </div>

    {{-- Notification & Reminder Settings --}}
    <div class="card">
        <form action="{{ route('settings.update') }}" method="POST">
            @csrf
            <div class="card-header" style="border-bottom:1px solid #f1f5f9; padding-bottom:15px; margin-bottom:15px;">
                <div>
                    <div class="card-title">Notification & Reminder Settings</div>
                    <div class="card-subtitle">Alerts and notification emails</div>
                </div>
            </div>
            <div class="form-group">
                <label class="form-label">Late Arrival Notification Email</label>
                <input type="email" name="late_email" class="form-input" value="{{ $settings['late_email'] ?? 'admin@ttdental.com' }}" required>
            </div>
            <button class="btn btn-primary" type="submit">Save Notification Settings</button>
        </form>
    </div>
</div>

@endsection
