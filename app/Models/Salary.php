<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Salary extends Model
{
    use HasFactory;

    protected $fillable = [
        'teacher_id',
        'date',
        'amount'
    ];

    public function teacher()
    {
        return $this->belongsTo(Teacher::class);
    }
}
