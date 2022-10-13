<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;

class Student extends Authenticatable
{
    // use HasFactory;
    use HasApiTokens;

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

    public function branch()
    {
        return $this->hasOne(Branch::class, 'id', 'branch_id');
    }
}
