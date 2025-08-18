<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class PerformanceReview extends Model
{
    protected $fillable = [
        'user_id', 'result', 'date'
    ];
    public function user() {
        return $this->belongsTo(User::class);
    }
}
