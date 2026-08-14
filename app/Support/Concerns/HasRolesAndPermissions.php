<?php

namespace App\Support\Concerns;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\Cache;

/**
 * Ivora's own authorisation layer.
 *
 * A user holds exactly one role (the job title) and the role carries a matrix of
 * permissions. On top of that, the clinic owner can grant or revoke single
 * permissions per user -- that override always wins over the role matrix, which
 * is how "Sana can edit the blog but nothing else in marketing" is expressed.
 */
trait HasRolesAndPermissions
{
    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(Permission::class)->withPivot('granted');
    }

    public function permissionMap(): array
    {
        return Cache::remember("user.{$this->id}.permissions", now()->addMinutes(30), function () {
            $fromRole = $this->role
                ? $this->role->permissions()->pluck('slug')->flip()->map(fn () => true)->toArray()
                : [];

            foreach ($this->permissions as $permission) {
                $fromRole[$permission->slug] = (bool) $permission->pivot->granted;
            }

            return $fromRole;
        });
    }

    public function forgetPermissionCache(): void
    {
        Cache::forget("user.{$this->id}.permissions");
    }

    public function isOwner(): bool
    {
        return $this->role?->slug === Role::OWNER;
    }

    public function hasRole(string|array $slugs): bool
    {
        return in_array($this->role?->slug, (array) $slugs, true);
    }

    /** The clinic owner bypasses the matrix entirely. */
    public function hasPermission(string $slug): bool
    {
        if ($this->isOwner()) {
            return true;
        }

        return (bool) ($this->permissionMap()[$slug] ?? false);
    }

    public function hasAnyPermission(array $slugs): bool
    {
        foreach ($slugs as $slug) {
            if ($this->hasPermission($slug)) {
                return true;
            }
        }

        return false;
    }

    /** Can this account open the staff console at all? */
    public function canAccessConsole(): bool
    {
        return $this->role && $this->role->level >= Role::LEVEL_STAFF;
    }
}
