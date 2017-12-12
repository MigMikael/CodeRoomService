<?php

use Illuminate\Database\Seeder;
use Carbon\Carbon;

class TeacherTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        DB::table('teacher')->insert([
            'name' => 'อ.ดร.ภิญโญ แท้ประสาทสิทธิ์',
            'email' => 'pinyo@example.com',
            'image' => 9,
            'username' => 'Pinyo',
            'password' => password_hash('Pinyo', PASSWORD_DEFAULT),
            'role' => 'admin',
            'token' => 'WfHp37ebFTHwP12esAPrvJVWkXWLpsDf',
            'status' => 'enable',
            'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
            'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
        ]);

        DB::table('student')->insert([
            'student_id' => 1,
            'name' => 'อ.ดร.ภิญโญ แท้ประสาทสิทธิ์',
            'email' => 'pinyo@example.com',
            'image' => 9,
            'token' => 'WfHp37ebFTHwP12esAPrvJVWkXWLpsDf',
            'ip' => '',
            'role' => 'hidden',
            'status' => 'enable',
            'username' => 'Pinyo',
            'password' => password_hash('Pinyo', PASSWORD_DEFAULT),
            'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
            'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
        ]);




        DB::table('teacher')->insert([
            'name' => 'อ.ดร.สิรักข์ แก้วจำนงค์',
            'email' => 'sirak@example.com',
            'image' => 10,
            'username' => 'Sirak',
            'password' => password_hash('Sirak', PASSWORD_DEFAULT),
            'role' => 'teacher',
            'status' => 'enable',
            'token' => 'uDhNjRjBOSvpdLsV9vzqfdOibUusKUVw',
            'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
            'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
        ]);

        DB::table('student')->insert([
            'student_id' => 2,
            'name' => 'อ.ดร.สิรักข์ แก้วจำนงค์',
            'email' => 'sirak@example.com',
            'image' => 10,
            'token' => 'uDhNjRjBOSvpdLsV9vzqfdOibUusKUVw',
            'ip' => '',
            'role' => 'hidden',
            'status' => 'enable',
            'username' => 'Sirak',
            'password' => password_hash('Sirak', PASSWORD_DEFAULT),
            'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
            'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
        ]);




        DB::table('teacher')->insert([
            'name' => 'อ.ดร.รัชดาพร คณาวงษ์',
            'email' => 'ratchadaporn@example.com',
            'image' => 11,
            'username' => 'Ratchadaporn',
            'password' => password_hash('Ratchadaporn', PASSWORD_DEFAULT),
            'role' => 'teacher',
            'status' => 'enable',
            'token' => 'GAT1gY1QUW9FKMAKQnD5sFMY8aE0VZfr',
            'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
            'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
        ]);

        DB::table('student')->insert([
            'student_id' => 3,
            'name' => 'อ.ดร.รัชดาพร คณาวงษ์',
            'email' => 'ratchadaporn@example.com',
            'image' => 11,
            'token' => 'GAT1gY1QUW9FKMAKQnD5sFMY8aE0VZfr',
            'ip' => '',
            'role' => 'hidden',
            'status' => 'enable',
            'username' => 'Ratchadaporn',
            'password' => password_hash('Ratchadaporn', PASSWORD_DEFAULT),
            'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
            'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
        ]);




        DB::table('teacher')->insert([
            'name' => 'อ.เสฐลัทธ์ รอดเหตุภัย',
            'email' => 'sethalat@example.com',
            'image' => 12,
            'username' => 'Sethalat',
            'password' => password_hash('Sethalat', PASSWORD_DEFAULT),
            'role' => 'teacher',
            'status' => 'enable',
            'token' => 'c8NDwllHQSBnkQKG5SF6aNmxeDYMg7PQ',
            'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
            'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
        ]);

        DB::table('student')->insert([
            'student_id' => 4,
            'name' => 'อ.เสฐลัทธ์ รอดเหตุภัย',
            'email' => 'sethalat@example.com',
            'image' => 12,
            'token' => 'c8NDwllHQSBnkQKG5SF6aNmxeDYMg7PQ',
            'ip' => '',
            'role' => 'hidden',
            'status' => 'enable',
            'username' => 'Sethalat',
            'password' => password_hash('Sethalat', PASSWORD_DEFAULT),
            'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
            'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
        ]);




        DB::table('teacher')->insert([
            'name' => 'อ.ดร.อรวรรณ เชาวลิต',
            'email' => 'orawan@example.com',
            'image' => 13,
            'username' => 'Orawan',
            'password' => password_hash('Orawan', PASSWORD_DEFAULT),
            'role' => 'teacher',
            'status' => 'enable',
            'token' => 'dN26Dm2s5sJordo8eE6qT3nsnYNRqqWR',
            'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
            'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
        ]);

        DB::table('student')->insert([
            'student_id' => 5,
            'name' => 'อ.ดร.อรวรรณ เชาวลิต',
            'email' => 'orawan@example.com',
            'image' => 13,
            'token' => 'dN26Dm2s5sJordo8eE6qT3nsnYNRqqWR',
            'ip' => '',
            'role' => 'hidden',
            'status' => 'enable',
            'username' => 'Orawan',
            'password' => password_hash('Orawan', PASSWORD_DEFAULT),
            'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
            'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
        ]);




        DB::table('teacher')->insert([
            'name' => 'อ.ดร.ทัศนวรรณ ศูนย์กลาง',
            'email' => 'tasanawan@example.com',
            'image' => 14,
            'username' => 'Tasanawan',
            'password' => password_hash('Tasanawan', PASSWORD_DEFAULT),
            'role' => 'teacher',
            'status' => 'enable',
            'token' => 'tNA3wfIDKk9WmULU6V8WGZ7TcmeHvkSn',
            'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
            'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
        ]);

        DB::table('student')->insert([
            'student_id' => 6,
            'name' => 'อ.ดร.ทัศนวรรณ ศูนย์กลาง',
            'email' => 'tasanawan@example.com',
            'image' => 14,
            'token' => 'tNA3wfIDKk9WmULU6V8WGZ7TcmeHvkSn',
            'ip' => '',
            'role' => 'hidden',
            'status' => 'enable',
            'username' => 'Tasanawan',
            'password' => password_hash('Tasanawan', PASSWORD_DEFAULT),
            'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
            'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
        ]);



        /*DB::table('teacher')->insert([
            'name' => 'The Archangel',
            'email' => 'Archangel@example.com',
            'image' => 15,
            'username' => 'Archangel',
            'password' => password_hash('Archangel', PASSWORD_DEFAULT),
            'role' => 'admin',
            'token' => 'WfHp37ebFWWWWW2esAPrvJVWkXWLpsDf',
            'status' => 'enable',
            'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
            'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
        ]);*/



        DB::table('teacher')->insert([
            'name' => 'CodeOne',
            'email' => 'codeone@admin.com',
            'image' => 16,
            'username' => 'CodeOne',
            'password' => password_hash('manmigau', PASSWORD_DEFAULT),
            'role' => 'admin',
            'token' => 'WfHp37ebFAAAAA2esAPrvJVWkXWLpsDf',
            'status' => 'enable',
            'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
            'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
        ]);
    }
}
