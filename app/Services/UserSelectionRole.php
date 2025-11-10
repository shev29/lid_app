<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

class UserSelectionRole
{
    public static function userCompanyRole(array $data): ?array {
        $companyId = $data['companyId'];
        $employeeId = $data['employeeId'];
        $menuId = $data['menuId'];
        $submenuId = $data['submenuId'];
        $selectedDefault = $data['selectedDefault'];
        $getUser = DB::table('vw_master_employee_active as a')
                            ->leftJoin('master_company as b', function($join) {
                                $join->on('b.company_id', 'a.company_id');
                            })
                            ->select('a.employee_name', 'a.company_id', 'b.company_code', 'b.company_name')
                            ->where('a.employee_id', $employeeId)
                            ->first();

        if(!$getUser) {
            return null;
        }

        $arrCompany[] = [
                        'id' => $getUser->company_id,
                        'text' => $getUser->company_name,
                        'selected' => true,
                    ];

        $getUserMultiCompany = DB::table('master_employees_multi_company as a')
                                    ->leftJoin('master_company as b', function($join) {
                                        $join->on('b.company_id', 'a.company_id');
                                    })
                                    ->select('a.company_id', 'b.company_code', 'b.company_name')
                                    ->where('a.employee_id', $employeeId)
                                    ->where('a.company_id', '<>', $getUser->company_id)
                                    ->where(function($query) use($menuId) {
                                        $query->whereNull('a.menu_id')
                                            ->orWhere('a.menu_id', '=', $menuId);
                                    })
                                    ->where(function($query) use($submenuId) {
                                        $query->whereNull('a.submenu_id')
                                            ->orWhere('a.submenu_id', '=', $submenuId);
                                    })
                                    ->where('a.is_active', 1)
                                    ->orderBy('a.company_id', 'asc')
                                    ->get();
        if ($getUserMultiCompany->isNotEmpty()) {
            if($selectedDefault == false) {
                $arrCompany[0]['selected'] = false;
            }

            foreach($getUserMultiCompany as $rowUserMultiCompany) {
                $arrCompany[] = [
                                    'id' => $rowUserMultiCompany->company_id,
                                    'text' => $rowUserMultiCompany->company_name,
                                    'selected' => false,
                                ];
            }
        }

        usort($arrCompany, function($a, $b) {
            return $a['id'] <=> $b['id'];
        });

        return $arrCompany;
    }

    public static function userSelection(array $data) {
        $companyId = $data['companyId'];
        $employeeId = $data['employeeId'];
        $employeeName = $data['employeeName'];
        $menuId = $data['menuId'];
        $submenuId = $data['submenuId'];
        $selectedDefault = $data['selectedDefault'];

        $arrSelection[] = [
                                'id' => $employeeId,
                                'text' => $employeeName,
                                'selected' => true,
                            ];

        $getUserSelectionRole = DB::table('master_role_users as a')
                                        ->leftJoin('master_role_details as b', 'a.role_id', '=', 'b.role_id')
                                        ->leftJoin('vw_master_employee_all as c', 'c.employee_id', '=', 'b.ref_id')
                                        ->select('a.user_role_id', 'c.employee_id', 'c.employee_name');

        if($companyId) {
                $getUserSelectionRole = $getUserSelectionRole->where('a.company_id', $companyId)
                                                            ->where(function($query) use($menuId) {
                                                                $query->whereNull('a.role_menu')
                                                                    ->orWhere('a.role_menu', '=', $menuId);
                                                            })
                                                            ->where(function($query) use($submenuId) {
                                                                $query->whereNull('a.role_submenu')
                                                                    ->orWhere('a.role_submenu', '=', $submenuId);
                                                            });
        }
        else {
             $getUserSelectionRole = $getUserSelectionRole->where(function($query) use($menuId) {
                                                            $query->whereNull('a.role_menu');
                                                        })
                                                        ->where(function($query) use($submenuId) {
                                                            $query->whereNull('a.role_submenu');
                                                        });
        }

        $getUserSelectionRole = $getUserSelectionRole->where('a.employee_id', $employeeId)
                                        ->where('a.role_type', 'SELECTION')
                                        ->where('a.role_action', '1')
                                        ->where('a.is_active', '1')
                                        ->get();
        if ($getUserSelectionRole->isNotEmpty()) {
            if($selectedDefault == false) {
                $arrSelection[0]['selected'] = false;
            }
            foreach($getUserSelectionRole as $rowUserSelectionRole) {
                $arrSelection[] = [
                                    'id' => $rowUserSelectionRole->employee_id,
                                    'text' => $rowUserSelectionRole->employee_name,
                                    'selected' => false,
                                ];
            }
        }
        return $arrSelection;
    }
}