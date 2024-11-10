<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ModifyStudyColumnInScholarPortfolioTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('scholar_portfolio', function (Blueprint $table) {
            $table->text('study')->change(); // Change to TEXT or LONGTEXT if needed
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('scholar_portfolio', function (Blueprint $table) {
            $table->string('study', 255)->change(); // Change back to original size
        });
    }
}
