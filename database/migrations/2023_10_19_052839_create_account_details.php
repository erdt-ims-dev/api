<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAccountDetails extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('account_details', function (Blueprint $table) {
            $table->uuid('id')->primary()->unique();
            $table->string('user_id');
            $table->string('first_name');
            $table->string('middle_name');
            $table->string('last_name');
            $table->string('program')->nullable();
            $table->binary('profile_picture');
            $table->binary('birth_certificate')->nullable();
            $table->binary('tor')->nullable();
            $table->binary('narrative_essay')->nullable();
            $table->binary('recommendation_letter')->nullable();
            $table->binary('medical_certificate')->nullable();
            $table->binary('nbi_clearance')->nullable();
            $table->binary('admission_notice')->nullable();
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
        Schema::dropIfExists('account_details');
    }
}
