<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateScholarLeaveApplicationStatusTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('scholar_leave_application_status', function (Blueprint $table) {
            $table->uuid('id')->primary()->unique();
            $table->string('scholar_leave_app_id');
            $table->string('user_id');
            $table->string('comment_id');
            $table->string('application_status');
            $table->binary('application_letter');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('scholar_leave_application_status');
    }
}
