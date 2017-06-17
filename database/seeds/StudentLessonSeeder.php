<?php

use Illuminate\Database\Seeder;
use Carbon\Carbon;

class StudentLessonSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('student_lesson')->insert([
            'student_id' => 1,
            'lesson_id' => 1,
            'progress' => 28,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
        ]);

        DB::table('student_lesson')->insert([
            'student_id' => 1,
            'lesson_id' => 2,
            'progress' => 99,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
        ]);

        DB::table('student_lesson')->insert([
            'student_id' => 1,
            'lesson_id' => 3,
            'progress' => 22,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
        ]);

        DB::table('student_lesson')->insert([
            'student_id' => 2,
            'lesson_id' => 1,
            'progress' => 55,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
        ]);

        DB::table('student_lesson')->insert([
            'student_id' => 3,
            'lesson_id' => 10,
            'progress' => 45,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
        ]);

        DB::table('student_lesson')->insert([
            'student_id' => 3,
            'lesson_id' => 12,
            'progress' => 90,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
        ]);
    }
}
