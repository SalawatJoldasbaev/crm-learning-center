<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentInGroup extends Model
{
    use HasFactory;

    protected $casts = [
        'active' => 'boolean',
    ];

    protected $fillable = [
        'group_id',
        'student_id',
        'start_date',
    ];

    public function group()
    {
        return $this->belongsTo(Group::class);
    }
}
