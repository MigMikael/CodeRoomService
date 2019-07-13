<?php

use Illuminate\Database\Seeder;
use Carbon\Carbon;

class TeacherCourseTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('teacher_course')->insert([
            'teacher_id' => 1,
            'course_id' => 1,
            'status' => 'enable',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
        ]);

        DB::table('teacher_course')->insert([
            'teacher_id' => 1,
            'course_id' => 2,
            'status' => 'enable',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
        ]);

        DB::table('teacher_course')->insert([
            'teacher_id' => 2,
            'course_id' => 1,
            'status' => 'enable',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
        ]);

        DB::table('teacher_course')->insert([
            'teacher_id' => 3,
            'course_id' => 2,
            'status' => 'enable',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
        ]);

        DB::table('teacher_course')->insert([
            'teacher_id' => 4,
            'course_id' => 3,
            'status' => 'enable',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
        ]);

        DB::table('teacher_course')->insert([
            'teacher_id' => 5,
            'course_id' => 4,
            'status' => 'enable',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
        ]);
    }
}
