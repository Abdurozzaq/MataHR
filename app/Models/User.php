<?php
namespace App\Models;

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
        'id',
        'supervisor_id',
        'name',
        'email',
        'role',
        'email_verified_at',
        'created_at',
        'updated_at',
        'full_name',
        'birth_date',
        'birth_place',
        'gender',
        'address',
        'phone',
        'personal_email',
        'marital_status',
        'nik',
        'employee_id',
        'position_id',
        'department_id',
        'start_date',
        'employment_status',
        'office_location',
        'supervisor',
        'salary',
        'benefits',
    'bank_account',
    'bank_name',
    'password'
    ];
    public function position() {
        return $this->belongsTo(Position::class);
    }
    public function department() {
        return $this->belongsTo(Department::class);
    }

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

    /**
     * Generate a new unique employee_id.
     * Format: EMP + 6 digit running number (e.g. EMP000123)
     */
    public static function generateEmployeeId()
    {
        $lastUser = self::orderByDesc('id')->first();
        $lastId = $lastUser && $lastUser->employee_id ? (int)preg_replace('/[^0-9]/', '', $lastUser->employee_id) : 0;
        $newId = $lastId + 1;
        return 'EMP' . str_pad($newId, 6, '0', STR_PAD_LEFT);
    }

    // Relasi profil karyawan
    public function emergency_contacts()
    {
        return $this->hasMany(EmergencyContact::class);
    }
    public function jobHistories()
    {
        return $this->hasMany(JobHistory::class);
    }
     public function job_histories()
    {
        return $this->hasMany(JobHistory::class);
    }
    public function trainings() {
        return $this->hasMany(Training::class);
    }
    public function certifications() {
        return $this->hasMany(Certification::class);
    }
    public function work_goals()
    {
        return $this->hasMany(WorkGoal::class);
    }
    public function workGoals()
    {
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
    public function supervisor() {
        return $this->belongsTo(User::class, 'supervisor_id');
    }
    public function emergencyContacts()
    {
        return $this->hasMany(EmergencyContact::class);
    }
}
