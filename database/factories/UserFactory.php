<?php

namespace Database\Factories;

use App\Models\Role;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    protected static ?string $password = null;

    public function definition(): array
    {
        return [
            'role_id' => Role::where('slug', Role::PATIENT)->value('id'),
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'phone' => '+9233'.fake()->unique()->numerify('########'),
            'email_verified_at' => now(),
            'password' => static::$password ??= bcrypt('password'),
            'gender' => fake()->randomElement(['male', 'female']),
            'date_of_birth' => fake()->dateTimeBetween('-70 years', '-8 years'),
            'city' => 'Karachi',
            'status' => 'active',
            'remember_token' => Str::random(10),
        ];
    }

    public function unverified(): static
    {
        return $this->state(fn () => ['email_verified_at' => null]);
    }

    public function staff(string $role = Role::RECEPTIONIST): static
    {
        return $this->state(fn () => ['role_id' => Role::where('slug', $role)->value('id')]);
    }
}
