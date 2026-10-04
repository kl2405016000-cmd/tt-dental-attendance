@extends('layouts.app')
@section('title', 'Add Employee — TT Dental Lab')
@section('header_title', 'Add Employee')

@section('content')
<div style="max-width: 760px; margin: 0 auto;">
    <div class="card">
        <div class="card-header">
            <div>
                <div class="card-title">Register New Employee</div>
                <div class="card-subtitle">Fill in the employee profile and optionally create a Staff Portal login.</div>
            </div>
            <a href="{{ route('employees.index') }}" class="btn btn-outline btn-sm">← Back to List</a>
        </div>

        <form action="{{ route('employees.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 18px;">
                <div class="form-group">
                    <label class="form-label">Employee ID <span class="req">*</span></label>
                    <input type="text" name="employee_id" class="form-input" placeholder="e.g. EMP001" value="{{ old('employee_id') }}" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Full Name <span class="req">*</span></label>
                    <input type="text" name="name" class="form-input" placeholder="e.g. Ahmad bin Ismail" value="{{ old('name') }}" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Position / Role</label>
                    <input type="text" name="position" class="form-input" placeholder="e.g. Senior Dental Technician" value="{{ old('position') }}">
                </div>
                <div class="form-group">
                    <label class="form-label">Department</label>
                    <input type="text" name="department" class="form-input" placeholder="e.g. Crown & Bridge" value="{{ old('department') }}">
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
                    <label class="form-label">Employment Status</label>
                    <select name="employment_status" class="form-select">
                        <option value="Full-Time" {{ old('employment_status') === 'Full-Time' ? 'selected' : '' }}>Full-Time</option>
                        <option value="Part-Time" {{ old('employment_status') === 'Part-Time' ? 'selected' : '' }}>Part-Time</option>
                        <option value="Contract" {{ old('employment_status') === 'Contract' ? 'selected' : '' }}>Contract</option>
                        <option value="Intern" {{ old('employment_status') === 'Intern' ? 'selected' : '' }}>Intern</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Profile Photo</label>
                    <input type="file" name="profile_photo" accept="image/*" class="form-input" style="padding:8px 12px; cursor:pointer;">
                </div>
            </div>

            {{-- Staff Account Section --}}
            <div style="background: #f8fafc; border: 1.5px solid #e2e8f0; border-radius: 12px; padding: 18px 20px; margin-bottom: 22px;">
                <label style="display: flex; align-items: center; gap: 10px; cursor: pointer; margin-bottom: 10px;">
                    <input type="checkbox" name="create_account" id="createAccountCheck" value="1" {{ old('create_account') ? 'checked' : '' }} style="width: 16px; height: 16px; accent-color: var(--primary);">
                    <span style="font-weight: 800; font-size: 14px; color: var(--text-head);">Create Staff Portal Login Account</span>
                </label>
                <div style="font-size: 12.5px; color: var(--text-muted); margin-bottom: 12px; padding-left: 26px;">
                    Enables this employee to log in to the Staff Portal to clock in/out, view history, and submit Medical Certificates.
                </div>

                <div id="accountFields" style="display: {{ old('create_account') ? 'block' : 'none' }}; padding-left: 26px;">
                    <label class="form-label">Initial Password (min 6 characters)</label>
                    <input type="password" name="password" class="form-input" placeholder="Set login password" style="max-width: 380px;">
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
    const check = document.getElementById('createAccountCheck');
    const fields = document.getElementById('accountFields');
    if (check && fields) {
        check.addEventListener('change', function() {
            fields.style.display = this.checked ? 'block' : 'none';
        });
    }
</script>
@endpush
