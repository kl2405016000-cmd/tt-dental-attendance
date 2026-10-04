@extends('layouts.app')
@section('title','Settings - TT Dental Lab')
@section('header_title','Settings')
@section('content')
<div class="page-heading"><div><h1>System Settings</h1><p>System information and security options.</p></div></div>
<div class="settings-grid">
<div class="settings-card"><div class="settings-icon">🏢</div><h2>TT Dental Laboratory</h2><p>Biometric Attendance System</p><div class="setting-row"><span>Application</span><strong>TT Dental Attendance</strong></div><div class="setting-row"><span>Environment</span><strong>{{ app()->environment() }}</strong></div><div class="setting-row"><span>Server Time</span><strong>{{ now()->format('d M Y, H:i') }}</strong></div></div>
<div class="settings-card"><div class="settings-icon">🔐</div><h2>Security</h2><p>Account protection is enabled for authenticated pages.</p><div class="setting-row"><span>Session protection</span><strong>Enabled</strong></div><div class="setting-row"><span>Password hashing</span><strong>Enabled</strong></div><div class="setting-row"><span>Password recovery</span><strong>OTP verification</strong></div></div>
</div>
@endsection
@push('styles')<style>.settings-grid{display:grid;grid-template-columns:repeat(2,1fr);gap:20px}.settings-card{background:#fff;border:1px solid #e2e8f0;border-radius:16px;padding:24px;box-shadow:0 5px 20px rgba(15,23,42,.04)}.settings-icon{width:46px;height:46px;border-radius:13px;background:#eff6ff;display:flex;align-items:center;justify-content:center;font-size:21px;margin-bottom:15px}.settings-card h2{font-size:18px;margin:0}.settings-card>p{font-size:12px;color:#94a3b8;margin:6px 0 18px}.setting-row{display:flex;justify-content:space-between;gap:20px;padding:13px 0;border-top:1px solid #f1f5f9;font-size:12px}.setting-row span{color:#64748b}.setting-row strong{text-align:right}@media(max-width:700px){.settings-grid{grid-template-columns:1fr}}</style>@endpush
