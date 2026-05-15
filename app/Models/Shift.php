<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Shift extends Model
{
    //
    protected $fillable = ['name', 'start_time', 'end_time', 'breaK_minute', 'hours', 'status', 'grace_minutes'];

    public function shiftSchedules()
    {
        return $this->hasMany(ShiftSchedule::class);
    }
}
