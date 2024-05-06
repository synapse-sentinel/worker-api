<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::updateOrCreate([
            'name' => 'John Partridge',
            'email' => 'jordan@partridge.rocks',
            'password' => bcrypt('password'),
        ]);
    }
}
