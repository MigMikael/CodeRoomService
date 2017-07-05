<?php

use Illuminate\Database\Seeder;
use Carbon\Carbon;

class LessonTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //Computer Programming I
        DB::table('lesson')->insert([
            'name' => 'บทนำเกี่ยวกับคอมพิวเตอร์และการโปรแกรม',
            'course_id' => 1,
            'status' => 'normal',
            'order' => 1,
            'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
            'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
        ]);

        DB::table('lesson')->insert([
            'name' => 'Quiz บทนำเกี่ยวกับคอมพิวเตอร์และการโปรแกรม',
            'course_id' => 1,
            'status' => 'test',
            'order' => 2,
            'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
            'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
        ]);

        DB::table('lesson')->insert([
            'name' => 'การวิเคราะห์และแก้ปัญหา',
            'course_id' => 1,
            'status' => 'normal',
            'order' => 3,
            'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
            'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
        ]);

        DB::table('lesson')->insert([
            'name' => 'โครงสร้างภาษาซี ตัวแปร และ การแสดงผลอย่างง่าย',
            'course_id' => 1,
            'status' => 'normal',
            'order' => 4,
            'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
            'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
        ]);

        DB::table('lesson')->insert([
            'name' => 'Quiz โครงสร้างภาษาซี ตัวแปร และ การแสดงผลอย่างง่าย',
            'course_id' => 1,
            'status' => 'false',
            'order' => 5,
            'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
            'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
        ]);

        DB::table('lesson')->insert([
            'name' => 'ตัวดำเนินการ (operators), การรับข้อมูลเข้า และ การแสดงผลลัพธ์',
            'course_id' => 1,
            'status' => 'normal',
            'order' => 6,
            'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
            'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
        ]);

        //Data Structures
        DB::table('lesson')->insert([
            'name' => 'Array',
            'course_id' => 3,
            'status' => 'normal',
            'order' => 1,
            'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
            'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
        ]);

        DB::table('lesson')->insert([
            'name' => 'Quiz Array',
            'course_id' => 3,
            'status' => 'test',
            'order' => 2,
            'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
            'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
        ]);

        DB::table('lesson')->insert([
            'name' => 'Link List',
            'course_id' => 3,
            'status' => 'normal',
            'order' => 3,
            'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
            'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
        ]);

        DB::table('lesson')->insert([
            'name' => 'Tree',
            'course_id' => 3,
            'status' => 'normal',
            'order' => 4,
            'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
            'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
        ]);

        DB::table('lesson')->insert([
            'name' => 'Graph',
            'course_id' => 3,
            'status' => 'normal',
            'order' => 5,
            'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
            'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
        ]);

        //Computer Programming II
        DB::table('lesson')->insert([
            'name' => 'แนะนํารายวิชาและรู้จัก Java',
            'course_id' => 2,
            'status' => 'normal',
            'order' => 1,
            'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
            'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
        ]);

        DB::table('lesson')->insert([
            'name' => 'ความรู้พื้นฐาน ตัวแปร ชนิดข้อมูล',
            'course_id' => 2,
            'status' => 'normal',
            'order' => 2,
            'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
            'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
        ]);

        DB::table('lesson')->insert([
            'name' => 'ตัวแปร ชนิดข้อมูล และการดําเนินการ',
            'course_id' => 2,
            'status' => 'normal',
            'order' => 3,
            'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
            'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
        ]);

        DB::table('lesson')->insert([
            'name' => 'คําสั่งเดี่ยว คําสั่งเงื่อนไข และชุดคําสั่ง',
            'course_id' => 2,
            'status' => 'normal',
            'order' => 4,
            'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
            'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
        ]);

        //OOSD
        DB::table('lesson')->insert([
            'name' => 'Quiz Lab 1',
            'course_id' => 4,
            'status' => 'test',
            'order' => 1,
            'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
            'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
        ]);

        DB::table('lesson')->insert([
            'name' => 'Quiz Lab 2',
            'course_id' => 4,
            'status' => 'test',
            'order' => 2,
            'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
            'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
        ]);

        DB::table('lesson')->insert([
            'name' => 'Quiz Lab 3',
            'course_id' => 4,
            'status' => 'test',
            'order' => 3,
            'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
            'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
        ]);
    }
}
