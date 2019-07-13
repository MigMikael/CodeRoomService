<?php

use Carbon\Carbon;
use Illuminate\Database\Seeder;

class StudentTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('student')->insert([
            'student_id' => '07560550',
            'name' => 'ชนะไชย พุทธรักษา',
            'email' => 'chanachai@example.com',
            'image' => 1,
            'username' => 'MigMikael',
            'password' => password_hash('mig39525G', PASSWORD_DEFAULT),
            'token' => '2t2bTG6KsgNuvTIY8oSvYWtRLrXC4P6R',
            'ip' => '',
            'status' => 'enable',
            'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
            'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
        ]);

        DB::table('student')->insert([
            'student_id' => '07560445',
            'name' => 'นันทิพัฒน์ พลบดี',
            'email' => 'nanthiphat@example.com',
            'image' => 2,
            'username' => 'Manny',
            'password' => password_hash('Manny', PASSWORD_DEFAULT),
            'token' => 'k1bNN5piKWmzVAWBwXFP8Hs2Qc0JTtb6',
            'ip' => '',
            'status' => 'enable',
            'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
            'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
        ]);

        DB::table('student')->insert([
            'student_id' => '07570497',
            'name' => 'ธนเดช พัดทอง',
            'email' => 'thanadej@example.com',
            'image' => 3,
            'username' => 'Au',
            'password' => password_hash('Au', PASSWORD_DEFAULT),
            'token' => 'eDAs36X1d3TDH8tZVdchphucYusqZq9S',
            'ip' => '',
            'status' => 'enable',
            'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
            'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
        ]);
    }
}
