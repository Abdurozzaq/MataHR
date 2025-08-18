<?php

namespace App\Models;

//use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable //implements MustVerifyEmail
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory;
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // Relasi profil karyawan
    public function emergencyContacts() {
        return $this->hasMany(EmergencyContact::class);
    }
    public function jobHistories() {
        return $this->hasMany(JobHistory::class);
    }
    public function trainings() {
        return $this->hasMany(Training::class);
    }
    public function certifications() {
        return $this->hasMany(Certification::class);
    }
    public function performanceReviews() {
        return $this->hasMany(PerformanceReview::class);
    }
    public function workGoals() {
        return $this->hasMany(WorkGoal::class);
    }
    public function careerPlans() {
        return $this->hasMany(CareerPlan::class);
    }
    public function supervisedEmployees() {
        return $this->hasMany(SupervisorEmployee::class, 'supervisor_id');
    }
    public function teamPerformances() {
        return $this->hasMany(TeamPerformance::class, 'supervisor_id');
    }
}
