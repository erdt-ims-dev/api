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
            $table->string('profile_picture', 16777215)->nullable();
            $table->string('birth_certificate', 16777215)->nullable();
            $table->string('tor', 16777215)->nullable();
            $table->string('narrative_essay', 16777215)->nullable();
            $table->string('recommendation_letter', 16777215)->nullable();
            $table->string('medical_certificate', 16777215)->nullable();
            $table->string('nbi_clearance', 16777215)->nullable();
            $table->string('admission_notice', 16777215)->nullable();
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
