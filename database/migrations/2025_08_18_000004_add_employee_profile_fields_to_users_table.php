<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('full_name')->nullable();
            $table->date('birth_date')->nullable();
            $table->string('birth_place')->nullable();
            $table->enum('gender', ['male', 'female', 'other'])->nullable();
            $table->text('address')->nullable();
            $table->string('phone')->nullable();
            $table->string('personal_email')->nullable();
            $table->enum('marital_status', ['single', 'married', 'divorced', 'widowed'])->nullable();
            $table->string('nik')->nullable();
            $table->string('employee_id')->nullable();
            $table->unsignedBigInteger('position_id')->nullable();
            $table->unsignedBigInteger('department_id')->nullable();
            $table->date('start_date')->nullable();
            $table->enum('employment_status', ['permanent', 'contract', 'intern'])->nullable();
            $table->string('office_location')->nullable();
            $table->string('supervisor')->nullable();
            $table->decimal('salary', 15, 2)->nullable();
            $table->text('benefits')->nullable();
            $table->string('bank_account')->nullable();
            $table->string('bank_name')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'full_name', 'birth_date', 'birth_place', 'gender', 'address', 'phone', 'personal_email', 'marital_status', 'nik',
                'employee_id', 'position_id', 'department_id', 'start_date', 'employment_status', 'office_location', 'supervisor',
                'salary', 'benefits', 'bank_account', 'bank_name'
            ]);
        });
    }
};
