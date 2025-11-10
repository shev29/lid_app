<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MasterEmployees extends Model
{
    use HasFactory;
    protected $table = 'vw_master_employee_active';
    protected $fillable = [
        'company_id',
        'employee_id',
        'employee_name',
        'employee_email',
        'is_active'
    ];
}
