<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class WorkGoal extends Model
{
    protected $fillable = [
        'user_id', 'goal', 'target_date'
    ];
    public function user() {
        return $this->belongsTo(User::class);
    }
}
