<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MasterEmployeesMultiCompany extends Model
{
    use HasFactory;
    protected $table = 'master_employees_multi_company';
    protected $fillable = [
        'company_id',
        'employee_id',
        'is_main',
        'is_active'
    ];
}