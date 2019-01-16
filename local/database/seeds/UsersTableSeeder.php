<?php

use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // DB::table('users')->insert([
        //         'name' => 'siddique',
        //         'email' => 'kisorniru@gmail.com',
        //         'password' => app('hash')->make('secret'),
        //     ]);

        // for ($i=0; $i < 10; $i++) { 
        //     DB::table('users')->insert([
        //         'name' => str_random(10),
        //         'email' => str_random(10).'@gmail.com',
        //         'password' => app('hash')->make('secret'),
        //     ]);
        // }
    }
}
