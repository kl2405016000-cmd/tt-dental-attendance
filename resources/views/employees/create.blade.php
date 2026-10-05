@extends('layouts.app')
@section('title', 'Add Employee — TT Dental Lab')
@section('header_title', 'Add Employee')

@section('content')
<div style="max-width: 860px; margin: 0 auto;">
    <div class="card">
        <div class="card-header">
            <div>
                <div class="card-title">Register New Employee</div>
                <div class="card-subtitle">Complete the staff biodata profile and optionally create a Staff Portal login account.</div>
            </div>
            <a href="{{ route('employees.index') }}" class="btn btn-outline btn-sm">← Back to List</a>
        </div>

        <form action="{{ route('employees.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            {{-- 1. PERSONAL INFORMATION --}}
            <div style="background: #f8fafc; border: 1.5px solid #e2e8f0; border-radius: 12px; padding: 18px 20px; margin-bottom: 22px;">
                <div style="font-size: 13px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.05em; color: var(--primary); margin-bottom: 14px; display: flex; align-items: center; gap: 8px;">
                    <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    1. Personal Information
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 16px;">
                    <div class="form-group">
                        <label class="form-label">Employee ID <span class="req">*</span></label>
                        <input type="text" name="employee_id" class="form-input" placeholder="e.g. EMP001" value="{{ old('employee_id') }}" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Full Name <span class="req">*</span></label>
                        <input type="text" name="name" class="form-input" placeholder="e.g. Ahmad bin Ismail" value="{{ old('name') }}" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">IC / Passport Number <span style="font-size: 11px; font-weight: 500; color: var(--text-muted);">(Optional)</span></label>
                        <input type="text" name="ic_passport_number" class="form-input" placeholder="e.g. 950101-14-5678 or A12345678" value="{{ old('ic_passport_number') }}">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Date of Birth</label>
                        <input type="date" name="date_of_birth" class="form-input" value="{{ old('date_of_birth') }}">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Gender</label>
                        <select name="gender" class="form-select">
                            <option value="">— Select Gender —</option>
                            <option value="Male" {{ old('gender') === 'Male' ? 'selected' : '' }}>Male</option>
                            <option value="Female" {{ old('gender') === 'Female' ? 'selected' : '' }}>Female</option>
                            <option value="Other" {{ old('gender') === 'Other' ? 'selected' : '' }}>Other</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Email Address</label>
                        <input type="email" name="email" id="emp_email" class="form-input" placeholder="employee@ttdental.com" value="{{ old('email') }}">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Phone Number</label>
                        <input type="text" name="phone" class="form-input" placeholder="+60 12-345 6789" value="{{ old('phone') }}">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Profile Photo</label>
                        <input type="file" name="profile_photo" accept="image/*" class="form-input" style="padding:8px 12px; cursor:pointer;">
                    </div>
                </div>

                <div class="form-group" style="margin-bottom: 0;">
                    <label class="form-label">Home Address</label>
                    <textarea name="address" rows="2" class="form-input" placeholder="Enter home residential address" style="resize: vertical;">{{ old('address') }}</textarea>
                </div>
            </div>

            {{-- 2. EMPLOYMENT INFORMATION --}}
            <div style="background: #f8fafc; border: 1.5px solid #e2e8f0; border-radius: 12px; padding: 18px 20px; margin-bottom: 22px;">
                <div style="font-size: 13px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.05em; color: var(--primary); margin-bottom: 14px; display: flex; align-items: center; gap: 8px;">
                    <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                    2. Employment Information
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                    <div class="form-group">
                        <label class="form-label">Position / Role</label>
                        <select name="position" id="positionSelect" class="form-select">
                            <option value="">— Select Position —</option>
                            @php
                                $positionOptions = [
                                    'Dental Technician',
                                    'Senior Dental Technician',
                                    'Junior Dental Technician',
                                    'CAD/CAM Technician',
                                    'Lab Assistant',
                                    'Administrator',
                                    'Other',
                                ];
                            @endphp
                            @foreach($positionOptions as $opt)
                                <option value="{{ $opt }}" {{ old('position') === $opt ? 'selected' : '' }}>{{ $opt }}</option>
                            @endforeach
                        </select>
                        <div id="customPositionWrap" style="display: {{ old('position') === 'Other' ? 'block' : 'none' }}; margin-top: 8px;">
                            <input type="text" name="custom_position" class="form-input" placeholder="Specify position / role" value="{{ old('custom_position') }}">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Department</label>
                        <select name="department" id="departmentSelect" class="form-select">
                            <option value="">— Select Department —</option>
                            @php
                                $departmentOptions = [
                                    'Technician',
                                    'CAD/CAM',
                                    'Crown & Bridge',
                                    'Denture',
                                    'Administration',
                                    'Management',
                                    'Other',
                                ];
                            @endphp
                            @foreach($departmentOptions as $opt)
                                <option value="{{ $opt }}" {{ old('department') === $opt ? 'selected' : '' }}>{{ $opt }}</option>
                            @endforeach
                        </select>
                        <div id="customDepartmentWrap" style="display: {{ old('department') === 'Other' ? 'block' : 'none' }}; margin-top: 8px;">
                            <input type="text" name="custom_department" class="form-input" placeholder="Specify department" value="{{ old('custom_department') }}">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Employment Status</label>
                        <select name="employment_status" class="form-select">
                            @foreach(['Full-Time','Part-Time','Contract','Intern'] as $opt)
                                <option value="{{ $opt }}" {{ old('employment_status', 'Full-Time') === $opt ? 'selected' : '' }}>{{ $opt }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Date Joined</label>
                        <input type="date" name="date_joined" class="form-input" value="{{ old('date_joined', date('Y-m-d')) }}">
                    </div>

                    <div class="form-group" style="grid-column: span 2;">
                        <label class="form-label">Active Status</label>
                        <label style="display: flex; align-items: center; gap: 8px; margin-top: 6px; cursor: pointer;">
                            <input type="checkbox" name="status" value="1" {{ old('status', '1') ? 'checked' : '' }} style="width: 16px; height: 16px; accent-color: var(--primary);">
                            <span style="font-size: 13.5px; font-weight: 600; color: var(--text-body);">Active in System (eligible for attendance clocking)</span>
                        </label>
                    </div>
                </div>
            </div>

            {{-- 3. EMERGENCY CONTACT --}}
            <div style="background: #f8fafc; border: 1.5px solid #e2e8f0; border-radius: 12px; padding: 18px 20px; margin-bottom: 22px;">
                <div style="font-size: 13px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.05em; color: var(--primary); margin-bottom: 14px; display: flex; align-items: center; gap: 8px;">
                    <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                    3. Emergency Contact
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 16px;">
                    <div class="form-group">
                        <label class="form-label">Emergency Contact Name</label>
                        <input type="text" name="emergency_contact_name" class="form-input" placeholder="e.g. Siti Sarah" value="{{ old('emergency_contact_name') }}">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Relationship</label>
                        <input type="text" name="emergency_contact_relationship" class="form-input" placeholder="e.g. Spouse, Parent, Sibling" value="{{ old('emergency_contact_relationship') }}">
                    </div>
                    <div class="form-group" style="grid-column: span 2;">
                        <label class="form-label">Emergency Contact Phone</label>
                        <input type="text" name="emergency_contact_phone" class="form-input" placeholder="e.g. +60 12-987 6543" value="{{ old('emergency_contact_phone') }}">
                    </div>
                    <div class="form-group" style="grid-column: span 2; margin-bottom: 0;">
                        <label class="form-label">Emergency Contact Address <span style="font-size: 11px; font-weight: 500; color: var(--text-muted);">(Optional)</span></label>
                        <textarea name="emergency_contact_address" rows="2" class="form-input" placeholder="Enter emergency contact residential address" style="resize: vertical;">{{ old('emergency_contact_address') }}</textarea>
                    </div>
                </div>
            </div>

            {{-- 4. SYSTEM INFORMATION & LOGIN SETUP --}}
            <div style="background: #f8fafc; border: 1.5px solid #e2e8f0; border-radius: 12px; padding: 18px 20px; margin-bottom: 22px;">
                <div style="font-size: 13px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.05em; color: var(--primary); margin-bottom: 14px; display: flex; align-items: center; gap: 8px;">
                    <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                    4. System Information & Staff Portal Login
                </div>

                <label style="display: flex; align-items: center; gap: 10px; cursor: pointer; margin-bottom: 10px;">
                    <input type="checkbox" name="create_account" id="createAccountCheck" value="1" {{ old('create_account') ? 'checked' : '' }} style="width: 16px; height: 16px; accent-color: var(--primary);">
                    <span style="font-weight: 800; font-size: 14px; color: var(--text-head);">Create Staff Portal Login Account</span>
                </label>
                <div style="font-size: 12.5px; color: var(--text-muted); margin-bottom: 12px; padding-left: 26px;">
                    Enables this employee to log in to the Staff Portal to clock in/out, view history, and submit Medical Certificates.
                </div>

                <div id="accountFields" style="display: {{ old('create_account') ? 'block' : 'none' }}; padding-left: 26px;">
                    <label class="form-label">Initial Password (min 6 characters)</label>
                    <input type="password" name="password" class="form-input" placeholder="Set login password" style="max-width: 380px;" autocomplete="new-password">
                </div>
            </div>

            <div style="display: flex; gap: 10px;">
                <button type="submit" class="btn btn-primary">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                    Save Employee
                </button>
                <a href="{{ route('employees.index') }}" class="btn btn-outline">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Create account toggle
    const check = document.getElementById('createAccountCheck');
    const fields = document.getElementById('accountFields');
    if (check && fields) {
        check.addEventListener('change', function() {
            fields.style.display = this.checked ? 'block' : 'none';
        });
    }

    // Custom position toggle
    const posSelect = document.getElementById('positionSelect');
    const customPosWrap = document.getElementById('customPositionWrap');
    if (posSelect && customPosWrap) {
        posSelect.addEventListener('change', function() {
            customPosWrap.style.display = this.value === 'Other' ? 'block' : 'none';
        });
    }

    // Custom department toggle
    const deptSelect = document.getElementById('departmentSelect');
    const customDeptWrap = document.getElementById('customDepartmentWrap');
    if (deptSelect && customDeptWrap) {
        deptSelect.addEventListener('change', function() {
            customDeptWrap.style.display = this.value === 'Other' ? 'block' : 'none';
        });
    }
</script>
@endpush
