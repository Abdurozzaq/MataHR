<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Attendance extends Model
{
    protected $fillable = [
        'user_id', 'clock_in_at', 'clock_out_at', 'latitude', 'longitude'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
