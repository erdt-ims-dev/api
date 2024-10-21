<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\AccountDetails;

class AccountDetailsFactory extends Factory
{
    protected $model = AccountDetails::class;

    public function definition()
    {
        return [
            'user_id' => \App\Models\User::factory(), // This will create a user and associate it with account details
            'first_name' => $this->faker->firstName,
            'middle_name' => $this->faker->optional()->firstName,
            'last_name' => $this->faker->lastName,
            'profile_picture' => $this->faker->imageUrl(640, 480, 'people', true), // Generates random image URL
            'program' => $this->faker->randomElement(['Program A', 'Program B', 'Program C']),
            'birth_certificate' => $this->faker->word . '.pdf',
            'tor' => $this->faker->word . '.pdf',
            'narrative_essay' => $this->faker->word . '.pdf',
            'recommendation_letter' => $this->faker->word . '.pdf',
            'medical_certificate' => $this->faker->word . '.pdf',
            'nbi_clearance' => $this->faker->word . '.pdf',
            'admission_notice' => $this->faker->word . '.pdf',
        ];
    }
}
