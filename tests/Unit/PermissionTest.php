<?php

namespace Tests\Unit;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PermissionTest extends TestCase
{
    use RefreshDatabase;

    protected bool $seed = true;

    public function test_the_owner_bypasses_the_permission_matrix(): void
    {
        $owner = User::whereRelation('role', 'slug', Role::OWNER)->first();

        $this->assertTrue($owner->hasPermission('anything.at.all'));
    }

    public function test_a_receptionist_cannot_manage_roles(): void
    {
        $receptionist = User::whereRelation('role', 'slug', Role::RECEPTIONIST)->first();

        $this->assertTrue($receptionist->hasPermission('appointments.create'));
        $this->assertFalse($receptionist->hasPermission('roles.manage'));
    }

    public function test_a_per_user_grant_overrides_the_role(): void
    {
        $receptionist = User::whereRelation('role', 'slug', Role::RECEPTIONIST)->first();
        $permission = Permission::where('slug', 'billing.manage')->first();

        $this->assertFalse($receptionist->hasPermission('billing.manage'));

        $receptionist->permissions()->syncWithoutDetaching([$permission->id => ['granted' => true]]);
        $receptionist->forgetPermissionCache();

        $this->assertTrue($receptionist->fresh()->hasPermission('billing.manage'));
    }

    public function test_a_per_user_revoke_overrides_the_role(): void
    {
        $receptionist = User::whereRelation('role', 'slug', Role::RECEPTIONIST)->first();
        $permission = Permission::where('slug', 'appointments.create')->first();

        $receptionist->permissions()->syncWithoutDetaching([$permission->id => ['granted' => false]]);
        $receptionist->forgetPermissionCache();

        $this->assertFalse($receptionist->fresh()->hasPermission('appointments.create'));
    }
}
