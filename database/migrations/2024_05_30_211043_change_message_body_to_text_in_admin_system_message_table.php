<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeMessageBodyToTextInAdminSystemMessageTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('admin_system_message', function (Blueprint $table) {
            $table->text('message_body')->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('admin_system_message', function (Blueprint $table) {
            $table->string('message_body', 255)->change(); // Revert to VARCHAR(255)
        });
    }
}