<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ModifyMiddleNameNullableInAccountDetails extends Migration
{
    public function up()
    {
        Schema::table('account_details', function (Blueprint $table) {
            $table->string('middle_name')->nullable()->change();
        });
    }

    public function down()
    {
        Schema::table('account_details', function (Blueprint $table) {
            $table->string('middle_name')->nullable(false)->change();
        });
    }
}
