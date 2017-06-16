<?php

use Carbon\Carbon;
use Illuminate\Database\Seeder;

class FileTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('file')->insert([
            'name' => 'mig.jpg',
            'mime' => 'image/jpeg',
            'original_name' => 'mig.jpg',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        DB::table('file')->insert([
            'name' => 'man.jpg',
            'mime' => 'image/jpeg',
            'original_name' => 'man.jpg',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        DB::table('file')->insert([
            'name' => 'au.jpg',
            'mime' => 'image/jpeg',
            'original_name' => 'au.jpg',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);
    }
}
