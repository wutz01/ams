<?php

use Illuminate\Database\Seeder;
use App\User;
class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
      $user = new User;
      $user->firstname     = 'STB';
      $user->middlename    = '';
      $user->lastname      = 'ADMIN';
      $user->username      = 'administrator';
      $user->email         = 'admin@stb.com';
      $user->password      = bcrypt('123123');
      $user->userType      = 'ADMIN';
      $user->save();

      $user = new User;
      $user->firstname     = 'STB';
      $user->middlename    = '';
      $user->lastname      = 'SECRETARY';
      $user->username      = 'secretary';
      $user->email         = 'secretary@stb.com';
      $user->password      = bcrypt('123123');
      $user->userType      = 'SECRETARY';
      $user->save();
    }
}
