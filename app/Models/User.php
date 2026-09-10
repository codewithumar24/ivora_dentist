<?php

namespace App\Models;

use App\Support\Concerns\HasRolesAndPermissions;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class User extends Authenticatable
{
    use HasFactory, HasRolesAndPermissions, Notifiable, SoftDeletes;

    protected $fillable = [
        'role_id', 'name', 'email', 'phone', 'password', 'avatar', 'gender',
        'date_of_birth', 'city', 'address', 'status', 'email_verified_at',
        'phone_verified_at', 'last_login_at', 'last_login_ip',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'phone_verified_at' => 'datetime',
            'last_login_at' => 'datetime',
            'date_of_birth' => 'date',
            'password' => 'hashed',
        ];
    }

    public function doctor(): HasOne
    {
        return $this->hasOne(Doctor::class);
    }

    public function patientProfile(): HasOne
    {
        return $this->hasOne(PatientProfile::class);
    }

    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class, 'patient_id');
    }

    public function medicalRecords(): HasMany
    {
        return $this->hasMany(MedicalRecord::class, 'patient_id');
    }

    public function prescriptions(): HasMany
    {
        return $this->hasMany(Prescription::class, 'patient_id');
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class, 'patient_id');
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class, 'patient_id');
    }

    public function scopePatients(Builder $query): Builder
    {
        return $query->whereRelation('role', 'slug', Role::PATIENT);
    }

    public function scopeStaff(Builder $query): Builder
    {
        return $query->whereRelation('role', 'level', '>=', Role::LEVEL_STAFF);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', 'active');
    }

    public function scopeSearch(Builder $query, ?string $term): Builder
    {
        return $query->when($term, fn ($q) => $q->where(function ($q) use ($term) {
            $q->where('name', 'like', "%{$term}%")
                ->orWhere('email', 'like', "%{$term}%")
                ->orWhere('phone', 'like', "%{$term}%");
        }));
    }

    public function getInitialsAttribute(): string
    {
        return Str::of($this->name)->explode(' ')->take(2)
            ->map(fn ($part) => Str::upper(Str::substr($part, 0, 1)))->implode('');
    }

    public function getAvatarUrlAttribute(): string
    {
        if ($this->avatar && Str::startsWith($this->avatar, ['http://', 'https://'])) {
            return $this->avatar;
        }

        // An uploaded path is only usable once `php artisan storage:link` has
        // been run; without the symlink it 404s and the card renders as a blank
        // box. Check the file exists rather than trusting the column.
        if ($this->avatar && Storage::disk('public')->exists($this->avatar)) {
            return asset('storage/'.$this->avatar);
        }

        return $this->initialsAvatar();
    }

    /**
     * Initials on the clinic's gradient, as an inline SVG data URI.
     *
     * Deliberately not a call to ui-avatars.com or any other service: a missing
     * photograph should not depend on a third party being reachable, and on a
     * clinic network it often is not.
     */
    public function initialsAvatar(): string
    {
        $initials = Str::of($this->name)
            ->replaceMatches('/[^\p{L}\s]/u', '')
            ->squish()
            ->explode(' ')
            ->take(2)
            ->map(fn ($part) => Str::upper(Str::substr($part, 0, 1)))
            ->implode('');

        $svg = <<<SVG
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 200 200">
          <defs><linearGradient id="g" x1="0" y1="0" x2="1" y2="1">
            <stop offset="0%" stop-color="#10403B"/><stop offset="100%" stop-color="#1C7A6E"/>
          </linearGradient></defs>
          <rect width="200" height="200" fill="url(#g)"/>
          <text x="100" y="100" fill="#A8E6D8" font-family="Inter Tight, system-ui, sans-serif"
                font-size="78" font-weight="600" text-anchor="middle" dominant-baseline="central">{$initials}</text>
        </svg>
        SVG;

        return 'data:image/svg+xml;base64,'.base64_encode($svg);
    }

    public function getAgeAttribute(): ?int
    {
        return $this->date_of_birth?->age;
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function hasVerifiedEmail(): bool
    {
        return ! is_null($this->email_verified_at);
    }
}
