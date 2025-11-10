<?php

namespace Modules\ApplicationManager\Http\Controllers;

use App\Http\Controllers\BaseController;
use App\Http\Controllers\Controller;
use App\Http\Controllers\RoleController;
use Illuminate\Http\Request;

class ApplicationManagerController extends BaseController
{
    public function appManager(Request $request) {
        $employeeId = $this->employeeId;
        $employeeIdEncrypt = $this->employeeIdEncrypt;
        $companyId = $this->companyId;
        $employeeName = $this->employeeName;
        $roleId = $this->roleId;

        $roleController = new RoleController();
        $requestData = new Request();
        $requestData->replace([
                                'employeeId' => $employeeId,
                                'roleId' => $roleId,
                            ]);
        $getNavigation = $roleController->navigation($requestData);
        $navMenu = $getNavigation['navMenu'];
        $navSubmenu = $getNavigation['navSubmenu'];

        return view('applicationmanager::index', compact('employeeId', 'navMenu', 'navSubmenu', 'employeeIdEncrypt', 'employeeName'));
    }

    public function storage(Request $request)
    {
        dd($this->employeeId);

        $employeeId = $this->employeeId;
        $employeeIdEncrypt = $this->employeeIdEncrypt;
        $companyId = $this->companyId;
        $employeeName = $this->employeeName;
        $roleId = $this->roleId;

        $roleController = new RoleController();
        $requestData = new Request();
        $requestData->replace([
                                'employeeId' => $employeeId,
                                'roleId' => $roleId,
                            ]);
        $getNavigation = $roleController->navigation($requestData);
        $navMenu = $getNavigation['navMenu'];
        $navSubmenu = $getNavigation['navSubmenu'];


        return view('applicationmanager::storage_manager', compact('employeeId', 'navMenu', 'navSubmenu', 'companyId', 'employeeIdEncrypt', 'employeeName'));
    }
}
