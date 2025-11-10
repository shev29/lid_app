<?php

namespace App\Http\Controllers;

use App\Services\SafeToken;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class BaseController extends Controller
{
    protected $user;
    protected $employeeId;
    protected $employeeIdEncrypt;
    protected $companyId;
    protected $departmentId;
    protected $employeeName;
    protected $roleId;

    public function __construct()
    {
        // $this->middleware(function ($request, $next) {
        //     view()->share('layout', 'layouts.app');
        //     return $next($request);
        // });
        $this->middleware(function ($request, $next) {
            // Share layout
            view()->share('layout', 'layouts.app');

            // Set user data jika sudah login
            $this->user = Auth::user();

            if ($this->user) {
                $this->employeeId = $this->user->employee_id;
                $this->companyId = $this->user->company_id;
                $this->departmentId = $this->user->department_id;
                $this->employeeName = Str::upper($this->user->employee_name);
                $this->roleId = $this->user->role_id;

                $encryptId = ['id' => $this->user->employee_id];
                $encryptId = SafeToken::encode($encryptId);
                $this->employeeIdEncrypt = $encryptId;

                view()->share([
                    'employeeId' => $this->employeeId,
                    'employeeIdEncrypt' => $this->employeeIdEncrypt,
                    'companyId' => $this->companyId,
                    'departmentId' => $this->departmentId,
                    'employeeName' => $this->employeeName,
                    'roleId' => $this->roleId,
                ]);
            }

            return $next($request);
        });
    }
}