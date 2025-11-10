<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;
    protected $fillable = [
        'company_id',
        'employee_id',
        'user_name',
        'employee_name',
        'email',
        // 'password',
        // 'password_old',
        'created_by',
        'updated_at',
        'last_login',
        'is_active',
        'login_attempts',
        'lockout_time'
    ];

    protected $hidden = [
        'password',
        'password_old',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'lockout_time' => 'datetime',
        'password' => 'hashed',
    ];

    public function getCompanyIdAttribute($originalValue)
    {
        // 1. Cek mainCompany dulu (multi company)
        if ($this->mainCompany()->exists()) {
            return $this->mainCompany->company_id;
        }

        // 2. Cek masterEmployee
        if ($this->masterEmployee()->exists()) {
            return $this->masterEmployee->company_id;
        }

        // 3. Fallback ke nilai asli
        return $originalValue;
    }

    public function masterEmployee()
    {
        return $this->belongsTo(MasterEmployees::class, 'employee_id', 'employee_id');
    }

    public function mainCompany()
    {
        return $this->hasOne(MasterEmployeesMultiCompany::class, 'employee_id', 'employee_id')
                   ->where('is_main', 1)
                   ->where('is_active', 1);
    }
}
