<?php

namespace Database\Seeders;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
    	$this->call(UserTableSeeder::class);
        $this->call(TaxTableSeeder::class);
        $this->call(PostTableSeeder::class);
        $this->call(MenuTableSeeder::class);
        $this->call(SliderTableSeeder::class);
        $this->call(OptionsTableSeeder::class);
        $this->call(ModuleTableSeeder::class);
        $this->call(LanguageTableSeeder::class);
        $this->call(CustomTableSeeder::class);
        $this->call(AccessTableSeeder::class);

    }
}
