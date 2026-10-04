@extends('layouts.app')
@section('title', 'Edit Employee — TT Dental Lab')
@section('header_title', 'Edit Employee')

@section('content')
<div style="max-width: 760px; margin: 0 auto;">
    <div class="card">
        <div class="card-header">
            <div>
                <div class="card-title">Edit Employee: {{ $employee->name }}</div>
                <div class="card-subtitle">Update profile details, employment status, and linked login credentials.</div>
            </div>
            <a href="{{ route('employees.index') }}" class="btn btn-outline btn-sm">← Back to List</a>
        </div>

        <form action="{{ route('employees.update', $employee) }}" method="POST" enctype="multipart/form-data">
            @csrf @method('PUT')

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 18px;">
                <div class="form-group">
                    <label class="form-label">Employee ID <span class="req">*</span></label>
                    <input type="text" name="employee_id" class="form-input" value="{{ old('employee_id', $employee->employee_id) }}" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Full Name <span class="req">*</span></label>
                    <input type="text" name="name" class="form-input" value="{{ old('name', $employee->name) }}" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Position / Role</label>
                    <input type="text" name="position" class="form-input" value="{{ old('position', $employee->position) }}">
                </div>
                <div class="form-group">
                    <label class="form-label">Department</label>
                    <input type="text" name="department" class="form-input" value="{{ old('department', $employee->department) }}">
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
                    <label class="form-label">Employment Status</label>
                    <select name="employment_status" class="form-select">
                        @foreach(['Full-Time','Part-Time','Contract','Intern'] as $opt)
                            <option value="{{ $opt }}" {{ old('employment_status', $employee->employment_status) === $opt ? 'selected' : '' }}>{{ $opt }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Active Status</label>
                    <label style="display: flex; align-items: center; gap: 8px; margin-top: 10px; cursor: pointer;">
                        <input type="checkbox" name="status" value="1" {{ old('status', $employee->status) ? 'checked' : '' }} style="width: 16px; height: 16px; accent-color: var(--primary);">
                        <span style="font-size: 13.5px; font-weight: 600; color: var(--text-body);">Active in System</span>
                    </label>
                </div>
            </div>

            {{-- Profile Photo --}}
            <div class="form-group">
                <label class="form-label">Profile Photo</label>
                <div style="display: flex; align-items: center; gap: 14px;">
                    <img src="{{ $employee->profile_photo_url }}" alt="Profile Preview"
                         style="width: 46px; height: 46px; border-radius: 10px; object-fit: cover; border: 2px solid var(--card-border);">
                    <input type="file" name="profile_photo" accept="image/*" class="form-input" style="flex:1; padding:8px 12px; cursor:pointer;">
                </div>
            </div>

            {{-- Staff Login Section --}}
            <div style="background: #f8fafc; border: 1.5px solid #e2e8f0; border-radius: 12px; padding: 18px 20px; margin-bottom: 22px;">
                @if($employee->user)
                    <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 12px;">
                        <span class="badge badge-green">✓ Staff Account Linked</span>
                        <span style="font-size: 13px; color: var(--text-muted);">{{ $employee->user->email }}</span>
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="resetPasswordInput">Reset Password (leave blank to keep current)</label>
                        <div style="position: relative; max-width: 380px;">
                            <input type="password" id="resetPasswordInput" name="password" class="form-input" placeholder="Enter new password (optional)" style="width: 100%; padding-right: 42px;" autocomplete="new-password">
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
                    Update Employee
                </button>
                <a href="{{ route('employees.index') }}" class="btn btn-outline">Cancel</a>
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
