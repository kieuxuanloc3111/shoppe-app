<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BlogSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('blogs')->insert([
            [
                'title' => 'First ',
                'image' => null,
                'description' => '1111111',
            ],
            [
                'title' => 'Second ',
                'image' => null,
                'description' => '222222',
            ],
            [
                'title' => 'Third ',
                'image' => null,
                'description' => '3333333',
            ],
        ]);
    }
}
