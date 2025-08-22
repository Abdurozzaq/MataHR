<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WorkSchedule extends Model
{

     protected $table = 'work_schedules';

    protected $fillable = [
        'day_of_week',
        'clock_in_start',
        'clock_in_end',
        'clock_out_start',
        'clock_out_end',
        'is_workday',
        'min_work_duration',
    ];
}
