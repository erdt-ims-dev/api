<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ModifyScholarLeaveApplicationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('scholar_leave_application', function (Blueprint $table) {
            // Remove old columns
            $table->dropColumn(['leave_start', 'leave_end', 'leave_letter']);
            
            // Add new columns
            $table->string('semester')->after('comment_id');
            $table->string('year')->after('semester');
            $table->text('file')->after('year');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('scholar_leave_application', function (Blueprint $table) {
            // Reverse the modifications: add back old columns and drop new ones
            $table->date('leave_start')->after('comment_id');
            $table->date('leave_end')->after('leave_start');
            $table->string('leave_letter')->after('leave_end');
            
            $table->dropColumn(['semester', 'year', 'file']);
        });
    }
}
