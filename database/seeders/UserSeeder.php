<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\AccountDetails;

class UserSeeder extends Seeder
{
    public function run()
    {
        // Generate 10 users with related Account Details
        User::factory()
        ->count(10)
        ->has(AccountDetails::factory()) // Correct method for one-to-one relationships
        ->create();

    }
}
