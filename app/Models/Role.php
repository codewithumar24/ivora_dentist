<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Role extends Model
{
    use HasFactory;

    public const OWNER = 'owner';               // the principal dentist, owns the clinic
    public const DENTIST = 'dentist';
    public const RECEPTIONIST = 'receptionist';
    public const ASSISTANT = 'assistant';       // dental assistant / hygienist
    public const ACCOUNTANT = 'accountant';
    public const EDITOR = 'editor';             // marketing / content
    public const PATIENT = 'patient';

    public const LEVEL_PATIENT = 10;
    public const LEVEL_STAFF = 20;
    public const LEVEL_CLINICAL = 40;
    public const LEVEL_OWNER = 90;

    protected $fillable = ['name', 'slug', 'description', 'is_locked', 'level'];

    protected $casts = ['is_locked' => 'boolean'];

    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(Permission::class);
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function isDeletable(): bool
    {
        return ! $this->is_locked && $this->users()->count() === 0;
    }
}
