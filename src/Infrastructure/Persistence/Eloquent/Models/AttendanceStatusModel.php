<?php

namespace Sokol\Infrastructure\Persistence\Eloquent\Models;

use Illuminate\Database\Eloquent\Model;

class AttendanceStatusModel extends Model
{
    protected $table = 'attendance_statuses';

    public $timestamps = false;

    protected $fillable = ['name'];
}
