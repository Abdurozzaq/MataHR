<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class JobHistory extends Model
{
    protected $fillable = [
        'user_id', 'position', 'department', 'start_date', 'end_date', 'type'
    ];
    public function user() {
        return $this->belongsTo(User::class);
    }
}
