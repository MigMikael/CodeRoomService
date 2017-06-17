<?php

use Carbon\Carbon;
use Illuminate\Database\Seeder;

class CourseTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $generator = new \App\Helper\TokenGenerate();
        DB::table('course')->insert([
            'name' => 'Computer Programming I',
            'image' => 4,
            'token' => $generator->generate(6),
            'color' => '244:67:54',
            'status' => 'enable',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
        ]);

        DB::table('course')->insert([
            'name' => 'Computer Programming II',
            'image' => 5,
            'token' => $generator->generate(6),
            'color' => '239:108:0',
            'status' => 'enable',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
        ]);

        DB::table('course')->insert([
            'name' => 'Data Structures',
            'image' => 6,
            'token' => $generator->generate(6),
            'color' => '0:131:143',
            'status' => 'enable',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
        ]);

        DB::table('course')->insert([
            'name' => 'Object Oriented Software Development',
            'image' => 7,
            'token' => $generator->generate(6),
            'color' => '63:81:181',
            'status' => 'enable',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
        ]);

        DB::table('course')->insert([
            'name' => 'Research Method',
            'image' => 8,
            'token' => $generator->generate(6),
            'color' => '0:105:92',
            'status' => 'disable',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
        ]);
    }
}
