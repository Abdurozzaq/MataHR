<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Emergency Contacts
        Schema::create('emergency_contacts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('name');
            $table->string('relationship');
            $table->string('phone');
            $table->timestamps();
        });
        // Job Histories
        Schema::create('job_histories', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('position');
            $table->string('department')->nullable();
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->string('type')->nullable(); // promosi/mutasi
            $table->timestamps();
        });
        // Trainings
        Schema::create('trainings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('title');
            $table->date('date')->nullable();
            $table->string('provider')->nullable();
            $table->timestamps();
        });
        // Certifications
        Schema::create('certifications', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('title');
            $table->date('date')->nullable();
            $table->string('issuer')->nullable();
            $table->timestamps();
        });
        // Work Goals
        Schema::create('work_goals', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->text('goal');
            $table->date('target_date')->nullable();
            $table->timestamps();
        });
        // Career Plans
        Schema::create('career_plans', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->text('plan');
            $table->date('target_date')->nullable();
            $table->timestamps();
        });
        // Supervisor Employees
        Schema::create('supervisor_employees', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('supervisor_id');
            $table->unsignedBigInteger('employee_id');
            $table->timestamps();
        });
        
    }

    public function down(): void
    {
        Schema::dropIfExists('emergency_contacts');
        Schema::dropIfExists('job_histories');
        Schema::dropIfExists('trainings');
        Schema::dropIfExists('certifications');
        Schema::dropIfExists('work_goals');
        Schema::dropIfExists('career_plans');
        Schema::dropIfExists('supervisor_employees');
    }
};
