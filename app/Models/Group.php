<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_id',
        'room_id',
        'time_id',
        'teacher_id',
        'name',
        'days',
        'group_start_date',
    ];

    protected $casts = [
        'days' => 'json',
    ];
}
