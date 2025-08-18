<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class EmergencyContact extends Model
{
    protected $fillable = [
        'user_id', 'name', 'relationship', 'phone'
    ];
    public function user() {
        return $this->belongsTo(User::class);
    }
}
