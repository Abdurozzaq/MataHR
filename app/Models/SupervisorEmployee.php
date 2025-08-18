<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class SupervisorEmployee extends Model
{
    protected $fillable = [
        'supervisor_id', 'employee_id'
    ];
    public function supervisor() {
        return $this->belongsTo(User::class, 'supervisor_id');
    }
    public function employee() {
        return $this->belongsTo(User::class, 'employee_id');
    }
}
