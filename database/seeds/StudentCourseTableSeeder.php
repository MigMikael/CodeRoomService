<?php
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class StudentCourseTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('student_course')->insert([
            'student_id' => 1, //'07560550'
            'course_id' => 1,
            'progress' => 69,
            'status' => 'enable',
            'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
            'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
        ]);

        DB::table('student_course')->insert([
            'student_id' => 2, //'07560445'
            'course_id' => 1,
            'progress' => 71,
            'status' => 'enable',
            'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
            'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
        ]);

        DB::table('student_course')->insert([
            'student_id' => 3, //'07570497'
            'course_id' => 1,
            'progress' => 42,
            'status' => 'disable',
            'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
            'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
        ]);

        DB::table('student_course')->insert([
            'student_id' => 3, //'07570497'
            'course_id' => 4,
            'progress' => 56,
            'status' => 'enable',
            'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
            'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
        ]);

        DB::table('student_course')->insert([
            'student_id' => 3, //'07570497'
            'course_id' => 2,
            'progress' => 88,
            'status' => 'enable',
            'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
            'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
        ]);

        DB::table('student_course')->insert([
            'student_id' => 1,//'07560550'
            'course_id' => 3,
            'progress' => 67,
            'status' => 'enable',
            'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
            'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
        ]);
    }
}
