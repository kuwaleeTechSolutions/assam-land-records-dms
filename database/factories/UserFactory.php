<?php

namespace Database\Factories;

use App\Domain\Identity\Enums\UserStatus;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    protected static ?string $password;

    public function definition(): array
    {
        return [
            'ulid' => (string) Str::ulid(),
            'username' => fake()->unique()->userName(),
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'mobile' => null,

            'password' => static::$password ??= Hash::make('password'),
            'status' => UserStatus::Active,
            'mfa_required' => false,
            'failed_login_count' => 0,

            'locked_until' => null,
            'last_login_at' => null,
            'last_login_ip' => null,
            'password_changed_at' => now(),

            'created_by' => null,
            'updated_by' => null,

            'remember_token' => Str::random(10),
        ];
    }

    public function suspended(): static
    {
        return $this->state(fn (): array => [
            'status' => UserStatus::Suspended,
        ]);
    }

    public function locked(): static
    {
        return $this->state(fn (): array => [
            'status' => UserStatus::Locked,
            'locked_until' => now()->addMinutes(30),
        ]);
    }

    public function disabled(): static
    {
        return $this->state(fn (): array => [
            'status' => UserStatus::Disabled,
        ]);
    }
}
