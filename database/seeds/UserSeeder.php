<?php

use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->insert([
            'username' => 'admin',
            'name' => 'Administrator',
            'email' => 'kiki@gmail.com',
            'password' => md5('admin'),
            'nowa' => '085155300552'
        ]);

        DB::table('users')->insert([
            'username' => 'operator',
            'name' => 'Operator',
            'email' => 'operator@gmail.com',
            'password' => md5('123123'),
            'nowa' => '085155300552'
        ]);

        DB::table('users')->insert([
            'username' => 'admin1',
            'name' => 'admin 1',
            'email' => 'admin1@gmail.com',
            'password' => md5('123123'),
            'nowa' => '085155300552'
        ]);

        DB::table('users')->insert([
            'username' => 'admin2',
            'name' => 'admin 2',
            'email' => 'admin2@gmail.com',
            'password' => md5('123123'),
            'nowa' => '085155300552'
        ]);
    }
}
