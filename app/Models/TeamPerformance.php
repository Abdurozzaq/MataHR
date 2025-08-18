<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class TeamPerformance extends Model
{
    protected $fillable = [
        'supervisor_id', 'performance_info', 'date'
    ];
    public function supervisor() {
        return $this->belongsTo(User::class, 'supervisor_id');
    }
}
