<?php

namespace Database\Seeders;

use App\User;
use App\Models\Customers;
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

        // Roles and customer types (also present in sample-data.sql)
        \Illuminate\Support\Facades\DB::table('bp_usertype')->truncate();
        \Illuminate\Support\Facades\DB::table('bp_usertype')->insert([
            ['id' => 1, 'role' => 'user'],
            ['id' => 2, 'role' => 'staff'],
            ['id' => 3, 'role' => 'admin'],
            ['id' => 4, 'role' => 'superadmin'],
        ]);
        \Illuminate\Support\Facades\DB::table('customer_types')->truncate();
        \Illuminate\Support\Facades\DB::table('customer_types')->insert([
            ['id' => 1, 'name' => 'Basic',          'discount_amount' => null, 'total_spend_amount' => null,  'status' => 'active'],
            ['id' => 2, 'name' => 'Gold Member',    'discount_amount' => '3',  'total_spend_amount' => '4500', 'status' => 'active'],
            ['id' => 3, 'name' => 'Diamond Member', 'discount_amount' => '5',  'total_spend_amount' => '10000','status' => 'active'],
        ]);

        // Demo customer (front-end login) — matches database/sample-data.sql
        Customers::truncate();
        Customers::insert([
            'customer_types_id'   => 1,
            'first_name'          => 'Demo',
            'last_name'           => 'Customer',
            'email'               => 'customer@example.com',
            'phone'               => '09000000000',
            'password'            => Hash::make('password'),
            'status'              => 1,
            'is_verified'         => 1,
            'total_reward_points' => 150,
            'created_at'          => now()->subDays(2),
            'updated_at'          => now()->subDays(2),
        ]);
    }
}
