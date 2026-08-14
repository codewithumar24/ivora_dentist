<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    protected bool $seed = true;

    public function test_a_patient_can_sign_in_and_reaches_the_portal(): void
    {
        $response = $this->post('/login', [
            'email' => 'patient@example.com',
            'password' => 'password',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('patient.dashboard'));
    }

    public function test_the_owner_lands_in_the_staff_console(): void
    {
        $response = $this->post('/login', [
            'email' => 'owner@ivoradental.com',
            'password' => 'password',
        ]);

        $response->assertRedirect(route('admin.dashboard'));
    }

    public function test_a_patient_cannot_open_the_console(): void
    {
        $patient = User::where('email', 'patient@example.com')->first();

        $this->actingAs($patient)->get('/console')->assertForbidden();
    }

    public function test_bad_credentials_are_rejected(): void
    {
        $this->post('/login', ['email' => 'patient@example.com', 'password' => 'wrong'])
            ->assertSessionHasErrors('email');

        $this->assertGuest();
    }
}
