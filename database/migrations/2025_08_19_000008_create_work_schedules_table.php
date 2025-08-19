<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('work_schedules', function (Blueprint $table) {
            $table->id();
            $table->unsignedTinyInteger('day_of_week'); // 0=Sunday, 1=Monday, ...
            $table->time('clock_in_start')->nullable();
            $table->time('clock_in_end')->nullable();
            $table->time('clock_out_start')->nullable();
            $table->time('clock_out_end')->nullable();
            $table->boolean('is_workday')->default(true);
            $table->integer('min_work_duration')->nullable(); // in minutes
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('work_schedules');
    }
};
