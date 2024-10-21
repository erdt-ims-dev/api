<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        // Call the UserSeeder
        $this->call(UserSeeder::class);
        // If needed, you can call the AccountDetailsSeeder separately
        // $this->call(AccountDetailsSeeder::class);
    }
}
