<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CountrySeeder extends Seeder
{
    public function run(): void
    {
        DB::table('countries')->insert([
            ['name' => 'Vietnam'],
            ['name' => 'United States'],
            ['name' => 'United Kingdom'],
            ['name' => 'Japan'],
            ['name' => 'Korea'],
            ['name' => 'China'],
            ['name' => 'Thailand'],
            ['name' => 'Singapore'],
        ]);
    }
}
