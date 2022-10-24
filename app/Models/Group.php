<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

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
        'group_end_date',
        'active',
        'branch_id',
    ];

    protected $casts = [
        'days' => 'json',
    ];

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    public function time()
    {
        return $this->belongsTo(TimeCourse::class);
    }

    protected function studentCount(): Attribute
    {
        $count = StudentInGroup::where('group_id', $this->id)->count();
        return Attribute::make(
            get: fn () => $count,
        );
    }

    public function ScopeActive($query)
    {
        return $query->where('active', true);
    }
}
