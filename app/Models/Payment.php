<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'group_id',
        'date',
        'amount',
        'description',
        'payment_type',
        'employee_id',
    ];
}
