<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Employee extends Model
{
    protected $fillable = [
        'employee_id',
        'name',
        'position',
        'email',
        'phone',
        'department',
        'employment_status',
        'profile_photo',
        'user_id',
        'face_data',
        'status',
    ];

    protected $casts = [
        'status' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class);
    }

    public function absences(): HasMany
    {
        return $this->hasMany(Absence::class);
    }

    public function faceRegistration(): HasOne
    {
        return $this->hasOne(FaceRegistration::class);
    }

    public function faceRegistrations(): HasMany
    {
        return $this->hasMany(FaceRegistration::class);
    }

    public function medicalCertificates(): HasMany
    {
        return $this->hasMany(MedicalCertificate::class);
    }

    public function getHasFaceRegisteredAttribute(): bool
    {
        return $this->faceRegistration()->exists();
    }

    public function getProfilePhotoUrlAttribute(): string
    {
        if (!$this->profile_photo) {
            return 'https://ui-avatars.com/api/?name=' . urlencode($this->name ?? 'Employee') . '&background=0ea5e9&color=fff';
        }

        return str_starts_with($this->profile_photo, 'http')
            ? $this->profile_photo
            : asset('storage/' . $this->profile_photo);
    }
}