@extends('layouts.app')
@section('title', 'Edit Employee — TT Dental Lab')
@section('header_title', 'Edit Employee')

@section('content')
<div style="max-width: 860px; margin: 0 auto;">
    <div class="card">
        <div class="card-header">
            <div>
                <div class="card-title">Edit Employee: {{ $employee->name }}</div>
                <div class="card-subtitle">Staff Biodata Profile, employment details, emergency contacts, and linked login credentials.</div>
            </div>
            <div style="display: flex; gap: 8px;">
                <a href="{{ route('employees.show', $employee) }}" class="btn btn-outline btn-sm">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                    View Profile
                </a>
                <a href="{{ route('employees.index') }}" class="btn btn-outline btn-sm">← Back to List</a>
            </div>
        </div>

        <form action="{{ route('employees.update', $employee) }}" method="POST" enctype="multipart/form-data">
            @csrf @method('PUT')

            {{-- 1. PERSONAL INFORMATION --}}
            <div style="background: #f8fafc; border: 1.5px solid #e2e8f0; border-radius: 12px; padding: 18px 20px; margin-bottom: 22px;">
                <div style="font-size: 13px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.05em; color: var(--primary); margin-bottom: 14px; display: flex; align-items: center; gap: 8px;">
                    <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    1. Personal Information
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 16px;">
                    <div class="form-group">
                        <label class="form-label">Employee ID <span class="req">*</span></label>
                        <input type="text" name="employee_id" class="form-input" value="{{ old('employee_id', $employee->employee_id) }}" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Full Name <span class="req">*</span></label>
                        <input type="text" name="name" class="form-input" value="{{ old('name', $employee->name) }}" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">IC / Passport Number <span style="font-size: 11px; font-weight: 500; color: var(--text-muted);">(Optional)</span></label>
                        <input type="text" name="ic_passport_number" class="form-input" placeholder="e.g. 950101-14-5678 or A12345678" value="{{ old('ic_passport_number', $employee->ic_passport_number) }}">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Date of Birth</label>
                        <input type="date" name="date_of_birth" class="form-input" value="{{ old('date_of_birth', $employee->date_of_birth ? ($employee->date_of_birth instanceof \DateTimeInterface ? $employee->date_of_birth->format('Y-m-d') : \Carbon\Carbon::parse($employee->date_of_birth)->format('Y-m-d')) : '') }}">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Gender</label>
                        <select name="gender" class="form-select">
                            <option value="">— Select Gender —</option>
                            <option value="Male" {{ old('gender', $employee->gender) === 'Male' ? 'selected' : '' }}>Male</option>
                            <option value="Female" {{ old('gender', $employee->gender) === 'Female' ? 'selected' : '' }}>Female</option>
                            <option value="Other" {{ old('gender', $employee->gender) === 'Other' ? 'selected' : '' }}>Other</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Email Address</label>
                        <input type="email" name="email" class="form-input" value="{{ old('email', $employee->email) }}">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Phone Number</label>
                        <input type="text" name="phone" class="form-input" value="{{ old('phone', $employee->phone) }}">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Profile Photo</label>
                        <div style="display: flex; align-items: center; gap: 14px;">
                            <img src="{{ $employee->profile_photo_url }}" alt="Profile Preview"
                                 style="width: 44px; height: 44px; border-radius: 10px; object-fit: cover; border: 2px solid var(--card-border);">
                            <input type="file" name="profile_photo" accept="image/*" class="form-input" style="flex:1; padding:8px 12px; cursor:pointer;">
                        </div>
                    </div>
                </div>

                <div class="form-group" style="margin-bottom: 0;">
                    <label class="form-label">Home Address</label>
                    <textarea name="address" rows="2" class="form-input" placeholder="Enter residential address" style="resize: vertical;">{{ old('address', $employee->address) }}</textarea>
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
                        @php
                            $standardPositions = [
                                'Dental Technician',
                                'Senior Dental Technician',
                                'Junior Dental Technician',
                                'CAD/CAM Technician',
                                'Lab Assistant',
                                'Administrator',
                            ];
                            $currentPosition = old('position', $employee->position);
                            $isCustomPosition = !empty($currentPosition) && !in_array($currentPosition, $standardPositions, true) && $currentPosition !== 'Other';
                        @endphp
                        <select name="position" id="editPositionSelect" class="form-select">
                            <option value="">— Select Position —</option>
                            @foreach($standardPositions as $pos)
                                <option value="{{ $pos }}" {{ $currentPosition === $pos ? 'selected' : '' }}>{{ $pos }}</option>
                            @endforeach
                            @if($isCustomPosition)
                                <option value="{{ $currentPosition }}" selected>{{ $currentPosition }}</option>
                            @endif
                            <option value="Other" {{ $currentPosition === 'Other' ? 'selected' : '' }}>Other</option>
                        </select>
                        <div id="editCustomPositionWrap" style="display: {{ $currentPosition === 'Other' ? 'block' : 'none' }}; margin-top: 8px;">
                            <input type="text" name="custom_position" class="form-input" placeholder="Specify position / role" value="{{ old('custom_position') }}">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Department</label>
                        @php
                            $standardDepartments = [
                                'Technician',
                                'CAD/CAM',
                                'Crown & Bridge',
                                'Denture',
                                'Administration',
                                'Management',
                            ];
                            $currentDepartment = old('department', $employee->department);
                            $isCustomDepartment = !empty($currentDepartment) && !in_array($currentDepartment, $standardDepartments, true) && $currentDepartment !== 'Other';
                        @endphp
                        <select name="department" id="editDepartmentSelect" class="form-select">
                            <option value="">— Select Department —</option>
                            @foreach($standardDepartments as $dept)
                                <option value="{{ $dept }}" {{ $currentDepartment === $dept ? 'selected' : '' }}>{{ $dept }}</option>
                            @endforeach
                            @if($isCustomDepartment)
                                <option value="{{ $currentDepartment }}" selected>{{ $currentDepartment }}</option>
                            @endif
                            <option value="Other" {{ $currentDepartment === 'Other' ? 'selected' : '' }}>Other</option>
                        </select>
                        <div id="editCustomDepartmentWrap" style="display: {{ $currentDepartment === 'Other' ? 'block' : 'none' }}; margin-top: 8px;">
                            <input type="text" name="custom_department" class="form-input" placeholder="Specify department" value="{{ old('custom_department') }}">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Employment Status</label>
                        <select name="employment_status" class="form-select">
                            @foreach(['Full-Time','Part-Time','Contract','Intern'] as $opt)
                                <option value="{{ $opt }}" {{ old('employment_status', $employee->employment_status) === $opt ? 'selected' : '' }}>{{ $opt }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Date Joined</label>
                        <input type="date" name="date_joined" class="form-input" value="{{ old('date_joined', $employee->date_joined ? ($employee->date_joined instanceof \DateTimeInterface ? $employee->date_joined->format('Y-m-d') : \Carbon\Carbon::parse($employee->date_joined)->format('Y-m-d')) : '') }}">
                    </div>

                    <div class="form-group" style="grid-column: span 2;">
                        <label class="form-label">Active Status</label>
                        <label style="display: flex; align-items: center; gap: 8px; margin-top: 6px; cursor: pointer;">
                            <input type="checkbox" name="status" value="1" {{ old('status', $employee->status) ? 'checked' : '' }} style="width: 16px; height: 16px; accent-color: var(--primary);">
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
                        <input type="text" name="emergency_contact_name" class="form-input" placeholder="e.g. Siti Sarah" value="{{ old('emergency_contact_name', $employee->emergency_contact_name) }}">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Relationship</label>
                        <input type="text" name="emergency_contact_relationship" class="form-input" placeholder="e.g. Spouse, Parent, Sibling" value="{{ old('emergency_contact_relationship', $employee->emergency_contact_relationship) }}">
                    </div>
                    <div class="form-group" style="grid-column: span 2;">
                        <label class="form-label">Emergency Contact Phone</label>
                        <input type="text" name="emergency_contact_phone" class="form-input" placeholder="e.g. +60 12-987 6543" value="{{ old('emergency_contact_phone', $employee->emergency_contact_phone) }}">
                    </div>
                    <div class="form-group" style="grid-column: span 2; margin-bottom: 0;">
                        <label class="form-label">Emergency Contact Address <span style="font-size: 11px; font-weight: 500; color: var(--text-muted);">(Optional)</span></label>
                        <textarea name="emergency_contact_address" rows="2" class="form-input" placeholder="Enter emergency contact residential address" style="resize: vertical;">{{ old('emergency_contact_address', $employee->emergency_contact_address) }}</textarea>
                    </div>
                </div>
            </div>

            {{-- 4. SYSTEM INFORMATION --}}
            <div style="background: #f8fafc; border: 1.5px solid #e2e8f0; border-radius: 12px; padding: 18px 20px; margin-bottom: 22px;">
                <div style="font-size: 13px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.05em; color: var(--primary); margin-bottom: 14px; display: flex; align-items: center; gap: 8px;">
                    <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z"/></svg>
                    4. System Information
                </div>

                {{-- System Information Read-Only Cards --}}
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 12px; margin-bottom: 16px;">
                    <div style="background: white; border: 1px solid #e2e8f0; border-radius: 8px; padding: 12px;">
                        <div style="font-size: 11px; font-weight: 700; color: var(--text-muted); text-transform: uppercase;">Login Email</div>
                        <div style="font-size: 13px; font-weight: 600; color: var(--text-head); margin-top: 4px; word-break: break-all;">
                            {{ $employee->user ? $employee->user->email : 'Not Created' }}
                        </div>
                    </div>
                    <div style="background: white; border: 1px solid #e2e8f0; border-radius: 8px; padding: 12px;">
                        <div style="font-size: 11px; font-weight: 700; color: var(--text-muted); text-transform: uppercase;">Account Role</div>
                        <div style="font-size: 13px; font-weight: 600; color: var(--text-head); margin-top: 4px;">
                            {{ $employee->user ? ucfirst($employee->user->role) : 'None' }}
                        </div>
                    </div>
                    <div style="background: white; border: 1px solid #e2e8f0; border-radius: 8px; padding: 12px;">
                        <div style="font-size: 11px; font-weight: 700; color: var(--text-muted); text-transform: uppercase;">Face Registration</div>
                        <div style="margin-top: 4px;">
                            @if($employee->has_face_registered)
                                <span class="badge badge-green">✓ Registered</span>
                            @else
                                <span class="badge badge-amber">Not Registered</span>
                            @endif
                        </div>
                    </div>
                    <div style="background: white; border: 1px solid #e2e8f0; border-radius: 8px; padding: 12px;">
                        <div style="font-size: 11px; font-weight: 700; color: var(--text-muted); text-transform: uppercase;">Account Status</div>
                        <div style="margin-top: 4px;">
                            <span class="badge {{ $employee->status ? 'badge-green' : 'badge-red' }}">
                                {{ $employee->status ? 'Active' : 'Inactive' }}
                            </span>
                        </div>
                    </div>
                </div>

                {{-- Account Management & Password Reset --}}
                @if($employee->user)
                    <div class="form-group" style="margin-bottom: 0;">
                        <label class="form-label" for="resetPasswordInput">Reset Password (leave blank to keep current password)</label>
                        <div style="position: relative; max-width: 380px;">
                            <input type="password" id="resetPasswordInput" name="password" class="form-input" placeholder="Enter new password (min 6 characters)" style="width: 100%; padding-right: 42px;" autocomplete="new-password">
                            <button type="button" id="toggleResetPassword" class="toggle-password-btn" aria-label="Show or hide password" title="Show or hide password" style="position: absolute; right: 8px; top: 50%; transform: translateY(-50%); background: none; border: none; padding: 6px; cursor: pointer; color: #94a3b8; display: flex; align-items: center; justify-content: center; border-radius: 6px; transition: color 0.15s ease, background 0.15s ease;">
                                <svg id="eyeOpenIcon" width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                                <svg id="eyeClosedIcon" width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="display: none;">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                @else
                    <label style="display: flex; align-items: center; gap: 10px; cursor: pointer; margin-bottom: 10px;">
                        <input type="checkbox" name="create_account" id="createAccountCheck" value="1" {{ old('create_account') ? 'checked' : '' }} style="width: 16px; height: 16px; accent-color: var(--primary);">
                        <span style="font-weight: 800; font-size: 14px; color: var(--text-head);">Create Staff Portal Login Account</span>
                    </label>
                    <div style="font-size: 12.5px; color: var(--text-muted); margin-bottom: 12px; padding-left: 26px;">
                        This employee does not have a linked login account yet.
                    </div>
                    <div id="accountFields" style="display: {{ old('create_account') ? 'block' : 'none' }}; padding-left: 26px;">
                        <label class="form-label">Initial Password (min 6 characters)</label>
                        <input type="password" name="password" class="form-input" placeholder="Set login password" style="max-width: 380px;">
                    </div>
                @endif
            </div>

            <div style="display: flex; gap: 10px;">
                <button type="submit" class="btn btn-primary">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                    Update Staff Biodata
                </button>
                <a href="{{ route('employees.show', $employee) }}" class="btn btn-outline">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    const check = document.getElementById('createAccountCheck');
    const fields = document.getElementById('accountFields');
    if (check && fields) {
        check.addEventListener('change', function() {
            fields.style.display = this.checked ? 'block' : 'none';
        });
    }

    // Toggle custom position
    const editPosSelect = document.getElementById('editPositionSelect');
    const editCustomPosWrap = document.getElementById('editCustomPositionWrap');
    if (editPosSelect && editCustomPosWrap) {
        editPosSelect.addEventListener('change', function() {
            editCustomPosWrap.style.display = this.value === 'Other' ? 'block' : 'none';
        });
    }

    // Toggle custom department
    const editDeptSelect = document.getElementById('editDepartmentSelect');
    const editCustomDeptWrap = document.getElementById('editCustomDepartmentWrap');
    if (editDeptSelect && editCustomDeptWrap) {
        editDeptSelect.addEventListener('change', function() {
            editCustomDeptWrap.style.display = this.value === 'Other' ? 'block' : 'none';
        });
    }

    // Password visibility toggle
    const toggleResetPw = document.getElementById('toggleResetPassword');
    const resetPwInput = document.getElementById('resetPasswordInput');
    const eyeOpen = document.getElementById('eyeOpenIcon');
    const eyeClosed = document.getElementById('eyeClosedIcon');

    if (toggleResetPw && resetPwInput) {
        toggleResetPw.addEventListener('click', function(e) {
            e.preventDefault();
            const isPassword = resetPwInput.type === 'password';
            resetPwInput.type = isPassword ? 'text' : 'password';
            if (eyeOpen && eyeClosed) {
                eyeOpen.style.display = isPassword ? 'none' : 'block';
                eyeClosed.style.display = isPassword ? 'block' : 'none';
            }
            toggleResetPw.setAttribute('aria-label', isPassword ? 'Hide password' : 'Show password');
            toggleResetPw.setAttribute('title', isPassword ? 'Hide password' : 'Show password');
        });

        toggleResetPw.addEventListener('mouseenter', function() {
            this.style.color = '#334155';
            this.style.background = '#e2e8f0';
        });
        toggleResetPw.addEventListener('mouseleave', function() {
            this.style.color = '#94a3b8';
            this.style.background = 'none';
        });
    }
</script>
@endpush
