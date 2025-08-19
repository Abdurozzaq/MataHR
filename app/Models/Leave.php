<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Leave extends Model
{
    protected $table = 'leaves';
    protected $guarded = [];

    public function employee()
    {
        return $this->belongsTo(User::class, 'employee_id', 'employee_id');
    }
}
