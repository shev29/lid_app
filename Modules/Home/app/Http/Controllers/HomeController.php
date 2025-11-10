<?php

namespace Modules\Home\Http\Controllers;

use App\Http\Controllers\BaseController;
use App\Http\Controllers\RoleController;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class HomeController extends BaseController {
    public function index(Request $request) {
        $employeeId = $this->employeeId;
        $companyId = $this->companyId;
        $employeeName = $this->employeeName;
        $roleId = $this->roleId;
        $employeeIdEncrypt = $this->employeeIdEncrypt;

        $roleController = new RoleController();
        $requestData = new Request();
        $requestData->replace([
                                'employeeId' => $employeeId,
                                'roleId' => $roleId,
                            ]);
        $getNavigation = $roleController->navigation($requestData);
        $navMenu = $getNavigation['navMenu'];
        $navSubmenu = $getNavigation['navSubmenu'];

        return view('home::index', compact('employeeIdEncrypt', 'navMenu', 'navSubmenu', 'employeeId', 'employeeName'));
    }

}
