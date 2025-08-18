<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class CareerPlan extends Model
{
    protected $fillable = [
        'user_id', 'plan', 'target_date'
    ];
    public function user() {
        return $this->belongsTo(User::class);
    }
}
