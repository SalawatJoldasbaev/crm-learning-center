<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;

class Employee extends Authenticatable
{
    use HasFactory;
    use HasApiTokens;

    protected $fillable = [
        'name', 'password', 'phone', 'file_id', 'role', 'gender', 'salary', 'branch_id',
    ];
    protected $casts = [
        'role' => 'json',
    ];
    public function branch()
    {
        return $this->hasOne(Branch::class, 'id', 'branch_id');
    }
}
