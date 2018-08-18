<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AttendanceMasterlist extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
      Schema::create('attendance_masterlist', function (Blueprint $table) {
        $table->integer('attendanceId')->unsigned()->nullable();
        $table->foreign('attendanceId')->references('id')
          ->on('attendance')->onDelete('cascade');
        $table->integer('brethrenId')->unsigned()->nullable();
        $table->foreign('brethrenId')->references('id')
          ->on('masterlist')->onDelete('cascade');
      });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
      Schema::dropIfExists('attendance_masterlist');
    }
}
