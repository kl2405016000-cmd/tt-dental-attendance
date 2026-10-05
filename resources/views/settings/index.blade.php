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

    {{-- Allowed Geolocation Locations (Multi-Location Geofence) --}}
    <div class="card">
        <form action="{{ route('settings.update') }}" method="POST">
            @csrf
            <div class="card-header" style="border-bottom:1px solid #f1f5f9; padding-bottom:15px; margin-bottom:15px;">
                <div>
                    <div class="card-title">Allowed Geolocation Locations (3 Locations)</div>
                    <div class="card-subtitle">Attendance is valid when staff is inside ANY of these 3 locations (Default radius: 500m)</div>
                </div>
            </div>

            {{-- Location 1: Workplace --}}
            <div style="background:#f8fafc; border:1px solid #e2e8f0; border-radius:8px; padding:15px; margin-bottom:20px;">
                <div style="font-weight:700; color:#1e293b; margin-bottom:10px; display:flex; align-items:center; gap:8px;">
                    <span style="background:#3b82f6; color:#fff; border-radius:50%; width:22px; height:22px; display:inline-flex; align-items:center; justify-content:center; font-size:12px;">1</span>
                    Location 1 — Workplace
                </div>
                <div class="form-group" style="margin-bottom:10px;">
                    <label class="form-label">Location Name</label>
                    <input type="text" name="location_1_name" class="form-input" value="{{ $settings['location_1_name'] ?? 'Workplace' }}" required>
                </div>
                <div class="form-group" style="margin-bottom:10px;">
                    <label class="form-label">Address Reference</label>
                    <input type="text" name="location_1_address" class="form-input" value="{{ $settings['location_1_address'] ?? 'Shop Apartment, No. 20-1F (1), Jalan Orkid 4, Taman Orkid, 43200 Cheras, Selangor, Malaysia.' }}">
                </div>
                <div style="display:grid; grid-template-columns:1fr 1fr 1fr; gap:12px;">
                    <div class="form-group">
                        <label class="form-label">Latitude</label>
                        <input type="text" name="location_1_lat" class="form-input" value="{{ $settings['location_1_lat'] ?? ($settings['workplace_lat'] ?? '3.0783092') }}" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Longitude</label>
                        <input type="text" name="location_1_lng" class="form-input" value="{{ $settings['location_1_lng'] ?? ($settings['workplace_lng'] ?? '101.7664661') }}" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Radius (Meters)</label>
                        <input type="number" name="location_1_radius" class="form-input" value="{{ $settings['location_1_radius'] ?? ($settings['workplace_radius'] ?? '500') }}" required>
                    </div>
                </div>
            </div>

            {{-- Location 2: Home / Demo --}}
            <div style="background:#f8fafc; border:1px solid #e2e8f0; border-radius:8px; padding:15px; margin-bottom:20px;">
                <div style="font-weight:700; color:#1e293b; margin-bottom:10px; display:flex; align-items:center; gap:8px;">
                    <span style="background:#10b981; color:#fff; border-radius:50%; width:22px; height:22px; display:inline-flex; align-items:center; justify-content:center; font-size:12px;">2</span>
                    Location 2 — Home / Demo
                </div>
                <div class="form-group" style="margin-bottom:10px;">
                    <label class="form-label">Location Name</label>
                    <input type="text" name="location_2_name" class="form-input" value="{{ $settings['location_2_name'] ?? 'Home / Demo' }}" required>
                </div>
                <div class="form-group" style="margin-bottom:10px;">
                    <label class="form-label">Address Reference</label>
                    <input type="text" name="location_2_address" class="form-input" value="{{ $settings['location_2_address'] ?? 'The Sky Residensi, Taman Shamelin Perkasa, 56100 Kuala Lumpur, Malaysia.' }}">
                </div>
                <div style="display:grid; grid-template-columns:1fr 1fr 1fr; gap:12px;">
                    <div class="form-group">
                        <label class="form-label">Latitude</label>
                        <input type="text" name="location_2_lat" class="form-input" value="{{ $settings['location_2_lat'] ?? '3.1247527' }}" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Longitude</label>
                        <input type="text" name="location_2_lng" class="form-input" value="{{ $settings['location_2_lng'] ?? '101.7374223' }}" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Radius (Meters)</label>
                        <input type="number" name="location_2_radius" class="form-input" value="{{ $settings['location_2_radius'] ?? '500' }}" required>
                    </div>
                </div>
            </div>

            {{-- Location 3: College / Demo --}}
            <div style="background:#f8fafc; border:1px solid #e2e8f0; border-radius:8px; padding:15px; margin-bottom:20px;">
                <div style="font-weight:700; color:#1e293b; margin-bottom:10px; display:flex; align-items:center; gap:8px;">
                    <span style="background:#8b5cf6; color:#fff; border-radius:50%; width:22px; height:22px; display:inline-flex; align-items:center; justify-content:center; font-size:12px;">3</span>
                    Location 3 — College / Demo
                </div>
                <div class="form-group" style="margin-bottom:10px;">
                    <label class="form-label">Location Name</label>
                    <input type="text" name="location_3_name" class="form-input" value="{{ $settings['location_3_name'] ?? 'College / Demo' }}" required>
                </div>
                <div class="form-group" style="margin-bottom:10px;">
                    <label class="form-label">Address Reference</label>
                    <input type="text" name="location_3_address" class="form-input" value="{{ $settings['location_3_address'] ?? 'Universiti Poly-Tech Malaysia (UPTM), Jalan 6/91, Taman Shamelin Perkasa, 56100 Kuala Lumpur, Wilayah Persekutuan Kuala Lumpur.' }}">
                </div>
                <div style="display:grid; grid-template-columns:1fr 1fr 1fr; gap:12px;">
                    <div class="form-group">
                        <label class="form-label">Latitude</label>
                        <input type="text" name="location_3_lat" class="form-input" value="{{ $settings['location_3_lat'] ?? '3.1276441' }}" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Longitude</label>
                        <input type="text" name="location_3_lng" class="form-input" value="{{ $settings['location_3_lng'] ?? '101.7373364' }}" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Radius (Meters)</label>
                        <input type="number" name="location_3_radius" class="form-input" value="{{ $settings['location_3_radius'] ?? '500' }}" required>
                    </div>
                </div>
            </div>

            <button class="btn btn-primary" type="submit">Save Allowed Geolocation Settings</button>
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
