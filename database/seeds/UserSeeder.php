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
      $user->firstname     = 'ADMIN';
      $user->middlename    = '';
      $user->lastname      = 'BLAISE';
      $user->mobileNo      = 'n/a';
      $user->phoneNo       = 'n/a';
      $user->email         = 'admin@blaise.com';
      $user->password      = bcrypt('123123');
      $user->address       = 'noWhere';
      $user->city          = 'Makati';
      $user->zipCode       = '1234';
      $user->country       = 'PH';
      $user->userType      = 'ADMIN';
      $user->save();

      $user = new User;
      $user->firstname     = 'SALES';
      $user->middlename    = '';
      $user->lastname      = 'AGENT 1';
      $user->mobileNo      = 'n/a';
      $user->phoneNo       = 'n/a';
      $user->email         = 'agent1@blaise.com';
      $user->password      = bcrypt('123123');
      $user->address       = 'noWhere';
      $user->city          = 'Makati';
      $user->zipCode       = '1234';
      $user->country       = 'PH';
      $user->userType      = 'SALES_AGENT';
      $user->save();

      $user = new User;
      $user->firstname     = 'SALES';
      $user->middlename    = '';
      $user->lastname      = 'AGENT 2';
      $user->mobileNo      = 'n/a';
      $user->phoneNo       = 'n/a';
      $user->email         = 'agent2@blaise.com';
      $user->password      = bcrypt('123123');
      $user->address       = 'noWhere';
      $user->city          = 'Laguna';
      $user->zipCode       = '1235';
      $user->country       = 'PH';
      $user->userType      = 'SALES_AGENT';
      $user->save();

      $user = new User;
      $user->firstname     = 'Juan';
      $user->middlename    = 'Dela';
      $user->lastname      = 'Cruz';
      $user->mobileNo      = 'n/a';
      $user->phoneNo       = 'n/a';
      $user->email         = 'client1@blaise.com';
      $user->password      = bcrypt('123123');
      $user->address       = 'Cabuyao';
      $user->city          = 'Laguna';
      $user->zipCode       = '1235';
      $user->country       = 'PH';
      $user->companyName   = 'TechHaven';
      $user->companyEmail  = 'admin@techhaven.com';
      $user->lineBusiness  = 'Computer Shop';
      $user->companyAddress = 'Cabuyao Laguna';
      $user->companyCity    = 'Laguna';
      $user->companyZipCode = '1235';
      $user->companyLandLine= '1234567';
      $user->companyCountry = 'PH';
      $user->designation   = 'PRIVATE';
      $user->userType      = 'CLIENT';
      $user->save();

      $user = new User;
      $user->firstname     = 'Juana';
      $user->middlename    = 'Dela';
      $user->lastname      = 'Hoya';
      $user->mobileNo      = '52135';
      $user->phoneNo       = '52167';
      $user->email         = 'client2@blaise.com';
      $user->password      = bcrypt('123123');
      $user->address       = 'Makati';
      $user->city          = 'Makati';
      $user->zipCode       = '1234';
      $user->country       = 'PH';
      $user->companyName   = 'SSS';
      $user->companyEmail  = 'admin@sss.com';
      $user->lineBusiness  = 'SSS loan and benefits';
      $user->companyAddress = 'Makati';
      $user->companyCity    = 'Makati';
      $user->companyZipCode = '1234';
      $user->companyLandLine= '682191';
      $user->companyCountry = 'PH';
      $user->designation   = 'GOVERNMENT';
      $user->userType      = 'CLIENT';
      $user->save();
    }
}
