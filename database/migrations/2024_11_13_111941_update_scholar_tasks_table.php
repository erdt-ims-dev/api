<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateScholarTasksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('scholar_tasks', function (Blueprint $table) {
            // Drop old columns
            $table->dropColumn(['midterm_assessment', 'final_assessment', 'approval_status']);

            // Add new columns
            $table->string('year')->after('scholar_id');
            $table->string('semester')->after('year');
            $table->string('type')->after('semester');
            $table->text('file')->after('type');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('scholar_tasks', function (Blueprint $table) {
            // Restore dropped columns
            $table->string('midterm_assessment')->after('scholar_id');
            $table->string('final_assessment')->after('midterm_assessment');
            $table->string('approval_status')->after('final_assessment');

            // Drop added columns
            $table->dropColumn(['year', 'semester', 'type', 'file']);
        });
    }
}
