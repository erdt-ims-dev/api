<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\User;

class UserFactory extends Factory
{
    protected $model = User::class;

    public function definition()
    {
        return [
            'uuid' => Str::uuid(),
            'email' => $this->faker->unique()->safeEmail,
            'email_verified_at' => now(),
            'password' => bcrypt('password'), // or you can use Hash::make('password')
            'session_token' => null, // or add logic if session tokens are needed
            'status' => 'active',
            'account_type' => 'new',
            'remember_token' => Str::random(10),
        ];
    }
}
