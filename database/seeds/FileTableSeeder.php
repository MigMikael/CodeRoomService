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
        // 1
        DB::table('file')->insert([
            'name' => 'mig.jpg',
            'mime' => 'image/jpeg',
            'original_name' => 'mig.jpg',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        // 2
        DB::table('file')->insert([
            'name' => 'man.jpg',
            'mime' => 'image/jpeg',
            'original_name' => 'man.jpg',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        // 3
        DB::table('file')->insert([
            'name' => 'au.jpg',
            'mime' => 'image/jpeg',
            'original_name' => 'au.jpg',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        // 4
        DB::table('file')->insert([
            'name' => 'c.png',
            'mime' => 'image/png',
            'original_name' => 'c.png',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        // 5
        DB::table('file')->insert([
            'name' => 'java.png',
            'mime' => 'image/png',
            'original_name' => 'java.png',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        // 6
        DB::table('file')->insert([
            'name' => 'datastruct.png',
            'mime' => 'image/png',
            'original_name' => 'datastruct.png',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        // 7
        DB::table('file')->insert([
            'name' => 'oosd.png',
            'mime' => 'image/png',
            'original_name' => 'oosd.png',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        // 8
        DB::table('file')->insert([
            'name' => 'rm.png',
            'mime' => 'image/png',
            'original_name' => 'rm.png',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        // 9
        DB::table('file')->insert([
            'name' => 'pinyo.jpg',
            'mime' => 'image/jpeg',
            'original_name' => 'pinyo.jpg',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        // 10
        DB::table('file')->insert([
            'name' => 'sirak.jpg',
            'mime' => 'image/jpeg',
            'original_name' => 'sirak.jpg',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        // 11
        DB::table('file')->insert([
            'name' => 'ratchadaporn.jpg',
            'mime' => 'image/jpeg',
            'original_name' => 'ratchadaporn.jpg',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        // 12
        DB::table('file')->insert([
            'name' => 'sethalat.jpg',
            'mime' => 'image/jpeg',
            'original_name' => 'sethalat.jpg',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        // 13
        DB::table('file')->insert([
            'name' => 'orawan.jpg',
            'mime' => 'image/jpeg',
            'original_name' => 'orawan.jpg',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        // 14
        DB::table('file')->insert([
            'name' => 'tasanawan.jpg',
            'mime' => 'image/jpeg',
            'original_name' => 'tasanawan.jpg',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        // 15
        DB::table('file')->insert([
            'name' => 'thearchangel.jpg',
            'mime' => 'image/jpeg',
            'original_name' => 'thearchangel.jpg',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        // 16
        DB::table('file')->insert([
            'name' => 'codeone.jpg',
            'mime' => 'image/jpeg',
            'original_name' => 'codeone.jpg',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);
    }
}
