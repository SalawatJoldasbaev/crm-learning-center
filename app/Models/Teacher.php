<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Teacher extends Authenticatable
{
    use HasFactory;
    use HasApiTokens;
    protected $fillable = [
        'branch_id',
        'name', 'password', 'phone', 'file_id', 'gender', 'salary_percentage'
    ];
    protected $casts = [
        'branch_ids' => 'json'
    ];
    public function branch()
    {
        return $this->hasOne(Branch::class, 'id', 'branch_id');
    }
}
