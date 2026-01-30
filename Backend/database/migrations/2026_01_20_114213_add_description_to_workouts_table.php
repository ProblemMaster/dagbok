<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDescriptionToWorkoutsTable extends Migration
{
    public function up()
    {
        if (!Schema::hasColumn('workouts', 'description')) {
            Schema::table('workouts', function (Blueprint $table) {
                $table->text('description')->nullable()->after('date');
            });
        }
    }

    public function down()
    {
        if (Schema::hasColumn('workouts', 'description')) {
            Schema::table('workouts', function (Blueprint $table) {
                $table->dropColumn('description');
            });
        }
    }
}
