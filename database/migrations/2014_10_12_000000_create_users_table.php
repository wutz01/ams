<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('email')->unique();
            $table->string('username')->unique();
            $table->string('password')->nullable();
            $table->string('firstname')->nullable();
            $table->string('middlename')->nullable();
            $table->string('lastname')->nullable();
            $table->string('userType'); // ADMIN / SECRETARY
            $table->rememberToken();
            $table->timestamps();
        });

        Schema::create('masterlist', function (Blueprint $table) {
            $table->increments('id');
            $table->string('churchId')->unique();
            $table->string('firstname')->nullable();
            $table->string('middlename')->nullable();
            $table->string('lastname')->nullable();
            $table->text('address')->nullable();
            $table->string('email')->nullable();
            $table->string('lokalOrigin')->nullable();
            $table->string('birthday')->nullable();
            $table->string('sabbathDay')->nullable();
            $table->string('contactNumber')->nullable();
            $table->binary('fingerPrint')->nullable();
            $table->string('memberType')->comments('WORKER / MEMBER')->nullable();
            $table->string('status')->nullable();
            $table->integer('isOfficer')->unsigned();
            $table->timestamps();
        });

        Schema::create('groupings', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('area')->nullable();
            $table->text('description')->nullable();
            $table->string('type')->nullable()->default('LG'); // LG (lokal group) / Committee
            $table->timestamps();
        });

        Schema::create('groupings_masterlist', function (Blueprint $table) {
          $table->integer('group_id')->unsigned()->nullable();
          $table->foreign('group_id')->references('id')
            ->on('groupings')->onDelete('cascade');
          $table->integer('brethren_id')->unsigned()->nullable();
          $table->foreign('brethren_id')->references('id')
            ->on('masterlist')->onDelete('cascade');
        });

        Schema::create('attendance', function (Blueprint $table) {
            $table->increments('id');
            $table->string('batch')->comment('WS/PM/TG');
            $table->string('time'); // (7:30AM.. etc)
            $table->string('date'); // (02/11/2018.. etc)
            $table->integer('worker_assign')->unsigned(); // from workers_masterlist
            $table->integer('officer_assign')->unsigned(); // from masterlist
            $table->timestamps();
        });

        Schema::create('attendance_list', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('attendance_id')->unsigned(); // from workers_masterlist
            $table->integer('brethren_id')->unsigned(); // from masterlist
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('attendance_list');
        Schema::dropIfExists('attendance');
        Schema::dropIfExists('groupings_masterlist');
        Schema::dropIfExists('groupings');
        Schema::dropIfExists('masterlist');
        Schema::dropIfExists('users');
    }
}
