<?php

namespace Database\Seeders;

use App\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::truncate();

        // Demo administrator — matches database/sample-data.sql
        User::create([
            'name'      => 'Admin',
            'email'     => 'admin@example.com',
            'password'  => Hash::make('password'),
            'role'      => 4,
            'api_token' => 'demo-token',
            'avatar'    => '',
            'status'    => 1,
            'verified'  => 1,
        ]);

        // A few sample staff accounts
        $faker = \Faker\Factory::create();
        for ($i = 0; $i < 3; $i++) {
            User::create([
                'name'      => $faker->name(),
                'email'     => $faker->unique()->safeEmail(),
                'password'  => Hash::make('password'),
                'role'      => 2,
                'api_token' => Str::random(60),
                'avatar'    => '',
                'status'    => 1,
                'verified'  => 1,
            ]);
        }
    }
}
