<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    protected $model = User::class;

    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),
                'role' => 'karyawan', // default role
        ];
    }

        /**
         * State for Manager/Supervisor
         */
        public function manager()
        {
            return $this->state(fn (array $attributes) => [
                'role' => 'manajer',
            ]);
        }

        /**
         * State for HR Generalist
         */
        public function hrGeneralist()
        {
            return $this->state(fn (array $attributes) => [
                'role' => 'hr_generalist',
            ]);
        }

        /**
         * State for Payroll Specialist
         */
        public function payrollSpecialist()
        {
            return $this->state(fn (array $attributes) => [
                'role' => 'payroll_specialist',
            ]);
        }

        /**
         * State for Recruitment Specialist
         */
        public function recruitmentSpecialist()
        {
            return $this->state(fn (array $attributes) => [
                'role' => 'recruitment_specialist',
            ]);
        }

        /**
         * State for System Administrator
         */
        public function systemAdministrator()
        {
            return $this->state(fn (array $attributes) => [
                'role' => 'administrator',
            ]);
        }

        /**
         * State for Executive/Leadership
         */
        public function executive()
        {
            return $this->state(fn (array $attributes) => [
                'role' => 'eksekutif',
            ]);
        }
    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
}
