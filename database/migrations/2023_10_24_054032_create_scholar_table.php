<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateScholarTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('scholar', function (Blueprint $table) {
            $table->id('id');
            $table->string('user_id');
            $table->string('scholar_request_id')->nullable();
            $table->string('scholar_task_id')->nullable();
            $table->string('scholar_portfolio_id')->nullable();
            $table->string('scholar_leave_app_id')->nullable();
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
        Schema::dropIfExists('scholar');
    }
}
