<?php

namespace App\Http\Controllers;

use App\Models\RoleModel;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    public function navigation(Request $request) {
        $employeeId = $request->employeeId;
        $roleId = $request->roleId;

        $navMenu = array();
        $navSubmenu = array();
        $model = new RoleModel();
        $getMenu = $model->masterMenus()
                    ->from('master_menus')
                    ->select('menu_id', 'menu_name', 'menu_icon', 'menu_type', 'route_name', 'menu_roles')
                    ->where('is_active', '1')
                    ->orderBy('menu_order', 'asc')
                    ->get();
        foreach ($getMenu as $rowMenu) {
            if($rowMenu->menu_roles == 'ALL_ROLE') {
                $navMenu[] = [
                                'menuId' => $rowMenu->menu_id,
                                'menuIcon' => $rowMenu->menu_icon,
                                'menuName' => $rowMenu->menu_name,
                                'menuType' => $rowMenu->menu_type,
                                'routeName' => $rowMenu->route_name,
                            ];
                if($rowMenu->menu_type === 'parent') {
                    $getSubmenu = $model->masterSubmenus()
                            ->from('master_submenus')
                            ->select('submenu_id', 'submenu_name', 'submenu_order', 'menu_type', 'route_name', 'submenu_roles')
                            ->where('menu_id', $rowMenu->menu_id)
                            ->where('is_active', '1')
                            ->orderBy('submenu_order', 'asc')
                            ->get();
                    foreach ($getSubmenu as $rowSubmenu) {
                        if($rowSubmenu->submenu_roles == 'ALL_ROLE') {
                            $navSubmenu[$rowMenu->menu_id][] = [
                                            'submenuId' => $rowSubmenu->submenu_id,
                                            'submenuName' => $rowSubmenu->submenu_name,
                                            'menuType' => $rowSubmenu->menu_type,
                                            'routeName' => $rowSubmenu->route_name,
                                        ];
                        }
                        else if($rowSubmenu->submenu_roles == 'SPECIFIED_ROLE') {
                            $getSubmenuRoleDetails = $model->masterRoleDetails()
                                                        ->from('master_role_details as a')
                                                        ->leftJoin('master_role_users as b', 'b.role_id', '=', 'a.role_id')
                                                        ->select('a.role_detail_id')
                                                        ->where('b.employee_id', $employeeId)
                                                        ->where('a.role_type', 'SUBMENUS')
                                                        ->where('a.ref_id', $rowSubmenu->submenu_id)
                                                        ->where('a.is_active', '1')
                                                        ->where('b.is_active', '1')
                                                        ->first();
                            if($getSubmenuRoleDetails) {
                                $navSubmenu[$rowMenu->menu_id][] = [
                                    'submenuId' => $rowSubmenu->submenu_id,
                                    'submenuName' => $rowSubmenu->submenu_name,
                                    'menuType' => $rowSubmenu->menu_type,
                                    'routeName' => $rowSubmenu->route_name,
                                ];
                            }
                        }
                    }
                }
            }
            else if($rowMenu->menu_roles == 'SPECIFIED_ROLE') {
                $getRoleDetails = $model->masterRoleDetails()
                                        ->from('master_role_details as a')
                                        ->leftJoin('master_role_users as b', 'b.role_id', '=', 'a.role_id')
                                        ->select('a.role_detail_id')
                                        ->where('b.employee_id', $employeeId)
                                        ->where('a.role_type', 'MENUS')
                                        ->where('a.ref_id', $rowMenu->menu_id)
                                        ->where('a.is_active', '1')
                                        ->where('b.is_active', '1')
                                        ->first();
                if($getRoleDetails) {
                    $navMenu[] = [
                        'menuId' => $rowMenu->menu_id,
                        'menuIcon' => $rowMenu->menu_icon,
                        'menuName' => $rowMenu->menu_name,
                        'menuType' => $rowMenu->menu_type,
                        'routeName' => $rowMenu->route_name,
                    ];
                    if($rowMenu->url_type === null) {
                        $getSubmenu = $model->masterSubmenus()
                                ->from('master_submenus')
                                ->select('submenu_id', 'submenu_name', 'submenu_order', 'menu_type', 'route_name', 'submenu_roles')
                                ->where('menu_id', $rowMenu->menu_id)
                                ->where('is_active', '1')
                                ->orderBy('submenu_order', 'asc')
                                ->get();
                        foreach ($getSubmenu as $rowSubmenu) {
                            if($rowSubmenu->submenu_roles == 'ALL_ROLE') {
                                $navSubmenu[$rowMenu->menu_id][] = [
                                                'submenuId' => $rowSubmenu->submenu_id,
                                                'submenuName' => $rowSubmenu->submenu_name,
                                                'menuType' => $rowSubmenu->menu_type,
                                                'routeName' => $rowSubmenu->route_name,
                                            ];
                            }
                            else if($rowSubmenu->submenu_roles == 'SPECIFIED_ROLE') {
                                $getSubmenuRoleDetails = $model->masterRoleDetails()
                                                            ->from('master_role_details as a')
                                                            ->leftJoin('master_role_users as b', 'b.role_id', '=', 'a.role_id')
                                                            ->select('a.role_detail_id')
                                                            ->where('b.employee_id', $employeeId)
                                                            ->where('a.role_type', 'SUBMENUS')
                                                            ->where('a.ref_id', $rowSubmenu->submenu_id)
                                                            ->where('a.is_active', '1')
                                                            ->where('b.is_active', '1')
                                                            ->first();
                                if($getSubmenuRoleDetails) {
                                    $navSubmenu[$rowMenu->menu_id][] = [
                                        'submenuId' => $rowSubmenu->submenu_id,
                                        'submenuName' => $rowSubmenu->submenu_name,
                                        'menuType' => $rowSubmenu->menu_type,
                                        'routeName' => $rowSubmenu->route_name,
                                    ];
                                }
                            }
                        }
                    }
                }
            }
        }

        return compact('navMenu', 'navSubmenu');
    }

}
