<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicPagesTest extends TestCase
{
    use RefreshDatabase;

    protected bool $seed = true;

    public function test_the_homepage_loads(): void
    {
        $this->get('/')->assertOk()->assertSee('Ivora');
    }

    public function test_the_treatment_list_loads(): void
    {
        $this->get('/treatments')->assertOk();
    }

    public function test_the_booking_form_loads(): void
    {
        $this->get('/book')->assertOk();
    }

    public function test_the_console_is_closed_to_guests(): void
    {
        $this->get('/console')->assertRedirect('/login');
    }
}
