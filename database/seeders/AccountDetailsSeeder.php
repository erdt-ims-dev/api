<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\AccountDetails;

class AccountDetailsSeeder extends Seeder
{
    public function run()
    {
        // Generate 10 account details
        AccountDetails::factory()->count(10)->create();
    }
}
