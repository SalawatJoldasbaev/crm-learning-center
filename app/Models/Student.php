<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory;

    protected $fillable = [
        'addition_phone',
        'first_name',
        'last_name',
        'phone',
        'password',
        'address',
        'birthday',
        'gender',
        'branch_id',
    ];

    protected $casts = [
        'addition_phone' => 'json',
    ];
}
