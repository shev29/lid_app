<?php

namespace Modules\ProcurementPurchasing\Http\Controllers;

use App\Http\Controllers\BaseController;
use App\Http\Controllers\EmailController;
use App\Http\Controllers\FlowRuleController;
use App\Http\Controllers\RoleController;
use App\Jobs\ConvertFileJob;
use App\Jobs\SendEmailJob;
use App\Services\FlowRuleServiceModel;
use App\Services\RuleEngineService;
use App\Services\SafeToken;
use App\Services\UserSelectionRole;
use Barryvdh\DomPDF\Facade\Pdf;
use DateTime;
use Exception;
use Illuminate\Database\Eloquent\Builder;

use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Modules\DocumentApproval\Models\DocumentApprovalModel;
use Modules\ProcurementPurchasing\Models\ProcurementPurchasingModel;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Facades\Excel;
use Modules\DocumentApproval\Http\Controllers\DocumentApprovalController as ControllersDocumentApprovalController;
use Modules\ProcurementPurchasing\Exports\ProcurementPurchaseExport;
use Yajra\DataTables\DataTables;
use App\Http\Middleware\DecodeModSecurityPlaceholders;

class ProcurementPurchasingController extends BaseController
{
    public function dashboard(Request $request){
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

        return view('procurementpurchasing::dashboard', compact('employeeId', 'navMenu', 'navSubmenu', 'employeeIdEncrypt', 'employeeName'));
    }

    public function request(Request $request){
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

        $getCompany = UserSelectionRole::userCompanyRole([
                                                            'employeeId' => $employeeId,
                                                            'companyId' => $companyId,
                                                            'menuId' => '4',
                                                            'submenuId' => '2',
                                                            'selectedDefault' => false
                                                        ]);
        // foreach ($getCompany as $key => $value) {
        //     $getCompany[$key]['id'] = SafeToken::encode(['c' => $value['id']]);
        // }

        if(count($getCompany) > 1) {
            $getCompany = Arr::prepend($getCompany, ['id' => 'ALL', 'text' => '-- ALL COMPANY --', 'selected' => true]);
        }
        $arrCompany = $getCompany;

        $arrSelection = UserSelectionRole::userSelection([
                                                            'employeeId' => $employeeId,
                                                            'employeeName' => $employeeName,
                                                            'companyId' => $companyId,
                                                            'menuId' => '4',
                                                            'submenuId' => '2',
                                                            'selectedDefault' => false
                                                        ]);

        return view('procurementpurchasing::request', compact('employeeId', 'navMenu', 'navSubmenu', 'companyId', 'employeeIdEncrypt', 'employeeName', 'arrCompany', 'arrSelection'));
    }

    public function archive(Request $request){
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

        $getCompany = UserSelectionRole::userCompanyRole([
                                                            'employeeId' => $employeeId,
                                                            'companyId' => $companyId,
                                                            'menuId' => '4',
                                                            'submenuId' => '3',
                                                            'selectedDefault' => false
                                                        ]);

        // foreach ($getCompany as $key => $value) {
        //     $getCompany[$key]['id'] = SafeToken::encode(['c' => $value['id']]);
        // }

        if(count($getCompany) > 1) {
            $getCompany = Arr::prepend($getCompany, ['id' => 'ALL', 'text' => '-- ALL COMPANY --', 'selected' => true]);
        }
        $arrCompany = $getCompany;

        $arrSelection = UserSelectionRole::userSelection([
                                                            'employeeId' => $employeeId,
                                                            'employeeName' => $employeeName,
                                                            'companyId' => $companyId,
                                                            'menuId' => '4',
                                                            'submenuId' => '3',
                                                            'selectedDefault' => false
                                                        ]);

        return view('procurementpurchasing::archive', compact('employeeId', 'navMenu', 'navSubmenu', 'employeeIdEncrypt', 'employeeName', 'arrCompany', 'arrSelection'));
    }

    public function settings(Request $request){
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

        $modelProcurement = new ProcurementPurchasingModel();
        $getUserCompany = $modelProcurement->vwMasterEmployeeActive()
                                        ->from('vw_master_employee_active as a')
                                        ->leftJoin('master_company as b', function($join) {
                                            $join->on('b.company_id', 'a.company_id');
                                        })
                                        ->select('b.company_id', 'b.company_code', 'b.company_name')
                                        ->where('a.employee_id', $employeeId)
                                        ->first();

        $documentApprovalController = new ControllersDocumentApprovalController();
        $requestData = new Request();
        $requestData->replace([
                                'employeeId' => $employeeId,
                                'selectedDefault' => true,
                            ]);
        $getCompany = $documentApprovalController->getCompany($requestData);
        $arrCompany = $getCompany;

        return view('procurementpurchasing::settings', compact('employeeId', 'navMenu', 'navSubmenu', 'employeeIdEncrypt', 'employeeName', 'arrCompany'));
    }

    public function userSelection(Request $request) {
        $model = new ProcurementPurchasingModel();
        $companyId = $request->companyId;
        // $encodedToken = SafeToken::decode($request->employeeId);
        $employeeId = $this->employeeId;
        $employeeName = $this->employeeName;

        $employeeIdSelected = $request->selectedId;
        // if($employeeIdSelected !== 'ALL') {
        //     $employeeIdSelected = SafeToken::decode($request->selectedId);
        //     $employeeIdSelected = $employeeIdSelected['id'];
        // }

        $getSelection = [];
        if($employeeIdSelected != $employeeId) {
            $getSelection = UserSelectionRole::userSelection([
                                                                'employeeId' => $employeeId,
                                                                'employeeName' => $employeeName,
                                                                'companyId' => $companyId,
                                                                'menuId' => '4',
                                                                'submenuId' => '2',
                                                                'selectedDefault' => false
                                                            ]);
        }
        else {
            $getEmployee = $model->vwMasterEmployeeAll()
                                ->from('vw_master_employee_all as a')
                                ->select('a.employee_name')
                                ->where('a.employee_id', $employeeIdSelected)
                                ->first();
            if($getEmployee) {
                $getSelection[] = [
                    'id' => $employeeIdSelected,
                    'text' => $getEmployee->employee_name,
                    'selected' => true,
                ];
            }
        }

        $arrSelection = $getSelection;
        return response()->json($arrSelection, 200);

        // $modelProcurement = new ProcurementPurchasingModel();
        // $arrEmployee = [];
        // $userSelection = UserSelectionRole::userSelection([
        //                                                     'employeeId' => $employeeId,
        //                                                     'companyId' => $this->companyId,
        //                                                     'menuId' => '4',
        //                                                     'submenuId' => '3',
        //                                                 ]);
        // if($userSelection) {
        //     if($request->type == 'PO_APPLICANT') {
        //         $getPoApplicant = $modelProcurement->docApprovalMatrix()
        //                                         ->from('doc_approval_matrix as a')
        //                                         ->leftJoin('vw_master_employee_all as b', 'a.employee_id', 'b.employee_id')
        //                                         ->select('a.employee_id', 'b.employee_name', 'b.position_name');

        //         if($request->companyId !== 'ALL') {
        //             $getPoApplicant = $getPoApplicant->where('a.company_id', $companyId);
        //         }

        //         $getPoApplicant = $getPoApplicant->where('a.matrix_as', 'APPLICANT')
        //                                         ->where('a.doc_type_id', '2')
        //                                         ->groupBy('employee_id', 'employee_name')
        //                                         ->get();
        //         if($getPoApplicant->isNotEmpty()) {
        //             $arrEmployee[] = ['id' => 'ALL', 'text' => '-- ALL APPLICANT --', 'selected' => ($employeeIdSelected === 'ALL') ? true : false];
        //             foreach($getPoApplicant as $row) {
        //                 $arrEmployee[] = [
        //                                     'id' => ($row->employee_id === $employeeIdSelected) ? $request->selectedId : SafeToken::encode(['id' => $row->employee_id]),
        //                                     'text' => $row->employee_name,
        //                                     'description1' => $row->position_name,
        //                                     'selected' => ($row->employee_id === $employeeIdSelected) ? true : false
        //                                 ];
        //             }
        //         }
        //         else {
        //             $arrEmployee[] = ['id' => $request->selectedId, 'text' => $this->employeeName, 'selected' => true];
        //         }
        //     }
        // }
        // else {
        //     $arrEmployee[] = ['id' => $request->selectedId, 'text' => $this->employeeName, 'selected' => true];
        // }
        // return response()->json($arrEmployee, 200);
    }

    public function orderFormRequest(Request $request) {
        $employeeId = $request->receiver;

        $response = [];
        $modelApproval = new DocumentApprovalModel();
        $modelProcurement = new ProcurementPurchasingModel();

        // if($employeeId != $this->employeeId) {
        //     // CHECK USER SELECTION
        //     $checkSelectionRoles = $modelProcurement->masterRoleDetails()
        //                                 ->from('master_role_users as a')
        //                                 ->leftJoin('master_role_details as b', 'b.role_id', '=', 'a.role_id')
        //                                 ->where('a.employee_id', $this->employeeId)
        //                                 ->where(function($query) {
        //                                     $query->whereNull('a.role_menu')
        //                                         ->orWhere('a.role_menu', '4');
        //                                 })
        //                                 ->where(function($query) {
        //                                     $query->whereNull('a.role_submenu')
        //                                         ->orWhere('a.role_submenu', '3');
        //                                 })
        //                                 ->where('b.ref_id', $employeeId)
        //                                 ->where('a.role_type', 'SELECTION')
        //                                 ->where('a.role_action', '1')
        //                                 ->where('a.is_active', '1')
        //                                 ->first();
        //     if(!$checkSelectionRoles) {
        //         return response()->json([
        //             'status' => 200,
        //             'message' => 'Forbidden',
        //             'ongoing' => 0,
        //             'data' => [],
        //             'hasMorePages' => false,
        //         ], 200);
        //     }
        // }

        $arrEmployeeId = [];
        if($employeeId != $this->employeeId) {
            // CHECK USER SELECTION
            $checkSelectionRoles = $modelProcurement->masterRoleDetails()
                                        ->from('master_role_users as a')
                                        ->leftJoin('master_role_details as b', 'b.role_id', '=', 'a.role_id')
                                        ->select('b.ref_id')
                                        ->where('a.employee_id', $this->employeeId)
                                        ->where(function($query) {
                                            $query->whereNull('a.role_menu')
                                                ->orWhere('a.role_menu', '4');
                                        })
                                        ->where(function($query) {
                                            $query->whereNull('a.role_submenu')
                                                ->orWhere('a.role_submenu', '3');
                                        })
                                        ->where('a.role_type', 'SELECTION')
                                        ->where('a.role_action', '1')
                                        ->where('a.is_active', '1');

            if($employeeId == 'ALL') {
                $checkSelectionRoles = $checkSelectionRoles->get();
            }
            else {
                $checkSelectionRoles = $checkSelectionRoles->where('b.ref_id', $employeeId)
                                                        ->first();
            }

            if(empty($checkSelectionRoles)) {
                return response()->json([
                    'status' => 200,
                    'message' => 'Forbidden',
                    'ongoing' => 0,
                    'data' => [],
                    'hasMorePages' => false
                ], 200);
            }
        }

        if($employeeId == 'ALL') {
            array_push($arrEmployeeId, $this->employeeId);
            foreach($checkSelectionRoles as $rowCheckSelectionRoles) {
                if(!in_array($rowCheckSelectionRoles->ref_id, $arrEmployeeId)) {
                    array_push($arrEmployeeId, $rowCheckSelectionRoles->ref_id);
                }
            }
        }

        $status = $request->input('status', 'FULLY_APPROVED');
        $startDateRange = $request->input('startDateRange', '');
        $endDateRange = $request->input('endDateRange', '');
        $applicant = $request->input('applicant', 'ALL');
        $companyId = $request->input('company', $this->companyId);
        $departmentId = $request->input('dept', 'ALL');
        $locationId = $request->input('location', 'ALL');
        $sortBy = $request->input('sortBy', 'LATEST');
        $perPage = 10;
        $currentPage = $request->input('page', 1);
        $search = $request->input('search', null);
        $countForm = null;
        if($currentPage == 1) {
            $countForm = $modelApproval->vwOngoingOrderForm()
                                    ->from('vw_ongoing_order_form as a')
                                    ->leftJoin('doc_approval_flow_sign as b', function($join) {
                                        $join->on('b.doc_approval_id', 'a.doc_approval_id')
                                            ->on('b.reference_id', 'a.latest_order_form_id');
                                    })
                                    ->select('a.*');

            if($employeeId == 'ALL') {
                $countForm = $countForm->whereIn('b.employee_id', $arrEmployeeId);
            }
            else {
                $countForm = $countForm->where('b.employee_id', $employeeId);
            }
                $countForm = $countForm->where('b.flow_as', 'RECEIVER')
                                        ->where('b.status', '3') // 3 = RECEIVED
                                        ->where('a.doc_status_id', '8')
                                        ->where('a.item_completed', '0')
                                        ->count();
        }

        $getForm = $modelApproval->vwOngoingOrderForm()
                                ->from('vw_ongoing_order_form as a')
                                ->leftJoin('doc_approval_flow_sign as b', function($getForm) {
                                    $getForm->on('b.doc_approval_id', 'a.doc_approval_id')
                                        ->on('b.reference_id', 'a.latest_order_form_id');
                                })
                                ->select('a.*')
                                ->where('b.flow_as', 'RECEIVER')
                                ->where('a.item_completed', '0');

        if($companyId != 'ALL') {
            $getForm = $getForm->where('a.company_id', $companyId);
        }

        if($employeeId == 'ALL') {
            $getForm = $getForm->whereIn('b.employee_id', $arrEmployeeId);
        }
        else {
            $getForm = $getForm->where('b.employee_id', $employeeId);
        }

        if($applicant != 'ALL') {
            $getForm = $getForm->where('a.employee_id', $applicant);
        }

        if($departmentId != 'ALL') {
            $getForm = $getForm->where('a.department_id', $departmentId);
        }

        if($locationId != 'ALL') {
            $getForm = $getForm->where('a.location_id', $locationId);
        }

        if($startDateRange != '' && $endDateRange != '') {
            if($status == 'FULLY_APPROVED') {
                $getForm = $getForm->whereBetween('a.final_decision_at', [$startDateRange, $endDateRange]);
            }
            else if($status == 'ONGOING') {
                $getForm = $getForm->whereBetween('a.submitted_at', [$startDateRange, $endDateRange]);
            }
        }

        if($status == 'FULLY_APPROVED') {
            $getForm = $getForm->where('b.status', '3') // 3 = RECEIVED
                            ->where('a.doc_status_id', '8');
        }
        else if($status == 'ONGOING') {
            $getForm = $getForm->where('b.status', '13') // 13 = PENDING
                            ->where('a.doc_status_id', '2');
        }

        if($sortBy == 'LATEST') {
            $getForm = $getForm->orderBy('a.final_decision_at', 'desc');
        }
        else {
            $getForm = $getForm->orderBy('a.final_decision_at', 'asc');
        }

        if ($search) {
            $getForm->where(function($query) use ($search) {
                $query->where('a.employee_name', 'like', '%'.$search.'%')
                    ->orWhere('a.doc_number', 'like', '%'.$search.'%')
                    ->orWhere('a.all_items', 'like', '%'.$search.'%')
                    ->orWhere('a.department_name', 'like', '%'.$search.'%');
            });
        }

        $getForm = $getForm->paginate($perPage, ['*'], 'page', $currentPage);

        foreach ($getForm as $rowForm) {
            $allItems = '';
            if($rowForm->all_items != null){
                $allItemsArray = explode('## ', $rowForm->all_items);
                if (count($allItemsArray) === 1) {
                    $allItems = '<li>'.$rowForm->all_items.'</li>';
                }
                else {
                    foreach ($allItemsArray as $index => $value) {
                        if($index + 1 == 6) {
                            $allItems .= '<li>... and others</li>';
                            break;
                        }
                        else {
                            $allItems .= '<li>'.($index + 1).'. '.$value.'</li>';
                        }
                    }
                }
            }

            if($rowForm->doc_status_id == '8') { // 8 = APPROVED
                $selectedVendor = '--';

                // $getOrderGroup = $modelApproval->documentApprovalOrderGroup()
                //                             ->from('doc_approval_order_group as a')
                //                             ->select('a.order_group_id', 'b.application_id')
                //                             ->where('a.doc_approval_id', $rowForm->doc_approval_id)
                //                             ->where('a.order_form_id', $rowForm->latest_order_form_id)
                //                             ->whereNull('a.canceled_id')
                //                             ->whereNotNull('a.comparison_id')
                //                             ->where('a.is_active', '1')
                //                             ->orderBy('a.order_detail_id', 'asc')
                //                             ->first();
                // if($getOrderItemCompare) {
                //     $comparisonId = $getOrderItemCompare->comparison_id;
                // }


                if($rowForm->all_group_id != '' || $rowForm->all_vendor != '') {
                    $allGroupIdArray = explode(',', $rowForm->all_group_id);
                    $allVendorArray = explode(',', $rowForm->all_vendor);

                    if (count($allGroupIdArray) > 1 || count($allVendorArray) > 1) {
                        $selectedVendor = 'MULTIPLE VENDOR';
                    }
                    else {
                        $getVendor = $modelProcurement->masterVendor()
                                                    ->from('master_vendor')
                                                    ->select('vendor_name')
                                                    ->where('vendor_id', Str::trim($allVendorArray[0]))
                                                    ->first();
                        $selectedVendor = $getVendor->vendor_name;
                    }
                }

                if($selectedVendor == '--') {
                    $token = ['cid' => $rowForm->company_id, 'id' => $rowForm->employee_id,'a' => $rowForm->doc_approval_id, 'b' => $rowForm->latest_order_form_id,'c' => null,'d'=>null];
                    $encodedToken = SafeToken::encode($token);

                    $dataType = 'VIEW';
                    $dataForm = 'ORDER_FORM';
                }
                else if($selectedVendor != 'MULTIPLE VENDOR') {
                    $comparisonId = null;
                    $getOrderItemCompare = $modelApproval->documentApprovalDetailOrderItem()
                                                ->from('doc_approval_detail_order_form_item as a')
                                                ->leftJoin('doc_approval_order_application as b', function($join) {
                                                    $join->on('b.comparison_id', 'a.comparison_id')
                                                        ->on('b.application_id', 'a.application_id')
                                                        ->where('b.is_active', '1');
                                                })
                                                ->select('a.comparison_id', 'b.application_id')
                                                ->where('a.doc_approval_id', $rowForm->doc_approval_id)
                                                ->where('a.order_form_id', $rowForm->latest_order_form_id)
                                                ->whereNull('a.canceled_id')
                                                ->whereNotNull('a.comparison_id')
                                                ->where('a.is_active', '1')
                                                ->orderBy('a.order_detail_id', 'asc')
                                                ->first();
                    if($getOrderItemCompare) {
                        $comparisonId = $getOrderItemCompare->comparison_id;
                    }

                    $token = ['cid' => $rowForm->company_id, 'id' => $rowForm->employee_id,'a' => $rowForm->doc_approval_id, 'b' => $rowForm->latest_order_form_id,'c' => $comparisonId,'d'=>null];
                    $encodedToken = SafeToken::encode($token);
                    // $dataType = ($comparisonId && !$getOrderItemCompare->application_id) ? 'VIEW' : 'NEW';
                    $dataType = ($comparisonId) ? 'VIEW' : 'NEW';
                    $dataForm = 'COMPARISON_FORM';
                }
                else {
                    $getOrderItemCompare = $modelApproval->documentApprovalDetailOrderItem()
                                                ->from('doc_approval_detail_order_form_item')
                                                ->select('comparison_id')
                                                ->where('doc_approval_id', $rowForm->doc_approval_id)
                                                ->where('order_form_id', $rowForm->latest_order_form_id)
                                                ->whereNull('canceled_id')
                                                ->whereNotNull('comparison_id')
                                                ->where('is_active', '1')
                                                ->orderBy('order_detail_id', 'asc')
                                                ->first();

                    $token = ['cid' => $rowForm->company_id, 'id' => $rowForm->employee_id,'a' => $rowForm->doc_approval_id, 'b' => $rowForm->latest_order_form_id,'c' => $getOrderItemCompare->comparison_id,'d'=>null];
                    $encodedToken = SafeToken::encode($token);
                    $dataType = 'VIEW_MULTIPLE';
                    $dataForm = 'ORDER_FORM';
                }

                $bottomCardRight = 'Received '.$this->timeAgo($rowForm->final_decision_at, false);
                $vendorRow = '<div class="data-row">
                                <span class="data-label">Selected Vendor</span>
                                <span class="data-separator">:</span>
                                <span class="data-value selectedVendorOrder">'.$selectedVendor.'</span>
                            </div>';
            }
            else if($rowForm->doc_status_id == '2') { // 2 = SUBMITTED (ONGOING)
                $dataType = 'VIEW_ONGOING';
                $dataForm = 'ORDER_FORM';
                $bottomCardRight = 'Submitted '.$this->timeAgo($rowForm->submitted_at, false);

                $token = ['cid' => $rowForm->company_id, 'id' => $rowForm->employee_id,'a' => $rowForm->doc_approval_id, 'b' => $rowForm->latest_order_form_id,'c' => null,'d'=>null];
                $encodedToken = SafeToken::encode($token);

                // GET LAST DECISION
                $getLastSigner = $modelApproval->docApprovalFlowSign()
                                            ->from('doc_approval_flow_sign as a')
                                            ->leftJoin('vw_master_employee_all as b', 'b.employee_id', 'a.employee_id')
                                            ->leftJoin('doc_approval_flow_comment as c', function($join) {
                                                $join->on('c.sign_flow_id', 'a.sign_flow_id')
                                                    ->on('c.employee_id', 'a.employee_id')
                                                    ->where('c.is_active', '1');
                                            })
                                            ->leftJoin('master_transaction_status AS d', 'd.transaction_status_id', 'a.status')
                                            ->select('a.decision_at', 'a.flow_as', 'b.employee_name', 'c.comment', 'd.transaction_status_name')
                                            ->where('a.doc_approval_id', $rowForm->doc_approval_id)
                                            ->where('a.reference_id', $rowForm->latest_order_form_id)
                                            ->where('a.is_active', '1')
                                            ->whereIn('a.flow_as', ['CHECKER_1','CHECKER_2','APPROVER'])
                                            ->whereIn('a.status', ['3', '8', '9', '14', '13']) // 8 = APPROVED, 9 = REJECTED, 14 = SEND BACK, 13 = PENDING
                                            ->orderBy('a.sign_flow_id', 'asc')
                                            ->orderBy('c.comment_id', 'desc')
                                            ->get();
                $singer = '';
                foreach($getLastSigner as $rowLastSigner) {
                    $decisionAt = $rowLastSigner->decision_at ? Carbon::parse($rowLastSigner->decision_at)->format('d-M-Y H:i:s') : '';
                    $singer .= '<li class="mb-2"><span class="fw-medium">'.Str::upper($rowLastSigner->employee_name).'</span>
                                    <br>Flow As : '.$rowLastSigner->flow_as.'
                                    <br>Decision : '.$rowLastSigner->transaction_status_name.'
                                    <br>Datetime : '.$decisionAt.'
                                    <br>Comment : '.$rowLastSigner->comment.'
                                </li>';
                }

                $vendorRow = '<div class="data-row">
                                <span class="data-label">Approval Flow</span>
                                <span class="data-separator">:</span>
                                <span class="data-value" style="line-height:1.3">'.$singer.'</span>
                            </div>';
            }

            $cardTitle = $cardBodyPt = '';
            if($rowForm->priority_name == 'URGENT' || $rowForm->priority_name == 'TOP URGENT') {
                $cardTitle = '<div class="card-header p-2-2 pb-0">
                                <div class="card-title m-0 fs-7"><div class="float-end"><span class="badge bg-danger fs-9 fw-semibold badge-priority">'.$rowForm->priority_name.'</span></div></div>
                            </div>';
                $cardBodyPt = ' pt-1';
            }

            $avatar = '<div class="d-flex align-items-center">
                            <div class="avatar avatar-xs d-flex align-items-center me-1">
                                <img class="avatar-img" src="'.asset('storage/avatars/'.$rowForm->avatar).'" alt="">
                            </div>
                            <span class="avatar-name-card fs-8 fw-medium">'.Str::upper($rowForm->employee_name).'</span>
                        </div>';
            $bottomCard = '<div class="d-flex justify-content-between align-items-center">
                                '.$avatar.'
                                <div class="text-end">
                                    <small class="text-muted text-nowrap">'.$bottomCardRight.'</small>
                                </div>
                            </div>';

            $response[] = [
                'html' => '<div class="card card-link-content mb-2-2" data-source="WEB" data-form="'.$dataForm.'" data-type="'.$dataType.'" data-token="'.$encodedToken.'">
                            '.$cardTitle.'
                            <div class="card-body p-2-2'.$cardBodyPt.'">
                                <div class="mb-2 data-details">
                                    <div class="data-row">
                                        <span class="data-label">Company</span>
                                        <span class="data-separator">:</span>
                                        <span class="data-value">'.$rowForm->company_name.'</span>
                                    </div>
                                    <div class="data-row">
                                        <span class="data-label">Order Form No.</span>
                                        <span class="data-separator">:</span>
                                        <span class="data-value">'.$rowForm->doc_number.'</span>
                                    </div>
                                    <div class="data-row">
                                        <span class="data-label">Department</span>
                                        <span class="data-separator">:</span>
                                        <span class="data-value">'.$rowForm->department_name.'</span>
                                    </div>
                                    <div class="data-row">
                                        <span class="data-label">Items</span>
                                        <span class="data-separator">:</span>
                                        <span class="data-value">
                                            <ul class="data-item-list itemListOrder">
                                                '.$allItems.'
                                            </ul>
                                        </span>
                                    </div>
                                    <div class="data-row">
                                        <span class="data-label">Grand Total Est.</span>
                                        <span class="data-separator">:</span>
                                        <span class="data-value">'.$this->formatNumber($rowForm->grand_total).' '.$rowForm->currency_code.'</span>
                                    </div>
                                    <div class="data-row">
                                        <span class="data-label">Purpose</span>
                                        <span class="data-separator">:</span>
                                        <span class="data-value">'.$rowForm->description.'</span>
                                    </div>
                                    '.$vendorRow.'
                                </div>
                                '.$bottomCard.'
                            </div>
                        </div>',
            ];
        }

        return response()->json([
            'status' => 200,
            'message' => 'Success',
            'ongoing' => $countForm,
            'data' => $response,
            'hasMorePages' => $getForm->hasMorePages()
        ], 200);
    }

    // public function orderFormRequest(Request $request) {
    //     $encodedToken = SafeToken::decode($request->receiver);
    //     $employeeId = $encodedToken['id'];

    //     $response = [];
    //     $modelApproval = new DocumentApprovalModel();
    //     $modelProcurement = new ProcurementPurchasingModel();
    //     $status = $request->input('status', 'FULLY_APPROVED');
    //     $startDateRange = $request->input('startDateRange', '');
    //     $endDateRange = $request->input('endDateRange', '');
    //     $applicant = $request->input('applicant', 'ALL');
    //     $companyId = $request->input('company', $this->companyId);
    //     $departmentId = $request->input('dept', 'ALL');
    //     $locationId = $request->input('location', 'ALL');
    //     $sortBy = $request->input('sortBy', 'LATEST');
    //     $perPage = 10;
    //     $currentPage = $request->input('page', 1);
    //     $search = $request->input('search', null);

    //     $countForm = null;
    //     if($currentPage == 1) {
    //         $countForm = $modelApproval->vwOngoingOrderForm()
    //                                 ->from('vw_ongoing_order_form as a')
    //                                 ->leftJoin('doc_approval_flow_sign as b', function($join) {
    //                                     $join->on('b.doc_approval_id', 'a.doc_approval_id')
    //                                         ->on('b.reference_id', 'a.latest_order_form_id');
    //                                 })
    //                                 ->select('a.*')
    //                                 ->where('b.employee_id', $employeeId)
    //                                 ->where('b.flow_as', 'RECEIVER')
    //                                 ->where('b.status', '3') // 3 = RECEIVED
    //                                 ->where('a.doc_status_id', '8')
    //                                 ->where('a.item_completed', '0')
    //                                 ->count();
    //     }

    //     $latestOrderForm = $modelProcurement->documentApprovalDetailOrder()
    //                                         ->from('doc_approval_detail_order_form as t')
    //                                         ->leftJoin('doc_approval_flow_sign as u', function($latestOrderForm) {
    //                                             $latestOrderForm->on('u.doc_approval_id', 't.doc_approval_id')
    //                                                             ->on('u.reference_id', 't.order_form_id');
    //                                         })
    //                                         ->select(
    //                                             't.doc_approval_id', 't.order_form_id', 't.order_partner_id', 't.employee_id',
    //                                             't.order_form_number', 't.order_form_status', 'b.grand_total', 'b.currency_code'
    //                                         )
    //                                         ->where('u.employee_id', '=', $employeeId)
    //                                         ->where('u.flow_as', 'RECEIVER')
    //                                         ->whereIn('t.order_form_status', ['2', '8'])
    //                                         ->where('t.form_status_id', '<>', '11')
    //                                         ->where('t.is_active', '=', '1');

    //     $getForm = $modelProcurement->documentApprovalHeader()
    //                             ->from('doc_approval_header as a')
    //                             ->leftJoinSub($latestOrderForm, 'b', function ($join) {
    //                                 $join->on('b.doc_approval_id', '=', 'a.doc_approval_id');
    //                             })
    //                             ->leftJoin('doc_approval_flow_sign as u', function($getForm) {
    //                                 $getForm->on('u.doc_approval_id', 'a.doc_approval_id')
    //                                         ->on('u.reference_id', 'b.order_form_id');
    //                             })
    //                             ->leftJoin('vw_master_employee_all as d', 'd.employee_id', '=', 'a.employee_id')
    //                             ->leftJoin(DB::raw("(
    //                                 SELECT order_form_id,
    //                                     GROUP_CONCAT(appliance_item ORDER BY application_item_id ASC SEPARATOR ', ') AS appliance_items
    //                                 FROM doc_approval_detail_order_form_item
    //                                 WHERE is_active = 1
    //                                 AND appliance_item <> 'VAT'
    //                                 GROUP BY order_form_id
    //                             ) as items"), 'items.order_form_id', '=', 'b.order_form_id')
    //                             ->select(
    //                                 'a.doc_approval_id', 'a.doc_name', 'a.company_id', 'a.company_name', 'a.employee_id',
    //                                 'd.employee_name', 'd.avatar', 'a.doc_number', 'a.doc_status_id', 'a.form_status_id', 'a.final_decision_at', 'a.department_id', 'a.department_name',
    //                                 'a.location_id', 'b.order_form_id', 'b.grand_total', 'b.currency_code',
    //                                 'b.order_form_status', 'b.application_header', 'b.application_reason',
    //                                 'b.priority', 'b.priority_name', 'b.application_submitted_date', 'b.ship_date',
    //                                 'b.vendor_id', 'b.vendor_name', 'b.vendor_address', 'b.application_delivery',
    //                                 'b.created_at', 'b.open_order_at', 'b.received_order_at', 'b.invoiced_order_at',
    //                                 'b.is_archived', 'items.appliance_items'
    //                             )
    //                             ->where('u.employee_id', $employeeId)
    //                             ->where('u.flow_as', 'RECEIVER')
    //                             ->where('a.doc_type_id', '1')
    //                             ->where('a.is_active', '1')
    //                             ->where('a.form_status_id', '<>', '11');

    //     // ///////////////////////////////////////////////
    //     $getForm = $modelApproval->vwOngoingOrderForm()
    //                             ->from('vw_ongoing_order_form as a')
    //                             ->leftJoin('doc_approval_flow_sign as b', function($getForm) {
    //                                 $getForm->on('b.doc_approval_id', 'a.doc_approval_id')
    //                                     ->on('b.reference_id', 'a.latest_order_form_id');
    //                             })
    //                             ->select('a.*')
    //                             ->where('b.employee_id', $employeeId)
    //                             ->where('b.flow_as', 'RECEIVER')
    //                             ->where('a.item_completed', '0');

    //     if($companyId != 'ALL') {
    //         $getForm = $getForm->where('a.company_id', $companyId);
    //     }

    //     if($applicant != 'ALL') {
    //         $getForm = $getForm->where('a.employee_id', $applicant);
    //     }

    //     if($departmentId != 'ALL') {
    //         $getForm = $getForm->where('a.department_id', $departmentId);
    //     }

    //     if($locationId != 'ALL') {
    //         $getForm = $getForm->where('a.location_id', $locationId);
    //     }

    //     if($startDateRange != '' && $endDateRange != '') {
    //         if($status == 'FULLY_APPROVED') {
    //             $getForm = $getForm->whereBetween('a.final_decision_at', [$startDateRange, $endDateRange]);
    //         }
    //         else if($status == 'ONGOING') {
    //             $getForm = $getForm->whereBetween('a.submitted_at', [$startDateRange, $endDateRange]);
    //         }
    //     }

    //     if($status == 'FULLY_APPROVED') {
    //         $getForm = $getForm->where('b.status', '3') // 3 = RECEIVED
    //                         ->where('a.doc_status_id', '8');
    //     }
    //     else if($status == 'ONGOING') {
    //         $getForm = $getForm->where('b.status', '13') // 13 = PENDING
    //                         ->where('a.doc_status_id', '2');
    //     }

    //     if($sortBy == 'LATEST') {
    //         $getForm = $getForm->orderBy('a.final_decision_at', 'desc');
    //     }
    //     else {
    //         $getForm = $getForm->orderBy('a.final_decision_at', 'asc');
    //     }

    //     if ($search) {
    //         $getForm->where(function($query) use ($search) {
    //             $query->where('a.employee_name', 'like', '%'.$search.'%')
    //                 ->orWhere('a.doc_number', 'like', '%'.$search.'%')
    //                 ->orWhere('a.all_items', 'like', '%'.$search.'%')
    //                 ->orWhere('a.department_name', 'like', '%'.$search.'%');
    //         });
    //     }

    //     $getForm = $getForm->paginate($perPage, ['*'], 'page', $currentPage);

    //     foreach ($getForm as $rowForm) {
    //         $allItems = '';
    //         if($rowForm->all_items != null){
    //             $allItemsArray = explode(', ', $rowForm->all_items);
    //             if (count($allItemsArray) === 1) {
    //                 $allItems = '<li>'.$rowForm->all_items.'</li>';
    //             }
    //             else {
    //                 foreach ($allItemsArray as $index => $value) {
    //                     $allItems .= '<li>'.($index + 1).'. '.$value.'</li>';
    //                 }
    //             }
    //         }

    //         if($rowForm->doc_status_id == '8') { // 8 = APPROVED
    //             $selectedVendor = '--';
    //             if($rowForm->all_vendor != '') {
    //                 $allVendorArray = explode(',', $rowForm->all_vendor);
    //                 if (count($allVendorArray) === 1) {
    //                     $getVendor = $modelProcurement->masterVendor()
    //                                                 ->from('master_vendor')
    //                                                 ->select('vendor_name')
    //                                                 ->where('vendor_id', Str::trim($allVendorArray[0]))
    //                                                 ->first();
    //                     $selectedVendor = $getVendor->vendor_name;
    //                 }
    //                 else {
    //                     $selectedVendor = 'MULTIPLE VENDOR';
    //                 }
    //             }

    //             if($selectedVendor == '--') {
    //                 $token = ['cid' => $rowForm->company_id, 'id' => $rowForm->employee_id,'a' => $rowForm->doc_approval_id, 'b' => $rowForm->latest_order_form_id,'c' => null,'d'=>null];
    //                 $encodedToken = SafeToken::encode($token);

    //                 $dataType = 'VIEW';
    //                 $dataForm = 'ORDER_FORM';
    //             }
    //             else if($selectedVendor != 'MULTIPLE VENDOR') {
    //                 $comparisonId = null;
    //                 $getOrderItemCompare = $modelApproval->documentApprovalDetailOrderItem()
    //                                             ->from('doc_approval_detail_order_form_item as a')
    //                                             ->leftJoin('doc_approval_order_application as b', function($join) {
    //                                                 $join->on('b.comparison_id', 'a.comparison_id')
    //                                                     ->on('b.application_id', 'a.application_id')
    //                                                     ->where('b.is_active', '1');
    //                                             })
    //                                             ->select('a.comparison_id', 'b.application_id')
    //                                             ->where('a.doc_approval_id', $rowForm->doc_approval_id)
    //                                             ->where('a.order_form_id', $rowForm->latest_order_form_id)
    //                                             ->whereNull('a.canceled_id')
    //                                             ->whereNotNull('a.comparison_id')
    //                                             ->where('a.is_active', '1')
    //                                             ->orderBy('a.order_detail_id', 'asc')
    //                                             ->first();
    //                 if($getOrderItemCompare) {
    //                     $comparisonId = $getOrderItemCompare->comparison_id;
    //                 }

    //                 $token = ['cid' => $rowForm->company_id, 'id' => $rowForm->employee_id,'a' => $rowForm->doc_approval_id, 'b' => $rowForm->latest_order_form_id,'c' => $comparisonId,'d'=>null];
    //                 $encodedToken = SafeToken::encode($token);
    //                 $dataType = ($comparisonId && !$getOrderItemCompare->application_id) ? 'VIEW' : 'NEW';
    //                 $dataForm = 'COMPARISON_FORM';
    //             }
    //             else {
    //                 $getOrderItemCompare = $modelApproval->documentApprovalDetailOrderItem()
    //                                             ->from('doc_approval_detail_order_form_item')
    //                                             ->select('comparison_id')
    //                                             ->where('doc_approval_id', $rowForm->doc_approval_id)
    //                                             ->where('order_form_id', $rowForm->latest_order_form_id)
    //                                             ->whereNull('canceled_id')
    //                                             ->whereNotNull('comparison_id')
    //                                             ->where('is_active', '1')
    //                                             ->orderBy('order_detail_id', 'asc')
    //                                             ->first();

    //                 $token = ['cid' => $rowForm->company_id, 'id' => $rowForm->employee_id,'a' => $rowForm->doc_approval_id, 'b' => $rowForm->latest_order_form_id,'c' => $getOrderItemCompare->comparison_id,'d'=>null];
    //                 $encodedToken = SafeToken::encode($token);
    //                 $dataType = 'VIEW_MULTIPLE';
    //                 $dataForm = 'ORDER_FORM';
    //             }

    //             $bottomCardRight = 'Received '.$this->timeAgo($rowForm->final_decision_at, false);
    //             $vendorRow = '<div class="data-row">
    //                             <span class="data-label">Selected Vendor</span>
    //                             <span class="data-separator">:</span>
    //                             <span class="data-value selectedVendorOrder">'.$selectedVendor.'</span>
    //                         </div>';
    //         }
    //         else if($rowForm->doc_status_id == '2') { // 2 = SUBMITTED (ONGOING)
    //             $dataType = 'VIEW_ONGOING';
    //             $dataForm = 'ORDER_FORM';
    //             $bottomCardRight = 'Submitted '.$this->timeAgo($rowForm->submitted_at, false);

    //             $token = ['cid' => $rowForm->company_id, 'id' => $rowForm->employee_id,'a' => $rowForm->doc_approval_id, 'b' => $rowForm->latest_order_form_id,'c' => null,'d'=>null];
    //             $encodedToken = SafeToken::encode($token);

    //             // GET LAST DECISION
    //             $getLastSigner = $modelApproval->docApprovalFlowSign()
    //                                         ->from('doc_approval_flow_sign as a')
    //                                         ->leftJoin('vw_master_employee_all as b', 'b.employee_id', 'a.employee_id')
    //                                         ->leftJoin('doc_approval_flow_comment as c', function($join) {
    //                                             $join->on('c.sign_flow_id', 'a.sign_flow_id')
    //                                                 ->on('c.employee_id', 'a.employee_id')
    //                                                 ->where('c.is_active', '1');
    //                                         })
    //                                         ->leftJoin('master_transaction_status AS d', 'd.transaction_status_id', 'a.status')
    //                                         ->select('a.decision_at', 'a.flow_as', 'b.employee_name', 'c.comment', 'd.transaction_status_name')
    //                                         ->where('a.doc_approval_id', $rowForm->doc_approval_id)
    //                                         ->where('a.reference_id', $rowForm->latest_order_form_id)
    //                                         ->where('a.is_active', '1')
    //                                         ->whereIn('a.flow_as', ['CHECKER_1','CHECKER_2','APPROVER'])
    //                                         ->whereIn('a.status', ['3', '8', '9', '14', '13']) // 8 = APPROVED, 9 = REJECTED, 14 = SEND BACK, 13 = PENDING
    //                                         ->orderBy('a.sign_flow_id', 'asc')
    //                                         ->orderBy('c.comment_id', 'desc')
    //                                         ->get();
    //             $singer = '';
    //             foreach($getLastSigner as $rowLastSigner) {
    //                 $decisionAt = $rowLastSigner->decision_at ? Carbon::parse($rowLastSigner->decision_at)->format('d-M-Y H:i:s') : '';
    //                 $singer .= '<li class="mb-2"><span class="fw-medium">'.Str::upper($rowLastSigner->employee_name).'</span>
    //                                 <br>Flow As : '.$rowLastSigner->flow_as.'
    //                                 <br>Decision : '.$rowLastSigner->transaction_status_name.'
    //                                 <br>Datetime : '.$decisionAt.'
    //                                 <br>Comment : '.$rowLastSigner->comment.'
    //                             </li>';
    //             }

    //             $vendorRow = '<div class="data-row">
    //                             <span class="data-label">Approval Flow</span>
    //                             <span class="data-separator">:</span>
    //                             <span class="data-value" style="line-height:1.3">'.$singer.'</span>
    //                         </div>';
    //         }

    //         $cardTitle = $cardBodyPt = '';
    //         if($rowForm->priority_name == 'URGENT' || $rowForm->priority_name == 'TOP URGENT') {
    //             $cardTitle = '<div class="card-header p-2-2 pb-0">
    //                             <div class="card-title m-0 fs-7"><div class="float-end"><span class="badge bg-danger fs-9 fw-semibold badge-priority">'.$rowForm->priority_name.'</span></div></div>
    //                         </div>';
    //             $cardBodyPt = ' pt-1';
    //         }

    //         $avatar = '<div class="d-flex align-items-center">
    //                         <div class="avatar avatar-xs d-flex align-items-center me-1">
    //                             <img class="avatar-img" src="'.asset('storage/avatars/'.$rowForm->avatar).'" alt="">
    //                         </div>
    //                         <span class="avatar-name-card fs-8 fw-medium">'.Str::upper($rowForm->employee_name).'</span>
    //                     </div>';
    //         $bottomCard = '<div class="d-flex justify-content-between align-items-center">
    //                             '.$avatar.'
    //                             <div class="text-end">
    //                                 <small class="text-muted text-nowrap">'.$bottomCardRight.'</small>
    //                             </div>
    //                         </div>';

    //         $response[] = [
    //             'html' => '<div class="card card-link-content mb-2-2" data-source="WEB" data-form="'.$dataForm.'" data-type="'.$dataType.'" data-token="'.$encodedToken.'">
    //                         '.$cardTitle.'
    //                         <div class="card-body p-2-2'.$cardBodyPt.'">
    //                             <div class="mb-2 data-details">
    //                                 <div class="data-row">
    //                                     <span class="data-label">Company</span>
    //                                     <span class="data-separator">:</span>
    //                                     <span class="data-value">'.$rowForm->company_name.'</span>
    //                                 </div>
    //                                 <div class="data-row">
    //                                     <span class="data-label">Order Form No.</span>
    //                                     <span class="data-separator">:</span>
    //                                     <span class="data-value">'.$rowForm->doc_number.'</span>
    //                                 </div>
    //                                 <div class="data-row">
    //                                     <span class="data-label">Department</span>
    //                                     <span class="data-separator">:</span>
    //                                     <span class="data-value">'.$rowForm->department_name.'</span>
    //                                 </div>
    //                                 <div class="data-row">
    //                                     <span class="data-label">Items</span>
    //                                     <span class="data-separator">:</span>
    //                                     <span class="data-value">
    //                                         <ul class="data-item-list itemListOrder">
    //                                             '.$allItems.'
    //                                         </ul>
    //                                     </span>
    //                                 </div>
    //                                 <div class="data-row">
    //                                     <span class="data-label">Grand Total Est.</span>
    //                                     <span class="data-separator">:</span>
    //                                     <span class="data-value">'.$this->formatNumber($rowForm->grand_total).' '.$rowForm->currency_code.'</span>
    //                                 </div>
    //                                 <div class="data-row">
    //                                     <span class="data-label">Purpose</span>
    //                                     <span class="data-separator">:</span>
    //                                     <span class="data-value">'.$rowForm->description.'</span>
    //                                 </div>
    //                                 '.$vendorRow.'
    //                             </div>
    //                             '.$bottomCard.'
    //                         </div>
    //                     </div>',
    //         ];
    //     }

    //     return response()->json([
    //         'status' => 200,
    //         'message' => 'Success',
    //         'ongoing' => $countForm,
    //         'data' => $response,
    //         'hasMorePages' => $getForm->hasMorePages()
    //     ], 200);
    // }

    public function applicationFormRequest(Request $request) {
        $employeeId = $request->receiver;
        $companyId = $request->input('company', $this->companyId);
        $response = [];

        $modelApproval = new DocumentApprovalModel();
        $modelProcurement = new ProcurementPurchasingModel();

        if($employeeId != $this->employeeId) {
            // CHECK USER SELECTION
            $checkSelectionRoles = $modelProcurement->masterRoleDetails()
                                        ->from('master_role_users as a')
                                        ->leftJoin('master_role_details as b', 'b.role_id', '=', 'a.role_id')
                                        ->where('a.employee_id', $this->employeeId)
                                        ->where(function($query) {
                                            $query->whereNull('a.role_menu')
                                                ->orWhere('a.role_menu', '4');
                                        })
                                        ->where(function($query) {
                                            $query->whereNull('a.role_submenu')
                                                ->orWhere('a.role_submenu', '3');
                                        })
                                        ->where('b.ref_id', $employeeId)
                                        ->where('a.role_type', 'SELECTION')
                                        ->where('a.role_action', '1')
                                        ->where('a.is_active', '1')
                                        ->first();
            if(!$checkSelectionRoles) {
                return response()->json([
                    'status' => 200,
                    'message' => 'Forbidden',
                    'ongoing' => 0,
                    'data' => [],
                    'hasMorePages' => false,
                ], 200);
            }
        }

        $priority = $request->input('priority', 'ALL');
        $startDateRange = $request->input('startDateRange', '');
        $endDateRange = $request->input('endDateRange', '');
        $sortBy = $request->input('sortBy', 'LATEST');
        $perPage = 10;
        $currentPage = $request->input('page', 1);
        $search = $request->input('search', null);

        $countForm = null;
        $latestApplications = $modelProcurement->docApprovalOrderApplication()
                                            ->from('doc_approval_order_application as t')
                                            ->leftJoin('doc_approval_order_group as u', 'u.application_id', '=', 't.application_id')
                                            ->leftJoin('doc_approval_flow_sign as v', function($join) {
                                                $join->on('v.doc_approval_id', '=', 't.doc_approval_id')
                                                    ->on('v.reference_id', '=', 't.application_id')
                                                    ->where('v.is_active', '=', '1');
                                            })
                                            ->select(
                                                't.created_by', 't.doc_approval_id', 't.order_form_id', 't.application_id',
                                                't.comparison_id', 'u.order_group_id', 't.application_title', 't.application_form_status',
                                                't.application_grand_total', 't.application_currency', 't.application_header',
                                                't.application_reason', 't.priority', 't.priority_name', 't.application_submitted_date',
                                                't.ship_date', 't.vendor_id', 't.vendor_name', 't.vendor_address', 't.application_delivery',
                                                't.open_order_at', 't.received_order_at', 't.invoiced_order_at', 't.created_at', 't.is_active', 't.is_archived', 'v.employee_id'
                                            )
                                            ->where('v.employee_id', $employeeId)
                                            ->where('v.flow_as', 'APPLICANT')
                                            ->where('t.application_form_status', '2')
                                            ->where('t.is_active', '1')
                                            ->where('t.is_archived', '0')
                                            ->where('u.is_need_revision', '0');

        $getForm = $modelProcurement->documentApprovalHeader()
                                ->from('doc_approval_header as a')
                                ->leftJoinSub($latestApplications, 'b', function ($join) {
                                    $join->on('b.doc_approval_id', '=', 'a.doc_approval_id');
                                })
                                ->leftJoin('doc_approval_flow_sign as e', function($join) {
                                    $join->on('e.doc_approval_id', '=', 'a.doc_approval_id')
                                        ->on('e.reference_id', '=', 'b.application_id')
                                        ->where('e.is_active', '=', '1');
                                })
                                ->leftJoin('vw_master_employee_all as d', 'd.employee_id', '=', 'a.employee_id')
                                ->leftJoin(DB::raw("(
                                    SELECT application_id,
                                        GROUP_CONCAT(appliance_item ORDER BY application_item_id ASC SEPARATOR '## ') AS appliance_items
                                    FROM doc_approval_order_application_item
                                    WHERE is_active = 1
                                    AND appliance_item NOT IN ('Delivery Fee', 'Discount', 'Total Price', 'VAT', 'PPh', 'Total Price + VAT - PPh')
                                    GROUP BY application_id
                                ) as items"), 'items.application_id', '=', 'b.application_id')
                                ->select(
                                    'a.doc_approval_id', 'b.application_id', 'b.comparison_id', 'b.order_group_id',
                                    'b.order_form_id', 'a.doc_name', 'a.company_id', 'a.company_name', 'a.employee_id',
                                    'd.employee_name', 'a.doc_number', 'a.doc_status_id', 'b.application_title',
                                    'a.form_status_id', 'a.final_decision_at', 'a.department_id', 'a.department_name',
                                    'a.location_id', 'b.application_grand_total', 'b.application_currency',
                                    'b.application_form_status', 'b.application_header', 'b.application_reason',
                                    'b.priority', 'b.priority_name', 'b.application_submitted_date', 'b.ship_date',
                                    'b.vendor_id', 'b.vendor_name', 'b.vendor_address', 'b.application_delivery',
                                    'b.created_at', 'b.open_order_at', 'b.received_order_at', 'b.invoiced_order_at',
                                    'b.is_archived', 'items.appliance_items'
                                )
                                // ->where('a.employee_id', $employeeId)
                                ->where('e.employee_id', $employeeId)
                                ->where('e.flow_as', 'APPLICANT')
                                ->where('a.doc_type_id', '2')
                                ->where('a.is_active', '1')
                                ->where('a.form_status_id', '2')
                                ->where('a.form_status_id', '<>', '11')
                                ->where('b.application_form_status', '2')
                                ->where('b.is_active', '1');

        if($currentPage == 1) {
            $countForm = $getForm->count();
        }

        if($companyId != 'ALL') {
            $getForm = $getForm->where('a.company_id', $companyId);
        }

        if($startDateRange != '' && $endDateRange != '') {
            $getForm = $getForm->whereBetween(DB::raw('DATE(b.created_at)'), [$startDateRange, $endDateRange]);
        }

        if($priority != 'ALL') {
            $getForm = $getForm->where('a.priority', $priority);
        }

        if($sortBy == 'LATEST') {
            $getForm = $getForm->orderBy('b.created_at', 'desc');
        }
        else if($sortBy == 'OLDEST') {
            $getForm = $getForm->orderBy('b.created_at', 'asc');
        }
        else if($sortBy == 'PRIORITY') {
            $getForm = $getForm->orderBy('a.priority', 'asc')
                                ->orderBy('b.created_at', 'asc');
        }

        if ($search) {
            $getForm = $getForm->where(function($query) use ($search) {
                            $query->where('d.employee_name', 'like', '%'.$search.'%')
                                ->orWhere('a.doc_number', 'like', '%'.$search.'%')
                                ->orWhere('items.appliance_items', 'like', '%'.$search.'%')
                                ->orWhere('a.department_name', 'like', '%'.$search.'%')
                                ->orWhere('b.vendor_name', 'like', '%'.$search.'%');
                        });
        }

        $getForm = $getForm->paginate($perPage, ['*'], 'page', $currentPage);

        foreach ($getForm as $rowForm) {
            $token = ['cid' => $rowForm->company_id, 'id' => $rowForm->employee_id,'a' => $rowForm->doc_approval_id, 'b' => $rowForm->application_id, 'c'=>$rowForm->comparison_id,'d'=>null,'g'=>$rowForm->order_group_id];
            $encodedToken = SafeToken::encode($token);
            $dataType = 'VIEW';
            $dataForm = 'APPLICATION_FORM';

            $allItems = '';
            if($rowForm->appliance_items != null){
                $allItemsArray = explode('## ', $rowForm->appliance_items);
                if (count($allItemsArray) === 1) {
                    $allItems = '<li>'.$rowForm->appliance_items.'</li>';
                }
                else {
                    foreach ($allItemsArray as $index => $value) {
                        if($index + 1 == 6) {
                            $allItems .= '<li>... and others</li>';
                            break;
                        }
                        else {
                            $allItems .= '<li>'.($index + 1).'. '.$value.'</li>';
                        }
                    }
                }
            }

            $badgeStatus = '';
            if($rowForm->application_form_status == '5') {
                $badgeStatus = '<div class="float-end"><span class="badge bg-warning fs-9 fw-semibold">NEED REVISE</span></div>';
            }

            $priority = '';
            if($rowForm->priority == '1' || $rowForm->priority == '2') {
                $arrPriority = ['1' => 'TOP URGENT', '2' => 'URGENT'];
                $priority = '<span class="badge bg-danger fs-9 fw-semibold badge-priority">'.$arrPriority[$rowForm->priority].'</span>';
            }

            $bottomCard = '<div class="d-flex justify-content-between align-items-center">
                                <div class="d-flex align-items-center">'.$priority.'</div>
                                <div class="text-end">
                                    <small class="text-muted text-nowrap text-end">Submitted '.$this->timeAgo($rowForm->created_at, false).'</small>
                                </div>
                            </div>';

            $response[] = [
                'html' => '<div class="card card-link-content mb-2-2" data-source="WEB" data-form="'.$dataForm.'" data-type="'.$dataType.'" data-token="'.$encodedToken.'">
                            <div class="card-header p-2-2 pb-0">
                                <div class="card-title m-0 fs-7">'.$rowForm->application_header.$badgeStatus.'</div>
                            </div>
                            <div class="card-body p-2-2 pt-1">
                                <div class="mb-2 data-details">
                                    <div class="data-row">
                                        <span class="data-label">Company</span>
                                        <span class="data-separator">:</span>
                                        <span class="data-value">'.$rowForm->company_name.'</span>
                                    </div>
                                    <div class="data-row">
                                        <span class="data-label">Title</span>
                                        <span class="data-separator">:</span>
                                        <span class="data-value lh-sm">'.$rowForm->application_title.'</span>
                                    </div>
                                    <div class="data-row">
                                        <span class="data-label">PO Number</span>
                                        <span class="data-separator">:</span>
                                        <span class="data-value">'.$rowForm->doc_number.'</span>
                                    </div>
                                    <div class="data-row">
                                        <span class="data-label">Vendor</span>
                                        <span class="data-separator">:</span>
                                        <span class="data-value">'.$rowForm->vendor_name.'</span>
                                    </div>
                                    <div class="data-row">
                                        <span class="data-label">Grand Total</span>
                                        <span class="data-separator">:</span>
                                        <span class="data-value">'.$this->formatNumber($rowForm->application_grand_total).' '.$rowForm->application_currency.'</span>
                                    </div>
                                    <div class="data-row">
                                        <span class="data-label">Items</span>
                                        <span class="data-separator">:</span>
                                        <span class="data-value lh-sm">'.$allItems.'</span>
                                    </div>
                                </div>
                                '.$bottomCard.'
                            </div>
                            </div>
                        </div>',
            ];
        }

        return response()->json([
            'status' => 200,
            'message' => 'Success',
            'ongoing' => $countForm,
            'data' => $response,
            'hasMorePages' => $getForm->hasMorePages()
        ], 200);
    }

    public function applicationCompleted(Request $request) {
        $employeeId = $request->receiver;
        $companyId = $request->input('company', $this->companyId);

        $response = [];
        $modelApproval = new DocumentApprovalModel();
        $modelProcurement = new ProcurementPurchasingModel();

        if($employeeId != $this->employeeId) {
            // CHECK USER SELECTION
            $checkSelectionRoles = $modelProcurement->masterRoleDetails()
                                        ->from('master_role_users as a')
                                        ->leftJoin('master_role_details as b', 'b.role_id', '=', 'a.role_id')
                                        ->where('a.employee_id', $this->employeeId)
                                        ->where(function($query) {
                                            $query->whereNull('a.role_menu')
                                                ->orWhere('a.role_menu', '4');
                                        })
                                        ->where(function($query) {
                                            $query->whereNull('a.role_submenu')
                                                ->orWhere('a.role_submenu', '3');
                                        })
                                        ->where('b.ref_id', $employeeId)
                                        ->where('a.role_type', 'SELECTION')
                                        ->where('a.role_action', '1')
                                        ->where('a.is_active', '1')
                                        ->first();
            if(!$checkSelectionRoles) {
                return response()->json([
                    'status' => 200,
                    'message' => 'Forbidden',
                    'ongoing' => 0,
                    'data' => [],
                    'hasMorePages' => false,
                ], 200);
            }
        }

        $status = $request->input('status', 'ALL');
        $priority = $request->input('priority', 'ALL');
        $startDateRange = $request->input('startDateRange', '');
        $endDateRange = $request->input('endDateRange', '');
        $sortBy = $request->input('sortBy', 'LATEST');
        $perPage = 10;
        $currentPage = $request->input('page', 1);
        $search = $request->input('search', null);
        $type = $request->input('type');

        // if ($request->status == 'ONGOING') {
        //     $status = '2'; // SUBMITTED
        // }
        // else if ($request->status == 'APPROVED') {
        //     $status = '8'; // APPROVED
        // }

        $countForm = null;
        $latestApplications = $modelProcurement->docApprovalOrderApplication()
                                            ->from('doc_approval_order_application as t')
                                            ->leftJoin('doc_approval_order_group as u', 'u.application_id', '=', 't.application_id')
                                            ->leftJoin('doc_approval_flow_sign as v', function($join) {
                                                $join->on('v.doc_approval_id', '=', 't.doc_approval_id')
                                                    ->on('v.reference_id', '=', 't.application_id')
                                                    ->where('v.is_active', '=', '1');
                                            });
        if($type == 'GOODS_RECEIVED') {
            $latestApplications = $latestApplications->leftJoin(DB::raw("(
                                                        SELECT application_id, inspection_form_status, incoming_date
                                                        FROM doc_approval_order_inspection
                                                        WHERE is_active = 1
                                                        ORDER BY incoming_date DESC
                                                        LIMIT 1
                                                    ) as v"), 'v.application_id', '=', 't.application_id')

                                            ->select(
                                                't.created_by', 't.doc_approval_id', 't.order_form_id', 't.application_id',
                                                't.comparison_id', 'u.order_group_id', 't.application_title', 't.application_form_status',
                                                't.application_grand_total', 't.application_currency', 't.application_header',
                                                't.application_reason', 't.priority', 't.priority_name', 't.application_submitted_date',
                                                't.ship_date', 't.vendor_id', 't.vendor_name', 't.vendor_address', 't.application_delivery',
                                                't.open_order_at', 't.received_order_at', 't.invoiced_order_at', 't.created_at', 't.is_archived', 't.goods_received', 'v.inspection_form_status', 'v.incoming_date')
                                                ->distinct();
        }
        else {
            $latestApplications = $latestApplications->select(
                                                't.created_by', 't.doc_approval_id', 't.order_form_id', 't.application_id',
                                                't.comparison_id', 'u.order_group_id', 't.application_title', 't.application_form_status',
                                                't.application_grand_total', 't.application_currency', 't.application_header',
                                                't.application_reason', 't.priority', 't.priority_name', 't.application_submitted_date',
                                                't.ship_date', 't.vendor_id', 't.vendor_name', 't.vendor_address', 't.application_delivery',
                                                't.open_order_at', 't.received_order_at', 't.invoiced_order_at', 't.created_at', 't.is_archived', 't.goods_received')
                                                ->distinct();
        }

        $latestApplications = $latestApplications->whereExists(function($query) {
                                                    $query->select(DB::raw(1))
                                                        ->from('doc_approval_order_application as t2')
                                                        ->whereColumn('t2.doc_approval_id', 't.doc_approval_id')
                                                        ->groupBy('t2.doc_approval_id')
                                                        ->havingRaw('MAX(t2.application_id) = t.application_id');
                                                })
                                            ->where('v.employee_id', $employeeId)
                                            ->where('v.flow_as', 'APPLICANT')
                                            ->where('t.is_active', '1')
                                            ->where('t.is_archived', '0')
                                            ->where('u.is_need_revision', '0');

        if($type == 'COMPLETED_APPROVAL') {
            $latestApplications = $latestApplications->whereIn('t.application_form_status', ['5','8','9','12']); // 2 = SUBMITTED, 9 = REJECTED
        }
        else if($type == 'OPEN_ORDER') {
            $latestApplications = $latestApplications->where('t.application_form_status', '24');  // 24 = OPEN ORDER
        }
        else if($type == 'GOODS_RECEIVED') {
            $latestApplications = $latestApplications->where(function($query) {
                                        $query->where('t.application_form_status', '21') // 21 = GOODS RECEIVED
                                            ->orWhere(function($query) {
                                                $query->whereIn('t.goods_received', ['PARTIAL RECEIVED','FULLY RECEIVED'])
                                                    ->where('v.inspection_form_status', '8');
                                        });
                                    });

        }

        $getForm = $modelProcurement->documentApprovalHeader()
                                ->from('doc_approval_header as a')
                                ->leftJoinSub($latestApplications, 'b', function ($join) {
                                    $join->on('b.doc_approval_id', '=', 'a.doc_approval_id');
                                })
                                ->leftJoin('doc_approval_flow_sign as e', function($join) use($employeeId) {
                                    $join->on('e.doc_approval_id', '=', 'a.doc_approval_id')
                                        ->on('e.reference_id', '=', 'b.application_id')
                                        ->where('e.employee_id', $employeeId)
                                        ->where('e.flow_as', 'APPLICANT')
                                        ->where('e.is_active', '1');
                                })
                                ->leftJoin('vw_master_employee_all as d', 'd.employee_id', '=', 'a.employee_id')
                                ->leftJoin(DB::raw("(
                                    SELECT application_id,
                                        GROUP_CONCAT(appliance_item ORDER BY application_item_id ASC SEPARATOR '## ') AS appliance_items
                                    FROM doc_approval_order_application_item
                                    WHERE is_active = 1
                                    AND appliance_item NOT IN ('Delivery Fee', 'Discount', 'Total Price', 'VAT', 'PPh', 'Total Price + VAT - PPh')
                                    GROUP BY application_id
                                ) as items"), 'items.application_id', '=', 'b.application_id')
                                ->whereExists(function($query) {
                                                    $query->select(DB::raw(1))
                                                        ->from('doc_approval_order_application as t2')
                                                        ->whereColumn('t2.doc_approval_id', 'b.doc_approval_id')
                                                        ->groupBy('t2.doc_approval_id')
                                                        ->havingRaw('MAX(t2.application_id) = b.application_id');
                                                })
                                ->where('e.employee_id', $employeeId)
                                ->where('e.flow_as', 'APPLICANT')
                                ->where('a.doc_type_id', '2')
                                ->where('a.is_active', '1');

        if($type == 'GOODS_RECEIVED') {
            $getForm = $getForm->select(
                                    'a.doc_approval_id', 'b.application_id', 'b.comparison_id', 'b.order_group_id',
                                    'b.order_form_id', 'a.doc_name', 'a.company_id', 'a.company_name', 'a.employee_id',
                                    'd.employee_name', 'a.doc_number', 'a.doc_status_id', 'b.application_title',
                                    'a.form_status_id', 'a.final_decision_at', 'a.department_id', 'a.department_name',
                                    'a.location_id', 'b.application_grand_total', 'b.application_currency',
                                    'b.application_form_status', 'b.application_header', 'b.application_reason',
                                    'b.priority', 'b.priority_name', 'b.application_submitted_date', 'b.ship_date',
                                    'b.vendor_id', 'b.vendor_name', 'b.vendor_address', 'b.application_delivery',
                                    'b.created_at', 'b.is_archived', 'items.appliance_items', 'b.goods_received', 'b.inspection_form_status', 'b.incoming_date')
                                ->distinct();
        }
        else {
            $getForm = $getForm->select(
                                    'a.doc_approval_id', 'b.application_id', 'b.comparison_id', 'b.order_group_id',
                                    'b.order_form_id', 'a.doc_name', 'a.company_id', 'a.company_name', 'a.employee_id',
                                    'd.employee_name', 'a.doc_number', 'a.doc_status_id', 'b.application_title',
                                    'a.form_status_id', 'a.final_decision_at', 'a.department_id', 'a.department_name',
                                    'a.location_id', 'b.application_grand_total', 'b.application_currency',
                                    'b.application_form_status', 'b.application_header', 'b.application_reason',
                                    'b.priority', 'b.priority_name', 'b.application_submitted_date', 'b.ship_date',
                                    'b.vendor_id', 'b.vendor_name', 'b.vendor_address', 'b.application_delivery',
                                    'b.created_at', 'b.open_order_at', 'b.is_archived', 'items.appliance_items', 'b.goods_received')
                                ->distinct();
        }

        if($currentPage == 1) {
            $countForm = $getForm;
            if($type == 'COMPLETED_APPROVAL') {
                // $countForm = $countForm->whereIn('form_status_id', ['2','9','12']); // 2 = SUBMITTED, 9 = REJECTED
                $countForm = $countForm->whereIn('b.application_form_status', ['5','8','9','12']); // 2 = SUBMITTED, 9 = REJECTED
            }
            else if($type == 'OPEN_ORDER') {
                $countForm = $countForm->where('b.application_form_status', '24')  // 24 = OPEN ORDER
                                    ->where(function($query) {
                                        $query->whereNull('b.goods_received')
                                            ->orWhere('b.goods_received', 'PARTIAL RECEIVED')
                                            ->orWhere('b.goods_received', 'FULLY RECEIVED');
                                    });
            }
            else if($type == 'GOODS_RECEIVED') {
                $countForm = $countForm->where(function($query) {
                                            $query->where('b.application_form_status', '21') // 21 = GOODS RECEIVED
                                                ->orWhere(function($query) {
                                                    $query->whereIn('b.goods_received', ['PARTIAL RECEIVED','FULLY RECEIVED'])
                                                        ->where('b.inspection_form_status', '8');
                                            });
                                        });

            }

            $countForm = $countForm->count();
        }

        if($type == 'COMPLETED_APPROVAL') {
            // $getForm = $getForm->whereIn('form_status_id', ['2','9','12']); // 2 = SUBMITTED, 9 = REJECTED
            // $getForm = $getForm->whereIn('b.application_form_status', ['5','8','9','12','5']); // 8 = APPROVED, 9 = REJECTED, 12 = CANCELED, 5 = NEED REVISE
            if($status == 'ALL') {
                $getForm = $getForm->where(function($query) {
                                $query->where(function($subQuery) {
                                    // Status 8 = APPROVED (form_status_id != 11)
                                    $subQuery->where('b.application_form_status', '8')
                                            ->where('a.form_status_id', '<>', '11');
                                })
                                ->orWhere(function($subQuery) {
                                    // Status 9 = REJECTED (not archived)
                                    $subQuery->where('b.application_form_status', '9')
                                            ->where('b.is_archived', '0');
                                })
                                ->orWhere(function($subQuery) {
                                    // Status 5 & 12 dengan form_status tertentu
                                    $subQuery->where(function($innerQuery) {
                                        $innerQuery->where('b.application_form_status', '5')
                                                ->where('a.form_status_id', '2');
                                    })
                                    ->orWhere(function($innerQuery) {
                                        $innerQuery->where('b.application_form_status', '12')
                                                ->where('a.form_status_id', '12');
                                    });
                                    // EXISTS condition untuk order form status
                                    $subQuery->whereExists(function($existsQuery) {
                                        $existsQuery->select(DB::raw(1))
                                                ->from('doc_approval_order_group as v')
                                                ->leftJoin('doc_approval_detail_order_form as w', 'v.order_form_id', '=', 'w.order_form_id')
                                                ->whereColumn('w.order_form_id', 'b.order_form_id')
                                                ->where(function($statusQuery) {
                                                    $statusQuery->where('w.order_form_status', 8)
                                                                ->orWhere(function($subStatusQuery) {
                                                                    $subStatusQuery->where('w.order_form_status', 5)
                                                                                    ->where('v.is_need_revision', '0')
                                                                                    ->where('w.is_active', 1);
                                                                });
                                                });
                                    });
                                });
                        });
            }
            else if($status == 'APPROVED') {
                $getForm = $getForm->where('b.application_form_status', '8')
                                ->where('a.form_status_id', '<>', '11');
            }
            else if($status == 'REJECTED') {
                $getForm = $getForm->where('b.application_form_status', '9')
                                ->where('b.is_archived', '0');
            }
            else {
                if($status == 'CANCELED') {
                    $getForm = $getForm->where('b.application_form_status', '12')
                                    ->where('a.form_status_id', '12');
                }
                else if($status == 'NEED_REVISE') {
                    $getForm = $getForm->where('b.application_form_status', '5')
                                    ->where('a.form_status_id', '2');
                }

                $getForm = $getForm->whereExists(function($existsQuery) {
                                $existsQuery->select(DB::raw(1))
                                        ->from('doc_approval_order_group as v')
                                        ->leftJoin('doc_approval_detail_order_form as w', 'v.order_form_id', '=', 'w.order_form_id')
                                        ->whereColumn('w.order_form_id', 'b.order_form_id')
                                        ->where(function($statusQuery) {
                                            $statusQuery->where('w.order_form_status', 8)
                                                        ->orWhere(function($subStatusQuery) {
                                                            $subStatusQuery->where('w.order_form_status', 5)
                                                                            ->where('v.is_need_revision', '0')
                                                                            ->where('w.is_active', 1);
                                                        });
                                        });
                            });
            }

            if($startDateRange != '' && $endDateRange != '') {
                $getForm = $getForm->whereBetween(DB::raw('DATE(b.created_at)'), [$startDateRange, $endDateRange]);
            }
        }
        else if($type == 'OPEN_ORDER') {
            $getForm = $getForm->where('b.application_form_status', '24')  // 24 = OPEN ORDER
                            ->where(function($query) {
                                $query->whereNull('b.goods_received')
                                    ->orWhere('b.goods_received', 'PARTIAL RECEIVED')
                                    ->orWhere('b.goods_received', 'FULLY RECEIVED');
                            });

            if($startDateRange != '' && $endDateRange != '') {
                $getForm = $getForm->whereBetween('b.open_order_at', [$startDateRange, $endDateRange]);
            }
        }
        else if($type == 'GOODS_RECEIVED') {
            $getForm = $getForm->where(function($query) {
                                    $query->where('b.application_form_status', '21') // 21 = GOODS RECEIVED
                                        ->orWhere(function($query) {
                                            $query->whereIn('b.goods_received', ['PARTIAL RECEIVED','FULLY RECEIVED'])
                                                ->where('b.inspection_form_status', '8');
                                    });
                                });

            if($startDateRange != '' && $endDateRange != '') {
                $getForm = $getForm->whereBetween('b.received_order_at', [$startDateRange, $endDateRange]);
            }
        }

        if($companyId != 'ALL') {
            $getForm = $getForm->where('a.company_id', $companyId);
        }

        if($priority != 'ALL') {
            $getForm = $getForm->where('b.priority', $priority);
        }

        if($type == 'COMPLETED_APPROVAL' && $sortBy == 'LATEST') {
            $getForm = $getForm->orderBy('b.created_at', 'desc');
        }
        else if($type == 'COMPLETED_APPROVAL' && $sortBy == 'OLDEST') {
            $getForm = $getForm->orderBy('b.created_at', 'asc');
        }
        else if($type == 'COMPLETED_APPROVAL' && $sortBy == 'PRIORITY') {
            $getForm = $getForm->orderBy('b.priority', 'asc')
                                ->orderBy('b.created_at', 'asc');
        }
        else if($type == 'OPEN_ORDER' && $sortBy == 'LATEST') {
            $getForm = $getForm->orderByRaw("ISNULL(b.open_order_at), b.open_order_at DESC")
                   ->orderBy('b.created_at', 'asc');
        }
        else if($type == 'OPEN_ORDER' && $sortBy == 'OLDEST') {
            $getForm = $getForm->orderByRaw("ISNULL(b.open_order_at), b.open_order_at ASC")
                   ->orderBy('b.created_at', 'asc');
        }
        else if($type == 'OPEN_ORDER' && $sortBy == 'PRIORITY') {
            $getForm = $getForm->orderBy('b.priority', 'asc')
                            ->orderByRaw("ISNULL(b.open_order_at), b.open_order_at ASC")
                            ->orderBy('b.created_at', 'asc');
        }
        else if($type == 'GOODS_RECEIVED' && $sortBy == 'LATEST') {
            $getForm = $getForm->orderByRaw("ISNULL(b.incoming_date), b.incoming_date DESC")
                   ->orderBy('b.created_at', 'asc');
        }
        else if($type == 'GOODS_RECEIVED' && $sortBy == 'OLDEST') {
            $getForm = $getForm->orderByRaw("ISNULL(b.incoming_date), b.incoming_date ASC")
                   ->orderBy('b.created_at', 'asc');
        }
        else if($type == 'GOODS_RECEIVED' && $sortBy == 'PRIORITY') {
            $getForm = $getForm->orderBy('b.priority', 'asc')
                            ->orderByRaw("ISNULL(b.incoming_date), b.incoming_date DESC")
                            ->orderBy('b.created_at', 'asc');
        }

        if ($search) {
            $getForm = $getForm->where(function($query) use ($search) {
                            $query->where('d.employee_name', 'like', '%'.$search.'%')
                                ->orWhere('a.doc_number', 'like', '%'.$search.'%')
                                ->orWhere('items.appliance_items', 'like', '%'.$search.'%')
                                ->orWhere('a.department_name', 'like', '%'.$search.'%')
                                ->orWhere('b.vendor_name', 'like', '%'.$search.'%');
                        });
        }

        $getForm = $getForm->paginate($perPage, ['*'], 'page', $currentPage);
        $arrDocApprovalId = [];
        foreach ($getForm as $rowForm) {
            $token = ['cid' => $rowForm->company_id, 'id' => $rowForm->employee_id,'a' => $rowForm->doc_approval_id, 'b' => $rowForm->application_id,'c' => $rowForm->comparison_id,'d'=>null, 'g'=>$rowForm->order_group_id];
            $encodedToken = SafeToken::encode($token);
            $dataType = 'VIEW';
            $dataForm = 'APPLICATION_FORM_COMPLETED';

            $badgeStatus = $date = $completedType = '';
            if($type == 'COMPLETED_APPROVAL') {
                if($rowForm->application_form_status == '8') { // 8 = APPROVED
                    $badgeStatus = '<div class="float-end"><span class="badge bg-success fs-9 fw-semibold">FULLY APPROVED</span></div>';
                    $completedType = 'APPROVED';
                }
                else if($rowForm->application_form_status == '9') { // 9 = REJECTED
                    $badgeStatus = '<div class="float-end"><span class="badge bg-danger fs-9 fw-semibold">FULLY REJECTED</span></div>';
                    $completedType = 'REJECTED';
                }
                else if($rowForm->application_form_status == '5') { // 5 = NEED REVISE
                    $badgeStatus = '<div class="float-end"><span class="badge bg-warning fs-9 fw-semibold">NEED REVISE</span></div>';
                    $completedType = 'NEED_REVISE';
                }
                else if($rowForm->application_form_status == '12') { // 12 = CANCELED
                    $badgeStatus = '<div class="float-end"><span class="badge bg-secondary fs-9 fw-semibold">CANCELED FORM</span></div>';
                    $completedType = 'CANCELED';
                }
                $date = 'Completed '.$this->timeAgo($rowForm->final_decision_at, false);
            }
            else if($type == 'OPEN_ORDER') {
                $badgeStatus = ($rowForm->goods_received == 'PARTIAL RECEIVED') ? '<div class="float-end"><span class="badge bg-info fs-9 fw-semibold">PARTIAL RECEIVED</span></div>' : '<div class="float-end"><span class="badge bg-info fs-9 fw-semibold">OPEN ORDER</span></div>';
                $date = 'Open Order '.$this->timeAgo($rowForm->open_order_at, false);
            }
            else if($type == 'GOODS_RECEIVED') {
                $badgeStatus = ($rowForm->application_form_status == '21' || $rowForm->goods_received == 'FULLY RECEIVED') ? '<div class="float-end"><span class="badge bg-success fs-9 fw-semibold">FULLY RECEIVED</span></div>' : '<div class="float-end"><span class="badge bg-info fs-9 fw-semibold">PARTIAL RECEIVED</span></div>';
                $date = (!empty($rowForm->incoming_date)) ? 'Latest Received Goods '.$this->timeAgo($rowForm->incoming_date, false) : '';
            }

            $priority = $bgClassName = '';
            if($rowForm->priority == '1' || $rowForm->priority == '2') {
                $arrPriority = ['1' => 'TOP URGENT', '2' => 'URGENT'];
                $priority = '<span class="badge bg-danger fs-9 fw-semibold badge-priority">'.$arrPriority[$rowForm->priority].'</span>';
            }
            $bottomCard = '<div class="d-flex justify-content-between align-items-center">
                                <div class="d-flex align-items-center">'.$priority.'</div>
                                <div class="text-end">
                                    <small class="text-muted text-nowrap text-end">'.$date.'</small>
                                </div>
                            </div>';

            $allItems = '';
            if($rowForm->appliance_items != null){
                $allItemsArray = explode('## ', $rowForm->appliance_items);
                if (count($allItemsArray) === 1) {
                    $allItems = '<li>'.$rowForm->appliance_items.'</li>';
                }
                else {
                    foreach ($allItemsArray as $index => $value) {
                        if($index + 1 == 6) {
                            $allItems .= '<li>... and others</li>';
                            break;
                        }
                        else {
                            $allItems .= '<li>'.($index + 1).'. '.$value.'</li>';
                        }
                    }
                }
            }

            if($type == 'COMPLETED_APPROVAL') {
                $response[] = [
                    'html' => '<div class="card card-link-content mb-2-2" data-source="WEB" data-form="'.$dataForm.'" data-type="'.$dataType.'" data-ccy="'.$rowForm->application_currency.'" data-no="'.$rowForm->doc_number.'" data-token="'.$encodedToken.'" data-completed="'.$completedType.'">
                                <div class="card-header p-2-2 pb-0">
                                    <div class="card-title m-0 fs-7">'.$rowForm->application_header.$badgeStatus.'</div>
                                </div>
                                <div class="card-body p-2-2 pt-1">
                                    <div class="mb-2 data-details">
                                        <div class="data-row">
                                            <span class="data-label">Company</span>
                                            <span class="data-separator">:</span>
                                            <span class="data-value">'.$rowForm->company_name.'</span>
                                        </div>
                                        <div class="data-row">
                                            <span class="data-label">PO Number</span>
                                            <span class="data-separator">:</span>
                                            <span class="data-value card_po_number">'.$rowForm->doc_number.'</span>
                                        </div>
                                        <div class="data-row">
                                            <span class="data-label">Title</span>
                                            <span class="data-separator">:</span>
                                            <span class="data-value lh-sm">'.$rowForm->application_title.'</span>
                                        </div>
                                        <div class="data-row">
                                            <span class="data-label">Vendor</span>
                                            <span class="data-separator">:</span>
                                            <span class="data-value card_po_number">'.$rowForm->vendor_name.'</span>
                                        </div>
                                        <div class="data-row">
                                            <span class="data-label">Grand Total</span>
                                            <span class="data-separator">:</span>
                                            <span class="data-value">'.$this->formatNumber($rowForm->application_grand_total).' '.$rowForm->application_currency.'</span>
                                        </div>
                                        <div class="data-row">
                                            <span class="data-label">Items</span>
                                            <span class="data-separator">:</span>
                                            <span class="data-value lh-sm">'.$allItems.'</span>
                                        </div>
                                    </div>
                                    '.$bottomCard.'
                                </div>
                            </div>',
                ];
            }
            else {
                $arrDocApprovalId[] = $rowForm->doc_approval_id;
                $response[] = [
                    'html' => '<div class="card card-link-content mb-2-2" data-source="WEB" data-form="'.$dataForm.'" data-type="'.$dataType.'" data-ccy="'.$rowForm->application_currency.'" data-token="'.$encodedToken.'">
                                <div class="card-header p-2-2 pb-0">
                                    <div class="card-title m-0 fs-7">'.$rowForm->application_header.$badgeStatus.'</div>
                                </div>
                                <div class="card-body p-2-2 pt-1">
                                    <div class="mb-1 data-details">
                                        <div class="data-row">
                                            <span class="data-label">Company</span>
                                            <span class="data-separator">:</span>
                                            <span class="data-value">'.$rowForm->company_name.'</span>
                                        </div>
                                        <div class="data-row">
                                            <span class="data-label">PO Number</span>
                                            <span class="data-separator">:</span>
                                            <span class="data-value card_po_number">'.$rowForm->doc_number.'</span>
                                        </div>
                                        <div class="data-row">
                                            <span class="data-label">Title</span>
                                            <span class="data-separator">:</span>
                                            <span class="data-value lh-sm">'.$rowForm->application_title.'</span>
                                        </div>
                                        <div class="data-row">
                                            <span class="data-label">Vendor</span>
                                            <span class="data-separator">:</span>
                                            <span class="data-value card_po_number">'.$rowForm->vendor_name.'</span>
                                        </div>
                                        <div class="data-row">
                                            <span class="data-label">Grand Total</span>
                                            <span class="data-separator">:</span>
                                            <span class="data-value">'.$this->formatNumber($rowForm->application_grand_total).' '.$rowForm->application_currency.'</span>
                                        </div>
                                        <div class="data-row">
                                            <span class="data-label">Items</span>
                                            <span class="data-separator">:</span>
                                            <span class="data-value lh-sm">'.$allItems.'</span>
                                        </div>
                                    </div>
                                    '.$bottomCard.'
                                </div>
                            </div>',
                ];
            }
        }

        return response()->json([
            'status' => 200,
            'message' => 'Success',
            'ongoing' => $countForm,
            'data' => $response,
            'docApprovalId' => $arrDocApprovalId,
            'hasMorePages' => $getForm->hasMorePages() // Indicate if there are more pages to load
        ], 200);
    }

    public function getComparison(Request $request) {
        $type = $request->type;
        $form = $request->form;
        $encodedToken = $request->token;
        $token = SafeToken::decode($encodedToken);
        $validator = Validator::make($token, [
            'id' => 'required',
            'a' => 'required',
            'b' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => 'Invalid token form', 'errors' => $validator->errors()], 422)
                            ->setStatusCode(422, 'Invalid token form');
        }

        $employeeId = $token['id'];
        $docApprovalId = $token['a'];
        $referenceId = $token['b'];
        $companyId = (array_key_exists('cid', $token)) ? $token['cid'] : '1';

        $response = collect();
        $modelApproval = new DocumentApprovalModel();
        $modelProcurement = new ProcurementPurchasingModel();
        $getHeader = $modelApproval->documentApprovalHeader()
                                ->from('doc_approval_header AS a')
                                ->leftJoin('master_transaction_status AS c', 'c.transaction_status_id', 'a.doc_status_id')
                                ->select('a.doc_name', 'a.doc_number', 'c.transaction_status_name')
                                ->where('a.doc_approval_id', $docApprovalId)
                                ->where('a.company_id', $companyId)
                                ->where('a.is_active', '1')
                                ->first();

        if($getHeader) {
            $processedStatus = false;
            $multiVendor = false;
            $arrOrderGroupId = [];
            $arrComparisonId = [];
            $arrApplicationId = [];

            $arrItemProcessed = [];
            $arrItemUnprocessed = [];

            $listItem = $title = $nextForm = $tokenForm = '';
            $applicationForm = false;
            $getOrderItem = $modelApproval->documentApprovalDetailOrderItem()
                                        ->from('doc_approval_detail_order_form_item as a')
                                        ->leftJoin('doc_approval_detail_order_form as c', 'c.order_form_id', '=', 'a.order_form_id')
                                        ->leftJoin('master_vendor as b', 'b.vendor_id', '=', 'a.vendor_id')
                                        ->leftJoin('doc_approval_order_group as d', function($join) {
                                            $join->on('d.order_group_id', 'a.order_group_id')
                                                ->where('d.is_active', '1');
                                                // ->orWhere('d.comparison_id', 'd.comparison_id');
                                        })
                                        ->select('a.order_form_id', 'a.appliance_item', 'd.order_group_id', 'd.comparison_id', 'd.application_id', 'a.vendor_id', 'b.vendor_name', 'c.doc_version', 'd.is_need_revision')
                                        ->where('a.doc_approval_id', $docApprovalId)
                                        ->where('a.company_id', $companyId)
                                        ->where('a.order_form_id', $referenceId)
                                        ->where('a.unit_name', '<>', 'VAT')
                                        ->whereNull('a.canceled_id')
                                        ->where('a.is_active', '1')
                                        ->orderBy('a.order_group_id', 'asc')
                                        ->orderBy('a.comparison_id', 'desc')
                                        ->orderBy('a.application_id', 'desc')
                                        ->orderBy('a.order_detail_id', 'asc')
                                        ->get();

            // $getOrderItem = $modelApproval->documentApprovalDetailOrderItem()
            //                             ->from('doc_approval_detail_order_form_item as a')
            //                             ->leftJoin(DB::raw('(SELECT application_id, vendor_id, order_form_id FROM doc_approval_order_application
            //                                                 WHERE order_form_id = "' . $referenceId . '"
            //                                                 ORDER BY application_id DESC
            //                                                 LIMIT 1) as c'), function($join) {
            //                                 $join->on('c.order_form_id', '=', 'a.order_form_id');
            //                             })
            //                             ->leftJoin('master_vendor as b', 'b.vendor_id', '=', 'c.vendor_id')
            //                             ->select('a.appliance_item', 'a.comparison_id', 'c.application_id', 'c.vendor_id', 'b.vendor_name')
            //                             ->where('a.doc_approval_id', $docApprovalId)
            //                             ->where('a.order_form_id', $referenceId)
            //                             ->where('a.unit_name', '<>', 'VAT')
            //                             ->whereNull('a.canceled_id')
            //                             ->where('a.is_active', '1')
            //                             ->orderBy('a.order_detail_id', 'asc')
            //                             ->get();

            foreach($getOrderItem as $rowOrderItem) {
                if($rowOrderItem->order_group_id && !in_array($rowOrderItem->order_group_id, $arrOrderGroupId)) {
                    $arrOrderGroupId[] = $rowOrderItem->order_group_id;
                    // $arrComparisonId[] = $rowOrderItem->comparison_id;
                    // $arrApplicationId[] = $rowOrderItem->application_id;

                    $arrItemProcessed[] = [
                                            'comparisonId' => $rowOrderItem->comparison_id,
                                            'applicationId' => $rowOrderItem->application_id,
                                            'orderFormId' => $rowOrderItem->order_form_id,
                                            'vendorId' => $rowOrderItem->vendor_id,
                                            'vendorName' => $rowOrderItem->vendor_name,
                                            'orderGroupId' => $rowOrderItem->order_group_id,
                                            'docVersion' => ' for Ver. '.$rowOrderItem->doc_version,
                                            'isNeedRevision' => $rowOrderItem->is_need_revision,
                                        ];

                    $processedStatus = true;
                }
                else if(!$rowOrderItem->order_group_id) {
                    $arrItemUnprocessed[] = [
                                                'item' => $rowOrderItem->appliance_item,
                                                'docVersion' => ' for Ver. '.$rowOrderItem->doc_version,
                                            ];
                }
            }

            // dd($arrItemProcessed);
            if($processedStatus === true) {
                if(count($arrItemProcessed) > 0 || count($arrItemUnprocessed) > 0 || $form == 'ORDER_FORM_LIST') {
                    $title = Str::title($getHeader->doc_name).' : '.$getHeader->doc_number;
                    $multiVendor = true;
                    // HERE TO CHECK
                    // if(count($arrItemProcessed) > 1 || (count($arrItemProcessed) > 0 && count($arrItemUnprocessed) > 0)) {
                    //     $multiVendor = true;
                    // }

                    $itemClass = $type == 'PROGRESS' ? ' progressList' : ' multiplePoList';
                    if(count($arrItemUnprocessed) > 0) {
                        if(($type == 'MY_REQUEST' && $form == 'ORDER_FORM') || $type == 'PROGRESS') {
                            $cancelItem = '';
                        }
                        else {
                            $cancelItem = '<div class="float-end">
                                                <button class="btn btn-sm btn-outline-danger cardHoverBtn" data-type="CANCEL_ITEM"><i class="fa-regular fa-trash-can-list"></i> Cancel Item</button>
                                            </div>';
                        }

                        $dataType = ($type == 'PROGRESS') ? 'PROGRESS' : '';
                        $listItem .= '<div class="card card-hover mb-2-2'.$itemClass.'" data-source="WEB" data-form="ORDER_FORM" data-type="'.$dataType.'" data-selectedversion="'.$arrItemUnprocessed[0]['docVersion'].'" data-token="'.$encodedToken.'">
                                        <div class="card-header p-2-2 pb-0">
                                            <div class="card-title m-0 fs-7 d-inline align-middle">
                                                Unprocessed Item
                                                '. $cancelItem.'
                                            </div>
                                        </div>
                                        <div class="card-body p-2-2">
                                            <div class="mb-2 data-details">
                                                <div class="data-row">
                                                    <span class="data-label">Items</span>
                                                    <span class="data-separator">:</span>
                                                    <span class="data-value">
                                                        <ul class="data-item-list">';
                                                        for($x = 0; $x < count($arrItemUnprocessed); $x++) {
                                                            $no = count($arrItemUnprocessed) > 1 ? ($x + 1).'. ' : '';
                                                            $listItem .= '<li>'.$no.$arrItemUnprocessed[$x]['item'].'</li>';
                                                        }
                                            $listItem .= '</ul>
                                                    </span>
                                                </div>
                                                <div class="data-row">
                                                    <span class="data-label">Selected Vendor</span>
                                                    <span class="data-separator">:</span>
                                                    <span class="data-value">--</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>';
                    }

                    for($x = 0; $x < count($arrItemProcessed); $x++) {
                        // $getOrderGroup = $modelProcurement->documentApprovalOrderGroup()
                        //                                     ->from('doc_approval_order_group as a')
                        //                                     ->select('a.comparison_id', 'a.application_id')
                        //                                     ->where('a.order_form_id', $arrItemProcessed[$x]['orderFormId'])
                        //                                     ->where('a.order_group_id', $arrItemProcessed[$x]['orderGroupId'])
                        //                                     ->where('a.is_active', '1')
                        //                                     ->orderBy('a.order_group_id', 'desc')
                        //                                     ->first();
                        // if($getOrderGroup) {
                        //     $itemProcessed = [];
                        //     if($getOrderGroup->application_id) {
                        //     }
                        //     if($getOrderGroup->comparison_id) {

                        //     }
                        // }

                        if(!$arrItemProcessed[$x]['applicationId']) {
                            $getProcessedItem = $modelProcurement->docApprovalOrderComparisonItem()
                                                                ->from('doc_approval_order_comparison_item')
                                                                ->select('comparison_item_id', 'appliance_item')
                                                                ->where('order_form_id', $arrItemProcessed[$x]['orderFormId'])
                                                                ->where('company_id', $companyId)
                                                                ->where('comparison_id', $arrItemProcessed[$x]['comparisonId'])
                                                                // ->whereNotNull('order_detail_id')
                                                                ->whereNotIn('appliance_item', ['Delivery Fee', 'Discount', 'Total Price', 'VAT', 'PPh', 'Total Price + VAT - PPh'])
                                                                ->where('is_active', '1')
                                                                ->orderBy('comparison_item_id', 'asc')
                                                                ->get();
                        }
                        else {
                            $getProcessedItem = $modelProcurement->docApprovalOrderApplication()
                                                        ->from('doc_approval_order_application_item')
                                                        ->select('application_item_id', 'appliance_item')
                                                        ->where('order_form_id', $arrItemProcessed[$x]['orderFormId'])
                                                        ->where('company_id', $companyId)
                                                        ->where('application_id', $arrItemProcessed[$x]['applicationId'])
                                                        // ->whereNotNull('order_detail_id')
                                                        ->whereNotIn('appliance_item', ['Delivery Fee', 'Discount', 'Total Price', 'VAT', 'PPh', 'Total Price + VAT - PPh'])
                                                        ->where('is_active', '1')
                                                        ->orderBy('application_item_id', 'asc')
                                                        ->get();
                        }

                        $itemProcessed = [];
                        foreach($getProcessedItem as $rowProcessedItem) {
                            $itemProcessed[] = $rowProcessedItem->appliance_item;
                        }

                        $timelineOrder = $timelineComparison =  $timelineApplication = '';
                        if($arrItemProcessed[$x]['comparisonId']) {
                            $timelineComparison = '<div class="timeline-item">
                                                        <div class="timeline-badge bg-success"><i class="fa-solid fa-check"></i></div>
                                                        <span class="d-block lh-md">COMPARISON FORM</span>
                                                        <span class="badge bg-success mt-1 fs-9 fw-semibold">COMPARISON AVAILABLE</span>
                                                    </div>';
                        }
                        else if(!$arrItemProcessed[$x]['comparisonId'] && $arrItemProcessed[$x]['applicationId']) {
                            $timelineComparison = '<div class="timeline-item">
                                                        <div class="timeline-badge bg-light-dark"></div>
                                                        <span class="d-block lh-md">COMPARISON FORM</span>
                                                        <span class="badge bg-default mt-1 fs-9 fw-semibold">NOT AVAILABLE</span>
                                                    </div>';
                        }

                        $timelineOrder = '<div class="timeline-item">
                                            <div class="timeline-badge bg-success"><i class="fa-solid fa-check"></i></div>
                                            <span class="d-block lh-md">ORDER FORM</span>
                                            <span class="badge bg-success mt-1 fs-9 fw-semibold">FULLY APPROVED</span>
                                        </div>';

                        if($arrItemProcessed[$x]['applicationId']) {
                            $getApplication = $modelProcurement->docApprovalOrderApplication()
                                                                    ->from('doc_approval_order_application as a')
                                                                    ->leftJoin('doc_approval_order_group as b', function($join) use($referenceId) {
                                                                        $join->on('b.application_id', 'a.application_id')
                                                                            ->where('b.order_form_id', $referenceId)
                                                                            ->where('b.is_active', '1')
                                                                            ->orderBy('b.order_group_id', 'desc');
                                                                    })
                                                                    ->select('a.application_id', 'a.doc_approval_id', 'a.order_form_id', 'a.comparison_id', 'a.application_form_status', 'b.order_group_id', 'b.is_need_revision', 'a.application_number', 'a.vendor_name')
                                                                    ->where('a.order_form_id', $referenceId)
                                                                    ->where('a.application_id', $arrItemProcessed[$x]['applicationId'])
                                                                    ->where('a.is_active', '1')
                                                                    ->orderBy('a.application_id', 'desc')
                                                                    ->first();
                            if($getApplication) {
                                $dataType = ($type == 'PROGRESS') ? 'PROGRESS' : 'VIEW';
                                if($type == 'MY_REQUEST') {
                                    $dataForm = 'ORDER_FORM';
                                    $token = ['cid'=>$companyId, 'id' => $employeeId,'a' => $docApprovalId,'b' => $referenceId, 'c' => $getApplication->comparison_id, 'd' => null, 'g' => $getApplication->order_group_id];
                                    $encodedToken = SafeToken::encode($token);
                                    $tokenForm = $encodedToken;
                                }
                                else {
                                    $dataForm = 'APPLICATION_FORM';
                                    $token = ['cid'=>$companyId, 'id' => $employeeId,'a' => $getApplication->doc_approval_id,'b' => $getApplication->application_id, 'c' => $getApplication->comparison_id, 'd' => null, 'g' => $getApplication->order_group_id];
                                    $encodedToken = SafeToken::encode($token);
                                    $tokenForm = $encodedToken;
                                }

                                if($getApplication->application_form_status == '2') { // 2 = SUBMITTED
                                    $timelineApplication = '<div class="timeline-item">
                                                                <div class="timeline-badge pulse-animation bg-info"></div>
                                                                <span class="d-block lh-md">APPLICATION & PO FORM</span>
                                                                <span class="d-block lh-md">PO No. : '.$getApplication->application_number.'</span>
                                                                <span class="d-block lh-md">Vendor : '.$getApplication->vendor_name.'</span>
                                                                <span class="badge pulse-animation bg-info mt-1 fs-9 fw-semibold">APPROVAL IN PROCESS</span>
                                                            </div>
                                                            <div class="timeline-item">
                                                                <div class="timeline-badge bg-light-dark"></div>
                                                                <span class="badge bg-light-dark fs-9 fw-semibold">OPEN ORDER</span>
                                                            </div>
                                                            <div class="timeline-item">
                                                                <div class="timeline-badge bg-light-dark"></div>
                                                                <span class="badge bg-light-dark fs-9 fw-semibold">RECEIVED GOODS</span>
                                                            </div>
                                                            <div class="timeline-item">
                                                                <div class="timeline-badge bg-light-dark"></div>
                                                                <span class="badge bg-light-dark fs-9 fw-semibold">INVOICED</span>
                                                            </div>';
                                }
                                else if($getApplication->application_form_status == '8') { // 8 = APPROVED
                                    $timelineApplication = '<div class="timeline-item">
                                                                <div class="timeline-badge pulse-animation bg-success"><i class="fa-solid fa-check"></i></div>
                                                                <span class="d-block lh-md">APPLICATION & PO FORM</span>
                                                                <span class="d-block lh-md">PO No. : '.$getApplication->application_number.'</span>
                                                                <span class="d-block lh-md">Vendor : '.$getApplication->vendor_name.'</span>
                                                                <span class="badge pulse-animation bg-success mt-1 fs-9 fw-semibold">FULLY APPROVED</span>
                                                            </div>
                                                            <div class="timeline-item">
                                                                <div class="timeline-badge bg-light-dark"></div>
                                                                <span class="badge bg-light-dark fs-9 fw-semibold">OPEN ORDER</span>
                                                            </div>
                                                            <div class="timeline-item">
                                                                <div class="timeline-badge bg-light-dark"></div>
                                                                <span class="badge bg-light-dark fs-9 fw-semibold">RECEIVED GOODS</span>
                                                            </div>
                                                            <div class="timeline-item">
                                                                <div class="timeline-badge bg-light-dark"></div>
                                                                <span class="badge bg-light-dark fs-9 fw-semibold">INVOICED</span>
                                                            </div>';
                                }
                                else if($getApplication->application_form_status == '5') { // 5 = NEED REVISE
                                    if($getApplication->is_need_revision == '1') {
                                        $timelineOrder = '<div class="timeline-item">
                                                            <div class="timeline-badge bg-warning"><i class="fa-solid fa-check"></i></div>
                                                            <span class="d-block lh-md">ORDER FORM</span>
                                                            <span class="badge bg-warning mt-1 fs-9 fw-semibold">NEED REVISE</span>
                                                        </div>';
                                    }

                                    $timelineApplication = '<div class="timeline-item">
                                                                <div class="timeline-badge pulse-animation bg-warning"><i class="fa-solid fa-check"></i></div>
                                                                <span class="d-block lh-md">APPLICATION & PO FORM</span>
                                                                <span class="d-block lh-md">PO No. : '.$getApplication->application_number.'</span>
                                                                <span class="badge pulse-animation bg-warning mt-1 fs-9 fw-semibold">NEED REVISE</span>
                                                            </div>';
                                }
                                else if($getApplication->application_form_status == '12') { // 12 = CANCELED
                                    $timelineApplication = '<div class="timeline-item">
                                                                <div class="timeline-badge pulse-animation bg-warning"><i class="fa-solid fa-times"></i></div>
                                                                <span class="d-block lh-md">APPLICATION & PO FORM</span>
                                                                <span class="d-block lh-md">PO No. : '.$getApplication->application_number.'</span>
                                                                <span class="badge pulse-animation bg-warning mt-1 fs-9 fw-semibold">CANCELED FORM</span>
                                                            </div>';
                                }
                                else if($getApplication->application_form_status == '24') { // 24 = OPEN ORDER
                                    $timelineApplication = '<div class="timeline-item">
                                                                <div class="timeline-badge bg-success"><i class="fa-solid fa-check"></i></div>
                                                                <span class="d-block lh-md">APPLICATION & PO FORM</span>
                                                                <span class="d-block lh-md">PO No. : '.$getApplication->application_number.'</span>
                                                                <span class="d-block lh-md">Vendor : '.$getApplication->vendor_name.'</span>
                                                                <span class="badge bg-success mt-1 fs-9 fw-semibold">FULLY APPROVED</span>
                                                            </div>
                                                            <div class="timeline-item">
                                                                <div class="timeline-badge pulse-animation bg-info"></div>
                                                                <span class="badge pulse-animation bg-info fs-9 fw-semibold">OPEN ORDER</span>
                                                            </div>
                                                            <div class="timeline-item">
                                                                <div class="timeline-badge bg-light-dark"></div>
                                                                <span class="badge bg-light-dark fs-9 fw-semibold">RECEIVED GOODS</span>
                                                            </div>
                                                            <div class="timeline-item">
                                                                <div class="timeline-badge bg-light-dark"></div>
                                                                <span class="badge bg-light-dark fs-9 fw-semibold">INVOICED</span>
                                                            </div>';
                                }
                                else if($getApplication->application_form_status == '21') { // 21 = RECEIVED GOODS
                                    $timelineApplication = '<div class="timeline-item">
                                                                <div class="timeline-badge bg-success"><i class="fa-solid fa-check"></i></div>
                                                                <span class="d-block lh-md">APPLICATION & PO FORM</span>
                                                                <span class="d-block lh-md">PO No. : '.$getApplication->application_number.'</span>
                                                                <span class="d-block lh-md">Vendor : '.$getApplication->vendor_name.'</span>
                                                                <span class="badge bg-success mt-1 fs-9 fw-semibold">FULLY APPROVED</span>
                                                            </div>
                                                            <div class="timeline-item">
                                                                <div class="timeline-badge bg-success"><i class="fa-solid fa-check"></i></div>
                                                                <span class="badge bg-success fs-9 fw-semibold">OPEN ORDER</span>
                                                            </div>
                                                            <div class="timeline-item">
                                                                <div class="timeline-badge pulse-animation bg-info"></div>
                                                                <span class="badge pulse-animation bg-info fs-9 fw-semibold">RECEIVED GOODS</span>
                                                            </div>
                                                            <div class="timeline-item">
                                                                <div class="timeline-badge bg-light-dark"></div>
                                                                <span class="badge bg-light-dark fs-9 fw-semibold">INVOICED</span>
                                                            </div>';
                                }
                                else if($getApplication->application_form_status == '22') { // 22 = INVOICED
                                    $timelineApplication = '<div class="timeline-item">
                                                                <div class="timeline-badge bg-success"><i class="fa-solid fa-check"></i></div>
                                                                <span class="d-block lh-md">APPLICATION & PO FORM</span>
                                                                <span class="d-block lh-md">PO No. : '.$getApplication->application_number.'</span>
                                                                <span class="d-block lh-md">Vendor : '.$getApplication->vendor_name.'</span>
                                                                <span class="badge bg-success mt-1 fs-9 fw-semibold">FULLY APPROVED</span>
                                                            </div>
                                                            <div class="timeline-item">
                                                                <div class="timeline-badge bg-success"><i class="fa-solid fa-check"></i></div>
                                                                <span class="badge bg-success fs-9 fw-semibold">OPEN ORDER</span>
                                                            </div>
                                                            <div class="timeline-item">
                                                                <div class="timeline-badge bg-success"><i class="fa-solid fa-check"></i></div>
                                                                <span class="badge bg-success fs-9 fw-semibold">RECEIVED GOODS</span>
                                                            </div>
                                                            <div class="timeline-item">
                                                                <div class="timeline-badge bg-success"><i class="fa-solid fa-check"></i></div>
                                                                <span class="badge bg-success fs-9 fw-semibold">INVOICED</span>
                                                            </div>';
                                }
                            }
                        }
                        else if($arrItemProcessed[$x]['comparisonId'] && !$arrItemProcessed[$x]['applicationId']) {
                            if($arrItemProcessed[$x]['isNeedRevision'] == '1') {
                                $timelineOrder = '<div class="timeline-item">
                                                    <div class="timeline-badge bg-warning"><i class="fa-solid fa-check"></i></div>
                                                    <span class="d-block lh-md">ORDER FORM</span>
                                                    <span class="badge bg-warning mt-1 fs-9 fw-semibold">NEED REVISE</span>
                                                </div>';
                            }

                            $dataType = ($type == 'PROGRESS') ? 'PROGRESS' : 'VIEW';
                            if($type == 'MY_REQUEST') {
                                $dataForm = 'ORDER_FORM';
                                $token = ['cid'=>$companyId,'id' => $employeeId,'a' => $docApprovalId,'b' => $referenceId,'c' => $arrItemProcessed[$x]['comparisonId'],'d' => null, 'g' => $arrItemProcessed[$x]['orderGroupId']];
                                $encodedToken = SafeToken::encode($token);
                                $tokenForm = $encodedToken;
                            }
                            else {
                                $dataForm = 'COMPARISON_FORM';
                                $token = ['cid'=>$companyId,'id' => $employeeId,'a' => $docApprovalId,'b' => $referenceId,'c' => $arrItemProcessed[$x]['comparisonId'],'d' => null, 'g' => $arrItemProcessed[$x]['orderGroupId']];
                                $encodedToken = SafeToken::encode($token);
                                $tokenForm = $encodedToken;
                            }

                            $timelineApplication = '<div class="timeline-item">
                                                        <div class="timeline-badge pulse-animation bg-secondary"></div>
                                                        <span class="d-block lh-md">Application & PO Form</span>
                                                        <span class="badge pulse-animation bg-secondary mt-1 fs-9 fw-semibold">NOT SUBMITTED</span>
                                                    </div>';
                        }

                        $listItem .= '<div class="card card-hover mb-2-2'.$itemClass.'" data-source="WEB" data-form="'.$dataForm.'" data-selectedversion="'.$arrItemProcessed[$x]['docVersion'].'" data-type="'.$dataType.'" data-token="'.$encodedToken.'">
                                        <div class="card-body p-2-2 pb-0">
                                            <div class="mb-2 data-details">
                                                <div class="data-row">
                                                    <span class="data-label">Items</span>
                                                    <span class="data-separator">:</span>
                                                    <span class="data-value">
                                                        <ul class="data-item-list">';
                                                        for($y = 0; $y < count($itemProcessed); $y++) {
                                                            $no = count($itemProcessed) > 1 ? ($y + 1).'. ' : '';
                                                            $listItem .= '<li>'.$no.$itemProcessed[$y].'</li>';
                                                        }
                                            $listItem .= '</ul>
                                                    </span>
                                                </div>
                                                <div class="data-row">
                                                    <span class="data-label">Selected Vendor</span>
                                                    <span class="data-separator">:</span>
                                                    <span class="data-value">'.$arrItemProcessed[$x]['vendorName'].'</span>
                                                </div>
                                                <div class="data-row">
                                                    <span class="data-label">Form</span>
                                                    <span class="data-separator">:</span>
                                                    <span class="data-value">
                                                        <div class="timeline pt-1" id="timelineApproval">
                                                            '.$timelineOrder.'
                                                            '.$timelineComparison.'
                                                            '.$timelineApplication.'
                                                        </div>
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>';
                    }
                }
            }
            else {
                $tokenForm = $encodedToken;
                $nextForm = 'ORDER_FORM';
            }

            $response = [
                'processedStatus' => $processedStatus,
                'multiVendor' => $multiVendor,
                'title' => $title,
                'listItem' => $listItem,
                'form' => $nextForm,
                'tokenForm' => $tokenForm,
            ];

            return response()->json([
                'status' => 200,
                'message' => 'Success',
                'data' => $response,
            ], 200);
        }
        else{
            $response['form'] = view('procurementpurchasing::form.view_disallowed')->render();
            return response()->json([
                'status' => 404,
                'message' => 'Not found',
                'data' => $response,
            ], 200);
        }
    }

    public function itemUnprocessed(Request $request) {
        $token = SafeToken::decode($request->token);
        $validator = Validator::make($token, [
            'id' => 'required',
            'a' => 'required',
            'b' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => 'Invalid token form', 'errors' => $validator->errors()], 422)
                            ->setStatusCode(422, 'Invalid token form');
        }

        $docApprovalId = $token['a'];
        $referenceId = $token['b'];
        $modelApproval = new DocumentApprovalModel();
        $response = array();
        $getData = $modelApproval->documentApprovalDetailOrderItem()
                                ->from('doc_approval_detail_order_form_item AS a')
                                ->leftJoin('doc_approval_order_group as d', function($join) {
                                    $join->on('d.order_group_id', 'a.order_group_id')
                                        ->where('d.is_active', '1');
                                        // ->orWhere('d.comparison_id', 'd.comparison_id');
                                })
                                ->select('a.order_detail_id', 'a.appliance_item', 'd.order_group_id')
                                ->where('a.doc_approval_id', $docApprovalId)
                                ->where('a.order_form_id', $referenceId)
                                ->where('a.unit_name', '<>', 'VAT')
                                // ->whereNull('a.comparison_id')
                                // ->whereNull('a.application_id')
                                ->whereNull('a.canceled_id')
                                ->where('a.is_active', '1')
                                ->orderBy('a.order_detail_id', 'asc')
                                ->get();

        foreach ($getData as $row) {
            if(!$row->order_group_id) {
                $response[] = [
                    'value' => $row->order_detail_id,
                    'label' => $row->appliance_item
                ];
            }
        }

        return response()->json([
            'status' => 200,
            'message' => 'Success',
            'data' => $response,
        ], 200);
    }

    public function viewForm(Request $request) {
        $form = $request->form;
        $encodedToken = $request->token;

        if($form == 'COMPARISON_FORM_TAB' && $encodedToken == null) {
            return response()->json(['message' => 'Form not found', 'errors' => 'Form not found'], 404)
                            ->setStatusCode(404, 'Form not found');
        }

        $token = SafeToken::decode($encodedToken);
        $validator = Validator::make($token, [
            'id' => 'required',
            'a' => 'required',
            'b' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => 'Invalid token form', 'errors' => $validator->errors()], 422)
                            ->setStatusCode(422, 'Invalid token form');
        }

        // $employeeId = $token['id'];
        $employeeId = $this->employeeId;
        $companyId = $this->companyId;
        $docApprovalId = $token['a'];
        $referenceId = $token['b'];
        $groupId = array_key_exists('g', $token) ? $token['g'] : null;

        $response = collect();
        $modelApproval = new DocumentApprovalModel();
        $modelProcurement = new ProcurementPurchasingModel();
        $getHeader = $modelApproval->documentApprovalHeader()
                            ->from('doc_approval_header AS a')
                            ->leftJoin('master_transaction_status AS c', 'c.transaction_status_id', 'a.doc_status_id')
                            ->select('a.*', 'c.transaction_status_name')
                            ->where('a.doc_approval_id', $docApprovalId)
                            ->where('a.is_active', '1')
                            ->first();
        if($getHeader) {
            $dataForm = collect();
            $footerButton = false;
            $response['footerButton'] = $actionButton = '';
            if($getHeader->doc_type_id == '1') { // 1 = ORDER FORM
                if($form == 'ORDER_FORM') {
                    // GET FORM
                    $dataForm = $dataForm->merge([
                        'docNumber' => $getHeader->doc_number,
                        'departmentName' => $getHeader->department_name,
                    ]);
                    $getForm = $modelProcurement->documentApprovalDetailOrder()
                                    ->from('doc_approval_detail_order_form as a')
                                    ->leftJoin('master_locations as b', 'b.location_id', '=', 'a.location_id')
                                    ->select('a.doc_approval_id', 'a.order_form_id', 'a.order_form_status', 'a.doc_version', 'a.grand_total', 'a.request_date', 'a.currency_code', 'a.created_at', 'b.location_name');

                    if($groupId != null) {
                        $getForm = $getForm->leftJoin('doc_approval_order_group as c', function($join) use ($groupId) {
                                                $join->on('c.order_form_id', 'a.order_form_id')
                                                    ->where('c.order_group_id', $groupId)
                                                    ->where('c.is_active', '1');
                                            });
                    }

                    if($referenceId != null) {
                        // IF REFERENCE ID VALUE IS SET, GET BY REFERENCE ID, IF NOT GET LATEST FORM
                        $getForm = $getForm->where('a.order_form_id', $referenceId);
                    }

                    $getForm = $getForm->where('a.doc_approval_id', $getHeader->doc_approval_id)
                                        ->where('a.is_active', '1')
                                        ->orderBy('a.order_form_id', 'desc')
                                        ->first();

                    $token = ['cid' => $companyId,'id' => $employeeId,'a' => $docApprovalId,'b' => $getForm->order_form_id];
                    $encodedToken = SafeToken::encode($token);
                    $response['tokenAttachment'] = $encodedToken;

                    $token = ['cid' => $companyId,'id' => $employeeId,'a' => $docApprovalId,'b' => $getForm->order_form_id,'c' => null,'d' => null];
                    $encodedToken = SafeToken::encode($token);
                    $response['tokenForm'] = $encodedToken;

                    if($getForm->order_form_status == '6') { // 6 = REVISED
                        $getAllVersion = $modelProcurement->documentApprovalDetailOrder()
                                                ->from('doc_approval_detail_order_form')
                                                ->select('doc_approval_id', 'doc_version', 'order_form_id')
                                                ->where('doc_approval_id', $getHeader->doc_approval_id)
                                                ->where('is_active', '1')
                                                ->orderBy('doc_version', 'desc')
                                                ->orderBy('order_form_id', 'desc')
                                                ->get();
                        $versionList = '';
                        $x = 1;
                        foreach ($getAllVersion as $rowAllVersion) {
                            $token = ['cid' => $companyId,'id' => $employeeId,'a' => $getHeader->doc_approval_id,'b' => $rowAllVersion->order_form_id,'c' => null,'d'=>null];
                            $encodedToken = SafeToken::encode($token);

                            $latestLabel = $x == 1 ? ' (Latest)' : '';
                            $active = $rowAllVersion->order_form_id == $getForm->order_form_id ? ' active' : '';
                            // $versionLabel = ($rowAllVersion->doc_version - 1 > 0) ? 'Rev. 0'.$rowAllVersion->doc_version - 1 : 'Old Version' ;
                            $versionLabel = 'Ver. '.$rowAllVersion->doc_version;
                            $selectedVersionLabel = $versionLabel.$latestLabel;
                            $versionList .= '<li><span class="dropdown-item docVersionItem'.$active.'" data-token="'.$encodedToken.'" data-form="ORDER_FORM">'.$selectedVersionLabel.'</span></li>';
                            $x++;
                        }

                        $response['selectedVersionLabel'] = $selectedVersionLabel;
                        $response['title'] = $getHeader->doc_name.' <div class="dropdown dropdownDocVersion">
                                                        <button type="button" class="btn btn-default dropdown-toggle py-1 px-2" data-bs-boundary="viewport" id="dropdownDocVersion" data-coreui-toggle="dropdown" aria-expanded="false" title="Document Version">
                                                            Ver. '.$getForm->doc_version.'
                                                        </button>
                                                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdownDocVersion" style="">
                                                            '.$versionList.'
                                                        </ul>
                                                    </div>';

                        $actionButton = '<div class="col-sm-12 col-md-3 d-flex justify-content-center justify-content-md-end mb-2 px-0">
                                            <button type="button" class="btn btn-info w-100 w-md-auto me-md-2 actionBtn" data-type="PROGRESS"  data-doc="'.$getHeader->doc_type_id.'" data-form="ORDER_FORM" data-token="'.$encodedToken.'">
                                                <i class="fa-regular fa-circle-info"></i> View Progress
                                            </button>
                                        </div>';

                        $response['footerButton'] = '<div class="container-fluid p-0">
                                                        <div class="alert alert-warning m-0 w-100 p-2 mb-2" role="alert">This form has been <span class="fw-semibold">REVISED</span>, You can view the latest version form</div>
                                                        <div class="row justify-content-center w-100 mx-0">
                                                            <div class="col-sm-12 col-md-3 d-flex justify-content-center justify-content-md-end mb-2 px-0">
                                                                <button type="button" class="btn btn-default w-100 w-md-auto me-md-2" data-coreui-dismiss="modal" title="Close">
                                                                    <i class="fas fa-xmark"></i> Close
                                                                </button>
                                                            </div>
                                                            '.$actionButton.'
                                                        </div>
                                                    </div>';
                    }
                    else {
                        if($getForm->doc_version > 1) {
                            $token = ['cid' => $companyId,'id' => $employeeId,'a' => $getHeader->doc_approval_id,'b' => $getForm->order_form_id,'c' => null,'d'=>null];
                            $encodedTokenLatest = SafeToken::encode($token);

                            $getAllVersion = $modelProcurement->documentApprovalDetailOrder()
                                                ->from('doc_approval_detail_order_form')
                                                ->select('doc_approval_id', 'doc_version', 'order_form_id')
                                                ->where('doc_approval_id', $getHeader->doc_approval_id)
                                                ->where('order_form_id', '<>', $getForm->order_form_id)
                                                ->where('is_active', '1')
                                                ->orderBy('order_form_id', 'desc')
                                                ->get();
                            $versionList = '';
                            foreach ($getAllVersion as $rowAllVersion) {
                                $token = ['cid' => $companyId,'id' => $employeeId,'a' => $getHeader->doc_approval_id,'b' => $rowAllVersion->order_form_id,'c' => null,'d'=>null];
                                $encodedToken = SafeToken::encode($token);

                                // $versionLabel = ($rowAllVersion->doc_version - 1 > 0) ? 'Rev. 0'.$rowAllVersion->doc_version - 1 : 'Old Version' ;
                                $versionLabel = 'Ver. '.$rowAllVersion->doc_version;
                                $versionList .= '<li><span class="dropdown-item docVersionItem" data-token="'.$encodedToken.'" data-form="ORDER_FORM">'.$versionLabel.'</span></li>';
                            }

                            $selectedVersionLabel = 'Ver. '.$getForm->doc_version.' (Latest)';
                            $response['selectedVersionLabel'] = $selectedVersionLabel;
                            $response['title'] = $getHeader->doc_name.' <div class="dropdown dropdownDocVersion">
                                                        <button type="button" class="btn btn-default dropdown-toggle py-1 px-2" data-bs-boundary="viewport" id="dropdownDocVersion" data-coreui-toggle="dropdown" aria-expanded="false" title="Document Version">
                                                            Ver. '.$getForm->doc_version.' (Latest)
                                                        </button>
                                                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdownDocVersion" style="">
                                                            <li><span class="dropdown-item docVersionItem active" data-token="'.$encodedTokenLatest.'" data-form="ORDER_FORM">'.$selectedVersionLabel.'</span></li>
                                                            '.$versionList.'
                                                        </ul>
                                                    </div>';

                            $encodedToken = $encodedTokenLatest;
                        }
                        else {
                            $response['selectedVersionLabel'] = '';
                            $response['title'] = $getHeader->doc_name;
                        }

                        if($getHeader->doc_status_id == '8') { // 8 = ORDER FORM FULLY APPROVED
                            $actionButtonComparison = '<div class="col-sm-12 col-md-3 d-flex justify-content-center justify-content-md-end mb-2 px-0">
                                                            <button type="button" class="btn btn-secondary w-100 w-md-auto me-md-2 nextNewForm" data-type="NEW" data-form="COMPARISON_FORM" data-token="'.$encodedToken.'">
                                                                <i class="fa-regular fa-list-ol"></i> Create Comparison Form
                                                            </button>
                                                        </div>';
                            $actionButtonApplication = '';
                            // $actionButtonApplication = '<div class="col-sm-12 col-md-3 d-flex justify-content-center justify-content-md-end mb-2 px-0">
                            //                                 <button type="button" class="btn btn-info w-100 w-md-auto me-md-2 nextNewFormConfirmation" data-type="NEW" data-form="APPLICATION_FORM" data-token="'.$encodedToken.'">
                            //                                     <i class="fa-regular fa-file-import"></i> Create Application Form
                            //                                 </button>
                            //                             </div>';

                            if($getForm->doc_version > 1) {
                                // GET PREVIOUS ORDER FORM
                                $getOrderFormPrev = $modelProcurement->documentApprovalDetailOrder()
                                                                ->from('doc_approval_detail_order_form as a')
                                                                ->select('a.doc_approval_id', 'a.order_form_id')
                                                                ->where('a.company_id', $companyId)
                                                                ->where('a.doc_approval_id', $getHeader->doc_approval_id)
                                                                ->where('a.order_form_id', '<>', $referenceId)
                                                                ->where('a.is_active', '1')
                                                                ->orderBy('a.order_form_id', 'desc')
                                                                ->first();

                                // GET COMPARISON IF ORDER FORM IS REVISED
                                $getComparisonForm = $modelProcurement->docApprovalOrderComparison()
                                                            ->select('comparison_id')
                                                            ->where('company_id', $companyId)
                                                            ->where('doc_approval_id', $getOrderFormPrev->doc_approval_id)
                                                            ->where('order_form_id', $getOrderFormPrev->order_form_id)
                                                            ->where('is_active', '1')
                                                            ->orderBy('comparison_id', 'desc')
                                                            ->first();
                                if($getComparisonForm) {
                                    $tokenComparison = ['cid' => $companyId,'id' => $employeeId, 'a' => $getOrderFormPrev->doc_approval_id,'b' => $getOrderFormPrev->order_form_id,'c' => $getComparisonForm->comparison_id,'d' => null];
                                    $tokenComparison = SafeToken::encode($tokenComparison);

                                    $actionButtonComparison = '<div class="col-sm-12 col-md-3 d-flex justify-content-center justify-content-md-end mb-2 px-0">
                                            <button type="button" class="btn btn-secondary w-100 w-md-auto me-md-2 actionBtn" data-type="REVISE" data-form="COMPARISON_FORM" data-token="'.$tokenComparison.'">
                                                <i class="fa-regular fa-list-ol"></i> Create Comparison Form
                                            </button>
                                        </div>';
                                }

                                // GET APPLICATION IF ORDER FORM IS REVISED
                                $getApplicationForm = $modelProcurement->docApprovalOrderApplication()
                                                                    ->from('doc_approval_order_application as a')
                                                                    ->select('a.doc_approval_id', 'a.application_id', 'a.order_form_id', 'a.doc_version', 'a.comparison_id')
                                                                    ->where('a.company_id', $companyId)
                                                                    ->where('a.order_form_id', $getOrderFormPrev->order_form_id)
                                                                    ->where('a.is_active', '1')
                                                                    ->orderBy('a.application_id', 'desc')
                                                                    ->first();
                                if($getApplicationForm) {
                                    $tokenApplication = ['cid' => $companyId, 'id' => $employeeId,'a' => $getApplicationForm->doc_approval_id, 'b' => $getApplicationForm->application_id, 'c'=> null, 'd'=>null];
                                    $tokenApplication = SafeToken::encode($tokenApplication);

                                    $actionButtonApplication = '<div class="col-sm-12 col-md-3 d-flex justify-content-center justify-content-md-end mb-2 px-0">
                                                                    <button type="button" class="btn btn-info w-100 w-md-auto me-md-2 nextNewFormConfirmation" data-type="REVISE" data-form="APPLICATION_FORM" data-token="'.$tokenApplication.'">
                                                                        <i class="fa-regular fa-file-import"></i> Create Application Form
                                                                    </button>
                                                                </div>';
                                }
                            }

                            $actionButton = '<div class="col-sm-12 col-md-3 d-flex justify-content-center justify-content-md-end mb-2 px-0">
                                                <button type="button" class="btn btn-warning w-100 w-md-auto me-md-2 actionBtn" data-type="SEND_BACK" data-form="ORDER_FORM" data-token="'.$encodedToken.'">
                                                    <i class="fa-solid fa-arrow-turn-left"></i> Send Back to User
                                                </button>
                                            </div>
                                            '.$actionButtonComparison.$actionButtonApplication;

                            $response['footerButton'] = '<div class="container-fluid p-0">
                                                            <div class="row justify-content-center w-100 mx-0">
                                                                <div class="col-sm-12 col-md-3 d-flex justify-content-center justify-content-md-end mb-2 px-0">
                                                                    <button type="button" class="btn btn-default w-100 w-md-auto me-md-2" data-coreui-dismiss="modal" title="Close">
                                                                        <i class="fas fa-xmark"></i> Close
                                                                    </button>
                                                                </div>
                                                                '.$actionButton.'
                                                            </div>
                                                        </div>';
                        }
                        else {
                            $response['footerButton'] = '<div class="container-fluid p-0">
                                                            <div class="row justify-content-center w-100 mx-0">
                                                                <div class="col-sm-12 col-md-5 d-flex justify-content-center justify-content-md-end mb-2 px-0">
                                                                    <button type="button" class="btn btn-default w-100 w-md-auto me-md-2" data-coreui-dismiss="modal" title="Close">
                                                                        <i class="fas fa-xmark"></i> Close
                                                                    </button>
                                                                </div>
                                                            </div>
                                                        </div>';
                        }
                    }
                }
                else if($form == 'COMPARISON_FORM') {
                    $comparisonId = $token['c'];

                    $tokenAttachment = ['cid' => $companyId,'id' => $employeeId,'a'=> $docApprovalId, 'b' => $comparisonId];
                    $tokenAttachment = SafeToken::encode($tokenAttachment);
                    $response['tokenAttachment'] = $tokenAttachment;

                    $response['title'] = 'Comparison Form';
                    $arrReviseOrderDetailId = $arrOrderGroupId = array();
                    $getComparisonForm = $modelProcurement->docApprovalOrderComparison()
                                                        ->from('doc_approval_order_comparison as a')
                                                        ->leftJoin('doc_approval_order_group as b', function($join) {
                                                            $join->on('b.comparison_id', 'a.comparison_id')
                                                                ->where('b.is_active', '1');
                                                        })
                                                        ->select('a.comparison_id', 'a.comparison_title', 'a.comparison_description', 'a.comparison_date', 'a.validity', 'a.vendor_id_selected','a.comparison_notes', 'b.order_group_id')
                                                        ->where('a.order_form_id', $referenceId)
                                                        ->where('a.comparison_id', $comparisonId)
                                                        ->where('a.is_active', '1')
                                                        ->orderBy('a.comparison_id', 'desc')
                                                        ->first();
                    if($getComparisonForm) {
                        $token = ['cid' => $companyId,'id' => $employeeId, 'a' => $docApprovalId,'b' => $referenceId,'c' => $comparisonId,'d' => null];
                        $encodedToken = SafeToken::encode($token);
                        $response['tokenForm'] = $encodedToken;
                        $dataForm = $dataForm->merge([
                            'tokenForm'=> $encodedToken,
                            'comparisonTitle' => $getComparisonForm->comparison_title,
                            'comparisonDescription' => $getComparisonForm->comparison_description,
                            'comparisonDate' => $getComparisonForm->comparison_date,
                            'validity' => $getComparisonForm->validity,
                            'vendorIdSelected' => $getComparisonForm->vendor_id_selected,
                            'comparisonNotes' => $getComparisonForm->comparison_notes,
                        ]);

                        $getComparisonItem = $modelProcurement->docApprovalOrderComparisonItem()
                                                            ->from('doc_approval_order_comparison_item as a')
                                                            ->leftJoin('doc_approval_detail_order_form_item as b', 'b.order_detail_id', 'a.order_detail_id', 'b.revise_order_detail_id')
                                                            ->select('a.comparison_item_id', 'a.order_detail_id', 'a.appliance_item', 'a.unit_name', 'a.unit_quantity', 'a.currency', 'b.revise_order_detail_id')
                                                            ->where('a.order_form_id', $referenceId)
                                                            ->where('a.comparison_id', $getComparisonForm->comparison_id)
                                                            ->where('a.is_active', '1')
                                                            ->orderBy('a.comparison_item_id', 'asc')
                                                            ->get();

                        $arrItem = [];
                        // dd($getComparisonItem);
                        foreach ($getComparisonItem as $rowItem) {
                            $reviseOrderDetailId = $rowItem->revise_order_detail_id ?: $rowItem->order_detail_id;

                            $getDetailOrderPrev = $modelProcurement->documentApprovalDetailOrderItem()
                                                                    ->from('doc_approval_detail_order_form_item as a')
                                                                    ->rightJoin('doc_approval_order_group as b', function($join) {
                                                                        $join->on('b.application_id', 'a.application_id')
                                                                            ->where('b.is_active', '1')
                                                                            ->orderBy('b.order_group_id', 'desc');
                                                                    })
                                                                    ->select('b.order_group_id', 'b.comparison_id', 'b.application_id')
                                                                    ->where('a.company_id', $companyId)
                                                                    ->where('a.doc_approval_id', $getHeader->doc_approval_id);

                            if($rowItem->order_detail_id == $rowItem->revise_order_detail_id) {
                                $getDetailOrderPrev =  $getDetailOrderPrev->where('a.order_form_id', $referenceId)
                                                                        // ->where('a.order_group_id', '<', $getComparisonForm->order_group_id)
                                                                        ->whereNotNull('a.application_id');
                                                                        // ->where('a.order_group_id', '<', $getComparisonForm->order_group_id);
                            }
                            else {
                                // $getDetailOrderPrev =  $getDetailOrderPrev->where('a.order_detail_id', '=',  $rowItem->revise_order_detail_id);
                            }

                            $getDetailOrderPrev =  $getDetailOrderPrev->where('a.order_detail_id', '=',  $rowItem->revise_order_detail_id);
                            $getDetailOrderPrev =  $getDetailOrderPrev->orderBy('b.application_id', 'desc')
                                                                    ->orderBy('b.comparison_id', 'desc')
                                                                    ->first();

                            if($getDetailOrderPrev) {
                                // if( $rowItem->order_detail_id == $rowItem->revise_order_detail_id) {
                                //     // GET ORDER GROUP BEFORE REVISION
                                //     if($getDetailOrderPrev->order_group_id && !in_array($getDetailOrderPrev->order_group_id, $arrOrderGroupId)) {
                                //         $arrOrderGroupId[] = $getDetailOrderPrev->order_group_id;
                                //     }
                                // }
                                // else if($rowItem->revise_order_detail_id && !in_array($rowItem->revise_order_detail_id, $arrReviseOrderDetailId)) {
                                //     $arrReviseOrderDetailId[] = $rowItem->revise_order_detail_id;
                                //     if($getDetailOrderPrev->order_group_id && !in_array($getDetailOrderPrev->order_group_id, $arrOrderGroupId)) {
                                //         $arrOrderGroupId[] = $getDetailOrderPrev->order_group_id;
                                //     }
                                // }

                                // $arrOrderGroupId[] = $getDetailOrderPrev->order_group_id;


                                $arrOrderGroupId[] = $getDetailOrderPrev->order_group_id;
                            }

                            // dd($arrOrderGroupId);
                            ////////////////////////
                            // if($rowItem->revise_order_detail_id && !in_array($rowItem->revise_order_detail_id, $arrReviseOrderDetailId)) {
                            //     $arrReviseOrderDetailId[] = $rowItem->revise_order_detail_id;

                            //     // GET ORDER FORM BEFORE REVISION
                            //     // $getDetailOrderPrev = $modelProcurement->documentApprovalDetailOrderItem()
                            //     //                                     ->from('doc_approval_detail_order_form_item as a')
                            //     //                                     ->rightJoin('doc_approval_order_group as b', function($join) {
                            //     //                                         $join->on('b.order_group_id', 'a.order_group_id')
                            //     //                                             ->where('b.is_active', '1')
                            //     //                                             ->orderBy('b.order_group_id', 'desc');
                            //     //                                     })
                            //     //                                     ->select('b.order_group_id', 'b.comparison_id', 'b.application_id')
                            //     //                                     ->where('a.company_id', $companyId)
                            //     //                                     ->where('a.doc_approval_id', $getHeader->doc_approval_id)
                            //     //                                     ->where('a.order_detail_id', '=',  $rowItem->revise_order_detail_id)
                            //     //                                     ->where('a.is_active', '1')
                            //     //                                     ->first();
                            //     if($getDetailOrderPrev) {
                            //         if($getDetailOrderPrev->order_group_id && !in_array($getDetailOrderPrev->order_group_id, $arrOrderGroupId)) {
                            //             $arrOrderGroupId[] = $getDetailOrderPrev->order_group_id;
                            //         }
                            //     }
                            // }

                            $arrItem[] = [
                                            'orderDetailId' => $rowItem->order_detail_id ?: '',
                                            'applianceItem' => $rowItem->appliance_item ?: '',
                                            'unitName' => $rowItem->unit_name ?: '',
                                            'unitQuantity' => $rowItem->unit_quantity ?: '',
                                            'currency' => $rowItem->currency ?: '',
                                            'reviseOrderDetailId' => $reviseOrderDetailId,
                                        ];
                        }

                        // dd($getDetailOrderPrev);

                        $dataForm = $dataForm->merge([
                            'documentType' => '6',
                            'itemForm' => $arrItem
                        ]);

                        $actionButton .= '<div class="col-sm-12 col-md-3 d-flex justify-content-center justify-content-md-end mb-2 px-0">
                                            <button type="button" class="btn btn-warning w-100 w-md-auto me-md-2 actionBtn" data-type="SEND_BACK" data-form="ORDER_FORM" data-token="'.$encodedToken.'">
                                                <i class="fa-solid fa-arrow-turn-left"></i> Send Back to User
                                            </button>
                                        </div>
                                        <div class="col-sm-12 col-md-3 d-flex justify-content-center justify-content-md-end mb-2 px-0">
                                            <button type="button" class="btn btn-secondary w-100 w-md-auto me-md-2 actionBtn" data-type="REVISE" data-form="COMPARISON_FORM" data-token="'.$encodedToken.'">
                                                <i class="fa-regular fa-pen-to-square"></i> Revise Comparison Form
                                            </button>
                                        </div>';
                    }

                    // $arrOrderGroupId = array();
                    // for($x = 0; $x < count($arrReviseOrderDetailId); $x++) {
                    //     // GET ORDER FORM BEFORE REVISION
                    //     $getDetailOrderPrev = $modelProcurement->documentApprovalDetailOrderItem()
                    //                                         ->from('doc_approval_detail_order_form_item as a')
                    //                                         ->rightJoin('doc_approval_order_group as b', function($join) {
                    //                                             $join->on('b.order_group_id', 'a.order_group_id')
                    //                                                 ->where('b.is_active', '1')
                    //                                                 ->orderBy('b.order_group_id', 'desc');
                    //                                         })
                    //                                         ->select('b.order_group_id', 'b.comparison_id', 'b.application_id')
                    //                                         ->where('a.company_id', $companyId)
                    //                                         ->where('a.doc_approval_id', $getHeader->doc_approval_id)
                    //                                         ->where('a.order_detail_id', '=', $arrReviseOrderDetailId[$x])
                    //                                         ->where('a.is_active', '1')
                    //                                         ->first();
                    //     if($getDetailOrderPrev) {
                    //         if($getDetailOrderPrev->order_group_id && !in_array($getDetailOrderPrev->order_group_id, $arrOrderGroupId) && $getDetailOrderPrev->order_group_id) {
                    //             $arrOrderGroupId[] = $getDetailOrderPrev->order_group_id;
                    //         }
                    //     }
                    // }

                    rsort($arrOrderGroupId);
                    $reviseApplication = false;
                    // dd($arrOrderGroupId);
                    for($x = 0; $x < count($arrOrderGroupId); $x++) {
                        // GET APPLICATION BEFORE REVISION
                        $getApplicationFormLatest = $modelProcurement->docApprovalOrderApplication()
                                                        ->from('doc_approval_order_application as a')
                                                        ->rightJoin('doc_approval_order_group as b', 'b.application_id', '=', 'a.application_id')
                                                        ->select('a.doc_approval_id', 'a.application_id', 'a.order_form_id', 'a.doc_version', 'a.comparison_id', 'b.order_group_id')
                                                        ->where('a.company_id', $companyId)
                                                        ->where('b.order_group_id', $arrOrderGroupId[$x])
                                                        ->where('b.is_active', '1')
                                                        ->orderBy('a.application_id', 'desc')
                                                        ->first();
                        if($getApplicationFormLatest) {
                            $reviseApplication = true;
                            // $tokenRevise = ['cid' => $companyId, 'id' => $employeeId,'a' => $getApplicationFormLatest->doc_approval_id, 'b' => $getApplicationFormLatest->application_id, 'c'=> $getApplicationFormLatest->comparison_id, 'd'=>null, 'g'=>$getApplicationFormLatest->order_group_id];
                            $tokenRevise = ['cid' => $companyId, 'id' => $employeeId,'a' => $getApplicationFormLatest->doc_approval_id, 'b' => $getApplicationFormLatest->application_id, 'c'=> $getApplicationFormLatest->comparison_id >= $comparisonId ?: $comparisonId , 'd'=>null, 'g'=>$getApplicationFormLatest->order_group_id];
                            $encodedToken = SafeToken::encode($tokenRevise);
                            break;
                        }
                    }

                    if($reviseApplication == true) {
                        $actionButton .= '<div class="col-sm-12 col-md-3 d-flex justify-content-center justify-content-md-end mb-2 px-0">
                                            <button type="button" class="btn btn-info w-100 w-md-auto me-md-2 nextNewForm" data-type="REVISE" data-form="APPLICATION_FORM" data-token="'.$encodedToken.'">
                                                <i class="fa-regular fa-file-import"></i> Create Application Form
                                            </button>
                                        </div>';
                    }
                    else {
                        $actionButton .= '<div class="col-sm-12 col-md-3 d-flex justify-content-center justify-content-md-end mb-2 px-0">
                                            <button type="button" class="btn btn-info w-100 w-md-auto me-md-2 nextNewForm" data-type="NEW" data-form="APPLICATION_FORM" data-token="'.$encodedToken.'">
                                                <i class="fa-regular fa-file-import"></i> Create Application Form
                                            </button>
                                        </div>';
                    }

                    $response['footerButton'] = '<div class="container-fluid p-0">
                                                    <div class="row justify-content-center w-100 mx-0">
                                                        <div class="col-sm-12 col-md-3 d-flex justify-content-center justify-content-md-end mb-2 px-0">
                                                            <button type="button" class="btn btn-default w-100 w-md-auto me-md-2" data-coreui-dismiss="modal" title="Close">
                                                                <i class="fas fa-xmark"></i> Close
                                                            </button>
                                                        </div>
                                                        '.$actionButton.'
                                                    </div>
                                                </div>';
                }
                else if($form == 'ORDER_FORM_TAB' || $form == 'COMPARISON_FORM_TAB') {
                    $formId = ($form == 'COMPARISON_FORM_TAB') ? $token['c'] : null;
                    $token = ['cid' => $companyId,'id' => $employeeId,'a' => $docApprovalId,'b' => $referenceId,'c' => $formId,'d' => null];
                    $encodedToken = SafeToken::encode($token);
                    $response['tokenForm'] = $encodedToken;
                }
            }
            else if($getHeader->doc_type_id == '2') { // 2 = APPLICATION & PO FORM
                $comparisonId = array_key_exists('c', $token) ? $token['c'] : null;
                $formId = $token['d'];
                // dd($token);
                $getForm = $modelProcurement->docApprovalOrderApplication()
                                ->from('doc_approval_order_application as a')
                                ->leftJoin('doc_approval_order_group as b', function($join) {
                                    $join->on('b.application_id', 'a.application_id')
                                        ->where('b.is_active', '1');
                                })
                                ->leftJoin('master_purchase_delivery as c', 'c.delivery_to_id', '=', 'a.delivery_to_id')
                                ->select('a.doc_approval_id', 'a.application_id', 'a.application_form_status', 'a.doc_version', 'a.order_form_id', 'a.comparison_id', 'b.order_group_id', 'a.application_number', 'a.application_header', 'a.application_title', 'a.application_grand_total', 'a.vendor_name', 'a.vendor_address', 'a.application_delivery', 'a.application_reason', 'a.application_currency', 'a.application_submitted_date', 'c.company_name', 'a.vendor_pic');

                if($referenceId != null) {
                    // IF REFERENCE ID VALUE IS SET, GET BY REFERENCE ID, IF NOT GET LATEST FORM
                    $getForm = $getForm->where('a.application_id', $referenceId);
                }

                $getForm = $getForm->where('a.doc_approval_id', $getHeader->doc_approval_id)
                                    ->where('b.order_group_id', $groupId)
                                    ->where('a.is_active', '1')
                                    ->orderBy('a.application_id', 'desc')
                                    ->first();

                                    // dd($getForm);
                if($form == 'APPLICATION_FORM') {
                    // dd($getForm);
                    $token = ['cid' => $companyId,'id' => $employeeId,'a' => $docApprovalId,'b' => $getForm->application_id, 'c' => $getForm->comparison_id, 'd'=> null, 'g'=>$getForm->order_group_id];
                    $encodedToken = SafeToken::encode($token);
                    $response['tokenForm'] =  $response['tokenAttachment'] = $encodedToken;

                    // $token = ['cid' => $companyId,'id' => $employeeId,'a' => $docApprovalId,'b' => $getForm->application_id,'c' => $getForm->comparison_id,'d' => null, 'g'=>$getForm->order_group_id];
                    // $jsonToken = json_encode($token);
                    // $encryptedToken = Crypt::encryptString($jsonToken);
                    // $encodedToken = rtrim(strtr(base64_encode($encryptedToken), '+/', '-_'), '=');
                    // $response['tokenForm'] = $encodedToken;

                    if($getForm->application_form_status == '6') { // 6 = REVISED
                        $getAllVersion = $modelProcurement->docApprovalOrderApplication()
                                                ->from('doc_approval_order_application as a')
                                                ->leftJoin('doc_approval_order_group as b', function($join) {
                                                    $join->on('b.application_id', 'a.application_id')
                                                        ->where('b.is_active', '1');
                                                })
                                                ->select('a.doc_approval_id', 'a.doc_version', 'a.application_id', 'a.comparison_id', 'b.order_group_id')
                                                ->where('a.doc_approval_id', $getHeader->doc_approval_id)
                                                ->where('a.is_active', '1')
                                                ->orderBy('a.doc_version', 'desc')
                                                ->orderBy('a.application_id', 'desc')
                                                ->get();
                        $versionList = '';
                        $x = 1;
                        foreach ($getAllVersion as $rowAllVersion) {
                            $token = ['cid' => $companyId,'id' => $employeeId,'a' => $getHeader->doc_approval_id,'b' => $rowAllVersion->application_id,'c' => $rowAllVersion->comparison_id,'d'=>null, 'g'=>$rowAllVersion->order_group_id];
                            $encodedToken = SafeToken::encode($token);

                            $latestLabel = $x == 1 ? ' (Latest)' : '';
                            $active = $rowAllVersion->application_id == $getForm->application_id ? ' active' : '';
                            // $versionLabel = ($rowAllVersion->doc_version - 1 > 0) ? 'Rev. 0'.$rowAllVersion->doc_version - 1 : 'Old Version' ;
                            $versionLabel = 'Ver. '.$rowAllVersion->doc_version;
                            $selectedVersionLabel = $versionLabel.$latestLabel;
                            $versionList .= '<li><span class="dropdown-item docVersionItem'.$active.'" data-token="'.$encodedToken.'" data-form="APPLICATION_FORM">'.$selectedVersionLabel.'</span></li>';
                            $x++;
                        }

                        $response['selectedVersionLabel'] = $selectedVersionLabel;
                        $response['title'] = $getHeader->doc_name.' <div class="dropdown dropdownDocVersion">
                                                        <button type="button" class="btn btn-default dropdown-toggle py-1 px-2" data-bs-boundary="viewport" id="dropdownDocVersion" data-coreui-toggle="dropdown" aria-expanded="false" title="Document Version">
                                                            Ver. '.$getForm->doc_version.'
                                                        </button>
                                                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdownDocVersion" style="">
                                                            '.$versionList.'
                                                        </ul>
                                                    </div>';

                        $actionButton = '<div class="col-sm-12 col-md-3 d-flex justify-content-center justify-content-md-end mb-2 px-0">
                                                <button type="button" class="btn btn-info w-100 w-md-auto me-md-2 actionBtn" data-type="PROGRESS"  data-doc="'.$getHeader->doc_type_id.'" data-form="APPLICATION_FORM" data-token="'.$encodedToken.'">
                                                    <i class="fa-regular fa-circle-info"></i> View Progress
                                                </button>
                                            </div>';

                        $response['footerButton'] = '<div class="container-fluid p-0">
                                                        <div class="alert alert-warning m-0 w-100 p-2 mb-2" role="alert">This form has been <span class="fw-semibold">REVISED</span>, You can view the latest version form</div>
                                                        <div class="row justify-content-center w-100 mx-0">
                                                            <div class="col-sm-12 col-md-3 d-flex justify-content-center justify-content-md-end mb-2 px-0">
                                                                <button type="button" class="btn btn-default w-100 w-md-auto me-md-2" data-coreui-dismiss="modal" title="Close">
                                                                    <i class="fas fa-xmark"></i> Close
                                                                </button>
                                                            </div>
                                                            '.$actionButton.'
                                                        </div>
                                                    </div>';
                    }
                    else {
                        if($getForm->doc_version > 1) {
                            $token = ['cid' => $companyId,'id' => $employeeId,'a' => $getHeader->doc_approval_id,'b' => $getForm->application_id, 'c' => $getForm->comparison_id,'d' => null, 'g'=>$getForm->order_group_id];
                            $encodedTokenLatest = SafeToken::encode($token);

                            $getAllVersion = $modelProcurement->docApprovalOrderApplication()
                                                ->from('doc_approval_order_application as a')
                                                ->leftJoin('doc_approval_order_group as b', function($join) {
                                                    $join->on('b.application_id', 'a.application_id')
                                                        ->where('b.is_active', '1');
                                                })
                                                ->select('a.doc_approval_id', 'a.doc_version', 'a.application_id', 'a.comparison_id', 'b.order_group_id')
                                                ->where('a.doc_approval_id', $getHeader->doc_approval_id)
                                                ->where('a.application_id', '<>', $getForm->application_id)
                                                ->where('a.is_active', '1')
                                                ->orderBy('a.doc_version', 'desc')
                                                ->orderBy('a.application_id', 'desc')
                                                ->get();
                            $versionList = '';
                            foreach ($getAllVersion as $rowAllVersion) {
                                $token = ['cid' => $companyId,'id' => $employeeId,'a' => $getHeader->doc_approval_id,'b' => $rowAllVersion->application_id,'c' => $rowAllVersion->comparison_id,'d' => null, 'g'=>$rowAllVersion->order_group_id];
                                $encodedToken = SafeToken::encode($token);

                                // $versionLabel = ($rowAllVersion->doc_version - 1 > 0) ? 'Rev. 0'.$rowAllVersion->doc_version - 1 : 'Old Version' ;
                                $versionLabel = 'Ver. '.$rowAllVersion->doc_version;
                                $versionList .= '<li><span class="dropdown-item docVersionItem" data-token="'.$encodedToken.'" data-form="APPLICATION_FORM">'.$versionLabel.'</span></li>';
                            }

                            $selectedVersionLabel = 'Ver. '.$getForm->doc_version.' (Latest)';
                            $response['selectedVersionLabel'] = $selectedVersionLabel;
                            $response['title'] = $getHeader->doc_name.' <div class="dropdown dropdownDocVersion">
                                                        <button type="button" class="btn btn-default dropdown-toggle py-1 px-2" data-bs-boundary="viewport" id="dropdownDocVersion" data-coreui-toggle="dropdown" aria-expanded="false" title="Document Version">
                                                            Ver. '.$getForm->doc_version.' (Latest)
                                                        </button>
                                                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdownDocVersion" style="">
                                                            <li><span class="dropdown-item docVersionItem active" data-token="'.$encodedTokenLatest.'" data-form="APPLICATION_FORM">'.$selectedVersionLabel.'</span></li>
                                                            '.$versionList.'
                                                        </ul>
                                                    </div>';

                            $encodedToken = $encodedTokenLatest;
                        }
                        else {
                            $response['selectedVersionLabel'] = '';
                            $response['title'] = $getHeader->doc_name;
                        }

                        $response['tokenForm'] = $encodedToken;
                        // dd($getHeader);
                        if($getForm->application_form_status == '2') { // 2 = SUBMITTED
                            $getOrderForm = $modelProcurement->documentApprovalDetailOrder()
                                                            ->select('doc_approval_id')
                                                            ->where('order_form_id', $getForm->order_form_id)
                                                            ->where('is_active', '1')
                                                            ->first();
                            $tokenReviseOrderForm = ['id' => $employeeId,'a' => $getOrderForm->doc_approval_id,'b' => $getForm->order_form_id];
                            $tokenReviseOrderForm = SafeToken::encode($tokenReviseOrderForm);

                            $token = ['cid' => $companyId,'id' => $employeeId,'a' => $getForm->doc_approval_id, 'b' => $getForm->application_id, 'c'=> $getForm->comparison_id, 'd'=>null, 'g' => $getForm->order_group_id];
                            $encodedToken = SafeToken::encode($token);
                            $response['tokenAttachment'] = $encodedToken;

                            $actionButton = '<div class="col-sm-12 col-md-3 d-flex justify-content-center justify-content-md-end mb-2 px-0">
                                                <button type="button" class="btn btn-info w-100 w-md-auto me-md-2 actionBtn" data-type="PROGRESS"  data-doc="'.$getHeader->doc_type_id.'" data-form="APPLICATION_FORM" data-token="'.$encodedToken.'">
                                                    <i class="fa-regular fa-circle-info"></i> View Progress
                                                </button>
                                            </div>
                                            <div class="col-sm-12 col-md-3 d-flex justify-content-center justify-content-md-end mb-2 px-0">
                                                <button type="button" class="btn btn-danger w-100 w-md-auto me-md-2 actionBtn" data-type="CANCEL" data-doc="'.$getHeader->doc_type_id.'" data-form="APPLICATION_FORM" data-no="'.$getForm->application_number.'" data-token="'.$encodedToken.'">
                                                    <i class="fa-regular fa-arrow-turn-left"></i> Cancel Approval Form
                                                </button>
                                            </div>';

                            $response['footerButton'] = '<div class="container-fluid p-0">
                                                            <div class="row justify-content-center w-100 mx-0">
                                                                <div class="col-sm-12 col-md-3 d-flex justify-content-center justify-content-md-end mb-2 px-0">
                                                                    <button type="button" class="btn btn-default w-100 w-md-auto me-md-2" data-coreui-dismiss="modal" title="Close">
                                                                        <i class="fas fa-xmark"></i> Close
                                                                    </button>
                                                                </div>
                                                                '.$actionButton.'
                                                            </div>
                                                        </div>';
                        }
                        else if($getForm->application_form_status == '12' || $getForm->application_form_status == '9') { // 12 = CANCELED, 9 = REJECTED
                            $token = ['cid' => $companyId, 'id' => $employeeId,'a' => $docApprovalId,'b' => $getForm->application_id, 'c' => $getForm->comparison_id,'d' => null, 'g'=> $getForm->order_group_id];
                            $encodedToken = SafeToken::encode($token);
                            $response['tokenAttachment'] = $encodedToken;

                            $token = ['cid' => $companyId, 'id' => $employeeId,'a' => $docApprovalId,'b' =>  $getForm->application_id,'c' => $getForm->comparison_id,'d' => null, 'g'=>$getForm->order_group_id];
                            $encodedToken = SafeToken::encode($token);
                            $response['tokenForm'] = $encodedToken;

                            $messageFooter = '';
                            if($getHeader->form_status_id == '12') {
                                $getOrderForm = $modelProcurement->documentApprovalDetailOrder()
                                                            ->select('doc_approval_id')
                                                            ->where('order_form_id', $getForm->order_form_id)
                                                            ->where('is_active', '1')
                                                            ->first();

                                $tokenReviseOrderForm = ['cid' => $companyId,'id' => $employeeId,'a' => $getOrderForm->doc_approval_id,'b' => $getForm->order_form_id,'c'=>$getHeader->doc_approval_id,'d'=>$getForm->application_id,'g'=> $getForm->order_group_id];
                                $tokenReviseOrderForm = SafeToken::encode($tokenReviseOrderForm);

                                $actionButton = '<div class="col-sm-12 col-md-3 d-flex justify-content-center justify-content-md-end mb-2 px-0">
                                                    <button type="button" class="btn btn-info w-100 w-md-auto me-md-2 actionBtn" data-type="PROGRESS"  data-doc="'.$getHeader->doc_type_id.'" data-form="APPLICATION_FORM" data-token="'.$encodedToken.'">
                                                        <i class="fa-regular fa-circle-info"></i> View Progress
                                                    </button>
                                                </div>
                                                <div class="col-sm-12 col-md-3 d-flex justify-content-center justify-content-md-end mb-2 px-0">
                                                    <button type="button" class="btn btn-secondary w-100 w-md-auto me-md-2 actionBtn" data-type="REVISE" data-doc="'.$getHeader->doc_type_id.'" data-no="'.$getForm->application_number.'" data-form="APPLICATION_FORM" data-token="'.$encodedToken.'">
                                                        <i class="fa-regular fa-pen-to-square"></i> Revise Application Form
                                                    </button>
                                                </div>
                                                <div class="col-sm-12 col-md-3 d-flex justify-content-center justify-content-md-end mb-2 px-0">
                                                    <button type="button" class="btn btn-warning w-100 w-md-auto me-md-2 actionBtn" data-type="SEND_BACK" data-form="ORDER_FORM" data-token="'.$tokenReviseOrderForm.'">
                                                        <i class="fa-solid fa-arrow-turn-left"></i> Send Back to User
                                                    </button>
                                                </div>';
                            }
                            else {
                                $actionButton = '<div class="col-sm-12 col-md-3 d-flex justify-content-center justify-content-md-end mb-2 px-0">
                                                        <button type="button" class="btn btn-info w-100 w-md-auto me-md-2 actionBtn" data-type="PROGRESS"  data-doc="'.$getHeader->doc_type_id.'" data-form="APPLICATION_FORM" data-token="'.$encodedToken.'">
                                                            <i class="fa-regular fa-circle-info"></i> View Progress
                                                        </button>
                                                    </div>
                                                    <div class="col-sm-12 col-md-3 d-flex justify-content-center justify-content-md-end mb-2 px-0">
                                                        <button type="button" class="btn btn-danger w-100 w-md-auto me-md-2 actionBtn" data-type="ARCHIVE" data-doc="'.$getHeader->doc_type_id.'" data-form="APPLICATION_FORM" data-token="'.$encodedToken.'">
                                                            <i class="fa-regular fa-cabinet-filing"></i> Move to Archive
                                                        </button>
                                                    </div>';
                                $messageFooter = '<div class="alert alert-danger m-0 w-100 p-2 mb-2" role="alert">This form has been <span class="fw-semibold">FULLY REJECTED</span>, Please click Move to Archive Button</div>';
                            }

                            $response['footerButton'] = '<div class="container-fluid p-0">
                                                                    '.$messageFooter.'
                                                                    <div class="row justify-content-center w-100 mx-0">
                                                                        <div class="col-sm-12 col-md-3 d-flex justify-content-center justify-content-md-end mb-2 px-0">
                                                                            <button type="button" class="btn btn-default w-100 w-md-auto me-md-2" data-coreui-dismiss="modal" title="Close">
                                                                                <i class="fas fa-xmark"></i> Close
                                                                            </button>
                                                                        </div>
                                                                        '.$actionButton.'
                                                                    </div>
                                                                </div>';
                        }
                        else if($getForm->application_form_status == '5') { // 5 = NEED REVISE
                            $getOrderForm = $modelProcurement->documentApprovalDetailOrder()
                                                            ->select('doc_approval_id')
                                                            ->where('order_form_id', $getForm->order_form_id)
                                                            ->where('is_active', '1')
                                                            ->first();

                            $tokenReviseOrderForm = ['cid' => $companyId,'id' => $employeeId,'a' => $getOrderForm->doc_approval_id,'b' => $getForm->order_form_id,'c'=>$getHeader->doc_approval_id,'d'=>$getForm->application_id,'g'=> $getForm->order_group_id];
                            $tokenReviseOrderForm = SafeToken::encode($tokenReviseOrderForm);

                            $token = ['cid' => $companyId, 'id' => $employeeId,'a' => $docApprovalId,'b' => $referenceId,'c' => $getForm->comparison_id,'d' => null, 'g'=>$getForm->order_group_id];
                            $encodedToken = SafeToken::encode($token);
                            $response['tokenForm'] = $encodedToken;

                            $actionButton = '<div class="col-sm-12 col-md-3 d-flex justify-content-center justify-content-md-end mb-2 px-0">
                                                    <button type="button" class="btn btn-info w-100 w-md-auto me-md-2 actionBtn" data-type="PROGRESS"  data-doc="'.$getHeader->doc_type_id.'" data-form="APPLICATION_FORM" data-token="'.$encodedToken.'">
                                                        <i class="fa-regular fa-circle-info"></i> View Progress
                                                    </button>
                                                </div>
                                                <div class="col-sm-12 col-md-3 d-flex justify-content-center justify-content-md-end mb-2 px-0">
                                                    <button type="button" class="btn btn-warning w-100 w-md-auto me-md-2 actionBtn" data-type="SEND_BACK" data-form="ORDER_FORM" data-token="'.$tokenReviseOrderForm.'">
                                                        <i class="fa-solid fa-arrow-turn-left"></i> Send Back to User
                                                    </button>
                                                </div>
                                                <div class="col-sm-12 col-md-3 d-flex justify-content-center justify-content-md-end mb-2 px-0">
                                                    <button type="button" class="btn btn-secondary w-100 w-md-auto me-md-2 actionBtn" data-type="REVISE" data-doc="'.$getHeader->doc_type_id.'" data-form="APPLICATION_FORM" data-token="'.$encodedToken.'">
                                                        <i class="fa-regular fa-pen-to-square"></i> Revise Application Form
                                                    </button>
                                                </div>';

                            $response['footerButton'] = '<div class="container-fluid p-0">
                                                            <div class="alert alert-danger m-0 w-100 p-2 mb-2" role="alert">This form has been <span class="fw-semibold">NEED REVISE</span></div>
                                                            <div class="row justify-content-center w-100 mx-0">
                                                                <div class="col-sm-12 col-md-3 d-flex justify-content-center justify-content-md-end mb-2 px-0">
                                                                    <button type="button" class="btn btn-default w-100 w-md-auto me-md-2" data-coreui-dismiss="modal" title="Close">
                                                                        <i class="fas fa-xmark"></i> Close
                                                                    </button>
                                                                </div>
                                                                '.$actionButton.'
                                                            </div>
                                                        </div>';
                        }
                        else if($getForm->application_form_status == '8' || $getForm->application_form_status == '24' || $getForm->application_form_status == '21') { // 8 = APPROVED, 24 = OPEN ORDER, 21 = GOODS RECEIVED
                            $token = ['cid' => $companyId, 'id' => $employeeId,'a' => $docApprovalId,'b' => $referenceId,'c' => $getForm->comparison_id,'d' => null, 'g'=>$getForm->order_group_id];
                            $encodedToken = SafeToken::encode($token);
                            $response['tokenForm'] = $encodedToken;

                            $nextStepButton = '';
                            if($getForm->application_form_status == '8') {
                                $nextStepButton = '<div class="col-sm-12 col-md-3 d-flex justify-content-center justify-content-md-end mb-2 px-0">
                                                <button type="button" class="btn btn-secondary w-100 w-md-auto me-md-2 actionBtn" data-type="OPEN_ORDER" data-form="APPLICATION_FORM" data-token="'.$encodedToken.'">
                                                    <i class="fa-regular fa-cart-shopping"></i> Set Open Order
                                                </button>
                                            </div>';
                            }
                            else if($getForm->application_form_status == '24') {
                                $nextStepButton = '<div class="col-sm-12 col-md-3 d-flex justify-content-center justify-content-md-end mb-2 px-0">
                                                <button type="button" class="btn btn-secondary w-100 w-md-auto me-md-2 actionBtn" data-type="GOODS_RECEIVED" data-form="APPLICATION_FORM" data-token="'.$encodedToken.'">
                                                    <i class="fa-regular fa-cube"></i> Goods Received
                                                </button>
                                            </div>';
                            }
                            else if($getForm->application_form_status == '21') {
                                $nextStepButton = '<div class="col-sm-12 col-md-3 d-flex justify-content-center justify-content-md-end mb-2 px-0">
                                                <button type="button" class="btn btn-secondary w-100 w-md-auto me-md-2 actionBtn" data-type="INVOICED" data-form="APPLICATION_FORM" data-token="'.$encodedToken.'">
                                                    <i class="fa-regular fa-receipt"></i> Invoiced Purchase
                                                </button>
                                            </div>';
                            }

                            $actionButton = '<div class="col-sm-12 col-md-3 d-flex justify-content-center justify-content-md-end mb-2 px-0">
                                                <button type="button" class="btn btn-info w-100 w-md-auto me-md-2 actionBtn" data-type="PROGRESS"  data-doc="'.$getHeader->doc_type_id.'" data-form="APPLICATION_FORM" data-token="'.$encodedToken.'">
                                                    <i class="fa-regular fa-circle-info"></i> View Progress
                                                </button>
                                            </div>
                                            <div class="col-sm-12 col-md-3 d-flex justify-content-center justify-content-md-end mb-2 px-0">
                                                <button type="button" class="btn btn-danger w-100 w-md-auto me-md-2 actionBtn" data-type="REVISE" data-doc="'.$getHeader->doc_type_id.'" data-no="'.$getForm->application_number.'" data-form="APPLICATION_FORM" data-token="'.$encodedToken.'"><i class="fa-regular fa-pen-to-square"></i> Revise Application Form
                                                </button>
                                            </div>'.$nextStepButton;
                                            // <div class="col-sm-12 col-md-3 d-flex justify-content-center justify-content-md-end mb-2 px-0">
                                            //     <button type="button" class="btn btn-secondary w-100 w-md-auto me-md-2 nextNewForm" data-type="NEW" data-form="INSPECTION_FORM" data-token="'.$encodedToken.'">
                                            //         <i class="fa-regular fa-list-ol"></i> Create Inspection Form
                                            //     </button>
                                            // </div>';

                            $response['footerButton'] = '<div class="container-fluid p-0">
                                        <div class="row justify-content-center w-100 mx-0">
                                            <div class="col-sm-12 col-md-3 d-flex justify-content-center justify-content-md-end mb-2 px-0">
                                                <button type="button" class="btn btn-default w-100 w-md-auto me-md-2" data-coreui-dismiss="modal" title="Close">
                                                    <i class="fas fa-xmark"></i> Close
                                                </button>
                                            </div>
                                            '.$actionButton.'
                                        </div>
                                    </div>';
                        }
                        else if($getForm->application_form_status == '22') { // 22 = INVOICED
                            $token = ['cid' => $companyId, 'id' => $employeeId,'a' => $docApprovalId,'b' => $referenceId,'c' => $getForm->comparison_id,'d' => null, 'g'=>$getForm->order_group_id];
                            $encodedToken = SafeToken::encode($token);
                            $response['tokenForm'] = $encodedToken;

                            $actionButton = '<div class="col-sm-12 col-md-3 d-flex justify-content-center justify-content-md-end mb-2 px-0">
                                                <button type="button" class="btn btn-info w-100 w-md-auto me-md-2 actionBtn" data-type="PROGRESS"  data-doc="'.$getHeader->doc_type_id.'" data-form="APPLICATION_FORM" data-token="'.$encodedToken.'">
                                                    <i class="fa-regular fa-circle-info"></i> View Progress
                                                </button>
                                            </div>';

                            $response['footerButton'] = '<div class="container-fluid p-0">
                                        <div class="row justify-content-center w-100 mx-0">
                                            <div class="col-sm-12 col-md-3 d-flex justify-content-center justify-content-md-end mb-2 px-0">
                                                <button type="button" class="btn btn-default w-100 w-md-auto me-md-2" data-coreui-dismiss="modal" title="Close">
                                                    <i class="fas fa-xmark"></i> Close
                                                </button>
                                            </div>
                                            '.$actionButton.'
                                        </div>
                                    </div>';
                        }
                    }
                }
                else if($form == 'APPLICATION_FORM_COMPLETED') {
                    if($getForm->doc_version > 1) {
                        $token = ['cid' => $companyId,'id' => $employeeId,'a' => $getHeader->doc_approval_id,'b' => $getForm->application_id,'c' => $getForm->comparison_id,'d' => null, 'g'=>$getForm->order_group_id];
                        $encodedTokenLatest = SafeToken::encode($token);
                        $response['tokenAttachment'] = $encodedTokenLatest;

                        $getAllVersion = $modelProcurement->docApprovalOrderApplication()
                                            ->from('doc_approval_order_application as a')
                                            ->leftJoin('doc_approval_order_group as b', function($join) {
                                                $join->on('b.application_id', 'a.application_id')
                                                    ->where('b.is_active', '1');
                                            })
                                            ->select('a.doc_approval_id', 'a.doc_version', 'a.application_id', 'a.comparison_id', 'b.order_group_id')
                                            ->where('a.doc_approval_id', $getHeader->doc_approval_id)
                                            ->where('a.application_id', '<>', $getForm->application_id)
                                            ->where('a.is_active', '1')
                                            ->orderBy('a.doc_version', 'desc')
                                            ->orderBy('a.application_id', 'desc')
                                            ->get();
                        $versionList = '';
                        foreach ($getAllVersion as $rowAllVersion) {
                            $token = ['cid' => $companyId,'id' => $employeeId,'a' => $getHeader->doc_approval_id, 'b'=>$rowAllVersion->application_id, 'c'=>$rowAllVersion->comparison_id, 'd' => null, 'g'=>$rowAllVersion->order_group_id];
                            $encodedToken = SafeToken::encode($token);

                            // $versionLabel = ($rowAllVersion->doc_version - 1 > 0) ? 'Rev. 0'.$rowAllVersion->doc_version - 1 : 'Old Version' ;
                            $versionLabel = 'Ver. '.$rowAllVersion->doc_version;
                            $versionList .= '<li><span class="dropdown-item docVersionItem" data-token="'.$encodedToken.'" data-form="APPLICATION_FORM">'.$versionLabel.'</span></li>';
                        }

                        $selectedVersionLabel = 'Ver. '.$getForm->doc_version.' (Latest)';
                        $response['selectedVersionLabel'] = $selectedVersionLabel;
                        $response['title'] = $getHeader->doc_name.' <div class="dropdown dropdownDocVersion">
                                                    <button type="button" class="btn btn-default dropdown-toggle py-1 px-2" data-bs-boundary="viewport" id="dropdownDocVersion" data-coreui-toggle="dropdown" aria-expanded="false" title="Document Version">
                                                        Ver. '.$getForm->doc_version.' (Latest)
                                                    </button>
                                                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdownDocVersion" style="">
                                                        <li><span class="dropdown-item docVersionItem active" data-token="'.$encodedTokenLatest.'" data-form="APPLICATION_FORM">'.$selectedVersionLabel.'</span></li>
                                                        '.$versionList.'
                                                    </ul>
                                                </div>';

                        $encodedToken = $encodedTokenLatest;
                    }
                    else {
                        $token = ['cid' => $companyId, 'id' => $employeeId,'a' => $getHeader->doc_approval_id,'b' => $getForm->application_id, 'c' => $getForm->comparison_id,'d' => null, 'g'=> $getForm->order_group_id];
                        $encodedToken = SafeToken::encode($token);
                        $response['tokenAttachment'] = $encodedToken;

                        $response['selectedVersionLabel'] = '';
                        $response['title'] = $getHeader->doc_name;
                    }

                    if($getForm->application_form_status == '12' || $getForm->application_form_status == '9') { // 12 = CANCELED, 9 = REJECTED
                        $token = ['cid' => $companyId, 'id' => $employeeId,'a' => $docApprovalId,'b' => $getForm->application_id, 'c' => $getForm->comparison_id,'d' => null, 'g'=> $getForm->order_group_id];
                        $encodedToken = SafeToken::encode($token);
                        $response['tokenAttachment'] = $encodedToken;

                        $token = ['cid' => $companyId, 'id' => $employeeId,'a' => $docApprovalId,'b' =>  $getForm->application_id,'c' => $getForm->comparison_id,'d' => null, 'g'=>$getForm->order_group_id];
                        $encodedToken = SafeToken::encode($token);
                        $response['tokenForm'] = $encodedToken;

                        $messageFooter = '';
                        if($getHeader->form_status_id == '12') {
                            $actionButton = '<div class="col-sm-12 col-md-2 d-flex justify-content-center justify-content-md-end mb-2 px-0">
                                                <button type="button" class="btn btn-default w-100 w-md-auto me-md-2" data-coreui-dismiss="modal" title="Close">
                                                    <i class="fas fa-xmark"></i> Close
                                                </button>
                                            </div>
                                            <div class="col-sm-12 col-md-2 d-flex justify-content-center justify-content-md-end mb-2 px-0">
                                                <button type="button" class="btn btn-info w-100 w-md-auto me-md-2 actionBtn" data-type="PROGRESS"  data-doc="'.$getHeader->doc_type_id.'" data-form="APPLICATION_FORM" data-token="'.$encodedToken.'">
                                                    <i class="fa-regular fa-circle-info"></i> View Progress
                                                </button>
                                            </div>
                                            <div class="col-sm-12 col-md-2 d-flex justify-content-center justify-content-md-end mb-2 px-0">
                                                <button type="button" class="btn btn-warning w-100 w-md-auto me-md-2 actionBtn" data-type="SEND_BACK" data-doc="'.$getHeader->doc_type_id.'" data-no="'.$getForm->application_number.'" data-form="APPLICATION_FORM" data-token="'.$encodedToken.'">
                                                    <i class="fa-solid fa-arrow-turn-left"></i> Send Back to User
                                                </button>
                                            </div>
                                            <div class="col-sm-12 col-md-3 d-flex justify-content-center justify-content-md-end mb-2 px-0">
                                                <button type="button" class="btn btn-secondary w-100 w-md-auto me-md-2 actionBtn" data-type="REVISE" data-doc="'.$getHeader->doc_type_id.'" data-no="'.$getForm->application_number.'" data-form="APPLICATION_FORM" data-token="'.$encodedToken.'">
                                                    <i class="fa-regular fa-pen-to-square"></i> Revise Application Form
                                                </button>
                                            </div>
                                            <div class="col-sm-12 col-md-2 d-flex justify-content-center justify-content-md-end mb-2 px-0">
                                                <button type="button" class="btn btn-danger w-100 w-md-auto me-md-2 actionBtn" data-type="ARCHIVE" data-doc="'.$getHeader->doc_type_id.'" data-form="APPLICATION_FORM" data-token="'.$encodedToken.'">
                                                    <i class="fa-regular fa-cabinet-filing"></i> Move to Archive
                                                </button>
                                            </div>';
                        }
                        else {
                            $actionButton = '<div class="col-sm-12 col-md-3 d-flex justify-content-center justify-content-md-end mb-2 px-0">
                                                <button type="button" class="btn btn-default w-100 w-md-auto me-md-2" data-coreui-dismiss="modal" title="Close">
                                                    <i class="fas fa-xmark"></i> Close
                                                </button>
                                            </div>
                                            <div class="col-sm-12 col-md-3 d-flex justify-content-center justify-content-md-end mb-2 px-0">
                                                <button type="button" class="btn btn-info w-100 w-md-auto me-md-2 actionBtn" data-type="PROGRESS"  data-doc="'.$getHeader->doc_type_id.'" data-form="APPLICATION_FORM" data-token="'.$encodedToken.'">
                                                    <i class="fa-regular fa-circle-info"></i> View Progress
                                                </button>
                                            </div>
                                            <div class="col-sm-12 col-md-3 d-flex justify-content-center justify-content-md-end mb-2 px-0">
                                                <button type="button" class="btn btn-danger w-100 w-md-auto me-md-2 actionBtn" data-type="ARCHIVE" data-doc="'.$getHeader->doc_type_id.'" data-form="APPLICATION_FORM" data-token="'.$encodedToken.'">
                                                    <i class="fa-regular fa-cabinet-filing"></i> Move to Archive
                                                </button>
                                            </div>';
                            $messageFooter = '<div class="alert alert-danger m-0 w-100 p-2 mb-2" role="alert">This form has been <span class="fw-semibold">FULLY REJECTED</span>, Please click Move to Archive Button</div>';
                        }

                        $response['footerButton'] = '<div class="container-fluid p-0">
                                                        '.$messageFooter.'
                                                        <div class="row justify-content-center w-100 mx-0">
                                                            '.$actionButton.'
                                                        </div>
                                                    </div>';
                    }
                    else if($getForm->application_form_status == '5') { // 5 = NEED REVISE
                        $getOrderForm = $modelProcurement->documentApprovalDetailOrder()
                                                        ->select('doc_approval_id')
                                                        ->where('order_form_id', $getForm->order_form_id)
                                                        ->where('is_active', '1')
                                                        ->first();

                        $tokenReviseOrderForm = ['cid' => $companyId,'id' => $employeeId,'a' => $getOrderForm->doc_approval_id,'b' => $getForm->order_form_id,'c'=>$getHeader->doc_approval_id,'d'=>$getForm->application_id,'g'=> $getForm->order_group_id];
                        $tokenReviseOrderForm = SafeToken::encode($tokenReviseOrderForm);

                        $token = ['cid' => $companyId, 'id' => $employeeId,'a' => $docApprovalId,'b' => $referenceId,'c' => $getForm->comparison_id,'d' => null, 'g'=>$getForm->order_group_id];
                        $encodedToken = SafeToken::encode($token);
                        $response['tokenForm'] = $encodedToken;

                        $actionButton = '<div class="col-sm-12 col-md-2 d-flex justify-content-center justify-content-md-end mb-2 px-0">
                                                <button type="button" class="btn btn-default w-100 w-md-auto me-md-2" data-coreui-dismiss="modal" title="Close">
                                                    <i class="fas fa-xmark"></i> Close
                                                </button>
                                            </div>
                                            <div class="col-sm-12 col-md-2 d-flex justify-content-center justify-content-md-end mb-2 px-0">
                                                <button type="button" class="btn btn-info w-100 w-md-auto me-md-2 actionBtn" data-type="PROGRESS"  data-doc="'.$getHeader->doc_type_id.'" data-form="APPLICATION_FORM" data-token="'.$encodedToken.'">
                                                    <i class="fa-regular fa-circle-info"></i> View Progress
                                                </button>
                                            </div>
                                            <div class="col-sm-12 col-md-2 d-flex justify-content-center justify-content-md-end mb-2 px-0">
                                                <button type="button" class="btn btn-warning w-100 w-md-auto me-md-2 actionBtn" data-type="SEND_BACK" data-form="ORDER_FORM" data-token="'.$tokenReviseOrderForm.'">
                                                    <i class="fa-solid fa-arrow-turn-left"></i> Send Back to User
                                                </button>
                                            </div>
                                            <div class="col-sm-12 col-md-3 d-flex justify-content-center justify-content-md-end mb-2 px-0">
                                                <button type="button" class="btn btn-secondary w-100 w-md-auto me-md-2 actionBtn" data-type="REVISE" data-doc="'.$getHeader->doc_type_id.'" data-no="'.$getForm->application_number.'" data-form="APPLICATION_FORM" data-token="'.$encodedToken.'">
                                                    <i class="fa-regular fa-pen-to-square"></i> Revise Application Form
                                                </button>
                                            </div>
                                            <div class="col-sm-12 col-md-3 d-flex justify-content-center justify-content-md-end mb-2 px-0">
                                                <button type="button" class="btn btn-danger w-100 w-md-auto me-md-2 actionBtn" data-type="CANCEL_TO_ARCHIVE" data-doc="'.$getHeader->doc_type_id.'" data-form="APPLICATION_FORM" data-token="'.$encodedToken.'">
                                                    <i class="fa-regular fa-cabinet-filing"></i> Cancel & Move to Archive
                                                </button>
                                            </div>';

                        $response['footerButton'] = '<div class="container-fluid p-0">
                                                        <div class="alert alert-danger m-0 w-100 p-2 mb-2" role="alert">This form has been <span class="fw-semibold">NEED REVISE</span></div>
                                                        <div class="row justify-content-center w-100 mx-0">
                                                            '.$actionButton.'
                                                        </div>
                                                    </div>';
                    }
                    else if($getForm->application_form_status == '8' || $getForm->application_form_status == '24' || $getForm->application_form_status == '21') { // 8 = APPROVED, 24 = OPEN ORDER, 21 = GOODS RECEIVED
                        $token = ['cid' => $companyId, 'id' => $employeeId,'a' => $docApprovalId,'b' => $referenceId,'c' => $getForm->comparison_id,'d' => null, 'g'=>$getForm->order_group_id];
                        $encodedToken = SafeToken::encode($token);
                        $response['tokenForm'] = $encodedToken;

                        $nextStepButton = '';
                        if($getForm->application_form_status == '8') {
                            $nextStepButton = '<div class="col-sm-12 col-md-3 d-flex justify-content-center justify-content-md-end mb-2 px-0">
                                            <button type="button" class="btn btn-secondary w-100 w-md-auto me-md-2 actionBtn" data-type="OPEN_ORDER" data-form="APPLICATION_FORM" data-token="'.$encodedToken.'">
                                                <i class="fa-regular fa-cart-shopping"></i> Set Open Order
                                            </button>
                                        </div>';
                        }
                        else if($getForm->application_form_status == '24') {
                            $nextStepButton = '<div class="col-sm-12 col-md-3 d-flex justify-content-center justify-content-md-end mb-2 px-0">
                                            <button type="button" class="btn btn-secondary w-100 w-md-auto me-md-2 actionBtn" data-type="GOODS_RECEIVED" data-form="APPLICATION_FORM" data-token="'.$encodedToken.'">
                                                <i class="fa-regular fa-cube"></i> Goods Received
                                            </button>
                                        </div>';
                        }
                        else if($getForm->application_form_status == '21') {
                            $nextStepButton = '<div class="col-sm-12 col-md-3 d-flex justify-content-center justify-content-md-end mb-2 px-0">
                                            <button type="button" class="btn btn-secondary w-100 w-md-auto me-md-2 actionBtn" data-type="INVOICED" data-form="APPLICATION_FORM" data-token="'.$encodedToken.'">
                                                <i class="fa-regular fa-receipt"></i> Invoiced Purchase
                                            </button>
                                        </div>';
                        }

                        $actionButton = '<div class="col-sm-12 col-md-3 d-flex justify-content-center justify-content-md-end mb-2 px-0">
                                            <button type="button" class="btn btn-info w-100 w-md-auto me-md-2 actionBtn" data-type="PROGRESS"  data-doc="'.$getHeader->doc_type_id.'" data-form="APPLICATION_FORM" data-token="'.$encodedToken.'">
                                                <i class="fa-regular fa-circle-info"></i> View Progress
                                            </button>
                                        </div>
                                        <div class="col-sm-12 col-md-3 d-flex justify-content-center justify-content-md-end mb-2 px-0">
                                            <button type="button" class="btn btn-danger w-100 w-md-auto me-md-2 actionBtn" data-type="REVISE" data-doc="'.$getHeader->doc_type_id.'" data-no="'.$getForm->application_number.'" data-form="APPLICATION_FORM" data-token="'.$encodedToken.'"><i class="fa-regular fa-pen-to-square"></i> Revise Application Form
                                            </button>
                                        </div>'.$nextStepButton;
                                        // <div class="col-sm-12 col-md-3 d-flex justify-content-center justify-content-md-end mb-2 px-0">
                                        //     <button type="button" class="btn btn-secondary w-100 w-md-auto me-md-2 nextNewForm" data-type="NEW" data-form="INSPECTION_FORM" data-token="'.$encodedToken.'">
                                        //         <i class="fa-regular fa-list-ol"></i> Create Inspection Form
                                        //     </button>
                                        // </div>';

                        $response['footerButton'] = '<div class="container-fluid p-0">
                                    <div class="row justify-content-center w-100 mx-0">
                                        <div class="col-sm-12 col-md-3 d-flex justify-content-center justify-content-md-end mb-2 px-0">
                                            <button type="button" class="btn btn-default w-100 w-md-auto me-md-2" data-coreui-dismiss="modal" title="Close">
                                                <i class="fas fa-xmark"></i> Close
                                            </button>
                                        </div>
                                        '.$actionButton.'
                                    </div>
                                </div>';
                    }
                }
                else if($form == 'APPLICATION_DATA') {
                    $token = ['cid' => $companyId, 'id' => $employeeId,'a' => $getHeader->doc_approval_id,'b' => $getForm->application_id, 'c' => null,'d' => null, 'g'=> $getForm->order_group_id, 't'=> 'PO_FORM_FILE'];
                    $encodedToken = SafeToken::encode($token);
                    $response['tokenAttachment'] = $encodedToken;
                    $response['header'] = $getForm->application_header;
                    $response['poNumber'] = $getForm->application_number;
                    $response['applicationTitle'] = $getForm->application_title;
                    $response['applicationGrandTotal'] = $this->formatNumber($getForm->application_grand_total).' '.$getForm->application_currency;
                    $response['currency'] = $getForm->application_currency;
                    $response['vendorPicId'] = $getForm->vendor_pic_id;
                    $response['vendorPicName'] = $getForm->vendor_pic;
                    $response['vendorPicEmail'] = '';
                    $response['vendorName'] = $getForm->vendor_name;
                    $response['deliveryToName'] = $getForm->company_name;
                    $response['applicationReason'] = $getForm->application_reason;

                    $getVendorPic = $modelProcurement->masterVendorPic()
                                                ->from('master_vendor_pic')
                                                ->select('pic_email',)
                                                ->where('vendor_pic_id', $getForm->vendor_pic_id)
                                                ->where('vendor_id', $getForm->vendor_id)
                                                ->first();
                    if($getVendorPic) {
                        $response['vendorPicEmail'] = $getVendorPic->pic_email ? $getVendorPic->pic_email : '';
                    }

                    $arrItem = [];
                    $getFormItem = $modelProcurement->docApprovalOrderApplicationItem()
                                                    ->from('doc_approval_order_application_item')
                                                    ->select('appliance_item', 'quantity', 'currency', 'unit_price', 'subtotal_price')
                                                    ->where('company_id', $companyId)
                                                    ->where('application_id', $getForm->application_id)
                                                    ->where('is_active', '1')
                                                    ->orderBy('application_item_id', 'asc')
                                                    ->get();
                    foreach ($getFormItem as $rowFormItem) {
                        $arrItem[] = [
                                        'item' => $rowFormItem->appliance_item,
                                        'quantity' => $rowFormItem->quantity,
                                    ];
                    }

                    $response->put('items', $arrItem);
                }
            }

            return response()->json([
                'status' => 200,
                'message' => 'Success',
                'data' => $response,
            ], 200);
        }
        else{
            $response['form'] = view('procurementpurchasing::form.view_disallowed')->render();
            return response()->json([
                'status' => 404,
                'message' => 'Not found',
                'data' => $response,
            ], 200);
        }
    }

    public function newForm(Request $request) {
        $token = SafeToken::decode($request->token);
        $validator = Validator::make($token, [
            'id' => 'required',
            'a' => 'required',
            'b' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => 'Invalid token form', 'errors' => $validator->errors()], 422)
                            ->setStatusCode(422, 'Invalid token form');
        }

        $type = $request->type;
        $form = $request->form;
        $docApprovalId = $token['a'];
        $referenceId = $token['b'];
        $comparisonId = $token['c'];
        $groupId = array_key_exists('g', $token) ? $token['g'] : null;
        $orderFormId = '';
        // $applicationId = $token['d'];

        // dd($token);
        $employeeId = $this->employeeId;
        $companyIdMain = $this->companyId;
        $employeeName = $this->employeeName;
        $roleId = $this->roleId;

        $response = collect();
        $dataForm = collect();
        $modelApproval = new DocumentApprovalModel();
        $modelProcurement = new ProcurementPurchasingModel();

        // $getEmployee = $modelProcurement->vwMasterEmployeeActive()
        //                                 ->from('vw_master_employee_active as a')
        //                                 ->leftJoin('master_employees_multi_company as b', function($join) {
        //                                     $join->on('b.employee_id', 'a.employee_id')
        //                                         ->where('b.is_active', '1');
        //                                 })
        //                                 ->select('a.employee_id', 'a.employee_name', 'a.employee_email', 'a.position_name', 'a.company_id', 'b.company_id as multiple_company_id')
        //                                 ->where('a.employee_id', $employeeId)
        //                                 ->where('a.is_active', '1')
        //                                 ->get();

        if($form != 'PO_INSPECTION') {
            $companyIdList = array($companyIdMain);
            $getEmployee = $modelProcurement->vwMasterEmployeeActive()
                                            ->from('vw_master_employee_active as a')
                                            ->leftJoin('master_employees_multi_company as b', function($join) {
                                                $join->on('b.employee_id', 'a.employee_id')
                                                    ->where('b.is_active', '1');
                                            })
                                            ->select('a.employee_id', 'a.employee_name', 'a.employee_email', 'a.position_name', 'a.company_id', 'b.company_id as multiple_company_id')
                                            ->where('a.employee_id', $employeeId)
                                            ->where('a.is_active', '1')
                                            ->get();
            foreach ($getEmployee as $rowEmployee) {
                if($rowEmployee->multiple_company_id && $rowEmployee->multiple_company_id != $companyIdMain) {
                    $companyIdList[] = $rowEmployee->company_id;
                }
            }
        }

        // if(($type == 'REVISE' || $type == 'REVISE_COMPARISON_REVISE_APPLICATION' || $type == 'OLD_COMPARISON_REVISE_APPLICATION' || $type == 'DELETE_COMPARISON_REVISE_APPLICATION') && $form == 'APPLICATION_FORM') {
        //     $getForm = $modelApproval->docApprovalOrderApplication()
        //                             ->from('doc_approval_order_application')
        //                             ->select('year', 'order_form_id')
        //                             ->where('company_id', $companyId)
        //                             ->where('doc_approval_id', $docApprovalId)
        //                             ->where('is_active', 1)
        //                             ->orderBy('application_id', 'desc')
        //                             ->first();
        //     $yearForm = $getForm->year;
        //     $orderFormId = $getForm->order_form_id;
        // }
        // else {
        //     // $getForm = $modelApproval->documentApprovalDetailOrder()
        //     //                         ->select('request_date')
        //     //                         ->where('doc_approval_id', $docApprovalId)
        //     //                         ->where('order_form_id', $referenceId)
        //     //                         ->where('is_active', 1)
        //     //                         ->first();
        //     // $yearForm = $getForm->request_date;

        //     $getForm = $modelApproval->documentApprovalHeader()
        //                             ->select('year')
        //                             ->where('company_id', $companyId)
        //                             ->where('doc_approval_id', $docApprovalId)
        //                             // ->where('order_form_id', $referenceId)
        //                             ->where('is_active', 1)
        //                             ->first();
        //     $yearForm = $getForm->year;
        // }

        $actionButton = '';
        $vendorIdSelected = null;
        if($form == 'COMPARISON_FORM' || $type == 'REVISE_COMPARISON_REVISE_APPLICATION') {
            $response['form'] = '<div class="row min-vh-75">
                                    <div class="d-flex justify-content-center align-items-center" style="flex: 1 1 auto">
                                        <div class="text-center">
                                            <i class="fa-light fa-file-circle-xmark fs-1"></i>
                                            <div class="d-block fs-7 mt-2">Your account is not allowed to access this form</div>
                                        </div>
                                    </div>
                                </div>';
            $response['footerButton'] = '<div class="container-fluid p-0">
                                            <div class="row justify-content-center w-100 mx-0">
                                                <div class="col-sm-12 col-md-4 d-flex justify-content-center justify-content-md-end mb-2 px-0">
                                                    <button type="button" class="btn btn-default w-100 w-md-auto me-md-2" data-coreui-dismiss="modal" title="Close">
                                                        <i class="fas fa-xmark"></i> Close
                                                    </button>
                                                </div>
                                            </div>
                                        </div>';

            if($type == 'REVISE_COMPARISON_REVISE_APPLICATION') {
                $getForm = $modelApproval->docApprovalOrderApplication()
                                        ->from('doc_approval_order_application')
                                        ->select('year', 'company_id', 'order_form_id', 'comparison_id')
                                        ->where('doc_approval_id', $docApprovalId)
                                        ->where('is_active', 1)
                                        ->orderBy('application_id', 'desc')
                                        ->first();
            }
            else if($form == 'COMPARISON_FORM') {
                $getForm = $modelApproval->documentApprovalDetailOrder()
                                        ->select('year', 'order_form_id', 'company_id')
                                        ->where('doc_approval_id', $docApprovalId)
                                        ->where('order_form_id', $referenceId)
                                        ->where('is_active', 1)
                                        ->latest('order_form_id')
                                        ->first();
            }

            if(!$getForm) {
                return response()->json([
                    'status' => 422,
                    'message' => 'Invalid token form',
                    'data' => $response,
                ], 422);
            }
            else if(!in_array($getForm->company_id, $companyIdList)) {
                return response()->json([
                    'status' => 404,
                    'message' => 'Your account is not allowed to access this form',
                    'data' => $response,
                ], 404);
            }

            $companyId = $getForm->company_id;
            $yearForm = $getForm->year;
            $orderFormId = $getForm->order_form_id;

            $response['title'] = 'Comparison Form';
            if($type == 'REVISE_COMPARISON_REVISE_APPLICATION') {
                $getApplicationForm = $modelProcurement->docApprovalOrderApplication()
                                                        ->from('doc_approval_order_application as a')
                                                        ->leftJoin('doc_approval_detail_order_form as b', function($join) {
                                                            $join->on('b.order_form_id', 'a.order_form_id')
                                                                ->where('b.is_active', '1');
                                                        })
                                                        ->select('b.doc_approval_id', 'b.order_form_id', 'a.comparison_id')
                                                        ->where('a.company_id', $companyId)
                                                        ->where('a.doc_approval_id', $docApprovalId)
                                                        ->where('a.application_id', $referenceId)
                                                        ->where('a.is_active', '1')
                                                        ->orderBy('a.application_id', 'desc')
                                                        ->first();

                $docApprovalId = $getApplicationForm->doc_approval_id;
                $referenceId = $getApplicationForm->order_form_id;
            }

            // GET LATEST ORDER FORM
            $getOrderFormLatest = $modelProcurement->documentApprovalDetailOrder()
                                                ->from('doc_approval_detail_order_form as a')
                                                ->select('a.doc_approval_id', 'a.order_form_id')
                                                ->where('a.company_id', $companyId)
                                                ->where('a.doc_approval_id', $docApprovalId)
                                                ->where('a.is_active', '1')
                                                ->orderBy('a.order_form_id', 'desc')
                                                ->first();

            if($referenceId != $getOrderFormLatest->order_form_id) {
                $getOrderFormLatest = $modelProcurement->documentApprovalDetailOrder()
                                                    ->from('doc_approval_detail_order_form as a')
                                                    ->select('a.doc_approval_id', 'a.order_form_id')
                                                    ->where('a.company_id', $companyId)
                                                    ->where('a.doc_approval_id', $docApprovalId)
                                                    ->where('a.order_form_id', '<>', $referenceId)
                                                    ->where('a.is_active', '1')
                                                    ->orderBy('a.order_form_id', 'desc')
                                                    ->first();
            }

            $latestOrderFormId = $getOrderFormLatest->order_form_id;

            $attachmentList = [];
            if($comparisonId == null) {
                // TOKEN FORM
                $token = ['cid' => $companyId, 'id' => $employeeId, 'a' => $docApprovalId,'b' => $referenceId,'c' => null,'d' => null];
                $encodedToken = SafeToken::encode($token);

                $tokenFormOrder = ['cid' => $companyId, 'id' => $employeeId, 'a' => $docApprovalId,'b' => $referenceId,'c' => null,'d' => null];
                $tokenFormOrder = SafeToken::encode($tokenFormOrder);

                $dataForm = $dataForm->merge([
                    'tokenForm' => $encodedToken,
                    'tokenFormOrder' => $tokenFormOrder,
                    'yearForm' => Carbon::parse($yearForm)->format('Y'),
                    'comparisonTitle' => '',
                    'comparisonDescription' => '',
                    'comparisonDate' => '',
                    // 'validity' => '',
                    'vendorIdSelected' => '',
                    'comparisonNotes' => '',
                    'type' => $type,
                    'attachmentList' => $attachmentList,
                ]);

                $arrVendor = [];
                if($type == 'REVISE' || $type == 'REVISE_COMPARISON_REVISE_APPLICATION') {
                    // $getFormItem = $modelApproval->documentApprovalDetailOrderItem()
                    //                             ->from('doc_approval_detail_order_form_item')
                    //                             ->select('order_detail_id', 'cost_center', 'appliance_item', 'brand_type', 'unit_name', 'unit_quantity', 'currency_code', 'unit_price_estimated', 'total_price_estimated', 'required_date')
                    //                             ->where('company_id', $companyId)
                    //                             ->where('doc_approval_id', $docApprovalId)
                    //                             ->where('order_form_id', $referenceId)
                    //                             ->where('unit_name', '<>', 'VAT')
                    //                             ->whereNull('canceled_id')
                    //                             ->where('is_active', '1')
                    //                             ->orderBy('order_detail_id', 'asc')
                    //                             ->get();

                    $getFormItem = $modelApproval->documentApprovalDetailOrderItem()
                                            ->from('doc_approval_detail_order_form_item as a')
                                            ->leftJoin('doc_approval_order_group as b', function($join) {
                                                $join->on('b.order_form_id', 'a.order_form_id')
                                                    ->where('b.is_active', '1')
                                                    ->orderBy('b.order_group_id', 'desc');
                                            })
                                            ->select('a.order_detail_id', 'a.revise_order_detail_id', 'a.cost_center', 'a.brand_type', 'a.unit_price_estimated', 'a.total_price_estimated', 'a.appliance_item', 'a.unit_name', 'a.unit_quantity', 'a.currency_code')
                                            ->where('a.company_id', $companyId)
                                            ->where('a.doc_approval_id', $docApprovalId)
                                            ->where('a.order_form_id', $latestOrderFormId)
                                            ->where('a.unit_name', '<>', 'VAT')
                                            ->whereNull('a.canceled_id')
                                            ->where(function($query) {
                                                $query->whereNull('a.comparison_id')
                                                    ->orWhere('b.is_need_revision', '1')
                                                    ->orWhere('b.is_canceled', '1')
                                                    ->orWhere('b.is_rejected', '1');
                                            })
                                            ->where('a.is_active', '1')
                                            ->orderBy('a.order_detail_id', 'asc')
                                            ->get();
                }
                else {
                    $getFormItem = $modelApproval->documentApprovalDetailOrderItem()
                                                ->from('doc_approval_detail_order_form_item')
                                                ->select('order_detail_id', 'revise_order_detail_id', 'cost_center', 'appliance_item', 'brand_type', 'unit_name', 'unit_quantity', 'currency_code', 'unit_price_estimated', 'total_price_estimated', 'required_date')
                                                ->where('company_id', $companyId)
                                                ->where('doc_approval_id', $docApprovalId)
                                                ->where('order_form_id', $referenceId)
                                                ->where('unit_name', '<>', 'VAT')
                                                ->whereNull('comparison_item_id')
                                                ->whereNull('application_id')
                                                ->whereNull('canceled_id')
                                                ->where('is_active', '1')
                                                ->orderBy('order_detail_id', 'asc')
                                                ->get();
                }

                $arrItem = $arrItemVendor = [];
                foreach ($getFormItem as $rowFormItem) {
                    $reviseOrderDetailId = '';
                    if($type == 'REVISE' || $type == 'REVISE_COMPARISON_REVISE_APPLICATION') {
                        $reviseOrderDetailId = $rowFormItem->revise_order_detail_id ?: $rowFormItem->order_detail_id;
                    }

                    $arrItem[] = [
                                    'comparisonItemId' => null,
                                    'orderDetailId' => $rowFormItem->order_detail_id,
                                    'reviseOrderDetailId' => $reviseOrderDetailId,
                                    'applianceItem' => $rowFormItem->appliance_item,
                                    'unitName' => $rowFormItem->unit_name,
                                    'unitQuantity' => $rowFormItem->unit_quantity,
                                    'currency' => $rowFormItem->currency_code == '%' ? 'IDR' : $rowFormItem->currency_code,
                                ];
                }

                if(count($arrItem) == 0) {
                    $response['form'] = '<div class="row min-vh-75">
                                            <div class="d-flex justify-content-center align-items-center" style="flex: 1 1 auto">
                                                <div class="text-center">
                                                    <i class="fa-light fa-file-circle-xmark fs-1"></i>
                                                    <div class="d-block fs-7 mt-2">No order item unprocessed</div>
                                                </div>
                                            </div>
                                        </div>';
                    $response['footerButton'] = '<div class="container-fluid p-0">
                                                    <div class="row justify-content-center w-100 mx-0">
                                                        <div class="col-sm-12 col-md-4 d-flex justify-content-center justify-content-md-end mb-2 px-0">
                                                            <button type="button" class="btn btn-default w-100 w-md-auto me-md-2" data-coreui-dismiss="modal" title="Close">
                                                                <i class="fas fa-xmark"></i> Close
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>';

                    return response()->json([
                        'status' => 404,
                        'message' => 'No order item unprocessed',
                        'data' => $response,
                    ], 404);
                }

                $currency = $getFormItem->pluck('currency_code')->unique();
                $applianceItem = ['Delivery Fee', 'Discount', 'Total Price', 'VAT', 'PPh', 'Total Price + VAT - PPh'];
                for($x = 0; $x < count($applianceItem); $x++) {
                    $arrItem[] = [
                        'comparisonItemId' => null,
                        'orderDetailId' => '',
                        'reviseOrderDetailId' => null,
                        'applianceItem' => $applianceItem[$x],
                        'unitName' => '',
                        'unitQuantity' => '',
                        'currency' => $currency == '%' ? 'IDR' : $currency,
                    ];
                }

                $dataForm->put('resetComparisonItem', true);
                $dataForm->put('documentType', '6');
                $dataForm->put('itemForm', $arrItem);
                $dataForm->put('arrVendor', $arrVendor);
                $dataForm->put('arrItemVendor', $arrItemVendor);

                // dd($dataForm);
                $response->put('form', view('procurementpurchasing::form.comparison_form.comparison_form', compact('dataForm'))->render());

                $actionButton = '<div class="col-sm-12 col-md-3 d-flex justify-content-center justify-content-md-end mb-2 px-0">
                                    <button type="button" class="btn btn-info w-100 w-md-auto me-md-2 submitForm" data-form="COMPARISON_FORM" data-token="" data-type="CREATE"><i class="fa-regular fa-file-import"></i> Submit Comparison Form</button>
                                </div>';
            }
            else {
                $getComparisonForm = $modelProcurement->docApprovalOrderComparison()
                                                ->select('*')
                                                ->where('company_id', $companyId)
                                                ->where('order_form_id', $referenceId)
                                                ->where('comparison_id', $comparisonId)
                                                ->where('is_active', '1')
                                                ->orderBy('comparison_id', 'desc')
                                                ->first();
                if($getComparisonForm) {
                    $resetComparisonItem = $getOrderFormLatest->order_form_id > $referenceId ? true : false;
                    $token = ['cid' => $companyId, 'id' => $employeeId, 'a' => $docApprovalId,'b' => $getOrderFormLatest->order_form_id,'c' => $comparisonId,'d' => null];
                    $encodedToken = SafeToken::encode($token);

                    $tokenFormOrder = ['cid' => $companyId, 'id' => $employeeId, 'a' => $docApprovalId,'b' => $getOrderFormLatest->order_form_id,'c' => null,'d' => null];
                    $tokenFormOrder = SafeToken::encode($tokenFormOrder);

                    $response['tokenForm'] = $encodedToken;
                    if($type == 'REVISE' || $type == 'REVISE_COMPARISON_REVISE_APPLICATION') {
                        $vendorIdSelected = $getComparisonForm->vendor_id_selected;

                        $getAttachment = $modelApproval->docApprovalAttachment()
                                            ->from('doc_approval_attachment')
                                            ->select('attachment_id', 'mime_type', 'filename', 'filename_original', 'filename_converted')
                                            ->where('doc_type_id', '6')
                                            ->where('company_id', $companyId)
                                            ->where('doc_approval_id', $docApprovalId)
                                            ->where('reference_id', $getComparisonForm->comparison_id)
                                            ->get();
                        foreach($getAttachment as $row) {
                            $tokenFile = ['a' => 'DOC_APPROVAL', 'b' => $row->attachment_id];
                            $tokenFile = SafeToken::encode($tokenFile);

                            $attachmentList[] = [
                                                    'tokenAttachment' => $tokenFile,
                                                    'icon' => $this->getFileIcon($row->mime_type, $row->filename_original),
                                                    'filename' => $row->filename_original,
                                                    'mimeType' => $row->mime_type,
                                                ];
                        }

                        $dataForm = $dataForm->merge([
                            'tokenForm' => $encodedToken,
                            'tokenFormOrder' => $tokenFormOrder,
                            'yearForm' => Carbon::parse($getComparisonForm->year_form)->format('Y'),
                            'comparisonTitle' => $getComparisonForm->comparison_title,
                            'comparisonDescription' => $getComparisonForm->comparison_description,
                            'comparisonDate' => $getComparisonForm->comparison_date,
                            'validity' => $getComparisonForm->validity,
                            'vendorIdSelected' => $vendorIdSelected,
                            'comparisonNotes' => $getComparisonForm->comparison_notes,
                            'type' => $type,
                            'attachmentList' => $attachmentList,
                        ]);

                        $getVendor = $modelApproval->docApprovalOrderComparisonVendor()
                                                    ->from('doc_approval_order_comparison_vendor')
                                                    ->select('comparison_vendor_id', 'vendor_id', 'vendor_name', 'vendor_pic_id', 'pic_name', 'vendor_address', 'quotation_validity')
                                                    ->where('company_id', $companyId)
                                                    ->where('order_form_id', $referenceId)
                                                    ->where('comparison_id', $getComparisonForm->comparison_id)
                                                    ->where('is_active', '1')
                                                    ->orderBy('comparison_vendor_id', 'asc')
                                                    ->get();
                        $arrVendor = [];
                        foreach ($getVendor as $rowVendor) {
                            $arrVendor[] = [
                                            'comparisonVendorId' => $rowVendor->comparison_vendor_id,
                                            'vendorId' => $rowVendor->vendor_id,
                                            'vendorName' => $rowVendor->vendor_name,
                                            'vendorPicId' => $rowVendor->vendor_pic_id,
                                            'vendorPic' => $rowVendor->pic_name,
                                            'vendorAddress' => $rowVendor->vendor_address,
                                        ];
                        }

                        $arrItem = $arrItemVendor = [];
                        if($resetComparisonItem == false) {
                            $getFormItemOthers = $modelApproval->documentApprovalDetailOrderItem()
                                                            ->from('doc_approval_detail_order_form_item as a')
                                                            ->leftJoin('doc_approval_order_group as b', function($join) {
                                                                $join->on('b.order_form_id', 'a.order_form_id')
                                                                    ->on('b.comparison_id', 'a.comparison_id')
                                                                    ->where('b.is_active', '1')
                                                                    ->orderBy('b.order_group_id', 'desc');
                                                            })
                                                            ->select('a.order_detail_id', 'a.revise_order_detail_id', 'a.appliance_item', 'a.unit_name', 'a.unit_quantity', 'a.currency_code')
                                                            ->where('a.company_id', $companyId)
                                                            ->where('a.doc_approval_id', $docApprovalId)
                                                            ->where('a.order_form_id', $latestOrderFormId)
                                                            ->where(function($query) use($comparisonId) {
                                                                $query->whereNull('a.comparison_id')
                                                                    ->orWhere(function($query) use($comparisonId) {
                                                                        $query->where('a.comparison_id', '<>', $comparisonId)
                                                                            ->where(function($query) {
                                                                                $query->orWhere('b.is_need_revision', '1')
                                                                                    ->orWhere('b.is_canceled', '1')
                                                                                    ->orWhere('b.is_rejected', '1');
                                                                            });
                                                                    });
                                                            })
                                                            ->where('a.unit_name', '<>', 'VAT')
                                                            ->whereNull('a.canceled_id')
                                                            ->where('a.is_active', '1')
                                                            ->orderBy('a.order_detail_id', 'asc')
                                                            ->get();

                            $getFormItem = $modelApproval->docApprovalOrderComparisonItem()
                                                        ->from('doc_approval_order_comparison_item as a')
                                                        ->leftJoin('doc_approval_detail_order_form_item as b', 'b.order_detail_id', 'a.order_detail_id')
                                                        ->select('a.comparison_item_id', 'a.order_detail_id', 'a.appliance_item', 'a.unit_name', 'a.unit_quantity', 'a.currency', 'b.revise_order_detail_id')
                                                        ->where('a.company_id', $companyId)
                                                        ->where('a.order_form_id', $referenceId)
                                                        ->where('a.comparison_id', $getComparisonForm->comparison_id)
                                                        ->where('a.is_active', '1')
                                                        ->orderBy('a.comparison_item_id', 'asc')
                                                        ->get();

                            $currency = $getFormItem->pluck('currency')->unique();
                            $insertedItemOthers = false;
                            $pphExist = false;
                            foreach ($getFormItem as $rowFormItem) {
                                if($rowFormItem->order_detail_id == null && $getFormItemOthers->isNotEmpty() && $insertedItemOthers == false) {
                                    foreach ($getFormItemOthers as $rowFormItemOthers) {
                                        $existingItems = array_filter($arrItem, function($item) use ($rowFormItemOthers) {
                                            return $item['orderDetailId'] == $rowFormItemOthers->order_detail_id;
                                        });

                                        if (empty($existingItems)) {
                                            $reviseOrderDetailId = '';
                                            if($type == 'REVISE' || $type == 'REVISE_COMPARISON_REVISE_APPLICATION') {
                                                $reviseOrderDetailId = $rowFormItemOthers->revise_order_detail_id ?: $rowFormItemOthers->order_detail_id;
                                            }

                                            if($rowFormItemOthers->appliance_item == 'Total Price + VAT') {
                                                $rowFormItemOthers->appliance_item = 'Total Price + VAT - PPh';
                                            }

                                            if($rowFormItemOthers->appliance_item == 'PPh') {
                                                $pphExist = true;
                                            }

                                            $arrItem[] = [
                                                'comparisonItemId' => null,
                                                'orderDetailId' => $rowFormItemOthers->order_detail_id,
                                                'reviseOrderDetailId' => $reviseOrderDetailId,
                                                'applianceItem' => $rowFormItemOthers->appliance_item,
                                                'unitName' => $rowFormItemOthers->unit_name,
                                                'unitQuantity' => $rowFormItemOthers->unit_quantity,
                                                'currency' => $rowFormItemOthers->currency_code,
                                            ];
                                        }
                                    }

                                    $insertedItemOthers = true;
                                }

                                $reviseOrderDetailId = '';
                                if($type == 'REVISE' || $type == 'REVISE_COMPARISON_REVISE_APPLICATION') {
                                    $reviseOrderDetailId = $rowFormItem->revise_order_detail_id ?: $rowFormItem->order_detail_id;
                                }

                                if($rowFormItem->appliance_item == 'Total Price + VAT') {
                                    $rowFormItem->appliance_item = 'Total Price + VAT - PPh';
                                }

                                if($rowFormItem->appliance_item == 'PPh') {
                                    $pphExist = true;
                                }

                                $arrItem[] = [
                                    'comparisonItemId' => $rowFormItem->comparison_item_id,
                                    'orderDetailId' => $rowFormItem->order_detail_id,
                                    'reviseOrderDetailId' => $reviseOrderDetailId,
                                    'applianceItem' => $rowFormItem->appliance_item,
                                    'unitName' => $rowFormItem->unit_name,
                                    'unitQuantity' => $rowFormItem->unit_quantity,
                                    'currency' => $rowFormItem->currency,
                                ];

                                $getFormItemVendor = $modelProcurement->docApprovalOrderComparisonItemVendor()
                                                    ->from('doc_approval_order_comparison_item_vendor as a')
                                                    ->select('a.comparison_vendor_id', 'a.unit_price', 'a.dpp_other_value', 'a.total_price')
                                                    ->where('a.company_id', $companyId)
                                                    ->where('a.comparison_id', $getComparisonForm->comparison_id)
                                                    ->where('a.comparison_item_id', $rowFormItem->comparison_item_id)
                                                    ->where('a.is_active', '1')
                                                    ->orderBy('a.comparison_item_vendor_id', 'asc')
                                                    ->get();
                                foreach ($getFormItemVendor as $rowItemVendor) {
                                    if($rowItemVendor->unit_price == 0.00 || $rowItemVendor->unit_price == null) {
                                        $unitPrice = '';
                                    }
                                    else if (fmod($rowItemVendor->unit_price, 1) === 0.00) {
                                        $unitPrice = number_format($rowItemVendor->unit_price, 0, '.', ',');
                                    }
                                    else {
                                        $unitPrice = number_format($rowItemVendor->unit_price, 2, '.', ',');
                                    }

                                    if($rowItemVendor->total_price == 0.00 || $rowItemVendor->total_price == null) {
                                        $totalPrice = '';
                                    }
                                    else if (fmod($rowItemVendor->total_price, 1) === 0.00) {
                                        $totalPrice = number_format($rowItemVendor->total_price, 0, '.', ',');
                                    }
                                    else {
                                        $totalPrice = number_format($rowItemVendor->total_price, 2, '.', ',');
                                    }

                                    $arrItemVendor[$rowItemVendor->comparison_vendor_id][$rowFormItem->comparison_item_id] = [
                                        'unitPrice' => $unitPrice,
                                        'dppOtherValue' => $rowItemVendor->dpp_other_value,
                                        'totalPrice' => $totalPrice,
                                    ];
                                }
                            }

                            if(count($arrItem) > 0 && $pphExist == false) {
                                array_splice($arrItem, 5, 0, [[
                                    'comparisonItemId' => null,
                                    'orderDetailId' => '',
                                    'reviseOrderDetailId' => null,
                                    'applianceItem' => 'PPh',
                                    'unitName' => '',
                                    'unitQuantity' => '',
                                    'currency' => 'IDR',
                                ]]);
                            }
                        }
                        else {
                            // $getFormItem = $modelApproval->documentApprovalDetailOrderItem()
                            //                         ->from('doc_approval_detail_order_form_item')
                            //                         ->select('order_detail_id', 'cost_center', 'appliance_item', 'brand_type', 'unit_name', 'unit_quantity', 'currency_code', 'unit_price_estimated', 'total_price_estimated', 'required_date')
                            //                         ->where('company_id', $companyId)
                            //                         ->where('doc_approval_id', $docApprovalId)
                            //                         ->where('order_form_id', $getOrderFormLatest->order_form_id)
                            //                         ->where('unit_name', '<>', 'VAT')
                            //                         ->whereNull('comparison_item_id')
                            //                         ->whereNull('application_id')
                            //                         ->whereNull('canceled_id')
                            //                         ->where('is_active', '1')
                            //                         ->orderBy('order_detail_id', 'asc')
                            //                         ->get();

                            $getFormItem = $modelApproval->documentApprovalDetailOrderItem()
                                                    ->from('doc_approval_detail_order_form_item as a')
                                                    ->leftJoin('doc_approval_order_group as b', function($join) {
                                                        $join->on('b.order_form_id', 'a.order_form_id')
                                                            ->where('b.is_active', '1')
                                                            ->orderBy('b.order_group_id', 'desc');
                                                    })
                                                    ->select('a.order_detail_id', 'a.revise_order_detail_id', 'a.cost_center', 'a.appliance_item', 'a.brand_type', 'a.unit_name', 'a.unit_quantity', 'a.currency_code', 'a.unit_price_estimated', 'a.total_price_estimated', 'required_date')
                                                    ->where('a.company_id', $companyId)
                                                    ->where('a.doc_approval_id', $docApprovalId)
                                                    ->where('a.order_form_id', $latestOrderFormId)
                                                    ->where('a.unit_name', '<>', 'VAT')
                                                    ->whereNull('a.canceled_id')
                                                    ->where(function($query) {
                                                        $query->whereNull('a.comparison_id')
                                                            ->orWhere('b.is_need_revision', '1')
                                                            ->orWhere('b.is_canceled', '1')
                                                            ->orWhere('b.is_rejected', '1');
                                                    })
                                                    ->where('a.is_active', '1')
                                                    ->orderBy('a.order_detail_id', 'asc')
                                                    ->get();

                            foreach ($getFormItem as $rowFormItem) {
                                $reviseOrderDetailId = '';
                                if($type == 'REVISE' || $type == 'REVISE_COMPARISON_REVISE_APPLICATION') {
                                    $reviseOrderDetailId = $rowFormItem->revise_order_detail_id ?: $rowFormItem->order_detail_id;
                                }

                                $arrItem[] = [
                                                'comparisonItemId' => null,
                                                'orderDetailId' => $rowFormItem->order_detail_id,
                                                'reviseOrderDetailId' => $reviseOrderDetailId,
                                                'applianceItem' => $rowFormItem->appliance_item,
                                                'unitName' => $rowFormItem->unit_name,
                                                'unitQuantity' => $rowFormItem->unit_quantity,
                                                'currency' => $rowFormItem->currency_code == '%' ? 'IDR' : $rowFormItem->currency_code,
                                            ];
                            }

                            if(count($arrItem) == 0) {
                                $response['form'] = '<div class="row min-vh-75">
                                                        <div class="d-flex justify-content-center align-items-center" style="flex: 1 1 auto">
                                                            <div class="text-center">
                                                                <i class="fa-light fa-file-circle-xmark fs-1"></i>
                                                                <div class="d-block fs-7 mt-2">No order item unprocessed</div>
                                                            </div>
                                                        </div>
                                                    </div>';
                                $response['footerButton'] = '<div class="container-fluid p-0">
                                                                <div class="row justify-content-center w-100 mx-0">
                                                                    <div class="col-sm-12 col-md-4 d-flex justify-content-center justify-content-md-end mb-2 px-0">
                                                                        <button type="button" class="btn btn-default w-100 w-md-auto me-md-2" data-coreui-dismiss="modal" title="Close">
                                                                            <i class="fas fa-xmark"></i> Close
                                                                        </button>
                                                                    </div>
                                                                </div>
                                                            </div>';

                                return response()->json([
                                    'status' => 404,
                                    'message' => 'No order item unprocessed',
                                    'data' => $response,
                                ], 404);
                            }

                            $currency = $getFormItem->pluck('currency_code')->unique();
                            $applianceItem = ['Delivery Fee', 'Discount', 'Total Price', 'VAT', 'PPh', 'Total Price + VAT - PPh'];
                            for($x = 0; $x < count($applianceItem); $x++) {
                                $arrItem[] = [
                                    'comparisonItemId' => null,
                                    'orderDetailId' => '',
                                    'reviseOrderDetailId' => null,
                                    'applianceItem' => $applianceItem[$x],
                                    'unitName' => '',
                                    'unitQuantity' => '',
                                    'currency' => $currency == '%' ? 'IDR' : $currency,
                                ];
                            }
                        }

                        // dd($arrItem);
                        $dataForm->put('resetComparisonItem', $resetComparisonItem);
                        $dataForm->put('documentType', '6');
                        $dataForm->put('itemForm', $arrItem);
                        $dataForm->put('arrVendor', $arrVendor);
                        $dataForm->put('arrItemVendor', $arrItemVendor);

                        // dd($dataForm);
                        $response->put('form', view('procurementpurchasing::form.comparison_form.comparison_form', compact('dataForm'))->render());
                        $actionButton .= '<div class="col-sm-12 col-md-3 d-flex justify-content-center justify-content-md-end mb-2 px-0">
                                            <button type="button" class="btn btn-info w-100 w-md-auto me-md-2 submitForm" data-form="COMPARISON_FORM" data-token="" data-type="CREATE"><i class="fa-regular fa-file-import"></i> Submit Comparison Form</button>
                                        </div>';
                    }
                    else {
                        $getApplicationForm = $modelProcurement->docApprovalOrderApplication()
                                                        ->from('doc_approval_order_application as a')
                                                        ->select('a.doc_approval_id', 'a.application_id', 'a.order_form_id', 'a.doc_version', 'a.comparison_id')
                                                        ->where('a.company_id', $companyId)
                                                        ->where('a.order_form_id', $getOrderFormLatest->order_form_id)
                                                        ->where('a.comparison_id', $comparisonId)
                                                        ->where('a.is_active', '1')
                                                        ->orderBy('a.application_id', 'desc')
                                                        ->first();
                        if($getApplicationForm) {
                            $tokenRevise = ['cid' => $companyId, 'id' => $employeeId,'a' => $getApplicationForm->doc_approval_id, 'b' => $getApplicationForm->application_id, 'c'=> $comparisonId, 'd'=>null];
                            $tokenRevise = SafeToken::encode($tokenRevise);

                            $actionButton .= '<div class="col-sm-12 col-md-3 d-flex justify-content-center justify-content-md-end mb-2 px-0">
                                                <button type="button" class="btn btn-warning w-100 w-md-auto me-md-2 actionBtn" data-type="SEND_BACK" data-form="ORDER_FORM" data-token="'.$encodedToken.'">
                                                    <i class="fa-solid fa-arrow-turn-left"></i> Send Back to User
                                                </button>
                                            </div>
                                            <div class="col-sm-12 col-md-3 d-flex justify-content-center justify-content-md-end mb-2 px-0">
                                                <button type="button" class="btn btn-secondary w-100 w-md-auto me-md-2 actionBtn" data-type="REVISE" data-form="COMPARISON_FORM" data-token="'.$encodedToken.'">
                                                    <i class="fa-regular fa-pen-to-square"></i> Revise Comparison Form
                                                </button>
                                            </div>
                                            <div class="col-sm-12 col-md-3 d-flex justify-content-center justify-content-md-end mb-2 px-0">
                                                <button type="button" class="btn btn-info w-100 w-md-auto me-md-2 actionBtn" data-type="REVISE" data-form="APPLICATION_FORM" data-doc="2" data-token="'.$tokenRevise.'">
                                                    <i class="fa-regular fa-file-import"></i> Create Application Form
                                                </button>
                                            </div>';
                        }
                        else {
                            $getApplicationFormLatest = $modelProcurement->docApprovalOrderApplication()
                                                                    ->from('doc_approval_order_application as a')
                                                                    ->select('a.doc_approval_id', 'a.application_id', 'a.order_form_id', 'a.doc_version', 'a.comparison_id')
                                                                    ->where('a.company_id', $companyId)
                                                                    ->where('a.order_form_id', $referenceId)
                                                                    // ->where('a.comparison_id', $comparisonId)
                                                                    ->where('a.is_active', '1')
                                                                    ->orderBy('a.application_id', 'desc')
                                                                    ->first();
                            if($getApplicationFormLatest) {
                            }

                            $actionButton .= '<div class="col-sm-12 col-md-3 d-flex justify-content-center justify-content-md-end mb-2 px-0">
                                                    <button type="button" class="btn btn-warning w-100 w-md-auto me-md-2 actionBtn" data-type="SEND_BACK" data-form="ORDER_FORM" data-token="'.$encodedToken.'">
                                                        <i class="fa-solid fa-arrow-turn-left"></i> Send Back to User
                                                    </button>
                                                </div>
                                                <div class="col-sm-12 col-md-3 d-flex justify-content-center justify-content-md-end mb-2 px-0">
                                                    <button type="button" class="btn btn-secondary w-100 w-md-auto me-md-2 actionBtn" data-type="REVISE" data-form="COMPARISON_FORM" data-token="'.$encodedToken.'">
                                                        <i class="fa-regular fa-pen-to-square"></i> Revise Comparison Form
                                                    </button>
                                                </div>
                                                <div class="col-sm-12 col-md-3 d-flex justify-content-center justify-content-md-end mb-2 px-0">
                                                    <button type="button" class="btn btn-info w-100 w-md-auto me-md-2 nextNewForm" data-type="NEW" data-form="APPLICATION_FORM" data-token="'.$encodedToken.'">
                                                        <i class="fa-regular fa-file-import"></i> Create Application Form
                                                    </button>
                                                </div>';
                        }
                    }
                }
                else {
                    $response['form'] = '<div class="row min-vh-75">
                                            <div class="d-flex justify-content-center align-items-center" style="flex: 1 1 auto">
                                                <div class="text-center">
                                                    <i class="fa-light fa-file-circle-xmark fs-1"></i>
                                                    <div class="d-block fs-7 mt-2">Form not found, please reload your browser.</div>
                                                </div>
                                            </div>
                                        </div>';
                    $response['footerButton'] = '<div class="container-fluid p-0">
                                                    <div class="row justify-content-center w-100 mx-0">
                                                        <div class="col-sm-12 col-md-4 d-flex justify-content-center justify-content-md-end mb-2 px-0">
                                                            <button type="button" class="btn btn-default w-100 w-md-auto me-md-2" data-coreui-dismiss="modal" title="Close">
                                                                <i class="fas fa-xmark"></i> Close
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>';

                    return response()->json([
                        'status' => 404,
                        'message' => 'Form not found, please reload your browser.',
                        'data' => $response,
                    ], 404);
                }
            }
        }
        else if($form == 'APPLICATION_FORM') {
            $documentTypeId = '2';

            // $getApplication = $modelProcurement->docApprovalOrderApplication()
            //                                     ->from('doc_approval_order_application')
            //                                     ->select('application_id')
            //                                     ->where('order_form_id', $referenceId)
            //                                     ->where('is_active', '1')
            //                                     ->first();
            // if($getApplication) {
            //     $actionButton = '<div class="col-sm-12 col-md-3 d-flex justify-content-center justify-content-md-end mb-2 px-0">
            //                         <button type="button" class="btn btn-info w-100 w-md-auto me-md-2 nextNewForm" data-type="NEW" data-form="APPLICATION_FORM" data-token="'.$encodedToken.'">
            //                             <i class="fa-regular fa-file-import"></i> Revise Application Form
            //                         </button>
            //                     </div>';
            // }
            // else {
            $vendorSelected = null;
            if($type == 'REVISE' || $type == 'OLD_COMPARISON_REVISE_APPLICATION' || $type == 'DELETE_COMPARISON_REVISE_APPLICATION'){
                $response['form'] = '<div class="row min-vh-75">
                                        <div class="d-flex justify-content-center align-items-center" style="flex: 1 1 auto">
                                            <div class="text-center">
                                                <i class="fa-light fa-file-circle-xmark fs-1"></i>
                                                <div class="d-block fs-7 mt-2">Your account is not allowed to access this form</div>
                                            </div>
                                        </div>
                                    </div>';
                $response['footerButton'] = '<div class="container-fluid p-0">
                                                <div class="row justify-content-center w-100 mx-0">
                                                    <div class="col-sm-12 col-md-4 d-flex justify-content-center justify-content-md-end mb-2 px-0">
                                                        <button type="button" class="btn btn-default w-100 w-md-auto me-md-2" data-coreui-dismiss="modal" title="Close">
                                                            <i class="fas fa-xmark"></i> Close
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>';

                $getForm = $modelApproval->docApprovalOrderApplication()
                                            ->from('doc_approval_order_application')
                                            ->select('year', 'order_form_id', 'company_id', 'application_currency', 'priority', 'priority_name', 'term_payment_id')
                                            ->where('doc_approval_id', $docApprovalId)
                                            ->where('is_active', 1)
                                            ->orderBy('application_id', 'desc')
                                            ->first();

                if(!$getForm) {
                    return response()->json([
                        'status' => 404,
                        'message' => 'Invalid token form',
                        'data' => $response,
                    ], 422);
                }
                else if(!in_array($getForm->company_id, $companyIdList)) {
                    return response()->json([
                        'status' => 404,
                        'message' => 'Your account is not allowed to access this form',
                        'data' => $response,
                    ], 404);
                }

                $companyId = $getForm->company_id;
                $yearForm = $getForm->year;
                $orderFormId = $getForm->order_form_id;

                $getApplication = $modelProcurement->docApprovalOrderApplication()
                                                ->from('doc_approval_order_application as a')
                                                ->leftJoin('doc_approval_detail_order_form as b', function($join) {
                                                    $join->on('b.order_form_id', 'a.order_form_id')
                                                        ->where('b.is_active', '1');
                                                })
                                                ->select('a.*', 'b.order_form_id as order_form_id_old', 'b.doc_approval_id as doc_approval_id_order')
                                                ->where('a.company_id', $companyId)
                                                ->where('a.doc_approval_id', $docApprovalId)
                                                ->where('a.application_id', $referenceId)
                                                // ->where('a.application_form_status', '<>', '6')// 6 = REVISED
                                                ->whereNotNull('b.doc_approval_id')
                                                ->where('a.is_active', '1')
                                                ->orderBy('a.application_id', 'desc')
                                                ->first();
                if(!$getApplication) {
                    $response['form'] = '<div class="row min-vh-75">
                                            <div class="d-flex justify-content-center align-items-center" style="flex: 1 1 auto">
                                                <div class="text-center">
                                                    <i class="fa-light fa-file-circle-xmark fs-1"></i>
                                                    <div class="d-block fs-7 mt-2">Form not found, please reload your browser.</div>
                                                </div>
                                            </div>
                                        </div>';
                    $response['footerButton'] = '<div class="container-fluid p-0">
                                                    <div class="row justify-content-center w-100 mx-0">
                                                        <div class="col-sm-12 col-md-4 d-flex justify-content-center justify-content-md-end mb-2 px-0">
                                                            <button type="button" class="btn btn-default w-100 w-md-auto me-md-2" data-coreui-dismiss="modal" title="Close">
                                                                <i class="fas fa-xmark"></i> Close
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>';

                    return response()->json([
                        'status' => 404,
                        'message' => 'Form not found, please reload your browser.',
                        'data' => $response,
                    ], 404);
                }

                // dd($getApplication);
                // GET LATEST ORDER FORM
                $getOrderFormLatest = $modelProcurement->documentApprovalDetailOrder()
                                                    ->from('doc_approval_detail_order_form as a')
                                                    ->select('a.doc_approval_id', 'a.order_form_id')
                                                    ->where('a.company_id', $companyId)
                                                    ->where('a.doc_approval_id', $getApplication->doc_approval_id_order)
                                                    ->where('a.is_active', '1')
                                                    ->orderBy('a.order_form_id', 'desc')
                                                    ->first();
                $latestOrderFormId = $getOrderFormLatest->order_form_id;

                if($type == 'DELETE_COMPARISON_REVISE_APPLICATION') {
                    $resetApplicationItem = true;
                    $comparisonId = null;
                }
                else {
                    $resetApplicationItem = $getOrderFormLatest->order_form_id > $getApplication->order_form_id_old ? true : false;
                    // $resetApplicationItem = true;

                    // if($getApplication->application_form_status != '6' && $getApplication->application_form_status != '5') { // 6 = REVISED, 5 = NEED REVISED
                    // if($getApplication->application_form_status != '6' && $resetApplicationItem == false) { // 6 = REVISED
                    if($resetApplicationItem == false) { // 6 = REVISED
                        $comparisonId = $getApplication->comparison_id >= $comparisonId ? $getApplication->comparison_id : $comparisonId;
                        // $comparisonId = $getApplication->comparison_id;
                    }
                }

                $token = ['cid' => $companyId, 'id' => $employeeId, 'a' => $docApprovalId,'b' => $referenceId,'c' => $comparisonId,'d' => null];
                $encodedToken = SafeToken::encode($token);

                $tokenFormOrder = ['cid' => $companyId, 'id' => $employeeId, 'a' => $getOrderFormLatest->doc_approval_id,'b' => $getOrderFormLatest->order_form_id,'c' => null,'d' => null];
                $tokenFormOrder = SafeToken::encode($tokenFormOrder);

                $tokenReviseComparison = ['cid' => $companyId, 'id' => $employeeId, 'a' => $getOrderFormLatest->doc_approval_id,'b' => $getOrderFormLatest->order_form_id,'c' => $comparisonId ?: $getApplication->comparison_id,'d' => null];
                $tokenReviseComparison = SafeToken::encode($tokenReviseComparison);

                $dataForm = $dataForm->merge([
                    'companyId' => $companyId,
                    'tokenForm' => $encodedToken,
                    'tokenFormOrder' => $tokenFormOrder,
                    'yearForm' => Carbon::parse($yearForm)->format('Y'),
                    'currencyCode' => $getForm->application_currency,
                    'requestType' => $type,
                    'priority' => $getForm->priority,
                    'termPaymentId' => $getForm->term_payment_id,
                    // 'purposeDescription' => $getApplication->description,
                ]);

                $compareItem = false;
                $vendorIdSelected = null;
                $getFormItemOthers = collect([]);
                $currencySelected = $getApplication->application_currency;
                $itemVat = $itemPPh = $itemDeliveryFee = 0;

                if(($getApplication->application_form_status == '6' || $getApplication->application_form_status == '12' || $type == 'OLD_COMPARISON_REVISE_APPLICATION') && $comparisonId) {
                    $getComparisonForm = $modelProcurement->docApprovalOrderComparison()
                                                        ->from('doc_approval_order_comparison as a')
                                                        ->leftJoin('doc_approval_order_comparison_vendor as b', function($join) {
                                                            $join->on('b.comparison_id', 'a.comparison_id')
                                                                ->on('b.vendor_id', 'a.vendor_id_selected');
                                                        })
                                                        ->leftJoin('doc_approval_order_group as c', function($join) use($latestOrderFormId) {
                                                            $join->on('c.comparison_id', 'a.comparison_id')
                                                                ->where('c.order_form_id', '=', $latestOrderFormId)
                                                                ->where('c.is_active', '1')
                                                                ->orderBy('c.order_group_id', 'desc');
                                                        })
                                                        ->select('a.comparison_id', 'a.doc_approval_id', 'a.comparison_title', 'a.comparison_description', 'a.comparison_date', 'a.validity', 'a.vendor_id_selected','a.comparison_notes', 'b.comparison_vendor_id', 'b.vendor_name', 'b.vendor_address_id', 'b.vendor_address', 'b.vendor_pic_id', 'b.pic_name', 'c.order_group_id', 'c.application_id')
                                                        ->where('a.company_id', $companyId)
                                                        ->where('a.comparison_id', $comparisonId)
                                                        ->where('a.is_active', '1')
                                                        ->orderBy('a.comparison_id', 'desc')
                                                        ->first();
                    if($getComparisonForm) {
                        $compareItem = true;
                        $tokenComparison = ['cid' => $companyId, 'id' => $employeeId, 'a' => $getComparisonForm->doc_approval_id,'b' => $latestOrderFormId,'c' => $comparisonId,'d' => null];
                        $tokenComparison = SafeToken::encode($tokenComparison);

                        $dataForm = $dataForm->merge([
                            'tokenFormComparison'=> $tokenComparison,
                        ]);

                        $vendorSelected = [
                            'vendorId' => $getComparisonForm->vendor_id_selected,
                            'vendorName' => $getComparisonForm->vendor_name,
                            'vendorAddressId' => $getComparisonForm->vendor_address_id,
                            'vendorAddress' => $getComparisonForm->vendor_address,
                            'vendorPicId' => $getComparisonForm->vendor_pic_id,
                            'vendorPic' => $getComparisonForm->pic_name,
                        ];

                        $getFormItem = $modelProcurement->docApprovalOrderComparisonItemVendor()
                                                        ->from('doc_approval_order_comparison_item_vendor as a')
                                                        ->leftJoin('doc_approval_order_comparison_item as b', function($join) {
                                                            $join->on('b.comparison_id', 'a.comparison_id')
                                                                ->on('b.comparison_item_id', 'a.comparison_item_id');
                                                        })
                                                        ->select('b.comparison_item_id', 'b.order_detail_id', 'b.appliance_item', 'b.unit_name', 'b.unit_quantity', 'a.currency_code', 'a.unit_price', 'a.dpp_other_value', 'a.total_price', 'b.currency as currency_symbol')
                                                        // ->where('b.order_form_id', $referenceId)
                                                        ->where('a.company_id', $companyId)
                                                        ->where('a.comparison_id', $getComparisonForm->comparison_id)
                                                        ->where('a.comparison_vendor_id', $getComparisonForm->comparison_vendor_id)
                                                        ->where('a.is_active', '1')
                                                        ->orderBy('b.comparison_item_id', 'asc')
                                                        ->get();
                    }
                }
                else if($comparisonId == null && $resetApplicationItem == true) {
                    if($type == 'DELETE_COMPARISON_REVISE_APPLICATION') {
                        $getFormItemOthers = $modelApproval->documentApprovalDetailOrderItem()
                                                            ->from('doc_approval_detail_order_form_item as a')
                                                            ->leftJoin('doc_approval_order_group as b', function($join) {
                                                                $join->on('b.order_form_id', 'a.order_form_id')
                                                                    ->on('b.comparison_id', 'a.comparison_id')
                                                                    ->where('b.is_active', '1')
                                                                    ->orderBy('b.order_group_id', 'desc');
                                                            })
                                                            ->select('a.order_detail_id', 'a.revise_order_detail_id', 'a.appliance_item', 'a.unit_name', 'a.unit_quantity', 'a.currency_code')
                                                            ->where('a.company_id', $companyId)
                                                            ->where('a.doc_approval_id', $getOrderFormLatest->doc_approval_id)
                                                            ->where('a.order_form_id', $getOrderFormLatest->order_form_id)
                                                            ->where(function($query) use($getApplication) {

                                                                    $query->where(function($query){
                                                                        $query->whereNull('a.application_id')
                                                                            ->whereNull('a.comparison_id');
                                                                    });
                                                                    $query->orWhere(function($query) use($getApplication) {
                                                                        $query->where('a.application_id', '<>', $getApplication->application_id)
                                                                            ->where(function($query) {
                                                                                $query->orWhere('b.is_need_revision', '1')
                                                                                    ->orWhere('b.is_canceled', '1')
                                                                                    ->orWhere('b.is_rejected', '1');
                                                                            });
                                                                    });
                                                            })
                                                            ->where('a.unit_name', '<>', 'VAT')
                                                            ->whereNull('a.canceled_id')
                                                            ->where('a.is_active', '1')
                                                            ->orderBy('a.order_detail_id', 'asc')
                                                            ->get();

                        $getFormItem = $modelProcurement->documentApprovalDetailOrderItem()
                                                    ->from('doc_approval_detail_order_form_item as a')
                                                    ->rightJoin('doc_approval_order_application_item as b', function($join) {
                                                        $join->on('b.order_detail_id', 'a.order_detail_id')
                                                            ->where('b.is_active', '1');
                                                    })
                                                    ->select('b.order_detail_id', 'a.revise_order_detail_id', 'a.cost_center', 'a.appliance_item', 'a.brand_type', 'a.unit_name', 'a.unit_quantity as quantity', 'a.currency_code as currency', DB::raw('NULL as currency_symbol'))
                                                    ->where('a.company_id', $companyId)
                                                    ->where('a.doc_approval_id', $getOrderFormLatest->doc_approval_id)
                                                    ->where('a.order_form_id', $getOrderFormLatest->order_form_id)
                                                    ->where('b.application_id', $getApplication->application_id)
                                                    ->where('a.unit_name', '<>', 'VAT')
                                                    ->whereNotNull('b.order_detail_id')
                                                    ->whereNull('a.canceled_id')
                                                    ->where('a.is_active', '1')
                                                    ->orderBy('a.order_detail_id', 'asc')
                                                    ->get();
                    }
                    else {
                        // $getFormItem = $modelProcurement->documentApprovalDetailOrderItem()
                        //                             ->from('doc_approval_detail_order_form_item')
                        //                             ->select('order_detail_id', 'cost_center', 'appliance_item', 'brand_type', 'unit_name', 'unit_quantity as quantity', 'currency_code as currency', DB::raw('NULL as currency_symbol'))
                        //                             ->where('company_id', $companyId)
                        //                             ->where('doc_approval_id', $getOrderFormLatest->doc_approval_id)
                        //                             ->where('order_form_id', $getOrderFormLatest->order_form_id)
                        //                             ->where('unit_name', '<>', 'VAT')
                        //                             ->whereNull('comparison_id')
                        //                             ->whereNull('application_id')
                        //                             ->whereNull('canceled_id')
                        //                             ->where('is_active', '1')
                        //                             ->orderBy('order_detail_id', 'asc')
                        //                             ->get();

                        $getFormItem = $modelApproval->documentApprovalDetailOrderItem()
                                                    ->from('doc_approval_detail_order_form_item as a')
                                                    ->leftJoin('doc_approval_order_group as b', function($join) {
                                                        $join->on('b.order_form_id', 'a.order_form_id')
                                                            ->where('b.is_active', '1')
                                                            ->orderBy('b.order_group_id', 'desc');
                                                    })
                                                    ->select('a.order_detail_id', 'a.cost_center', 'a.appliance_item', 'a.brand_type', 'a.unit_name', 'a.unit_quantity as quantity', 'a.currency_code as currency',  DB::raw('NULL as currency_symbol'))
                                                    ->where('a.company_id', $companyId)
                                                    ->where('a.doc_approval_id', $getOrderFormLatest->doc_approval_id)
                                                    ->where('a.order_form_id', $getOrderFormLatest->order_form_id)
                                                    ->where('a.unit_name', '<>', 'VAT')
                                                    ->whereNull('a.canceled_id')
                                                    ->where(function($query) {
                                                        $query->whereNull('a.comparison_id')
                                                            ->orWhere('b.is_need_revision', '1')
                                                            ->orWhere('b.is_canceled', '1')
                                                            ->orWhere('b.is_rejected', '1');
                                                    })
                                                    ->where('a.is_active', '1')
                                                    ->orderBy('a.order_detail_id', 'asc')
                                                    ->get();
                    }

                    $getApplication->application_submitted_date = '';
                    $getApplication->vendor_id = null;
                    $getApplication->vendor_name = $getApplication->vendor_address = $getApplication->vendor_pic = '';
                }
                else {
                    $getFormItem = $modelProcurement->docApprovalOrderApplicationItem()
                                                    ->from('doc_approval_order_application_item')
                                                    ->select('*')
                                                    ->where('company_id', $companyId)
                                                    ->where('application_id', $referenceId)
                                                    ->orderBy('application_item_id', 'asc')
                                                    ->get();
                }

                // if($comparisonId != null) {
                //     $getComparisonForm = $modelProcurement->docApprovalOrderComparison()
                //                                         ->from('doc_approval_order_comparison as a')
                //                                         ->select('a.doc_approval_id')
                //                                         ->where('a.company_id', $companyId)
                //                                         ->where('a.order_form_id', $getOrderFormLatest->order_form_id)
                //                                         ->where('a.comparison_id', $comparisonId)
                //                                         ->where('a.is_active', '1')
                //                                         ->orderBy('a.comparison_id', 'desc')
                //                                         ->first();
                //     if($getComparisonForm) {
                //         $compareItem = true;
                //         $tokenComparison = ['cid' => $companyId, 'id' => $employeeId, 'a' => $getComparisonForm->doc_approval_id,'b' => $getOrderFormLatest->order_form_id,'c' => $comparisonId,'d' => null];
                //         $tokenComparison = json_encode($tokenComparison);
                //         $tokenComparison = Crypt::encryptString($tokenComparison);
                //         $tokenComparison = rtrim(strtr(base64_encode($tokenComparison), '+/', '-_'), '=');

                //         $dataForm = $dataForm->merge([
                //             'tokenFormComparison'=> $tokenComparison,
                //         ]);
                //     }
                // }

                $quotationList = $arrItem = [];
                $totalPriceVat =  number_format($getApplication->application_estimate, 2, '.', ',');

                if($compareItem == true) {
                    $insertedItemOthers = false;
                    foreach ($getFormItem as $rowFormItem) {
                        $unitPrice = $totalPrice = '';
                        if(($getApplication->application_form_status == '6' || $getApplication->application_form_status == '12' || $type == 'OLD_COMPARISON_REVISE_APPLICATION' || $type == 'DELETE_COMPARISON_REVISE_APPLICATION') && $comparisonId) {

                            if($type != 'OLD_COMPARISON_REVISE_APPLICATION' && $rowFormItem->order_detail_id == null && $getFormItemOthers->isNotEmpty() && $insertedItemOthers == false) {
                                foreach ($getFormItemOthers as $rowFormItemOthers) {
                                    $existingItems = array_filter($arrItem, function($item) use ($rowFormItemOthers) {
                                        return $item['orderDetailId'] == $rowFormItemOthers->order_detail_id;
                                    });

                                    if (empty($existingItems)) {
                                        $reviseOrderDetailId = '';
                                        if($type != 'OLD_COMPARISON_REVISE_APPLICATION') {
                                            $reviseOrderDetailId = $rowFormItemOthers->revise_order_detail_id ?: $rowFormItemOthers->order_detail_id;
                                        }

                                        $arrItem[] = [
                                            'orderDetailId' => $rowFormItemOthers->order_detail_id,
                                            'reviseOrderDetailId' => $reviseOrderDetailId,
                                            'applianceItem' => $rowFormItemOthers->appliance_item,
                                            'unitQuantity' => $rowFormItemOthers->unit_quantity,
                                            'currency' => $rowFormItemOthers->currency_code,
                                            'currencySymbol' => null,
                                            'unitPrice' => '',
                                            'dppOtherValue' => '',
                                            'totalPrice' => '',
                                        ];
                                    }
                                }

                                $insertedItemOthers = true;
                            }

                            $unitPrice = number_format($rowFormItem->unit_price, 2, '.', ',');
                            $totalPrice = number_format($rowFormItem->total_price, 2, '.', ',');

                            $reviseOrderDetailId = $rowFormItem->revise_order_detail_id ?: $rowFormItem->order_detail_id;
                            $arrItem[] = [
                                'orderDetailId' => $rowFormItem->order_detail_id,
                                'reviseOrderDetailId' => $reviseOrderDetailId,
                                'applianceItem' => $rowFormItem->appliance_item,
                                'unitQuantity' => $rowFormItem->unit_quantity,
                                'currency' => $rowFormItem->currency_code == '%' ? 'IDR' : $rowFormItem->currency_code,
                                'currencySymbol' => $rowFormItem->currency_symbol,
                                'unitPrice' => $unitPrice,
                                'dppOtherValue' => $rowFormItem->dpp_other_value,
                                'totalPrice' => $totalPrice,
                            ];

                            if($rowFormItem->appliance_item == 'Total Price + VAT - PPh') {
                                $totalPriceVat = number_format($rowFormItem->total_price, 2, '.', ',');
                            }

                            if($rowFormItem->appliance_item == 'VAT') {
                                $itemVat = Str::replace(',', '', $this->formatNumber($rowFormItem->total_price));
                            }
                            else if($rowFormItem->appliance_item == 'PPh') {
                                $itemPPh = Str::replace(',', '', $this->formatNumber($rowFormItem->total_price));
                            }
                            else if($rowFormItem->appliance_item == 'Delivery Fee') {
                                $itemDeliveryFee = Str::replace(',', '', $this->formatNumber($rowFormItem->total_price));
                            }
                        }
                        else {
                            // if($comparisonId != null) {
                            //     $unitPrice = number_format($rowFormItem->unit_price, 2, '.', ',');
                            //     $totalPrice = number_format($rowFormItem->total_price, 2, '.', ',');

                            //     if($rowFormItem->appliance_item == 'Total Price + VAT') {
                            //         $totalPriceVat = number_format($rowFormItem->total_price, 2, '.', ',');
                            //     }
                            // }

                            $unitPrice = number_format($rowFormItem->unit_price, 2, '.', ',');
                            $totalPrice = number_format($rowFormItem->subtotal_price, 2, '.', ',');

                            // if($rowFormItem->unit_price == 0.00 || $rowFormItem->unit_price == null) {
                            //     $unitPrice = '';
                            // }
                            // else if (fmod($rowFormItem->unit_price, 1) === 0.00) {
                            //     $unitPrice = number_format($rowFormItem->unit_price, 0, '.', ',');
                            // }
                            // else {
                            //     $unitPrice = number_format($rowFormItem->unit_price, 2, '.', ',');
                            // }

                            // if($rowFormItem->total_price == 0.00 || $rowFormItem->total_price == null) {
                            //     $totalPrice = '';
                            // }
                            // else if (fmod($rowFormItem->total_price, 1) === 0.00) {
                            //     $totalPrice = number_format($rowFormItem->total_price, 0, '.', ',');
                            // }
                            // else {
                            //     $totalPrice = number_format($rowFormItem->total_price, 2, '.', ',');
                            // }

                            $arrItem[] = [
                                            'orderDetailId' => $rowFormItem->order_detail_id,
                                            'reviseOrderDetailId' => null,
                                            'applianceItem' => $rowFormItem->appliance_item,
                                            'unitQuantity' => $rowFormItem->quantity,
                                            'currency' => $rowFormItem->currency_code == '%' ? 'IDR' : $rowFormItem->currency_code,
                                            'currencySymbol' => $rowFormItem->currency,
                                            'unitPrice' => $unitPrice,
                                            'dppOtherValue' =>  $rowFormItem->dpp_other_value,
                                            'totalPrice' => $totalPrice,
                                        ];

                            if($rowFormItem->appliance_item == 'VAT') {
                                $itemVat = Str::replace(',', '', $this->formatNumber($rowFormItem->subtotal_price));
                            }
                            else if($rowFormItem->appliance_item == 'PPh') {
                                $itemPPh = Str::replace(',', '', $this->formatNumber($rowFormItem->subtotal_price));
                            }
                            else if($rowFormItem->appliance_item == 'Delivery Fee') {
                                $itemDeliveryFee = Str::replace(',', '', $this->formatNumber($rowFormItem->subtotal_price));
                            }
                        }
                    }

                    $getQuotation = $modelProcurement->docApprovalAttachment()
                                                ->from('doc_approval_attachment')
                                                ->select('attachment_id', 'mime_type', 'filename', 'filename_original', 'filename_converted')
                                                ->where('doc_type_id', '6')
                                                ->where('company_id', $companyId)
                                                ->where('reference_id', $comparisonId)
                                                ->get();
                    foreach($getQuotation as $row) {
                        $tokenFile = ['a' => 'DOC_APPROVAL', 'b' => $row->attachment_id];
                        $tokenFile = SafeToken::encode($tokenFile);

                        $quotationList[] = [
                                            'tokenAttachment' => $tokenFile,
                                            'icon' => $this->getFileIcon($row->mime_type, $row->filename_original),
                                            'filename' => $row->filename_original,
                                        ];
                    }
                }
                else {
                    $dataForm = $dataForm->merge([
                        'tokenFormComparison'=> null,
                    ]);

                    foreach ($getFormItem as $rowFormItem) {
                        // if($rowFormItem->order_detail_id == null || $rowFormItem->order_detail_id == '') {
                        if($rowFormItem->appliance_item == 'Delivery Fee' || $rowFormItem->appliance_item == 'Discount' || $rowFormItem->appliance_item == 'Total Price' || $rowFormItem->appliance_item == 'VAT' || $rowFormItem->appliance_item == 'PPh' || $rowFormItem->appliance_item == 'Total Price + VAT - PPh'){
                            continue;
                        }

                        $unitPrice = $totalPrice = '';
                        // if($comparisonId != null) {
                        //     $unitPrice = number_format($rowFormItem->unit_price, 2, '.', ',');
                        //     $totalPrice = number_format($rowFormItem->total_price, 2, '.', ',');

                        //     if($rowFormItem->appliance_item == 'Total Price + VAT') {
                        //         $totalPriceVat = number_format($rowFormItem->total_price, 2, '.', ',');
                        //     }
                        // }

                        // $unitPrice = number_format($rowFormItem->unit_price, 2, '.', ',');
                        // $totalPrice = number_format($rowFormItem->subtotal_price, 2, '.', ',');

                        if($rowFormItem->unit_price == 0.00 || $rowFormItem->unit_price == null) {
                            $unitPrice = '';
                        }
                        else if (fmod($rowFormItem->unit_price, 1) === 0.00) {
                            $unitPrice = number_format($rowFormItem->unit_price, 0, '.', ',');
                        }
                        else {
                            $unitPrice = number_format($rowFormItem->unit_price, 2, '.', ',');
                        }

                        if($rowFormItem->subtotal_price == 0.00 || $rowFormItem->subtotal_price == null) {
                            $totalPrice = '';
                        }
                        else if (fmod($rowFormItem->subtotal_price, 1) === 0.00) {
                            $totalPrice = number_format($rowFormItem->subtotal_price, 0, '.', ',');
                        }
                        else {
                            $totalPrice = number_format($rowFormItem->subtotal_price, 2, '.', ',');
                        }

                        $reviseOrderDetailId = '';
                        if($getApplication->application_form_status == '6' || $getApplication->application_form_status == '12' || $type == 'OLD_COMPARISON_REVISE_APPLICATION' || $type == 'DELETE_COMPARISON_REVISE_APPLICATION') {
                            $reviseOrderDetailId = $rowFormItem->revise_order_detail_id ?: $rowFormItem->order_detail_id;
                        }

                        $arrItem[] = [
                                        'orderDetailId' => $rowFormItem->order_detail_id,
                                        'reviseOrderDetailId' => $reviseOrderDetailId,
                                        'applianceItem' => $rowFormItem->appliance_item,
                                        'unitQuantity' => $rowFormItem->quantity,
                                        'currency' => $rowFormItem->currency_code == '%' ? 'IDR' : $rowFormItem->currency_code,
                                        'currencySymbol' => $rowFormItem->currency,
                                        'unitPrice' => $unitPrice,
                                        'dppOtherValue' => '',
                                        'totalPrice' => $totalPrice,
                                    ];
                    }

                    if($type != 'OLD_COMPARISON_REVISE_APPLICATION' && $getFormItemOthers->isNotEmpty()) {
                        foreach ($getFormItemOthers as $rowFormItemOthers) {
                            $existingItems = array_filter($arrItem, function($item) use ($rowFormItemOthers) {
                                return $item['orderDetailId'] == $rowFormItemOthers->order_detail_id;
                            });

                            if (empty($existingItems)) {
                                $reviseOrderDetailId = $rowFormItemOthers->revise_order_detail_id ?: $rowFormItemOthers->order_detail_id;
                                $arrItem[] = [
                                    'orderDetailId' => $rowFormItemOthers->order_detail_id,
                                    'reviseOrderDetailId' => $reviseOrderDetailId,
                                    'applianceItem' => $rowFormItemOthers->appliance_item,
                                    'unitQuantity' => $rowFormItemOthers->unit_quantity,
                                    'currency' => $rowFormItem->currency_code == '%' ? 'IDR' : $rowFormItem->currency_code,
                                    'currencySymbol' => null,
                                    'unitPrice' => '',
                                    'dppOtherValue' => '',
                                    'totalPrice' => '',
                                ];
                            }
                        }

                        $insertedItemOthers = true;
                    }

                    $arrApplianceItem = ['Delivery Fee', 'Discount', 'Total Price', 'VAT', 'PPh', 'Total Price + VAT - PPh'];
                    for($x = 0; $x < count($arrApplianceItem); $x++) {
                        $item = false;
                        foreach ($getFormItem as $rowFormItem) {
                            if($rowFormItem->appliance_item == $arrApplianceItem[$x]) {
                                $item = true;
                                $unitPrice = $totalPrice = '';
                                // if($arrApplianceItem[$x] == 'VAT'){
                                //     $unitPrice = number_format($rowFormItem->unit_price, 0, '.', ',');
                                // }
                                // else {
                                //     $unitPrice = number_format($rowFormItem->unit_price, 2, '.', ',');
                                // }

                                // $totalPrice = number_format($rowFormItem->subtotal_price, 2, '.', ',');
                                if($rowFormItem->unit_price == 0.00 || $rowFormItem->unit_price == null) {
                                    $unitPrice = '';
                                }
                                else if (fmod($rowFormItem->unit_price, 1) === 0.00) {
                                    $unitPrice = number_format($rowFormItem->unit_price, 0, '.', ',');
                                }
                                else {
                                    $unitPrice = number_format($rowFormItem->unit_price, 2, '.', ',');
                                }

                                if($rowFormItem->subtotal_price == 0.00 || $rowFormItem->subtotal_price == null) {
                                    $totalPrice = '';
                                }
                                else if (fmod($rowFormItem->subtotal_price, 1) === 0.00) {
                                    $totalPrice = number_format($rowFormItem->subtotal_price, 0, '.', ',');
                                }
                                else {
                                    $totalPrice = number_format($rowFormItem->subtotal_price, 2, '.', ',');
                                }

                                $arrItem[] = [
                                                'orderDetailId' => $rowFormItem->order_detail_id,
                                                'reviseOrderDetailId' => null,
                                                'applianceItem' => $rowFormItem->appliance_item,
                                                'unitQuantity' => $rowFormItem->quantity,
                                                'currency' => $rowFormItem->currency == '%' ? 'IDR' : $rowFormItem->currency,
                                                'currencySymbol' => $rowFormItem->currency,
                                                'unitPrice' => $unitPrice,
                                                'dppOtherValue' => $rowFormItem->dpp_other_value ?: '',
                                                'totalPrice' => $totalPrice,
                                            ];
                            }

                            if($item == true) {
                                break;
                            }
                        }

                        if($item == false) {
                            if($arrApplianceItem[$x] == 'VAT'){
                                $currencySymbol = '%';
                            }
                            else {
                                $currencySymbol = $currencySelected;
                            }

                            $arrItem[] = [
                                'orderDetailId' => '',
                                'reviseOrderDetailId' => '',
                                'applianceItem' => $arrApplianceItem[$x],
                                'unitQuantity' => '',
                                'currency' => $currencySelected == '%' ? 'IDR' : $currencySelected,
                                'currencySymbol' => $currencySymbol,
                                'unitPrice' => '',
                                'dppOtherValue' => '',
                                'totalPrice' => '',
                            ];
                        }
                    }
                }

                // dd($arrItem);
                if(count($arrItem) == 0) {
                    $response['form'] = '<div class="row min-vh-75">
                                            <div class="d-flex justify-content-center align-items-center" style="flex: 1 1 auto">
                                                <div class="text-center">
                                                    <i class="fa-light fa-file-circle-xmark fs-1"></i>
                                                    <div class="d-block fs-7 mt-2">No order item unprocessed</div>
                                                </div>
                                            </div>
                                        </div>';
                    $response['footerButton'] = '<div class="container-fluid p-0">
                                                    <div class="row justify-content-center w-100 mx-0">
                                                        <div class="col-sm-12 col-md-4 d-flex justify-content-center justify-content-md-end mb-2 px-0">
                                                            <button type="button" class="btn btn-default w-100 w-md-auto me-md-2" data-coreui-dismiss="modal" title="Close">
                                                                <i class="fas fa-xmark"></i> Close
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>';

                    return response()->json([
                        'status' => 404,
                        'message' => 'No order item unprocessed',
                        'data' => $response,
                    ], 404);
                }

                $arrApplicationNumber = Str::of($getApplication->application_number)->explode('-');
                // $arrSeqNumber = Str::of($arrApplicationNumber[0])->explode('R-');
                $version = $arrApplicationNumber[3] + 1;
                // if(count($arrSeqNumber) == 2) {
                //     $seqNumber = $arrSeqNumber[0].'R-'.($arrSeqNumber[1] + 1);
                // }
                // else {
                //     $seqNumber = $arrSeqNumber[0].'R-1';
                // }

                // $applicationFormatNumber = str_replace($arrApplicationNumber[3], '', $getApplication->application_number);
                $applicationNumber = $arrApplicationNumber[0].'-'.$arrApplicationNumber[1].'-'.$arrApplicationNumber[2].'-'.$version;

                $arrCostCenterSelected = [];
                $getFormCostCenter = $modelProcurement->docApprovalOrderApplicationCostCenter()
                                                    ->from('doc_approval_order_application_cost_center')
                                                    ->select('cost_center_id', 'cost_center', 'percentage')
                                                    ->where('application_id', $referenceId)
                                                    ->where('company_id', $companyId)
                                                    ->where('order_form_id', $getApplication->order_form_id)
                                                    ->where('is_active', '1')
                                                    ->orderBy('application_cost_center_id', 'asc')
                                                    ->get();
                foreach ($getFormCostCenter as $rowCostCenter) {
                    $arrCostCenterSelected[] = [
                        'costCenterId' => $rowCostCenter->cost_center_id,
                        'costCenter' => $rowCostCenter->cost_center,
                        'percentage' => $rowCostCenter->percentage,
                    ];
                }

                $arrReimburseSelected = [];
                $getFormReimburse = $modelProcurement->docApprovalOrderApplicationReimburse()
                                                    ->from('doc_approval_order_application_reimburse')
                                                    ->select('application_reimburse_id', 'reimburse_to', 'percentage')
                                                    ->where('application_id', $referenceId)
                                                    ->where('is_active', '1')
                                                    ->orderBy('application_reimburse_id', 'asc')
                                                    ->get();
                foreach ($getFormReimburse as $rowReimburse) {
                    $arrReimburseSelected[] = [
                        'reimburseId' => $rowReimburse->application_reimburse_id,
                        'reimburseTo' => $rowReimburse->reimburse_to,
                        'percentage' => $rowReimburse->percentage,
                    ];
                }

                $arrBudgetSelected = [];
                $getBudgetSelected = $modelProcurement->docApprovalOrderApplicationBudget()
                                            ->from('doc_approval_order_application_budget')
                                            ->select('budget_id', 'budget_no', 'budget_amount', 'within_over', 'budget_remaining')
                                            ->where('application_id', $referenceId)
                                            ->where('company_id', $companyId)
                                            ->where('is_active', '1')
                                            ->orderBy('application_budget_id', 'asc')
                                            ->get();
                foreach ($getBudgetSelected as $rowBudgetSelected) {
                    $budgetAmount = $this->formatNumber($rowBudgetSelected->budget_amount);
                    $budgetRemaining = $this->formatNumber($rowBudgetSelected->budget_remaining);
                    $arrBudgetSelected[] = [
                        'budgetId' => $rowBudgetSelected->budget_id,
                        'budgetNo' => $rowBudgetSelected->budget_no,
                        'budgetAmount' => ($rowBudgetSelected->budget_amount < 0) ? '('.Str::replace('-', '', $budgetAmount).')' : $budgetAmount,
                        'withinOver' => $rowBudgetSelected->within_over,
                        'budgetRemaining' => ($rowBudgetSelected->budget_remaining < 0) ? '('.Str::replace('-', '', $budgetRemaining).')' : $budgetRemaining,
                    ];
                }

                // $budgetWithin = '';
                // if($getApplication->budget_within) {
                //     if($getApplication->budget_within < 0) {
                //         $getApplication->budget_within = str_replace('-', '', $getApplication->budget_within);
                //         $budgetWithin = '('.number_format($getApplication->budget_within, 0, '.', ',').')';
                //     }
                //     else {
                //         $budgetWithin = number_format($getApplication->budget_within, 0, '.', ',');
                //     }
                // }

                $vendorId = $vendorName = $vendorAddress =  $vendorPicId = $vendorPicName = null;
                if(($getApplication->application_form_status == '6' || $getApplication->application_form_status == '12' || $type == 'OLD_COMPARISON_REVISE_APPLICATION' || $type == 'DELETE_COMPARISON_REVISE_APPLICATION') && $comparisonId) {
                    // GET VENDOR SELECTED LATEST ACTIVE COMPARISON FORM
                    $getVendor = $modelProcurement->masterVendor()
                                                ->from('master_vendor as a')
                                                ->leftJoin('master_vendor_pic as b', function($join) {
                                                    $join->on('b.vendor_id', 'a.vendor_id')
                                                        ->where('b.is_active', '1');
                                                })
                                                ->select('a.vendor_id', 'a.vendor_name', 'a.vendor_address', 'b.vendor_pic_id', 'b.pic_name')
                                                ->where('a.company_id', $companyId)
                                                ->where('a.vendor_id', $vendorIdSelected)
                                                ->first();
                    if($getVendor) {
                        $vendorId = $getVendor->vendor_id;
                        $vendorName = $getVendor->vendor_name;
                        $vendorAddress = $getVendor->vendor_address;
                        $vendorPicId = $getVendor->vendor_pic_id;
                        $vendorPicName = $getVendor->pic_name;
                    }
                }
                else {
                    $vendorId = $getApplication->vendor_id;
                    $vendorName = $getApplication->vendor_name;
                    $vendorAddress = $getApplication->vendor_address;
                    $vendorPicId = $getApplication->vendor_pic_id;
                    $vendorPicName = $getApplication->pic_name;
                }

                $arrDelivery = preg_split('/\r\n|\r|\n/', $getApplication->application_delivery);
                $deliveryName = $arrDelivery[0];
                $deliveryAddress = str_replace($deliveryName, '', $getApplication->application_delivery);

                $dataForm = $dataForm->merge([
                    'compareItem' => $compareItem,
                    'applicationId' => $getApplication->application_id,
                    'applicationNumber' => $applicationNumber,
                    'applicationHeader' => $getApplication->application_header,
                    'titleApplication' => $getApplication->application_title,
                    'purposeReason' => $getApplication->application_reason,
                    'purposeRemarks' => $getApplication->application_remark,
                    'totalPriceVat' => $totalPriceVat,
                    'vendorSelected' => $vendorSelected,
                    // 'submittedDate' => $getApplication->application_submitted_date,
                    'submittedDate' => '',
                    // 'vendorId' => $vendorId,
                    // 'vendorName' => $vendorName,
                    // 'vendorAddress' => $vendorAddress,
                    // 'vendorPicId' => $vendorPicId,
                    // 'vendorPicName' => $vendorPicName,
                    'deliveryId' => $getApplication->delivery_to_id,
                    'deliveryName' => $deliveryName,
                    'deliveryAddress' => Str::trim($deliveryAddress),
                    'invoiceToId' => $getApplication->invoice_to_id,
                    'invoiceToAddress' => $getApplication->application_invoice,
                    'quotationList' => $quotationList,
                    'currencySelected' => $currencySelected,
                    'purchaseTypeSelected' => $getApplication->purchase_type_id,
                    'formTypeSelected' => $getApplication->form_type,
                    // 'budgetNo' => ($getApplication->budget_no) ?: '',
                    // 'budgetAmount' => ($getApplication->budget_amount) ? number_format($getApplication->budget_amount, 0, '.', ',') : '',
                    // 'budgetWithinOver' => ($getApplication->budget_within_over) ?: '',
                    // 'budgetWithin' => $budgetWithin,
                    // 'reimburseTo' => ($getApplication->reimburse_to) ?: '',
                    // 'reimburseToNext' => ($getApplication->reimburse_to_next) ?: '',
                    'paymentType' => $getApplication->payment_type,
                    'shipDate' => $getApplication->ship_date,
                    'costCenterSelected' => $arrCostCenterSelected,
                    'reimburseToSelected' => $arrReimburseSelected,
                    'budgetSelected' => $arrBudgetSelected,
                    'vat' => $itemVat,
                    'pph' => $itemPPh,
                    'deliveryFee' => $itemDeliveryFee,
                ]);

                $getCompany = $modelProcurement->masterCompany()
                                            ->select('company_id', 'company_name', 'company_title', 'company_address')
                                            ->where('company_id', $companyId)
                                            ->where('is_active', '=', '1')
                                            ->orderBy('company_id', 'desc')
                                            ->first();
                $company = [
                                'companyId' => $getCompany->company_id,
                                'companyName' => Str::trim($getCompany->company_name),
                                'companyTitle' => Str::trim($getCompany->company_title),
                                'companyAddress' => Str::trim($getCompany->company_address),
                            ];

                $arrCcy = [];
                $getCcy = $modelProcurement->masterCurrency()
                                            ->select('currency_id', 'currency_code')
                                            ->where('is_active', '=', '1')
                                            ->orderBy('currency_code', 'asc')
                                            ->get();
                foreach ($getCcy as $rowCcy) {
                    $arrCcy[] = [
                                    'value' => $rowCcy->currency_code,
                                    'label' => $rowCcy->currency_code
                                ];
                }

                $arrPurchaseType = [];
                $getPurchaseType = $modelProcurement->masterPurchaseType()
                                            ->from('master_purchase_type as a')
                                            ->leftJoin('master_application_form as b', 'b.application_form_id', 'a.application_form_id')
                                            ->leftJoin('master_budget_no as c', 'c.budget_no_id', 'a.budget_no_id')
                                            ->select('a.purchase_type_id', 'a.format_number', 'a.purchase_type', 'b.application_form_type', 'c.budget_no', 'a.form_type')
                                            ->where('a.is_active', '=', '1')
                                            ->orderBy('a.purchase_type', 'asc')
                                            ->get();
                foreach ($getPurchaseType as $rowPurchaseType) {
                    $arrPurchaseType[] = [
                                        'value' => $rowPurchaseType->purchase_type_id,
                                        'label' => $rowPurchaseType->purchase_type,
                                        'applicationType' => $rowPurchaseType->application_form_type,
                                        'formatNumber' => $rowPurchaseType->format_number,
                                        'budgetNo' => $rowPurchaseType->budget_no,
                                        'formType' => $rowPurchaseType->form_type,
                                    ];
                }

                $arrCostCenter = [];
                $getCostCenter = $modelProcurement->vwMasterCostCenter()
                                            ->select('cost_center', 'cost_center_short', 'name')
                                            ->where('company_id', $companyId)
                                            ->whereNotNull('cost_center')
                                            ->orderBy('cost_center', 'asc')
                                            ->groupBy('cost_center', 'name')
                                            ->get();
                foreach ($getCostCenter as $rowCostCenter) {
                    $arrCostCenter[] = [
                                        'id' => $rowCostCenter->cost_center_short,
                                        'text' => $rowCostCenter->cost_center_short,
                                        'description1' => $rowCostCenter->name,
                                    ];
                }

                $arrBudget = [];
                $getBudget = $modelProcurement->docApprovalOrderApplicationBudget()
                                            ->select('budget_id', 'budget_no', 'budget_amount')
                                            ->where('company_id', $companyId)
                                            ->orderBy('application_budget_id', 'asc')
                                            ->get();
                foreach ($getBudget as $rowBudget) {
                    $arrBudget[] = [
                                        'id' => $rowBudget->budget_id,
                                        'text' => $rowBudget->budget_no,
                                        'description1' => $rowBudget->budget_amount,
                                    ];
                }

                $dataForm = $dataForm->merge([
                    'company' => $company,
                    'currency' => $arrCcy,
                    'purchaseType' => $arrPurchaseType,
                    'costCenter' => $arrCostCenter,
                    'budgetList' => $arrBudget,
                ]);

                // APPLICATION PO APPROVAL
                $arrSigner = [];
                $getSigner = $modelProcurement->docApprovalMatrix()
                                            ->from('doc_approval_matrix as a')
                                            ->leftJoin('vw_master_employee_active as b', function($join) use ($companyId) {
                                                $join->on('b.employee_id', 'a.employee_id_approval')
                                                    ->where('b.company_id', $companyId);
                                            })
                                            ->select('a.matrix_id', 'a.employee_id_approval', 'b.employee_name', 'b.position_name', 'a.matrix_as')
                                            ->where('a.company_id', $companyId)
                                            ->where('a.employee_id', $employeeId)
                                            ->where('a.doc_type_id', $documentTypeId)
                                            ->where('a.is_active', '1')
                                            // ->whereNotIn('a.matrix_as', ['RECEIVER'])
                                            ->where('a.matrix_as', 'APPLICANT')
                                            ->where('a.is_active', '1')
                                            ->where('a.is_deleted', '0')
                                            ->orderBy('a.matrix_order', 'asc')
                                            ->orderBy('a.matrix_id', 'asc')
                                            ->first();
                if($getSigner) {
                    $arrSigner[$getSigner->matrix_as][] = [
                        'employeeId' => $getSigner->employee_id_approval,
                        'employeeName' => Str::upper($getSigner->employee_name),
                        'positionName' => $getSigner->position_name,
                        'matrixAs' => $getSigner->matrix_as,
                        'matrixId' => $getSigner->matrix_id,
                    ];
                }

                $response['title'] = $getApplication->application_header;
                $actionButton = '';
                if($type == 'REVISE' || $type == 'OLD_COMPARISON_REVISE_APPLICATION') {
                    $actionButton .= '<div class="col-sm-12 col-md-3 d-flex justify-content-center justify-content-md-end mb-2 px-0">
                                        <button type="button" class="btn btn-secondary w-100 w-md-auto me-md-2 actionBtn" data-type="REVISE" data-form="COMPARISON_FORM" data-token="'.$tokenReviseComparison.'">
                                            <i class="fa-regular fa-pen-to-square"></i> Revise Comparison Form
                                        </button>
                                    </div>';
                }

                $actionButton .= '<div class="col-sm-12 col-md-3 d-flex justify-content-center justify-content-md-end mb-2 px-0">
                                    <button type="button" class="btn btn-info w-100 w-md-auto me-md-2 submitForm" data-type="NEW" data-form="APPLICATION_FORM" data-token="'.$encodedToken.'">
                                        <i class="fa-regular fa-file-import"></i> Submit Application & PO Form
                                    </button>
                                </div>';

                $dataForm = $dataForm->merge([
                    'signer' => $arrSigner,
                ]);
            }
            else {
                $response['form'] = '<div class="row min-vh-75">
                                        <div class="d-flex justify-content-center align-items-center" style="flex: 1 1 auto">
                                            <div class="text-center">
                                                <i class="fa-light fa-file-circle-xmark fs-1"></i>
                                                <div class="d-block fs-7 mt-2">Your account is not allowed to access this form</div>
                                            </div>
                                        </div>
                                    </div>';
                $response['footerButton'] = '<div class="container-fluid p-0">
                                                <div class="row justify-content-center w-100 mx-0">
                                                    <div class="col-sm-12 col-md-4 d-flex justify-content-center justify-content-md-end mb-2 px-0">
                                                        <button type="button" class="btn btn-default w-100 w-md-auto me-md-2" data-coreui-dismiss="modal" title="Close">
                                                            <i class="fas fa-xmark"></i> Close
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>';

                $getForm = $modelApproval->documentApprovalHeader()
                                    ->from('doc_approval_header as a')
                                    ->leftJoin('doc_approval_detail_order_form as b', 'b.doc_approval_id', '=', 'a.doc_approval_id')
                                    ->select('a.year', 'a.company_id', 'b.currency_code', 'a.priority', 'a.priority_name')
                                    ->where('a.doc_approval_id', $docApprovalId)
                                    ->where('a.is_active', 1)
                                    ->latest('b.order_form_id')
                                    ->first();
                if(!$getForm) {
                    return response()->json([
                        'status' => 404,
                        'message' => 'Invalid token form',
                        'data' => $response,
                    ], 422);
                }
                else if(!in_array($getForm->company_id, $companyIdList)) {
                    return response()->json([
                        'status' => 404,
                        'message' => 'Your account is not allowed to access this form',
                        'data' => $response,
                    ], 404);
                }

                $companyId = $getForm->company_id;
                $yearForm = $getForm->year;
                $token = ['cid' => $companyId, 'id' => $employeeId, 'a' => $docApprovalId,'b' => $referenceId,'c' => $comparisonId,'d' => null];
                $encodedToken = SafeToken::encode($token);

                $tokenFormOrder = ['cid' => $companyId, 'id' => $employeeId, 'a' => $docApprovalId,'b' => $referenceId,'c' => null,'d' => null];
                $tokenFormOrder = SafeToken::encode($tokenFormOrder);

                $getPurpose = $modelProcurement->docApprovalDetailOrderFormPurpose()
                                                ->from('doc_approval_detail_order_form_purpose as a')
                                                ->select('description', 'reason', 'remarks')
                                                ->where('company_id', $companyId)
                                                ->where('doc_approval_id', $docApprovalId)
                                                ->where('order_form_id', $referenceId)
                                                ->where('is_active', '1')
                                                ->orderBy('order_purpose_id', 'desc')
                                                ->first();

                $itemVat = $itemPPh = $itemDeliveryFee = 0;
                $dataForm = $dataForm->merge([
                    'tokenForm'=> $encodedToken,
                    'companyId' => $companyId,
                    'tokenFormOrder'=> $tokenFormOrder,
                    'yearForm' => Carbon::parse($yearForm)->format('Y'),
                    'currencyCode' => $getForm->currency_code,
                    'requestType' => $type,
                    'purposeDescription' => $getPurpose->description,
                    'purposeReason' => $getPurpose->reason,
                    'purposeRemarks' => $getPurpose->remarks,
                    'priority' => $getForm->priority,
                    'termPaymentId' => $getForm->term_payment_id,
                ]);

                if($comparisonId != null) {
                    $getComparisonForm = $modelProcurement->docApprovalOrderComparison()
                                                        ->from('doc_approval_order_comparison as a')
                                                        ->leftJoin('doc_approval_order_comparison_vendor as b', function($join) {
                                                            $join->on('b.comparison_id', 'a.comparison_id')
                                                                ->on('b.vendor_id', 'a.vendor_id_selected');
                                                        })
                                                        ->select('a.comparison_id', 'a.doc_approval_id', 'a.comparison_title', 'a.comparison_description', 'a.comparison_date', 'a.validity', 'a.vendor_id_selected','a.comparison_notes', 'b.comparison_vendor_id', 'b.vendor_name', 'b.vendor_address_id', 'b.vendor_address', 'b.vendor_pic_id', 'b.pic_name')
                                                        ->where('a.company_id', $companyId)
                                                        ->where('a.order_form_id', $referenceId)
                                                        ->where('a.comparison_id', $comparisonId)
                                                        ->where('a.is_active', '1')
                                                        ->orderBy('a.comparison_id', 'desc')
                                                        ->first();
                    if($getComparisonForm) {
                        $tokenComparison = ['cid' => $companyId, 'id' => $employeeId, 'a' => $getComparisonForm->doc_approval_id,'b' => $referenceId,'c' => $comparisonId,'d' => null];
                        $tokenComparison = SafeToken::encode($tokenComparison);

                        $dataForm = $dataForm->merge([
                            'tokenFormComparison'=> $tokenComparison,
                        ]);

                        $vendorSelected = [
                            'vendorId' => $getComparisonForm->vendor_id_selected,
                            'vendorName' => $getComparisonForm->vendor_name,
                            'vendorAddressId' => $getComparisonForm->vendor_address_id,
                            'vendorAddress' => $getComparisonForm->vendor_address,
                            'vendorPicId' => $getComparisonForm->vendor_pic_id,
                            'vendorPic' => $getComparisonForm->pic_name,
                        ];

                        $getFormItem = $modelProcurement->docApprovalOrderComparisonItemVendor()
                                                    ->from('doc_approval_order_comparison_item_vendor as a')
                                                    ->leftJoin('doc_approval_order_comparison_item as b', function($join) {
                                                        $join->on('b.comparison_id', 'a.comparison_id')
                                                            ->on('b.comparison_item_id', 'a.comparison_item_id');
                                                    })
                                                    ->leftJoin('doc_approval_detail_order_form_item as c', function($join) {
                                                        $join->on('c.order_detail_id', 'b.order_detail_id');
                                                    })

                                                    ->select('b.comparison_item_id', 'b.order_detail_id', 'c.revise_order_detail_id', 'b.appliance_item', 'b.unit_name', 'b.unit_quantity', 'a.currency_code', 'a.unit_price', 'a.dpp_other_value', 'a.total_price', 'b.currency as currency_symbol')
                                                    ->where('a.company_id', $companyId)
                                                    ->where('b.order_form_id', $referenceId)
                                                    ->where('a.comparison_id', $getComparisonForm->comparison_id)
                                                    ->where('a.comparison_vendor_id', $getComparisonForm->comparison_vendor_id)
                                                    ->where('a.is_active', '1')
                                                    ->orderBy('b.comparison_item_id', 'asc')
                                                    ->get();

                        // $getFormItem = $modelProcurement->docApprovalOrderComparisonItem()
                        //                             ->from('doc_approval_order_comparison_item')
                        //                             ->select('comparison_item_id', 'order_detail_id', 'appliance_item', 'unit_name', 'unit_quantity', 'currency as currency_code')
                        //                             ->where('order_form_id', $referenceId)
                        //                             ->where('comparison_id', $getComparisonForm->comparison_id)
                        //                             ->where('is_active', '1')
                        //                             ->orderBy('comparison_item_id', 'asc')
                        //                             ->get();
                    }
                    else {
                        $dataForm = $dataForm->merge([
                            'tokenFormComparison'=> null,
                        ]);
                    }
                }
                else{
                    $dataForm = $dataForm->merge([
                        'tokenFormComparison'=> null,
                    ]);
                    $getFormItem = $modelProcurement->documentApprovalDetailOrderItem()
                                                ->from('doc_approval_detail_order_form_item')
                                                ->select('order_detail_id', 'revise_order_detail_id', 'cost_center', 'appliance_item', 'brand_type', 'unit_name', 'unit_quantity', 'currency_code', 'unit_price_estimated', 'total_price_estimated', 'required_date', DB::raw('NULL as currency_symbol'))
                                                ->where('company_id', $companyId)
                                                ->where('doc_approval_id', $docApprovalId)
                                                ->where('order_form_id', $referenceId)
                                                ->where('unit_name', '<>', 'VAT')
                                                ->whereNull('comparison_id')
                                                ->whereNull('application_id')
                                                ->whereNull('canceled_id')
                                                ->where('is_active', '1')
                                                ->orderBy('order_detail_id', 'asc')
                                                ->get();
                }

                $arrItem = [];
                $totalPriceVat = '';
                foreach ($getFormItem as $rowFormItem) {
                    $unitPrice = $dppOtherValue = $totalPrice = '';
                    if($comparisonId != null) {
                        $unitPrice = number_format($rowFormItem->unit_price, 2, '.', ',');
                        $totalPrice = number_format($rowFormItem->total_price, 2, '.', ',');
                        $dppOtherValue = $rowFormItem->dpp_other_value;

                        if($rowFormItem->appliance_item == 'Total Price + VAT - PPh') {
                            $totalPriceVat = number_format($rowFormItem->total_price, 2, '.', ',');
                        }
                    }

                    $reviseOrderDetailId = $rowFormItem->revise_order_detail_id ?: '';
                    $arrItem[] = [
                                    'orderDetailId' => $rowFormItem->order_detail_id,
                                    'reviseOrderDetailId' => $reviseOrderDetailId,
                                    'applianceItem' => $rowFormItem->appliance_item,
                                    'unitName' => $rowFormItem->unit_name,
                                    'unitQuantity' => $rowFormItem->unit_quantity,
                                    'currency' => $rowFormItem->currency_code,
                                    'currencySymbol' => $rowFormItem->currency_code,
                                    'unitPrice' => $unitPrice,
                                    'dppOtherValue' => $dppOtherValue,
                                    'totalPrice' => $totalPrice,
                                ];

                    if($rowFormItem->appliance_item == 'VAT') {
                        $itemVat = Str::replace(',', '', $this->formatNumber($rowFormItem->total_price));
                    }
                    else if($rowFormItem->appliance_item == 'PPh') {
                        $itemPPh = Str::replace(',', '', $this->formatNumber($rowFormItem->total_price));
                    }
                    else if($rowFormItem->appliance_item == 'Delivery Fee') {
                        $itemDeliveryFee = Str::replace(',', '', $this->formatNumber($rowFormItem->total_price));
                    }
                }

                // dd($arrItem);
                if(count($arrItem) == 0) {
                    $response['form'] = '<div class="row min-vh-75">
                                            <div class="d-flex justify-content-center align-items-center" style="flex: 1 1 auto">
                                                <div class="text-center">
                                                    <i class="fa-light fa-file-circle-xmark fs-1"></i>
                                                    <div class="d-block fs-7 mt-2">No order item unprocessed</div>
                                                </div>
                                            </div>
                                        </div>';
                    $response['footerButton'] = '<div class="container-fluid p-0">
                                                    <div class="row justify-content-center w-100 mx-0">
                                                        <div class="col-sm-12 col-md-4 d-flex justify-content-center justify-content-md-end mb-2 px-0">
                                                            <button type="button" class="btn btn-default w-100 w-md-auto me-md-2" data-coreui-dismiss="modal" title="Close">
                                                                <i class="fas fa-xmark"></i> Close
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>';

                    return response()->json([
                        'status' => 404,
                        'message' => 'No order item unprocessed',
                        'data' => $response,
                    ], 404);
                }

                $currency = $getFormItem->pluck('currency_code')->unique();
                $quotationList = [];
                if($comparisonId != null) {
                    $compareItem = true;
                    // $getVendor = $modelProcurement->masterVendor()
                    //                             ->from('master_vendor as a')
                    //                             ->leftJoin('master_vendor_pic as b', function($join) {
                    //                                 $join->on('b.vendor_id', 'a.vendor_id')
                    //                                     ->where('b.is_active', '1');
                    //                             })
                    //                             ->select('a.vendor_id', 'a.vendor_name')
                    //                             ->where('a.company_id', $companyId)
                    //                             ->where('a.vendor_id', $vendorIdSelected)
                    //                             ->first();
                    // if($getVendor) {
                    //     $vendorId = $getVendor->vendor_id;
                    //     $vendorName = $getVendor->vendor_name;
                    //     // $vendorAddress = $getVendor->vendor_address;
                    //     // $vendorPicId = $getVendor->vendor_pic_id;
                    //     // $vendorPicName = $getVendor->pic_name;

                    //     $getVendorAddress = $modelProcurement->masterVendor()
                    //                                     ->from('master_vendor_address as b')
                    //                                     ->select('b.vendor_address_id as addressId', 'b.vendor_address as addressName', 'b.vendor_phone as vendorPhone')
                    //                                     ->where('b.vendor_id', $getVendor->vendor_id)
                    //                                     ->first();

                    //     $getVendorPic = $modelProcurement->masterVendor()
                    //                                     ->from('master_vendor_pic as b')
                    //                                     ->select('b.vendor_pic_id as picId', 'b.pic_name as picName', 'b.pic_email as picEmail', 'b.pic_phone as picPhone')
                    //                                     ->where('b.vendor_id', $getVendor->vendor_id)
                    //                                     ->first();
                    // }

                    $getQuotation = $modelProcurement->docApprovalAttachment()
                                                ->from('doc_approval_attachment')
                                                ->select('attachment_id', 'mime_type', 'filename', 'filename_original', 'filename_converted')
                                                ->where('company_id', $companyId)
                                                ->where('doc_approval_id', $docApprovalId)
                                                ->where('doc_type_id', '6')
                                                ->where('reference_id', $comparisonId)
                                                ->get();
                    foreach($getQuotation as $row) {
                        $tokenFile = ['a' => 'DOC_APPROVAL', 'b' => $row->attachment_id];
                        $tokenFile = SafeToken::encode($tokenFile);

                        $quotationList[] = [
                                            'tokenAttachment' => $tokenFile,
                                            'icon' => $this->getFileIcon($row->mime_type, $row->filename_original),
                                            'filename' => $row->filename_original,
                                        ];
                    }
                }
                else{
                    $compareItem = false;
                    $applianceItem = ['Delivery Fee', 'Discount', 'Total Price', 'VAT', 'PPh', 'Total Price + VAT - PPh'];
                    for($x = 0; $x < count($applianceItem); $x++) {
                        if($applianceItem[$x] == 'VAT'){
                            $currencySymbol = '%';
                        }
                        else {
                            $currencySymbol = $currency;
                        }

                        $arrItem[] = [
                            'orderDetailId' => '',
                            'reviseOrderDetailId' => '',
                            'comparisonItemId' => '',
                            'applianceItem' => $applianceItem[$x],
                            'unitName' => '',
                            'unitQuantity' => '',
                            'currency' => $currency,
                            'currencySymbol' => $currencySymbol,
                            'unitPrice' => '',
                            'dppOtherValue' => '',
                            'totalPrice' => '',
                        ];
                    }
                }

                $arrBudgetSelected[] = [
                    'budgetId' => '',
                    'budgetNo' => '',
                    'budgetAmount' => '',
                    'withinOver' => 'Within',
                    'budgetRemaining' => '',
                ];

                $arrReimburseSelected[] = [
                    'reimburseId' => '',
                    'reimburseTo' => '',
                    'percentage' => '',
                ];

                $dataForm = $dataForm->merge([
                    'compareItem' => $compareItem,
                    'applicationId' => null,
                    'applicationNumber' => '',
                    'applicationHeader' => '',
                    'titleApplication' => '',
                    'submittedDate' => '',
                    'totalPriceVat' => $totalPriceVat,
                    'vendorSelected' => $vendorSelected,
                    'currencySelected' => '',
                    'deliveryId' => null,
                    'deliveryName' => '',
                    'invoiceToId' => null,
                    'invoiceToAddress' => '',
                    'deliveryAddress' => '',
                    'quotationList' => $quotationList,
                    'purchaseTypeSelected' => null,
                    'formTypeSelected' => null,
                    'budgetNo' => '',
                    'budgetAmount' => '',
                    'budgetWithinOver' => 'Within',
                    'budgetWithin' => '',
                    'reimburseTo' => '',
                    'reimburseToNext' => '',
                    'paymentType' => '1',
                    'shipDate' => '',
                    'costCenterSelected' => [],
                    'reimburseToSelected' => $arrReimburseSelected,
                    'budgetSelected' => $arrBudgetSelected,
                    'vat' => $itemVat,
                    'pph' => $itemPPh,
                    'deliveryFee' => $itemDeliveryFee,
                    // 'comparisonTitle' => '',
                    // 'comparisonDescription' => '',
                    // 'comparisonDate' => '',
                    // 'validity' => '',
                    // 'vendorIdSelected' => '',
                    // 'comparisonNotes' => '',
                ]);

                $getCompany = $modelProcurement->masterCompany()
                                            ->select('company_id', 'company_name', 'company_title', 'company_address')
                                            ->where('company_id', $companyId)
                                            ->where('is_active', '=', '1')
                                            ->orderBy('company_id', 'desc')
                                            ->first();
                $company = [
                                'companyId' => $getCompany->company_id,
                                'companyName' => Str::trim($getCompany->company_name),
                                'companyTitle' => Str::trim($getCompany->company_title),
                                'companyAddress' => Str::trim($getCompany->company_address),
                            ];

                $arrCcy = [];
                // $getCcy = $modelProcurement->masterCurrency()
                //                             ->select('currency_id', 'currency_code')
                //                             ->where('is_active', '=', '1')
                //                             ->orderBy('currency_code', 'asc')
                //                             ->get();
                // foreach ($getCcy as $rowCcy) {
                //     $arrCcy[] = [
                //                     'value' => $rowCcy->currency_code,
                //                     'label' => $rowCcy->currency_code
                //                 ];
                // }

                $arrPurchaseType = [];
                $getPurchaseType = $modelProcurement->masterPurchaseType()
                                            ->from('master_purchase_type as a')
                                            ->leftJoin('master_application_form as b', 'b.application_form_id', 'a.application_form_id')
                                            ->leftJoin('master_budget_no as c', 'c.budget_no_id', 'a.budget_no_id')
                                            ->select('a.purchase_type_id', 'a.format_number', 'a.purchase_type', 'b.application_form_type', 'c.budget_no', 'a.form_type')
                                            ->where('a.is_active', '=', '1')
                                            ->orderBy('a.purchase_type', 'asc')
                                            ->get();
                foreach ($getPurchaseType as $rowPurchaseType) {
                    $arrPurchaseType[] = [
                                        'value' => $rowPurchaseType->purchase_type_id,
                                        'label' => $rowPurchaseType->purchase_type,
                                        'applicationType' => $rowPurchaseType->application_form_type,
                                        'formatNumber' => $rowPurchaseType->format_number,
                                        'budgetNo' => $rowPurchaseType->budget_no,
                                        'formType' => $rowPurchaseType->form_type,
                                    ];
                }

                $arrCostCenter = [];
                $getCostCenter = $modelProcurement->vwMasterCostCenter()
                                            ->select('cost_center', 'cost_center_short', 'name')
                                            ->where('company_id', $companyId)
                                            ->whereNotNull('cost_center')
                                            ->orderBy('cost_center', 'asc')
                                            ->groupBy('cost_center', 'name')
                                            ->get();
                foreach ($getCostCenter as $rowCostCenter) {
                    $arrCostCenter[] = [
                                        'id' => $rowCostCenter->cost_center_short,
                                        'text' => $rowCostCenter->cost_center_short,
                                        'description1' => $rowCostCenter->name,
                                    ];
                }

                $arrBudget = [];
                $getBudget = $modelProcurement->docApprovalOrderApplicationBudget()
                                            ->select('budget_id', 'budget_no', 'budget_amount')
                                            ->where('company_id', $companyId)
                                            ->orderBy('application_budget_id', 'asc')
                                            ->get();
                foreach ($getBudget as $rowBudget) {
                    $arrBudget[] = [
                                        'id' => $rowBudget->budget_id,
                                        'text' => $rowBudget->budget_no,
                                        'description1' => $rowBudget->budget_amount,
                                    ];
                }

                $dataForm = $dataForm->merge([
                    'company' => $company,
                    'currency' => $arrCcy,
                    'purchaseType' => $arrPurchaseType,
                    'costCenter' => $arrCostCenter,
                    'budgetList' => $arrBudget,
                ]);

                // APPLICATION PO APPROVAL
                $arrSigner = [];
                $getSigner = $modelProcurement->docApprovalMatrix()
                                            ->from('doc_approval_matrix as a')
                                            ->leftJoin('vw_master_employee_active as b', function($join) use ($companyId) {
                                                $join->on('b.employee_id', 'a.employee_id_approval')
                                                    ->where('b.company_id', $companyId);
                                            })
                                            ->select('a.matrix_id', 'a.employee_id_approval', 'b.employee_name', 'b.position_name', 'a.matrix_as')
                                            ->where('a.company_id', $companyId)
                                            ->where('a.employee_id', $employeeId)
                                            ->where('a.doc_type_id', $documentTypeId)
                                            ->where('a.is_active', '1')
                                            // ->whereNotIn('a.matrix_as', ['RECEIVER'])
                                            ->where('a.matrix_as', 'APPLICANT')
                                            ->where('a.is_active', '1')
                                            ->where('a.is_deleted', '0')
                                            ->orderBy('a.matrix_order', 'asc')
                                            ->orderBy('a.matrix_id', 'asc')
                                            ->first();
                if($getSigner) {
                    $arrSigner[$getSigner->matrix_as][] = [
                        'employeeId' => $getSigner->employee_id_approval,
                        'employeeName' => Str::upper($getSigner->employee_name),
                        'positionName' => $getSigner->position_name,
                        'matrixAs' => $getSigner->matrix_as,
                        'matrixId' => $getSigner->matrix_id,
                    ];
                }
                else {
                    $response['form'] = '<div class="row min-vh-75">
                                            <div class="d-flex justify-content-center align-items-center" style="flex: 1 1 auto">
                                                <div class="text-center">
                                                    <i class="fa-light fa-file-circle-xmark fs-1"></i>
                                                    <div class="d-block fs-7 mt-2">Your account is not allowed to access this form</div>
                                                </div>
                                            </div>
                                        </div>';
                    $response['footerButton'] = '<div class="container-fluid p-0">
                                                    <div class="row justify-content-center w-100 mx-0">
                                                        <div class="col-sm-12 col-md-4 d-flex justify-content-center justify-content-md-end mb-2 px-0">
                                                            <button type="button" class="btn btn-default w-100 w-md-auto me-md-2" data-coreui-dismiss="modal" title="Close">
                                                                <i class="fas fa-xmark"></i> Close
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>';

                    return response()->json([
                        'status' => 404,
                        'message' => 'Your account is not allowed to access this form',
                        'data' => $response,
                    ], 404);
                }
                // foreach($getSigner AS $rowSigner) {
                //     if($rowSigner->matrix_as == 'APPLICANT' && $rowSigner->employee_id_approval != $employeeId) {
                //         continue;
                //     }

                //     $approvalName = ($rowSigner->employee_name != null) ? $rowSigner->employee_name : '--';
                //     $arrSigner[$rowSigner->matrix_as][] = [
                //                                                 'employeeId' => $rowSigner->employee_id_approval,
                //                                                 'employeeName' => $approvalName,
                //                                                 'positionName' => $rowSigner->position_name,
                //                                                 'matrixAs' => $rowSigner->matrix_as,
                //                                                 'matrixId' => $rowSigner->matrix_id,
                //                                             ];
                // }

                // if(!array_key_exists('APPLICANT', $arrSigner)) {
                //     $response['form'] = '<div class="row min-vh-75">
                //                             <div class="d-flex justify-content-center align-items-center" style="flex: 1 1 auto">
                //                                 <div class="text-center">
                //                                     <i class="fa-light fa-file-circle-xmark fs-1"></i>
                //                                     <div class="d-block fs-7 mt-2">Your account is not allowed to access this form</div>
                //                                 </div>
                //                             </div>
                //                         </div>';
                //     $response['footerButton'] = '<div class="container-fluid p-0">
                //                                     <div class="row justify-content-center w-100 mx-0">
                //                                         <div class="col-sm-12 col-md-4 d-flex justify-content-center justify-content-md-end mb-2 px-0">
                //                                             <button type="button" class="btn btn-default w-100 w-md-auto me-md-2" data-coreui-dismiss="modal" title="Close">
                //                                                 <i class="fas fa-xmark"></i> Close
                //                                             </button>
                //                                         </div>
                //                                     </div>
                //                                 </div>';

                //     return response()->json([
                //         'status' => 404,
                //         'message' => 'Your account is not allowed to access this form',
                //         'data' => $response,
                //     ], 404);
                // }

                $dataForm = $dataForm->merge([
                    'signer' => $arrSigner,
                ]);

                $response['title'] = 'APPLICATION FORM';
                // $actionButton = '<div class="col-sm-12 col-md-3 d-flex justify-content-center justify-content-md-end mb-2 px-0 d-none">
                //                     <button type="button" class="btn btn-secondary w-100 w-md-auto me-md-2 submitForm" data-type="NEW_PREVIEW" data-form="APPLICATION_FORM" data-token="'.$encodedToken.'">
                //                         <i class="fa-regular fa-magnifying-glass-waveform"></i> Preview Application & PO Form
                //                     </button>
                //                 </div>';
                $actionButton = '<div class="col-sm-12 col-md-3 d-flex justify-content-center justify-content-md-end mb-2 px-0">
                                    <button type="button" class="btn btn-secondary w-100 w-md-auto me-md-2 actionBtn" data-type="REVISE" data-form="COMPARISON_FORM" data-token="'.$encodedToken.'">
                                        <i class="fa-regular fa-pen-to-square"></i> Revise Comparison Form
                                    </button>
                                </div>
                                <div class="col-sm-12 col-md-3 d-flex justify-content-center justify-content-md-end mb-2 px-0">
                                    <button type="button" class="btn btn-info w-100 w-md-auto me-md-2 submitForm" data-type="NEW" data-form="APPLICATION_FORM" data-token="'.$encodedToken.'">
                                        <i class="fa-regular fa-file-import"></i> Submit Application & PO Form
                                    </button>
                                </div>';
            }

            $response['footerButton'] = '<div class="container-fluid p-0">
                                            <div class="row justify-content-center w-100 mx-0">
                                                <div class="col-sm-12 col-md-3 d-flex justify-content-center justify-content-md-end mb-2 px-0">
                                                    <button type="button" class="btn btn-default w-100 w-md-auto me-md-2" data-coreui-dismiss="modal" title="Close">
                                                        <i class="fas fa-xmark"></i> Close
                                                    </button>
                                                </div>
                                                '.$actionButton.'
                                            </div>
                                        </div>';

            $dataForm->put('documentType', $documentTypeId);
            $dataForm->put('itemForm', $arrItem);
            $dataForm->put('companyId', $companyId);
            $dataForm->put('companyIdEncode', SafeToken::encode(['c' => $companyId]));

            $getPriority = $modelApproval->masterPriorityLevel()
                            ->select('priority_id as id', 'priority_name as text', 'priority_description as desc')
                            ->where(function($getPriority) {
                                $getPriority->whereNull('priority_type')
                                        ->orWhere('priority_type', '=', 'PURCHASING');
                            })
                            ->where('is_active', '=', '1')
                            ->orderBy('priority_id', 'asc')
                            ->get();
            $dataForm->put('priorityLevel', $getPriority);

            $getTermPayment = $modelProcurement->masterTermPayment()
                                        ->select('term_payment_id as id', 'term_payment as text', 'term_payment_desc as desc')
                                        ->where('is_active', '=', '1')
                                        ->orderBy('term_payment_id', 'asc')
                                        ->get();
            $dataForm->put('termPayment', $getTermPayment);

            $requestData = new Request();
            $requestData->replace([
                                    'search' => '',
                                    'data' => 'ALL',
                                    'type' => 'GOODS_DELIVERY',
                                ]);
            $getDelivery = $this->getDelivery($requestData);
            $getDelivery = json_decode($getDelivery->getContent(), true);
            $deliveryList = [];
            foreach($getDelivery as $rowDelivery) {
                $deliveryList[] = ['cid' => $companyId, 'id' => $rowDelivery['deliveryToId'], 'label' => $rowDelivery['deliveryName'], 'description' => $rowDelivery['deliveryAddress']];
            }
            $dataForm->put('deliveryList', $deliveryList);

            $requestData = new Request();
            $requestData->replace([
                                    'search' => '',
                                    'data' => 'ALL',
                                    'type' => 'INVOICE',
                                ]);
            $getInvoiceTo = $this->getDelivery($requestData);
            $getInvoiceTo = json_decode($getInvoiceTo->getContent(), true);
            $invoiceToList = [];
            foreach($getInvoiceTo as $rowInvoiceTo) {
                $invoiceToList[] = ['cid' => $companyId, 'id' => $rowInvoiceTo['deliveryToId'], 'label' => $rowInvoiceTo['deliveryName'], 'description' => $rowInvoiceTo['deliveryAddress']];
            }

            if($type == 'NEW'){
                if(count($getInvoiceTo) == 1) {
                    $invoiceToAddress = $getInvoiceTo[0]['companyName']."\r\n".$getInvoiceTo[0]['deliveryAddress'];

                    $dataForm->put('invoiceToId', $getInvoiceTo[0]['deliveryToId']);
                    $dataForm->put('invoiceToAddress', $invoiceToAddress);
                }
            }

            $dataForm->put('invoiceToList', $invoiceToList);

            // dd($dataForm['signer']);
            $response->put('form', view('procurementpurchasing::form.application_form.new_form', compact('dataForm'))->render());
        }
        else if($form == 'PO_INSPECTION') {
            $encodedToken = $request->token;
            $companyId = $token['cid'];

            $getHeader = $modelApproval->documentApprovalHeader()
                            ->from('doc_approval_header AS a')
                            ->leftJoin('master_transaction_status AS c', 'c.transaction_status_id', 'a.doc_status_id')
                            ->select('a.*', 'c.transaction_status_name')
                            ->where('a.doc_approval_id', $docApprovalId)
                            ->where('a.is_active', '1')
                            ->first();
            if($getHeader) {
                $getForm = $modelProcurement->docApprovalOrderApplication()
                                        ->from('doc_approval_order_application as a')
                                        ->leftJoin('doc_approval_order_group as b', function($join) {
                                            $join->on('b.application_id', 'a.application_id')
                                                ->where('b.is_active', '1');
                                        })
                                        ->select('a.doc_approval_id', 'a.application_id', 'a.application_form_status', 'a.doc_version', 'a.order_form_id', 'a.comparison_id', 'b.order_group_id', 'a.application_number', 'a.application_header', 'a.application_title', 'a.application_grand_total', 'a.vendor_name', 'a.vendor_address', 'a.application_delivery', 'a.application_reason', 'a.application_currency', 'a.application_submitted_date', 'a.vendor_pic')
                                        ->where('a.application_id', $referenceId)
                                        ->where('a.doc_approval_id', $getHeader->doc_approval_id)
                                        ->where('a.is_active', '1')
                                        ->latest('application_id')
                                        ->first();
                if($getForm) {
                    $arrItem = [];
                    $getFormItem = $modelProcurement->docApprovalOrderApplicationItem()
                                                    ->from('doc_approval_order_application_item')
                                                    ->select('*')
                                                    ->where('company_id', $companyId)
                                                    ->where('application_id', $referenceId)
                                                    ->whereNotIn('appliance_item', ['Delivery Fee', 'Discount', 'Total Price', 'VAT', 'PPh', 'Total Price + VAT - PPh'])
                                                    ->where(function($query) {
                                                        $query->whereNull('incoming_qty')
                                                            ->whereNull('remaining_qty')
                                                            ->orWhere('remaining_qty', '>', 0);
                                                    })
                                                    ->orderBy('application_item_id', 'asc')
                                                    ->get();

                    foreach ($getFormItem as $rowFormItem) {
                        $unitPrice = $totalPrice = '';
                        $unitPrice = number_format($rowFormItem->unit_price, 2, '.', ',');
                        $totalPrice = number_format($rowFormItem->subtotal_price, 2, '.', ',');

                        $arrItem[] = [
                                        'itemId' => $rowFormItem->application_item_id,
                                        'applianceItem' => $rowFormItem->appliance_item,
                                        'unitQuantity' => ($rowFormItem->remaining_qty != null && $rowFormItem->remaining_qty > 0) ? $rowFormItem->remaining_qty : $rowFormItem->quantity,
                                    ];
                    }

                    $arrChecker = $arrConfirmer = [];
                    $getMatrix = $modelProcurement->docApprovalMatrix()
                                                ->from('doc_approval_matrix as a')
                                                ->leftJoin('vw_master_employee_active as b', 'b.employee_id', '=', 'a.employee_id_approval')
                                                ->select('a.matrix_as', 'b.employee_id', 'b.employee_name', 'b.position_name')
                                                ->where('a.doc_type_id', '4')
                                                ->where('a.company_id', $companyId)
                                                ->where('a.department_id', $getHeader->department_id)
                                                ->where('a.employee_id', $employeeId)
                                                ->whereIn('a.matrix_as', ['INSPECTION_CHECKER','INSPECTION_CONFIRMER'])
                                                ->whereNotNull('b.employee_id')
                                                ->where('a.is_active', '1')
                                                ->orderBy('a.matrix_order', 'asc')
                                                ->get();
                    foreach ($getMatrix as $rowMatrix) {
                        if($rowMatrix->matrix_as === 'INSPECTION_CHECKER') {
                            $arrChecker[] = [
                                'id' => $rowMatrix->employee_id,
                                'text' => $rowMatrix->employee_name,
                                'description1' => $rowMatrix->position_name
                            ];
                        }
                        else if($rowMatrix->matrix_as === 'INSPECTION_CONFIRMER') {
                            $arrConfirmer[] = [
                                'id' => $rowMatrix->employee_id,
                                'text' => $rowMatrix->employee_name,
                                'description1' => $rowMatrix->position_name
                            ];
                        }
                    }

                    $response['data'] = [
                        'poDate' => Carbon::parse($getForm->application_submitted_date)->format('d-M-Y'),
                        'vendor' => $getForm->vendor_name."\n".$getForm->vendor_address,
                        'deliveryTo' => $getForm->application_delivery,
                        'checker' => $arrChecker,
                        'confirmer' => $arrConfirmer,
                        'item' => $arrItem,
                    ];
                }
            }
        }
        // else if($form == 'INSPECTION_FORM') {
        //     $documentTypeId = '4';
        //     $response['title'] = 'Inspection Form';

        //     $getInspectionForm = $modelProcurement->docApprovalOrderInspection()
        //                                         ->from('doc_approval_order_inspection as a')
        //                                         ->leftJoin('doc_approval_order_application as b', 'b.application_id', '=', 'a.application_id')
        //                                         ->leftJoin('doc_approval_order_group as c', function($join) {
        //                                             $join->on('c.application_id', 'b.application_id')
        //                                                 ->where('c.is_active', '1');
        //                                         })
        //                                         ->select('a.*', 'b.comparison_id', 'b.company_id', 'c.order_group_id')
        //                                         // ->where('a.company_id', $companyId)
        //                                         ->where('a.application_id', $referenceId)
        //                                         ->where('a.is_active', '1')
        //                                         ->first();

        //     $token = ['cid' => $getInspectionForm->company_id, 'id' => $employeeId, 'a' => $docApprovalId,'b' => $referenceId,'c' => $getInspectionForm->comparison_id,'d' => $getInspectionForm->inspection_id, 'g'=>$getInspectionForm->order_group_id];
        //     $encodedToken = SafeToken::encode($token);
        //     $response['tokenForm'] = $encodedToken;

        //     $getInspectionItem = $modelProcurement->docApprovalOrderInspectionItem()
        //                                         ->from('doc_approval_order_inspection_item')
        //                                         ->select('*')
        //                                         ->where('company_id', $getInspectionForm->company_id)
        //                                         ->where('inspection_id', $getInspectionForm->inspection_id)
        //                                         ->where('application_id', $referenceId)
        //                                         ->where('is_active', '1')
        //                                         ->orderBy('inspection_item_id', 'asc')
        //                                         ->get();
        //     $arrItem = [];
        //     foreach($getInspectionItem as $rowInspectionItem) {
        //         $arrItem[] = [
        //             'applianceItem' => $rowInspectionItem->appliance_item,
        //             'quantity' => $rowInspectionItem->quantity,
        //         ];
        //     }

        //     $dataForm = collect([
        //         'poNumber' => $getInspectionForm->po_number,
        //         'poDate' => $getInspectionForm->po_date,
        //         'incomingDate' => $getInspectionForm->incoming_date,
        //         'vendorId' => $getInspectionForm->vendor_id,
        //         'vendorName' => $getInspectionForm->vendor_name,
        //         'vendorAddress' => $getInspectionForm->vendor_address,
        //         'inspectionDelivery' => $getInspectionForm->inspection_delivery,
        //         'shipDate' => $getInspectionForm->ship_date,
        //         'inspectionRemarks' => $getInspectionForm->inspection_remarks,
        //         'itemForm' => $arrItem,
        //         'filePath' => $getInspectionForm->path_detail,
        //         'filename' => $getInspectionForm->filename,
        //     ]);

        //     $requestData = new Request();
        //     $requestData->replace([
        //                             'documentType' => '4',
        //                             'dataForm' => compact('dataForm'),
        //                         ]);
        //     $documentApprovalController = new ControllersDocumentApprovalController();
        //     $createInspectionForm = $documentApprovalController->generatePdf($requestData);

        //     $actionButton = '<div class="col-sm-12 col-md-3 d-flex justify-content-center justify-content-md-end mb-2 px-0">
        //                             <button type="button" class="btn btn-info w-100 w-md-auto me-md-2 submitForm" data-type="NEW" data-form="INSPECTION" data-token="'.$encodedToken.'">
        //                                 <i class="fa-regular fa-file-import"></i> Send Inspection Form
        //                             </button>
        //                         </div>';
        // }

        $response['footerButton'] = '<div class="container-fluid p-0">
                                        <div class="row justify-content-center w-100 mx-0">
                                            <div class="col-sm-12 col-md-3 d-flex justify-content-center justify-content-md-end mb-2 px-0">
                                                <button type="button" class="btn btn-default w-100 w-md-auto me-md-2" data-coreui-dismiss="modal" title="Close">
                                                    <i class="fas fa-xmark"></i> Close
                                                </button>
                                            </div>
                                            '.$actionButton.'
                                        </div>
                                    </div>';

        return response()->json([
            'status' => 200,
            'message' => 'Success',
            'data' => $response,
        ], 200);
    }

    public function getApprovalMatrix(Request $request) {
        $employeeId = $this->employeeId;
        $companyId = $this->companyId;
        $employeeName = $this->employeeName;
        $roleId = $this->roleId;
        $documentTypeId = $request->input('documentTypeId');
        $modelProcurement = new ProcurementPurchasingModel();
        $arrSigner = [];
        $getSigner = $modelProcurement->docApprovalMatrix()
                                    ->from('doc_approval_matrix as a')
                                    ->leftJoin('vw_master_employee_active as b', function($join) use ($companyId) {
                                        $join->on('b.employee_id', 'a.employee_id_approval')
                                            ->where('b.company_id', $companyId);
                                    })
                                    ->select('a.matrix_id', 'a.employee_id_approval', 'b.employee_name', 'b.position_name', 'a.matrix_as')
                                    ->where('a.company_id', $companyId)
                                    ->where('a.employee_id', $employeeId)
                                    ->where('a.doc_type_id', $documentTypeId)
                                    ->where('a.is_active', '1')
                                    ->whereNotIn('a.matrix_as', ['RECEIVER', 'APPLICANT'])
                                    ->orderBy('a.matrix_order', 'asc')
                                    ->orderBy('a.matrix_id', 'asc')
                                    ->get();

        foreach($getSigner AS $rowSigner) {
            $approvalName = ($rowSigner->employee_name != null) ? Str::upper($rowSigner->employee_name) : '--';
            $arrSigner[] = [
                                'employeeId' => $rowSigner->employee_id_approval,
                                'employeeName' => $approvalName,
                                'positionName' => $rowSigner->position_name,
                                'matrixAs' => $rowSigner->matrix_as,
                                'matrixId' => $rowSigner->matrix_id,
                            ];
        }

        return $arrSigner;
    }

    public function getVendor(Request $request) {
        $employeeId = $this->employeeId;
        $companyId = $this->companyId;

        $dataType = $request->data;
        $modelProcurement = new ProcurementPurchasingModel();
        $results = [];
        $search = $request->search;

        $getData = $modelProcurement->masterVendor()
                                    ->from('master_vendor as a')
                                    ->leftJoin('master_vendor_pic as b', function($join) {
                                        $join->on('b.vendor_id', 'a.vendor_id')
                                            ->where('b.is_active', '1');
                                    })
                                    ->where(function($query) use ($companyId) {
                                        $query->whereNull('a.company_id')
                                            ->orWhere('a.company_id', $companyId);
                                    });

        if($dataType == 'SELECT_HEADER') {
            $getData  = $getData->select('a.vendor_id', 'a.vendor_name')
                                ->where(function($query) use ($search) {
                                    $query->where('a.vendor_name', 'like', '%'.$search.'%')
                                        ->orWhere('a.vendor_keywords', 'like', '%'.$search.'%')
                                        ->orWhere('b.pic_name', 'like', '%'.$search.'%');
                                });
        }
        else if($dataType == 'DETAIL_FORM') {
            $getData  = $getData->select('a.vendor_id', 'a.vendor_name')
                                ->where('a.vendor_id', $search);
        }

        $getData  = $getData->where('a.is_active', '1')
                            ->orderBy('a.vendor_name', 'asc')
                            ->groupBy('a.vendor_id','a.vendor_name')
                            ->get();

        if($dataType == 'SELECT_HEADER') {
            $results = $getData->map(function ($rowVendor) {
                // if($rowVendor->pic_name){
                //     $description = $rowVendor->pic_name.'<br>'.$rowVendor->vendor_address;
                // }
                // else {
                //     $description = $rowVendor->vendor_address;
                // }

                return [
                    'value' => $rowVendor->vendor_id,
                    'label' => $rowVendor->vendor_name,
                    // 'description' => $description,
                    'vendorPicId' => $rowVendor->vendor_pic_id ?: '',
                    'vendorPicName' => $rowVendor->pic_name ?: '',
                    'vendorAddress' => $rowVendor->vendor_address ?: '',
                ];
            });
        }
        else if($dataType == 'DETAIL_FORM') {
            $results = $getData->map(function ($rowVendor) {
                $token = ['id' => $rowVendor->vendor_id,'a' => $rowVendor->vendor_pic_id];
                $encodedToken = SafeToken::encode($token);

                return [
                    'vendorName' => $rowVendor->vendor_name ?: '',
                    'vendorAddress' => $rowVendor->vendor_address ?: '',
                    'vendorPicName' => $rowVendor->pic_name ?: '',
                    'vendorPicId' => $rowVendor->vendor_pic_id ?: '',
                    'vendorPicEmail' => $rowVendor->pic_email ?: '',
                    'tokenForm' => $encodedToken,
                ];
            });
        }

        return response()->json($results);
    }

    public function getVendorDetails(Request $request) {
        $employeeId = $this->employeeId;
        $companyId = $this->companyId;

        $dataType = $request->data;
        $modelProcurement = new ProcurementPurchasingModel();
        $results = [];
        $vendorId = $request->id;
        $type = $request->type;
        $getVendor = $getVendorComponents = $getVendorPurchaseType = null;

        $getVendorAddress = $modelProcurement->masterVendor()
                                        ->from('master_vendor as a')
                                        ->leftJoin('master_vendor_address as b', function($join) {
                                            $join->on('b.vendor_id', 'a.vendor_id')
                                                ->where('b.is_active', '1');
                                        })
                                        ->select('b.vendor_address_id as addressId', 'b.vendor_address as addressName', 'b.vendor_phone as vendorPhone')
                                        ->where(function($query) use ($companyId) {
                                            $query->whereNull('a.company_id')
                                                ->orWhere('a.company_id', $companyId);
                                        })
                                        ->where('b.vendor_id', $vendorId)
                                        ->where('b.is_active', '1')
                                        ->orderBy('b.vendor_address_id', 'asc')
                                        ->get();

        $getVendorPic = $modelProcurement->masterVendor()
                                        ->from('master_vendor as a')
                                        ->leftJoin('master_vendor_pic as b', function($join) {
                                            $join->on('b.vendor_id', 'a.vendor_id')
                                                ->where('b.is_active', '1');
                                        })
                                        ->select('b.vendor_pic_id as picId', 'b.pic_name as picName', 'b.pic_email as picEmail', 'b.pic_phone as picPhone')
                                        ->where(function($query) use ($companyId) {
                                            $query->whereNull('a.company_id')
                                                ->orWhere('a.company_id', $companyId);
                                        })
                                        ->where('b.vendor_id', $vendorId)
                                        ->where('b.is_active', '1')
                                        ->orderBy('b.pic_name', 'asc')
                                        ->get();

        $getVendorSite = $modelProcurement->masterVendor()
                                        ->from('master_vendor as a')
                                        ->leftJoin('master_vendor_site as b', function($join) {
                                            $join->on('b.vendor_id', 'a.vendor_id')
                                                ->where('b.is_active', '1');
                                        })
                                        ->select('b.site_id as siteId', 'b.location_id as locationId', 'b.location_name as locationName')
                                        ->where(function($query) use ($companyId) {
                                            $query->whereNull('a.company_id')
                                                ->orWhere('a.company_id', $companyId);
                                        })
                                        ->where('b.vendor_id', $vendorId)
                                        ->where('b.is_active', '1')
                                        ->orderBy('b.location_name', 'asc')
                                        ->get();

        if($type == 'ALL') {
            $getVendor = $modelProcurement->masterVendor()
                                    ->from('master_vendor as a')
                                    ->select('a.vendor_account as vendorAccount', 'a.vendor_name as vendorName', 'a.group', 'a.currency')
                                    ->where(function($query) use ($companyId) {
                                        $query->whereNull('a.company_id')
                                            ->orWhere('a.company_id', $companyId);
                                    })
                                    ->where('a.vendor_id', $vendorId)
                                    ->first();

            $getVendorComponents = $modelProcurement->masterVendor()
                                            ->from('master_vendor as a')
                                            ->leftJoin('master_vendor_components as b', function($join) {
                                                $join->on('b.vendor_id', 'a.vendor_id')
                                                    ->where('b.is_active', '1');
                                            })
                                            ->select('b.component_vendor_id as componentVendorId', 'b.component_id as componentId', 'b.component_name as componentName')
                                            ->where(function($query) use ($companyId) {
                                                $query->whereNull('a.company_id')
                                                    ->orWhere('a.company_id', $companyId);
                                            })
                                            ->where('b.vendor_id', $vendorId)
                                            ->where('b.is_active', '1')
                                            ->orderBy('b.component_name', 'asc')
                                            ->get();

            $getVendorPurchaseType = $modelProcurement->masterVendor()
                                            ->from('master_vendor as a')
                                            ->leftJoin('master_vendor_purchase_type as b', function($join) {
                                                $join->on('b.vendor_id', 'a.vendor_id')
                                                    ->where('b.is_active', '1');
                                            })
                                            ->select('b.purchase_type_vendor_id as purchaseTypeVendorId', 'b.purchase_type_group_id as purchaseTypeGroupId', 'b.purchase_type_group_name as purchaseTypeName')
                                            ->where(function($query) use ($companyId) {
                                                $query->whereNull('a.company_id')
                                                    ->orWhere('a.company_id', $companyId);
                                            })
                                            ->where('b.vendor_id', $vendorId)
                                            ->where('b.is_active', '1')
                                            ->orderBy('b.purchase_type_group_name', 'asc')
                                            ->get();
        }
        else if($type == 'WITH_HEADER') {
            $getVendor = $modelProcurement->masterVendor()
                                    ->from('master_vendor as a')
                                    ->select('a.vendor_account as vendorAccount', 'a.vendor_name as vendorName', 'a.group', 'a.currency')
                                    ->where(function($query) use ($companyId) {
                                        $query->whereNull('a.company_id')
                                            ->orWhere('a.company_id', $companyId);
                                    })
                                    ->where('a.vendor_id', $vendorId)
                                    ->first();
        }

        $token = ['id' => $vendorId];
        $encodedToken = SafeToken::encode($token);

        $results = collect([
            'token' => $encodedToken,
            'vendor' => $getVendor,
            'vendorAddress' => $getVendorAddress,
            'vendorPic' => $getVendorPic,
            'vendorComponents' => $getVendorComponents,
            'vendorPurchaseType' => $getVendorPurchaseType,
            'vendorSite' => $getVendorSite,
        ]);

        return response()->json([
            'status' => 200,
            'message' => 'Success',
            'data' => $results,
        ], 200);
    }

    public function getDelivery(Request $request) {
        $employeeId = $this->employeeId;
        $companyId = $this->companyId;

        $dataType = $request->data;
        $modelProcurement = new ProcurementPurchasingModel();
        $results = [];
        $search = $request->search;
        $type = $request->type;

        $getData = $modelProcurement->masterDelivery()
                                    ->from('master_purchase_delivery')
                                    ->select('delivery_to_id', 'code', 'company_name', 'delivery_name', 'delivery_address');

        if($type == 'GOODS_DELIVERY') {
           $getData = $getData->where('type', 'GOODS_DELIVERY');
        }
        else if($type == 'INVOICE') {
           $getData = $getData->where('type', 'INVOICE');
        }

        if($dataType == 'SELECT_HEADER') {
           $getData = $getData->where(function($query) use ($search) {
                            $query->where('code', 'like', '%'.$search.'%')
                                ->orWhere('delivery_name', 'like', '%'.$search.'%')
                                ->orWhere('delivery_address', 'like', '%'.$search.'%');
                        });
        }
        else if($dataType == 'DETAIL_FORM') {
            $getData = $getData->where('delivery_to_id', $search);
        }

        $getData = $getData->where(function($query) use ($companyId) {
                                $query->whereNull('company_id')
                                    ->orWhere('company_id', $companyId);
                            })
                            ->where('is_active', '1')
                            ->orderBy('delivery_name', 'asc')
                            ->get();

        if($dataType == 'SELECT_HEADER') {
            $results = $getData->map(function ($rowDelivery) {
                // if($rowVendor->pic_name){
                //     $description = $rowVendor->pic_name.'<br>'.$rowVendor->vendor_address;
                // }
                // else {
                //     $description = $rowVendor->vendor_address;
                // }

                // $deliveryName = $rowDelivery->code != null ? $rowDelivery->code.' - '.$rowDelivery->company_name : $rowDelivery->company_name;
                return [
                    'value' => $rowDelivery->delivery_to_id,
                    'label' => $rowDelivery->delivery_name,
                    'deliveryAddress' => $rowDelivery->delivery_address ?: '',
                    'companyName' => $rowDelivery->company_name ?: '',
                ];
            });
        }
        else if($dataType == 'DETAIL_FORM') {
            $results = $getData->map(function ($rowDelivery) {
                $token = ['id' => $rowDelivery->delivery_to_id];
                $encodedToken = SafeToken::encode($token);

                return [
                    'deliveryToId' => $rowDelivery->delivery_to_id,
                    'companyName' => $rowDelivery->company_name ?: '',
                    'deliveryName' => $rowDelivery->delivery_name ?: '',
                    'deliveryAddress' => $rowDelivery->delivery_address ?: '',
                    'tokenForm' => $encodedToken,
                ];
            });
        }
        else {
            $results = $getData->map(function ($rowDelivery) {
                return [
                    'deliveryToId' => $rowDelivery->delivery_to_id,
                    'code' => $rowDelivery->code ?: '',
                    'companyName' => $rowDelivery->company_name,
                    'deliveryName' => $rowDelivery->delivery_name,
                    'deliveryAddress' => $rowDelivery->delivery_address,
                ];
            });
        }

        return response()->json($results);
    }

    public function saveDelivery(Request $request) {
        $employeeId = $this->employeeId;
        $model = new ProcurementPurchasingModel();
        try {
            $validatedData = $request->validate([
                'companyId' => 'required|string',
                'companyNameDelivery' => 'required|string|max:255',
                'companyDeliveryAddress' => 'required|string|max:255',
            ], [
                'companyId.required' => 'Company ID is required',
                'companyNameDelivery.required' => 'Delivery name is required',
                'companyDeliveryAddress.required' => 'Delivery address is required',
            ]);

            $token = SafeToken::decode($request->companyId);
            $companyId = $token['c'];

            $getCompany = $model->masterCompany()
                                ->select('company_title')
                                ->where('company_id', $companyId)
                                ->where('is_active', '=', '1')
                                ->latest('company_id')
                                ->first();
            $dataInsert = [
                'company_id' => $companyId,
                // 'code' => Str::trim($request->codeDelivery),
                'type' => 'GOODS_DELIVERY',
                'company_name' => $getCompany->company_title,
                'delivery_name' => Str::trim($request->companyNameDelivery),
                'delivery_address' => Str::trim($request->companyDeliveryAddress),
                'created_by' => $employeeId,
                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
            ];
            $model->masterDelivery()->create($dataInsert);

            $message = 'Successfully Save New Delivery';
            return response()->json(['message' => $message], 200);
        }
        catch (ValidationException $e) {
            return response()->json(['message' => 'Please fill the required form.', 'errors' => $e->errors()], 422)
                            ->setStatusCode(422, 'Please fill the required form.');
        }
    }

    public function addVendor(Request $request) {
        $employeeId = $this->employeeId;
        $companyId = $this->companyId;
        $modelProcurement = new ProcurementPurchasingModel();
        try {
            $validateRules = [
                // 'vendorAccount' => 'required|string|max:100',
                'vendorAccount' => [
                    'required',
                    'string',
                    'max:100',
                    function ($attribute, $value, $fail) use ($modelProcurement) {
                        $cleanedValue = Str::squish($value);
                        if ($cleanedValue !== '-' &&
                            $modelProcurement->masterVendor()
                                ->where('vendor_account', $cleanedValue)
                                ->exists()) {
                            $fail('Vendor account already exists');
                        }
                    }
                ],
                'vendorCompanyName' => 'required|string|max:255',
                'vendorGroup' => 'required|string|max:50',
                'vendorCcy' => 'required|string|max:10',
                // 'vendorComponent.0' => 'required|int',
                // 'vendorPurchaseTypeGroup.0' => 'required|int',
                'vendorAddress.0' => 'required|string|max:255',
                'vendorPicName.0' => 'required|string|max:255',
            ];
            $validateMessages = [
                'vendorAccount.required' => 'Vendor account is required',
                'vendorCompanyName.required' => 'Vendor name is required',
                'vendorGroup.required' => 'Group is required',
                'vendorCcy.required' => 'Currency is required',
                // 'vendorComponent.0.required' => 'Component is required',
                // 'vendorPurchaseTypeGroup.0' => 'Purchase type is required',
                'vendorAddress.0.required' => 'Vendor address is required',
                'vendorPicName.0.required' => 'Vendor PIC name is required',
            ];

            foreach ($request->vendorPhone ?: [] as $key => $value) {
                if (!empty($value)) {
                    $validateRules["vendorPhone.{$key}"] = 'numeric';
                    $validateMessages["vendorPhone.{$key}.numeric"] = 'Phone number must be numeric';
                }
            }

            foreach ($request->vendorPicEmail ?: [] as $key => $value) {
                if (!empty($value)) {
                    $validateRules["vendorPicEmail.{$key}"] = 'required|email';
                    $validateMessages["vendorPicEmail.{$key}.required"] = 'Email not valid';
                }
            }

            foreach ($request->vendorPicPhone ?: [] as $key => $value) {
                if (!empty($value)) {
                    $validateRules["vendorPicPhone.{$key}"] = 'numeric';
                    $validateMessages["vendorPicPhone.{$key}.numeric"] = 'Phone number must be numeric';
                }
            }

            $request->validate($validateRules, $validateMessages);

            $dataInsert = [
                'company_id' => $companyId,
                'vendor_account' => Str::squish($request->vendorAccount),
                'vendor_name' => $this->formatCompanyName($request->vendorCompanyName),
                'group' => $request->vendorGroup,
                'currency' => $request->vendorCcy,
                'created_by' => $employeeId,
                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
            ];
            $insertVendor = $modelProcurement->masterVendor()->create($dataInsert);

            if(!empty($request->vendorComponent)) {
                for($x = 0; $x < count($request->vendorComponent); $x++){
                    $getComponents = $modelProcurement->masterPurchaseComponents()
                                                        ->from('master_purchase_components as a')
                                                        ->select('a.component_name')
                                                        // ->where('a.company_id', $companyId)
                                                        ->where('a.component_id', $request->vendorComponent[$x])
                                                        ->where('a.is_active', '1')
                                                        ->first();

                    $dataInsert = [
                        'vendor_id' => $insertVendor->id,
                        'component_id' => $request->vendorComponent[$x],
                        'component_name' => $getComponents->component_name,
                        'is_active' => '1',
                    ];
                    $insertComponent = $modelProcurement->masterVendorComponents()->create($dataInsert);
                }
            }

            if(!empty($request->vendorPurchaseTypeGroup)) {
                for($x = 0; $x < count($request->vendorPurchaseTypeGroup); $x++){
                    $getPurchaseTypeGroup = $modelProcurement->masterPurchaseTypeGroup()
                                                        ->from('master_purchase_type_group as a')
                                                        ->select('a.purchase_type_group_name')
                                                        ->where(function($query) use ($companyId) {
                                                            $query->whereNull('a.company_id')
                                                                ->orWhere('a.company_id', $companyId);
                                                        })
                                                        ->where('a.purchase_type_group_id', $request->vendorPurchaseTypeGroup[$x])
                                                        ->where('a.is_active', '1')
                                                        ->first();

                    $dataInsert = [
                        'vendor_id' => $insertVendor->id,
                        'purchase_type_group_id' => $request->vendorPurchaseTypeGroup[$x],
                        'purchase_type_group_name' => $getPurchaseTypeGroup->purchase_type_group_name,
                        'is_active' => '1',
                    ];
                    $insertPurchaseTypeGroup = $modelProcurement->masterVendorPurchaseType()->create($dataInsert);
                }
            }

            if(!empty($request->vendorSite)) {
                for($x = 0; $x < count($request->vendorSite); $x++){
                    $getLocation = $modelProcurement->masterLocations()
                                                    ->from('master_locations as a')
                                                    ->select('a.location_name')
                                                    ->where(function($query) use ($companyId) {
                                                        $query->whereNull('a.company_id')
                                                            ->orWhere('a.company_id', $companyId);
                                                    })
                                                    ->where('a.location_id', $request->vendorSite[$x])
                                                    ->where('a.is_active', '1')
                                                    ->first();

                    $dataInsert = [
                        'vendor_id' => $insertVendor->id,
                        'location_id' => $request->vendorSite[$x],
                        'location_name' => $getLocation->location_name,
                        'is_active' => '1',
                    ];
                    $insertSite = $modelProcurement->masterVendorSite()->create($dataInsert);
                }
            }

            foreach ($request->vendorAddress as $index => $row) {
                $vendorAddress =  Str::of($request->vendorAddress[$index])
                                        ->replaceMatches('/\n\s+/', "\n")                       // Hapus spasi setelah newline
                                        ->replaceMatches('/\s+\n/', "\n")                       // Hapus spasi sebelum newline
                                        ->replaceMatches('/[ ]{2,}/', ' ')                      // Ganti spasi ganda
                                        ->replaceMatches('/\n{2,}/', "\n")                      // Ganti newline berlebih
                                        ->trim();
                $vendorPhone = Str::of($request->vendorPhone[$index])->trim();
                $dataInsert = [
                    'vendor_id' => $insertVendor->id,
                    'vendor_address' => $vendorAddress,
                    'vendor_phone' => $vendorPhone != '' ? $vendorPhone : null,
                    'is_active' => '1',
                ];
                $insertAddress = $modelProcurement->masterVendorAddress()->create($dataInsert);
            }

            for($x = 0; $x < count($request->vendorPicName); $x++){
                $vendorPicName = Str::of($request->vendorPicName[$x])->trim();
                $vendorPicEmail = Str::of($request->vendorPicEmail[$x])->trim()->lower();
                $vendorPicPhone = Str::of($request->vendorPicPhone[$x])->trim();

                $dataInsert = [
                    'vendor_id' => $insertVendor->id,
                    'pic_name' => $vendorPicName,
                    'pic_email' => $vendorPicEmail != '' ? $vendorPicEmail : null,
                    'pic_phone' => $vendorPicPhone != '' ? $vendorPicPhone : null,
                    'is_active' => '1',
                ];
                $insertPic = $modelProcurement->masterVendorPic()->create($dataInsert);
            }

            $message = 'Successfully Save New Vendor';
            return response()->json(['message' => $message], 200);
        }
        catch (ValidationException $e) {
            return response()->json(['message' => 'Please fill the required form.', 'errors' => $e->errors()], 422)
                            ->setStatusCode(422, 'Please fill the required form.');
        }
    }

    public function updateVendor(Request $request) {
        $token = SafeToken::decode($request->tokenForm);
        $validator = Validator::make($token, [
            'id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => 'Invalid token form', 'errors' => $validator->errors()], 422)
                            ->setStatusCode(422, 'Invalid token form');
        }

        $vendorId = $token['id'];

        $employeeId = $this->employeeId;
        $companyId = $this->companyId;
        $modelProcurement = new ProcurementPurchasingModel();
        try {
            $validateRules = [
                // 'vendorAccount' => 'required|string|max:100',
                'vendorAccount' => [
                    'required',
                    'string',
                    'max:100',
                    // function ($attribute, $value, $fail) use ($modelProcurement) {
                    //     $cleanedValue = Str::squish($value);
                    //     if ($cleanedValue !== '-' &&
                    //         $modelProcurement->masterVendor()
                    //             ->where('vendor_account', $cleanedValue)
                    //             ->exists()) {
                    //         $fail('Vendor account already exists');
                    //     }
                    // }
                ],
                'vendorCompanyName' => 'required|string|max:255',
                'vendorGroup' => 'required|string|max:50',
                'vendorCcy' => 'required|string|max:10',
                // 'vendorComponent.0' => 'required|int',
                // 'vendorPurchaseTypeGroup.0' => 'required|int',
                'vendorAddress.0' => 'required|string|max:255',
                'vendorPicName.0' => 'required|string|max:255',
            ];
            $validateMessages = [
                'vendorAccount.required' => 'Vendor account is required',
                'vendorCompanyName.required' => 'Vendor name is required',
                'vendorGroup.required' => 'Group is required',
                'vendorCcy.required' => 'Currency is required',
                // 'vendorComponent.0.required' => 'Component is required',
                // 'vendorPurchaseTypeGroup.0' => 'Purchase type is required',
                'vendorAddress.0.required' => 'Vendor address is required',
                'vendorPicName.0.required' => 'Vendor PIC name is required',
            ];

            foreach ($request->vendorPhone ?: [] as $key => $value) {
                if (!empty($value)) {
                    $validateRules["vendorPhone.{$key}"] = 'numeric';
                    $validateMessages["vendorPhone.{$key}.numeric"] = 'Phone number must be numeric';
                }
            }

            foreach ($request->vendorPicEmail ?: [] as $key => $value) {
                if (!empty($value)) {
                    $validateRules["vendorPicEmail.{$key}"] = 'email';
                    $validateMessages["vendorPicEmail.{$key}.email"] = 'Email not valid';
                }
            }

            foreach ($request->vendorPicPhone ?: [] as $key => $value) {
                if (!empty($value)) {
                    $validateRules["vendorPicPhone.{$key}"] = 'numeric';
                    $validateMessages["vendorPicPhone.{$key}.numeric"] = 'Phone number must be numeric';
                }
            }

            $request->validate($validateRules, $validateMessages);

            $dataUpdate = [
                'vendor_account' => Str::squish($request->vendorAccount),
                'vendor_name' => $this->formatCompanyName($request->vendorCompanyName),
                'group' => $request->vendorGroup,
                'currency' => $request->vendorCcy,
                'updated_by' => $employeeId,
                'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
            ];
            $modelProcurement->masterVendor()
                            ->where('vendor_id', $vendorId)
                            ->where('is_active', 1)
                            ->update($dataUpdate);

            if(!empty($request->vendorComponent)) {
                // VENDOR COMPONENT
                $vendorComponentSelected = array_map('intval', (array) $request->vendorComponent);
                $existingVendorComponent = $modelProcurement->masterVendorComponents()
                                                            ->where('vendor_id', $vendorId)
                                                            ->where('is_active', '1')
                                                            ->get()
                                                            ->keyBy('component_id');
                // DISABLED NON SELECTED COMPONENT
                foreach ($existingVendorComponent as $componentId => $row) {
                    if (!in_array($componentId, $vendorComponentSelected)) {
                        $modelProcurement->masterVendorComponents()
                                        ->where('component_id', $componentId)
                                        ->where('vendor_id', $vendorId)
                                        ->update(['is_active' => '0']);
                    }
                }
                // ADD COMPONENT NOT EXIST
                foreach ($vendorComponentSelected as $componentId) {
                    if (!$existingVendorComponent->has($componentId)) {
                        $getComponent = $modelProcurement->masterPurchaseComponents()
                                                    ->select('component_name')
                                                    // ->where('company_id', $companyId)
                                                    ->where('component_id', $componentId)
                                                    ->where('is_active', '1')
                                                    ->first();

                        if ($getComponent) {
                            $modelProcurement->masterVendorComponents()->create([
                                'vendor_id' => $vendorId,
                                'component_id' => $componentId,
                                'component_name' => $getComponent->component_name,
                                'is_active' => '1',
                            ]);
                        }
                    }
                }
                // END VENDOR COMPONENT
            }
            else {
                $existingComponent = $modelProcurement->masterVendorComponents()
                                                    ->where('vendor_id', $vendorId)
                                                    ->where('is_active', '1')
                                                    ->get()
                                                    ->keyBy('component_id');
                // DISABLED NON SELECTED
                foreach ($existingComponent as $componentId => $row) {
                    $modelProcurement->masterVendorComponents()
                                    ->where('component_id', $componentId)
                                    ->where('vendor_id', $vendorId)
                                    ->update(['is_active' => '0']);
                }
            }

            if(!empty($request->vendorPurchaseTypeGroup)) {
                // VENDOR PURCHASE TYPE GROUP
                $vendorPurchaseTypeGroupSelected = array_map('intval', (array) $request->vendorPurchaseTypeGroup);
                $existingPurchaseTypeGroup = $modelProcurement->masterVendorPurchaseType()
                                                            ->where('vendor_id', $vendorId)
                                                            ->where('is_active', '1')
                                                            ->get()
                                                            ->keyBy('purchase_type_group_id');
                // DISABLED NON SELECTED PURCHASE TYPE
                foreach ($existingPurchaseTypeGroup as $purchaseTypeGroupId => $row) {
                    if (!in_array($purchaseTypeGroupId, $vendorPurchaseTypeGroupSelected)) {
                        $modelProcurement->masterVendorPurchaseType()
                                        ->where('purchase_type_group_id', $purchaseTypeGroupId)
                                        ->where('vendor_id', $vendorId)
                                        ->update(['is_active' => '0']);
                    }
                }
                // ADD PURCHASE TYPE NOT EXIST
                foreach ($vendorPurchaseTypeGroupSelected as $purchaseTypeGroupId) {
                    if (!$existingPurchaseTypeGroup->has($purchaseTypeGroupId)) {
                        $getPurchaseTypeGroup = $modelProcurement->masterPurchaseTypeGroup()
                                                        ->select('purchase_type_group_name')
                                                        // ->where('company_id', $companyId)
                                                        ->where('purchase_type_group_id', $purchaseTypeGroupId)
                                                        ->where('is_active', '1')
                                                        ->first();
                        if ($getPurchaseTypeGroup) {
                            $modelProcurement->masterVendorPurchaseType()->create([
                                'vendor_id' => $vendorId,
                                'purchase_type_group_id' => $purchaseTypeGroupId,
                                'purchase_type_group_name' => $getPurchaseTypeGroup->purchase_type_group_name,
                                'is_active' => '1',
                            ]);
                        }
                    }
                }
                // END VENDOR PURCHASE TYPE GROUP
            }
            else {
                $existingTypeGroup = $modelProcurement->masterVendorPurchaseType()
                                                    ->where('vendor_id', $vendorId)
                                                    ->where('is_active', '1')
                                                    ->get()
                                                    ->keyBy('purchase_type_group_id');
                // DISABLED NON SELECTED
                foreach ($existingTypeGroup as $groupId => $row) {
                    $modelProcurement->masterVendorPurchaseType()
                                    ->where('purchase_type_group_id', $groupId)
                                    ->where('vendor_id', $vendorId)
                                    ->update(['is_active' => '0']);
                }
            }

            // VENDOR SITE
            if(!empty($request->vendorSite)) {
                $vendorSiteSelected = array_map('intval', (array) $request->vendorSite);
                $existingVendorSite = $modelProcurement->masterVendorSite()
                                                    ->where('vendor_id', $vendorId)
                                                    ->where('is_active', '1')
                                                    ->get()
                                                    ->keyBy('location_id');
                // DISABLED NON SELECTED SITE LOCATION
                foreach ($existingVendorSite as $locationId => $row) {
                    if (!in_array($locationId, $vendorSiteSelected)) {
                        $modelProcurement->masterVendorSite()
                                        ->where('location_id', $locationId)
                                        ->where('vendor_id', $vendorId)
                                        ->update(['is_active' => '0']);
                    }
                }
                // ADD SITE LOCATION NOT EXIST
                foreach ($vendorSiteSelected as $locationId) {
                    if (!$existingVendorSite->has($locationId)) {
                        $getLocation = $modelProcurement->masterLocations()
                                                        ->select('location_name')
                                                        // ->where('company_id', $companyId)
                                                        ->where('location_id', $locationId)
                                                        ->where('is_active', '1')
                                                        ->first();
                        if ($getLocation) {
                            $modelProcurement->masterVendorSite()->create([
                                'vendor_id' => $vendorId,
                                'location_id' => $locationId,
                                'location_name' => $getLocation->location_name,
                                'is_active' => '1',
                            ]);
                        }
                    }
                }
            }
            else {
                $existingVendorSite = $modelProcurement->masterVendorSite()
                                                    ->where('vendor_id', $vendorId)
                                                    ->where('is_active', '1')
                                                    ->get()
                                                    ->keyBy('location_id');
                // DISABLED NON SELECTED SITE LOCATION
                foreach ($existingVendorSite as $locationId => $row) {
                    $modelProcurement->masterVendorSite()
                                    ->where('location_id', $locationId)
                                    ->where('vendor_id', $vendorId)
                                    ->update(['is_active' => '0']);
                }
            }
            // END VENDOR SITE

            // VENDOR ADDRESS
            $vendorAddressSelectedId = array_map('intval', (array) $request->vendorAddressId);
            $existingVendorAddressId = $modelProcurement->masterVendorAddress()
                                                        ->where('vendor_id', $vendorId)
                                                        ->where('is_active', '1')
                                                        ->get()
                                                        ->keyBy('vendor_address_id');
            // DISABLED NON SELECTED ADDRESS
            foreach ($existingVendorAddressId as $vendorAddressId => $row) {
                if (!in_array($vendorAddressId, $vendorAddressSelectedId)) {
                    $modelProcurement->masterVendorAddress()
                                    ->where('vendor_address_id', $vendorAddressId)
                                    ->where('vendor_id', $vendorId)
                                    ->update(['is_active' => '0']);
                }
            }
            // ADD ADDRESS NOT EXIST
            foreach ($vendorAddressSelectedId as $index => $vendorAddressId) {
                $vendorAddress =  Str::of($request->vendorAddress[$index])
                                        ->replaceMatches('/\n\s+/', "\n")                       // Hapus spasi setelah newline
                                        ->replaceMatches('/\s+\n/', "\n")                       // Hapus spasi sebelum newline
                                        ->replaceMatches('/[ ]{2,}/', ' ')                      // Ganti spasi ganda
                                        ->replaceMatches('/\n{2,}/', "\n")                      // Ganti newline berlebih
                                        ->trim();

                $vendorPhone = Str::of($request->vendorPhone[$index])->trim();

                if ($existingVendorAddressId->has($vendorAddressId)) {
                    $modelProcurement->masterVendorAddress()
                                    ->where('vendor_address_id', $vendorAddressId)
                                    ->where('vendor_id', $vendorId)
                                    ->update([
                                                'vendor_address' => $vendorAddress,
                                                'vendor_phone' => $vendorPhone != '' ? $vendorPhone : null,
                                                'is_active' => '1',
                                            ]);
                }
                else {
                    $dataInsert = [
                        'vendor_id' => $vendorId,
                        'vendor_address' => $vendorAddress,
                        'vendor_phone' => $vendorPhone != '' ? $vendorPhone : null,
                        'is_active' => '1',
                    ];
                    $modelProcurement->masterVendorAddress()->create($dataInsert);
                }
            }
            // END VENDOR ADDRESS

            // VENDOR PIC
            $vendorPicSelectedId = array_map('intval', (array) $request->vendorPicId);
            $existingVendorPicId = $modelProcurement->masterVendorPic()
                                                    ->where('vendor_id', $vendorId)
                                                    ->where('is_active', '1')
                                                    ->get()
                                                    ->keyBy('vendor_pic_id');
            // DISABLED NON SELECTED PIC
            foreach ($existingVendorPicId as $vendorPicId => $row) {
                if (!in_array($vendorPicId, $vendorPicSelectedId)) {
                    $modelProcurement->masterVendorPic()
                                    ->where('vendor_pic_id', $vendorPicId)
                                    ->where('vendor_id', $vendorId)
                                    ->update(['is_active' => '0']);
                }
            }
            // ADD PIC NOT EXIST
            foreach ($vendorPicSelectedId as $index => $vendorPicId) {
                $vendorPicName = Str::of($request->vendorPicName[$index])->trim();
                $vendorPicEmail = Str::of($request->vendorPicEmail[$index])->trim()->lower();
                $vendorPicPhone = Str::of($request->vendorPicPhone[$index])->trim();

                if ($existingVendorPicId->has($vendorPicId)) {
                    $modelProcurement->masterVendorPic()
                                    ->where('vendor_pic_id', $vendorPicId)
                                    ->where('vendor_id', $vendorId)
                                    ->update([
                                                'pic_name' => $vendorPicName,
                                                'pic_email' => $vendorPicEmail != '' ? $vendorPicEmail : null,
                                                'pic_phone' => $vendorPicPhone != '' ? $vendorPicPhone : null,
                                                'is_active' => '1',
                                            ]);
                }
                else {
                    $dataInsert = [
                        'vendor_id' => $vendorId,
                        'pic_name' => $vendorPicName,
                        'pic_email' => $vendorPicEmail != '' ? $vendorPicEmail : null,
                        'pic_phone' => $vendorPicPhone != '' ? $vendorPicPhone : null,
                        'is_active' => '1',
                    ];
                    $modelProcurement->masterVendorPic()->create($dataInsert);
                }
            }
            // END VENDOR PIC

            $message = 'Successfully Updated Vendor';
            return response()->json(['message' => $message], 200);
        }
        catch (ValidationException $e) {
            return response()->json(['message' => 'Please fill the required form.', 'errors' => $e->errors()], 422)
                            ->setStatusCode(422, 'Please fill the required form.');
        }
    }

    public function addOrderFormApplicant(Request $request) {
        $modelProcurement = new ProcurementPurchasingModel();
        try {
            $validateRules = [
                'filterOrderFormCompany' => 'required',
                'employeeApplicantOrder.0' => 'required|string|max:255',
                'employeeDepartmentOrder' => 'required|string|max:255',
                'firstCheckerOrder.0' => 'required|string|max:255',
                'approverOrder.0' => 'required|string|max:255',
                'poApplicant.0' => 'required|string|max:255',
                'inspectionReceiver.0' => 'required|string|max:255',
            ];
            $validateMessages = [
                'filterOrderFormCompany.required' => 'Company is required',
                'employeeApplicantOrder.0.required' => 'Employee is required',
                'employeeDepartmentOrder.required' => 'Department order form is required',
                'firstCheckerOrder.0.required' => 'Checker is required',
                'approverOrder.0.required' => 'Approver is required',
                'poApplicant.0.required' => 'PO Applicant (Order Form receiver) is required',
                'inspectionReceiver.0.required' => 'Inspection receiver is required',
            ];

            $request->validate($validateRules, $validateMessages);

            $token = SafeToken::decode($request->filterOrderFormCompany);
            $companyId = $token['c'];
            $departmentId = $request->employeeDepartmentOrder;

            for($x = 0; $x < count($request->employeeApplicantOrder); $x++){
                $employeeId = $request->employeeApplicantOrder[$x];
                $getExistApprovalMatrix = $modelProcurement->masterPurchaseTypeGroup()
                                                        ->from('doc_approval_matrix as a')
                                                        ->leftJoin('vw_master_employee_active as b', 'a.employee_id', '=', 'b.employee_id')
                                                        ->select('a.matrix_id', 'b.employee_name')
                                                        ->where('a.company_id', $companyId)
                                                        ->where('a.employee_id', $employeeId)
                                                        ->where('a.department_id', $departmentId)
                                                        ->where('a.doc_type_id', '1')
                                                        ->where('a.is_deleted', '0')
                                                        ->first();
                if($getExistApprovalMatrix) {
                    return response()->json(['message' => 'Flow for '.Str::upper($getExistApprovalMatrix->employee_name).' already exist, please edit flow', 'errors' => ''], 422)
                                        ->setStatusCode(422, 'Flow  '.Str::upper($getExistApprovalMatrix->employee_name).' already exist, please edit flow');
                }
            }

            for($y = 0; $y < count($request->employeeApplicantOrder); $y++){
                $employeeId = $request->employeeApplicantOrder[$y];
                $dataApplicant = [
                    'company_id' => $companyId,
                    'doc_type_id' => '1', // 1 = ORDER FORM
                    'employee_id' => $employeeId,
                    'department_id' => $request->employeeDepartmentOrder,
                ];

                for($x = 0; $x < count($request->firstCheckerOrder); $x++){
                    $dataApproval = [
                        'matrix_as' => 'CHECKER_1',
                        'employee_id_approval' => $request->firstCheckerOrder[$x],
                        'is_active' => '1',
                    ];

                    $dataInsert = array_merge($dataApplicant, $dataApproval);
                    $modelProcurement->docApprovalMatrix()->create($dataInsert);
                }

                if($request->secondCheckerOrder) {
                    for($x = 0; $x < count($request->secondCheckerOrder); $x++){
                        $dataApproval = [
                            'matrix_as' => 'CHECKER_2',
                            'employee_id_approval' => $request->secondCheckerOrder[$x],
                            'is_active' => '1',
                        ];

                        $dataInsert = array_merge($dataApplicant, $dataApproval);
                        $modelProcurement->docApprovalMatrix()->create($dataInsert);
                    }
                }

                if($request->ccOrder) {
                    for($x = 0; $x < count($request->ccOrder); $x++){
                        $dataApproval = [
                            'matrix_as' => 'CC',
                            'employee_id_approval' => $request->ccOrder[$x],
                            'is_active' => '1',
                        ];

                        $dataInsert = array_merge($dataApplicant, $dataApproval);
                        $modelProcurement->docApprovalMatrix()->create($dataInsert);
                    }
                }

                for($x = 0; $x < count($request->approverOrder); $x++){
                    $dataApproval = [
                        'matrix_as' => 'APPROVER',
                        'employee_id_approval' => $request->approverOrder[$x],
                        'is_active' => '1',
                    ];

                    $dataInsert = array_merge($dataApplicant, $dataApproval);
                    $modelProcurement->docApprovalMatrix()->create($dataInsert);
                }

                if($request->poApplicant) {
                    for($x = 0; $x < count($request->poApplicant); $x++){
                        $dataApproval = [
                            'matrix_as' => 'RECEIVER',
                            'employee_id_approval' => $request->poApplicant[$x],
                            'is_active' => '1',
                        ];

                        $dataInsert = array_merge($dataApplicant, $dataApproval);
                        $modelProcurement->docApprovalMatrix()->create($dataInsert);
                    }
                }

                if($request->inspectionReceiver) {
                    for($x = 0; $x < count($request->inspectionReceiver); $x++){
                        $getExistReceiver = $modelProcurement->masterPurchaseTypeGroup()
                                                            ->from('doc_approval_matrix')
                                                            ->select('matrix_id', 'is_active')
                                                            ->where('company_id', $companyId)
                                                            ->where('employee_id', $employeeId)
                                                            ->where('department_id', $request->employeeDepartmentOrder)
                                                            ->where('department_id', $request->inspectionReceiver[$x])
                                                            ->where('matrix_as', 'INSPECTION_RECEIVER')
                                                            ->where('doc_type_id', '4')
                                                            ->where('is_deleted', '0')
                                                            ->first();
                        if($getExistReceiver) {
                            if($getExistReceiver->is_active == '0') {
                               $modelProcurement->docApprovalMatrix()
                                                ->where('matrix_id', $getExistReceiver->matrix_id)
                                                ->update(['is_active' => '1']);
                            }
                        }
                        else {
                            $dataInsert = [
                                'company_id' => $companyId,
                                'doc_type_id' => '4', // 4 = INSPECTION FORM
                                'employee_id' => $employeeId,
                                'department_id' => $request->employeeDepartmentOrder,
                                'matrix_as' => 'INSPECTION_RECEIVER',
                                'employee_id_approval' => $request->inspectionReceiver[$x],
                                'matrix_order' => '1',
                                'is_active' => '1',
                            ];

                            $modelProcurement->docApprovalMatrix()->create($dataInsert);
                        }
                    }
                }
            }

            $message = 'Successfully Save Order Form Applicant';
            return response()->json(['message' => $message], 200);
        }
        catch (ValidationException $e) {
            return response()->json(['message' => 'Please fill the required form.', 'errors' => $e->errors()], 422)
                            ->setStatusCode(422, 'Please fill the required form.');
        }
    }

    public function updateOrderFormApplicant(Request $request) {
        $modelProcurement = new ProcurementPurchasingModel();
        $token = SafeToken::decode($request->tokenForm);
        $companyId = $token['c'];
        $employeeId = $token['id'];
        $departmentId = $token['d'];

        if($request->type == 'EDIT_ORDER_FORM_APPLICANT') {
            try {
                $validateRules = [
                    'filterOrderFormCompany' => 'required',
                    'employeeApplicantOrder.0' => 'required|string|max:255',
                    'employeeDepartmentOrder' => 'required|string|max:255',
                    'firstCheckerOrder.0' => 'required|string|max:255',
                    'approverOrder.0' => 'required|string|max:255',
                    'inspectionReceiver.0' => 'required|string|max:255',
                ];
                $validateMessages = [
                    'filterOrderFormCompany.required' => 'Company is required',
                    'employeeApplicantOrder.0.required' => 'Employee is required',
                    'employeeDepartmentOrder.required' => 'Department order form is required',
                    'firstCheckerOrder.0.required' => 'Checker is required',
                    'approverOrder.0.required' => 'Approver is required',
                    'inspectionReceiver.0.required' => 'Inspection receiver is required',
                ];

                $request->validate($validateRules, $validateMessages);
                $dataApplicant = [
                    'company_id' => $companyId,
                    'doc_type_id' => '1', // 1 = ORDER FORM
                    'employee_id' => $employeeId,
                    'department_id' => $request->employeeDepartmentOrder,
                ];

                // REVIEWER 1
                $firstCheckerOrder = array_map('intval', (array) $request->firstCheckerOrder);
                $existFirstCheckerOrder = $modelProcurement->docApprovalMatrix()
                                                        ->select('employee_id_approval')
                                                        ->where('employee_id', $employeeId)
                                                        ->where('company_id', $companyId)
                                                        ->where('department_id', $departmentId)
                                                        ->where('doc_type_id', '1')
                                                        ->where('matrix_as', 'CHECKER_1')
                                                        ->where('is_deleted', '0')
                                                        ->get()
                                                        ->keyBy('employee_id_approval');
                // DISABLED NON SELECTED REVIEWER 1
                foreach ($existFirstCheckerOrder as $employeeIdApproval => $row) {
                    if (!in_array($employeeIdApproval, $firstCheckerOrder)) {
                        $modelProcurement->docApprovalMatrix()
                                        ->where('employee_id', $employeeId)
                                        ->where('company_id', $companyId)
                                        ->where('department_id', $departmentId)
                                        ->where('doc_type_id', '1')
                                        ->where('matrix_as', 'CHECKER_1')
                                        ->where('employee_id_approval', $employeeIdApproval)
                                        ->update(['is_active' => '0']);
                    }
                }
                // ADD REVIEWER 1 NOT EXIST
                foreach ($firstCheckerOrder as $employeeIdApproval) {
                    if (!$existFirstCheckerOrder->has($employeeIdApproval)) {
                        $dataApproval = [
                            'matrix_as' => 'CHECKER_1',
                            'matrix_order' => '1',
                            'employee_id_approval' => $employeeIdApproval,
                            'is_active' => '1',
                        ];

                        $dataInsert = array_merge($dataApplicant, $dataApproval);
                        $modelProcurement->docApprovalMatrix()->create($dataInsert);
                    }
                }
                // END REVIEWER 1

                // REVIEWER 2
                $secondCheckerOrder = array_map('intval', (array) $request->secondCheckerOrder);
                $existSecondCheckerOrder = $modelProcurement->docApprovalMatrix()
                                                        ->select('employee_id_approval')
                                                        ->where('employee_id', $employeeId)
                                                        ->where('company_id', $companyId)
                                                        ->where('department_id', $departmentId)
                                                        ->where('doc_type_id', '1')
                                                        ->where('matrix_as', 'CHECKER_2')
                                                        ->where('is_deleted', '0')
                                                        ->get()
                                                        ->keyBy('employee_id_approval');
                // DISABLED NON SELECTED REVIEWER 2
                foreach ($existSecondCheckerOrder as $employeeIdApproval => $row) {
                    if (!in_array($employeeIdApproval, $secondCheckerOrder)) {
                        $modelProcurement->docApprovalMatrix()
                                        ->where('employee_id', $employeeId)
                                        ->where('company_id', $companyId)
                                        ->where('department_id', $departmentId)
                                        ->where('doc_type_id', '1')
                                        ->where('matrix_as', 'CHECKER_2')
                                        ->where('employee_id_approval', $employeeIdApproval)
                                        ->update(['is_active' => '0']);
                    }
                }
                // ADD REVIEWER 2 NOT EXIST
                foreach ($secondCheckerOrder as $employeeIdApproval) {
                    if (!$existSecondCheckerOrder->has($employeeIdApproval)) {
                        $dataApproval = [
                            'matrix_as' => 'CHECKER_2',
                            'matrix_order' => '1',
                            'employee_id_approval' => $employeeIdApproval,
                            'is_active' => '1',
                        ];

                        $dataInsert = array_merge($dataApplicant, $dataApproval);
                        $modelProcurement->docApprovalMatrix()->create($dataInsert);
                    }
                }
                // END REVIEWER 2

                // CC
                $ccOrder = array_map('intval', (array) $request->ccOrder);
                $existCcOrder = $modelProcurement->docApprovalMatrix()
                                                        ->where('employee_id', $employeeId)
                                                        ->where('company_id', $companyId)
                                                        ->where('department_id', $departmentId)
                                                        ->where('doc_type_id', '1')
                                                        ->where('matrix_as', 'CC')
                                                        ->where('is_deleted', '0')
                                                        ->get()
                                                        ->keyBy('employee_id_approval');
                // DISABLED NON SELECTED CC
                foreach ($existCcOrder as $employeeIdApproval => $row) {
                    if (!in_array($employeeIdApproval, $ccOrder)) {
                        $modelProcurement->docApprovalMatrix()
                                        ->where('employee_id', $employeeId)
                                        ->where('company_id', $companyId)
                                        ->where('department_id', $departmentId)
                                        ->where('doc_type_id', '1')
                                        ->where('matrix_as', 'CC')
                                        ->where('employee_id_approval', $employeeIdApproval)
                                        ->update(['is_active' => '0']);
                    }
                }
                // ADD CC NOT EXIST
                foreach ($ccOrder as $employeeIdApproval) {
                    if (!$existCcOrder->has($employeeIdApproval)) {
                        $dataApproval = [
                            'matrix_as' => 'CC',
                            'matrix_order' => '1',
                            'employee_id_approval' => $employeeIdApproval,
                            'is_active' => '1',
                        ];

                        $dataInsert = array_merge($dataApplicant, $dataApproval);
                        $modelProcurement->docApprovalMatrix()->create($dataInsert);
                    }
                }
                // END CC

                // APPROVER
                $approverOrder = array_map('intval', (array) $request->approverOrder);
                $existApproverOrder = $modelProcurement->docApprovalMatrix()
                                                        ->where('employee_id', $employeeId)
                                                        ->where('company_id', $companyId)
                                                        ->where('department_id', $departmentId)
                                                        ->where('doc_type_id', '1')
                                                        ->where('matrix_as', 'APPROVER')
                                                        ->where('is_deleted', '0')
                                                        ->get()
                                                        ->keyBy('employee_id_approval');
                // DISABLED NON SELECTED APPROVER
                foreach ($existApproverOrder as $employeeIdApproval => $row) {
                    if (!in_array($employeeIdApproval, $approverOrder)) {
                        $modelProcurement->docApprovalMatrix()
                                        ->where('employee_id', $employeeId)
                                        ->where('company_id', $companyId)
                                        ->where('department_id', $departmentId)
                                        ->where('doc_type_id', '1')
                                        ->where('matrix_as', 'APPROVER')
                                        ->where('employee_id_approval', $employeeIdApproval)
                                        ->update(['is_active' => '0']);
                    }
                }
                // ADD APPROVER NOT EXIST
                foreach ($approverOrder as $employeeIdApproval) {
                    if (!$existApproverOrder->has($employeeIdApproval)) {
                        $dataApproval = [
                            'matrix_as' => 'APPROVER',
                            'matrix_order' => '1',
                            'employee_id_approval' => $employeeIdApproval,
                            'is_active' => '1',
                        ];

                        $dataInsert = array_merge($dataApplicant, $dataApproval);
                        $modelProcurement->docApprovalMatrix()->create($dataInsert);
                    }
                }
                // END APPROVER

                // RECEIVER
                $poApplicant = array_map('intval', (array) $request->poApplicant);
                $existReceiverOrder = $modelProcurement->docApprovalMatrix()
                                                        ->select('employee_id_approval')
                                                        ->where('employee_id', $employeeId)
                                                        ->where('company_id', $companyId)
                                                        ->where('department_id', $departmentId)
                                                        ->where('doc_type_id', '1')
                                                        ->where('matrix_as', 'RECEIVER')
                                                        ->where('is_deleted', '0')
                                                        ->get()
                                                        ->keyBy('employee_id_approval');
                // DISABLED NON SELECTED RECEIVER
                foreach ($existReceiverOrder as $employeeIdApproval => $row) {
                    if (!in_array($employeeIdApproval, $poApplicant)) {
                        $modelProcurement->docApprovalMatrix()
                                        ->where('employee_id', $employeeId)
                                        ->where('company_id', $companyId)
                                        ->where('department_id', $departmentId)
                                        ->where('doc_type_id', '1')
                                        ->where('matrix_as', 'RECEIVER')
                                        ->where('employee_id_approval', $employeeIdApproval)
                                        ->update(['is_active' => '0']);
                    }
                }
                // ADD RECEIVER NOT EXIST
                foreach ($poApplicant as $employeeIdApproval) {
                    if (!$existReceiverOrder->has($employeeIdApproval)) {
                        $dataApproval = [
                            'matrix_as' => 'RECEIVER',
                            'matrix_order' => '1',
                            'employee_id_approval' => $employeeIdApproval,
                            'is_active' => '1',
                        ];

                        $dataInsert = array_merge($dataApplicant, $dataApproval);
                        $modelProcurement->docApprovalMatrix()->create($dataInsert);
                    }
                }
                // END RECEIVER

                // INSPECTION RECEIVER
                $inspectionReceiver = array_map('intval', (array) $request->inspectionReceiver);
                $existInspectionReceiver = $modelProcurement->docApprovalMatrix()
                                                        ->select('employee_id_approval')
                                                        ->where('employee_id', $employeeId)
                                                        ->where('company_id', $companyId)
                                                        ->where('department_id', $departmentId)
                                                        ->where('doc_type_id', '4')
                                                        ->where('matrix_as', 'INSPECTION_RECEIVER')
                                                        ->where('is_deleted', '0')
                                                        ->get()
                                                        ->keyBy('employee_id_approval');
                // DISABLED NON SELECTED INSPECTION RECEIVER
                foreach ($existInspectionReceiver as $employeeIdApproval => $row) {
                    if (!in_array($employeeIdApproval, $inspectionReceiver)) {
                        $modelProcurement->docApprovalMatrix()
                                        ->where('employee_id', $employeeId)
                                        ->where('company_id', $companyId)
                                        ->where('department_id', $departmentId)
                                        ->where('doc_type_id', '4')
                                        ->where('matrix_as', 'INSPECTION_RECEIVER')
                                        ->where('matrix_order', '1')
                                        ->where('employee_id_approval', $employeeIdApproval)
                                        ->update(['is_active' => '0']);
                    }
                }
                // ADD INSPECTION RECEIVER NOT EXIST
                foreach ($inspectionReceiver as $employeeIdApproval) {
                    if (!$existInspectionReceiver->has($employeeIdApproval)) {
                        $dataInsert = [
                            'company_id' => $companyId,
                            'doc_type_id' => '4', // 4 = INSPECTION FORM
                            'employee_id' => $employeeId,
                            'department_id' => $request->employeeDepartmentOrder,
                            'matrix_as' => 'INSPECTION_RECEIVER',
                            'matrix_order' => '1',
                            'employee_id_approval' => $employeeIdApproval,
                            'is_active' => '1',
                        ];

                        $modelProcurement->docApprovalMatrix()->create($dataInsert);
                    }
                }
                // END INSPECTION RECEIVER

                $message = 'Successfully Update Order Form Applicant';
                return response()->json(['message' => $message], 200);
            }
            catch (ValidationException $e) {
                return response()->json(['message' => 'Please fill the required form.', 'errors' => $e->errors()], 422)
                                ->setStatusCode(422, 'Please fill the required form.');
            }
        }
        else if($request->type == 'DISABLE_ORDER_FORM_APPLICANT' || $request->type == 'ENABLE_ORDER_FORM_APPLICANT' || $request->type == 'DELETE_ORDER_FORM_APPLICANT') {
            $dataUpdate = [
                'is_active' => ($request->type == 'DISABLE_ORDER_FORM_APPLICANT' || $request->type == 'DELETE_ORDER_FORM_APPLICANT') ? '0' : '1',
                'is_deleted' => ($request->type == 'DELETE_ORDER_FORM_APPLICANT') ? '1' : '0',
            ];

            $modelProcurement->docApprovalMatrix()
                            ->where('employee_id', $employeeId)
                            ->where('company_id', $companyId)
                            ->where('department_id', $departmentId)
                            ->where('doc_type_id', '1')
                            ->update($dataUpdate);

            $modelProcurement->multiDepartment()
                            ->where('employee_id', $employeeId)
                            ->where('company_id', $companyId)
                            ->where('department_id', $departmentId)
                            ->update($dataUpdate);

            $message = $request->type == 'DISABLE_ORDER_FORM_APPLICANT' ? 'Successfully Disabled Applicant Flow'
                        : (
                            $request->type == 'ENABLE_ORDER_FORM_APPLICANT' ? 'Successfully Enabled Applicant Flow' : 'Successfully Deleted Applicant Flow'
                        );

            return response()->json(['message' => $message], 200);
        }
    }

    public function addInspector(Request $request) {
        $modelProcurement = new ProcurementPurchasingModel();
        try {
            $validateRules = [
                'filterInspectorCompany' => 'required',
                'inspectionReceiver.0' => 'required|string|max:255',
                'inspectionDepartmentOrder' => 'required|string|max:255',
                'inspectionChecker.0' => 'required|string|max:255',
                'inspectionConfirmer.0' => 'required|string|max:255',
            ];
            $validateMessages = [
                'filterInspectorCompany.required' => 'Company is required',
                'inspectionReceiver.0.required' => 'Inspection receiver is required',
                'inspectionDepartmentOrder.required' => 'Department order form is required',
                'inspectionChecker.0.required' => 'Inspection checker is required',
                'inspectionConfirmer.0.required' => 'Inspection confirmer is required',
            ];

            $request->validate($validateRules, $validateMessages);

            $token = SafeToken::decode($request->filterInspectorCompany);
            $companyId = $token['c'];
            $departmentId = $request->inspectionDepartmentOrder;

            // for($x = 0; $x < count($request->inspectionReceiver); $x++){
            //     $employeeId = $request->inspectionReceiver[$x];
            //     $getExistApprovalMatrix = $modelProcurement->docApprovalMatrix()
            //                                             ->from('doc_approval_matrix as a')
            //                                             ->leftJoin('vw_master_employee_active as b', 'a.employee_id', '=', 'b.employee_id')
            //                                             ->select('a.matrix_id', 'b.employee_name')
            //                                             ->where('a.company_id', $companyId)
            //                                             ->where('a.employee_id', $employeeId)
            //                                             ->where('a.department_id', $departmentId)
            //                                             ->where('a.doc_type_id', '4')
            //                                             ->where('a.is_deleted', '0')
            //                                             ->first();
            //     if($getExistApprovalMatrix) {
            //         return response()->json(['message' => 'Flow for '.Str::upper($getExistApprovalMatrix->employee_name).' already exist, please edit flow', 'errors' => ''], 422)
            //                             ->setStatusCode(422, 'Flow  '.Str::upper($getExistApprovalMatrix->employee_name).' already exist, please edit flow');
            //     }
            // }

            for($y = 0; $y < count($request->inspectionReceiver); $y++){
                $employeeId = $request->inspectionReceiver[$y];
                $dataInspector = [
                    'company_id' => $companyId,
                    'doc_type_id' => '4', // 4 = INSPECTION FORM
                    'employee_id' => $employeeId,
                    'department_id' => $request->inspectionDepartmentOrder,
                ];

                if($request->inspectionChecker) {
                    for($x = 0; $x < count($request->inspectionChecker); $x++){
                        $dataApproval = [
                            'matrix_as' => 'INSPECTION_CHECKER',
                            'employee_id_approval' => $request->inspectionChecker[$x],
                            'matrix_order' => '2',
                            'is_active' => '1',
                        ];

                        $dataInsert = array_merge($dataInspector, $dataApproval);
                        $modelProcurement->docApprovalMatrix()->create($dataInsert);
                    }
                }

                if($request->inspectionConfirmer) {
                    for($x = 0; $x < count($request->inspectionConfirmer); $x++){
                        $dataApproval = [
                            'matrix_as' => 'INSPECTION_CONFIRMER',
                            'employee_id_approval' => $request->inspectionConfirmer[$x],
                            'matrix_order' => '3',
                            'is_active' => '1',
                        ];

                        $dataInsert = array_merge($dataInspector, $dataApproval);
                        $modelProcurement->docApprovalMatrix()->create($dataInsert);
                    }
                }
            }

            $message = 'Successfully Save Inspection Flow';
            return response()->json(['message' => $message], 200);
        }
        catch (ValidationException $e) {
            return response()->json(['message' => 'Please fill the required form.', 'errors' => $e->errors()], 422)
                            ->setStatusCode(422, 'Please fill the required form.');
        }
    }

    public function updateInspector(Request $request) {
        $modelProcurement = new ProcurementPurchasingModel();
        $token = SafeToken::decode($request->tokenForm);
        $companyId = $token['c'];
        $employeeId = $token['id'];

        if($request->type == 'EDIT_INSPECTOR') {
            try {
                $validateRules = [
                    'filterInspectorCompany' => 'required',
                    'inspectionReceiver.0' => 'required|string|max:255',
                    'inspectionDepartmentOrder' => 'required|string|max:255',
                    'inspectionChecker.0' => 'required|string|max:255',
                    'inspectionConfirmer.0' => 'required|string|max:255',
                ];
                $validateMessages = [
                    'filterInspectorCompany.required' => 'Company is required',
                    'inspectionReceiver.0.required' => 'Inspection receiver is required',
                    'inspectionDepartmentOrder.required' => 'Department order form is required',
                    'inspectionChecker.0.required' => 'Inspection checker is required',
                    'inspectionConfirmer.0.required' => 'Inspection confirmer is required',
                ];

                $request->validate($validateRules, $validateMessages);
                $departmentId = $request->inspectionDepartmentOrder;
                $dataInspector = [
                    'company_id' => $companyId,
                    'doc_type_id' => '4', // 4 = INSPECTION FORM
                    'employee_id' => $employeeId,
                    'department_id' => $departmentId,
                ];

                // INSPECTION CHECKER
                $inspectionChecker = array_map('intval', (array) $request->inspectionChecker);
                $existInspectionChecker = $modelProcurement->docApprovalMatrix()
                                                        ->select('employee_id_approval')
                                                        ->where('employee_id', $employeeId)
                                                        ->where('company_id', $companyId)
                                                        ->where('department_id', $departmentId)
                                                        ->where('doc_type_id', '4')
                                                        ->where('matrix_as', 'INSPECTION_CHECKER')
                                                        ->where('is_deleted', '0')
                                                        ->get()
                                                        ->keyBy('employee_id_approval');
                // DISABLED NON SELECTED INSPECTION CHECKER
                foreach ($existInspectionChecker as $employeeIdApproval => $row) {
                    if (!in_array($employeeIdApproval, $inspectionChecker)) {
                        $modelProcurement->docApprovalMatrix()
                                        ->where('employee_id', $employeeId)
                                        ->where('company_id', $companyId)
                                        ->where('department_id', $departmentId)
                                        ->where('doc_type_id', '4')
                                        ->where('matrix_as', 'INSPECTION_CHECKER')
                                        ->where('matrix_order', '2')
                                        ->where('employee_id_approval', $employeeIdApproval)
                                        ->update(['is_active' => '0']);
                    }
                }
                // ADD INSPECTION CHECKER NOT EXIST
                foreach ($inspectionChecker as $employeeIdApproval) {
                    if (!$existInspectionChecker->has($employeeIdApproval)) {
                        $dataApproval = [
                            'matrix_as' => 'INSPECTION_CHECKER',
                            'matrix_order' => '1',
                            'employee_id_approval' => $employeeIdApproval,
                            'is_active' => '1',
                        ];

                        $dataInsert = array_merge($dataInspector, $dataApproval);
                        $modelProcurement->docApprovalMatrix()->create($dataInsert);
                    }
                }
                // END INSPECTION CHECKER

                // INSPECTION CONFIMER
                $inspectionConfirmer = array_map('intval', (array) $request->inspectionConfirmer);
                $existInspectionConfirmer = $modelProcurement->docApprovalMatrix()
                                                        ->select('employee_id_approval')
                                                        ->where('employee_id', $employeeId)
                                                        ->where('company_id', $companyId)
                                                        ->where('department_id', $departmentId)
                                                        ->where('doc_type_id', '4')
                                                        ->where('matrix_as', 'INSPECTION_CONFIRMER')
                                                        ->where('is_deleted', '0')
                                                        ->get()
                                                        ->keyBy('employee_id_approval');
                // DISABLED NON SELECTED INSPECTION CONFIMER
                foreach ($existInspectionConfirmer as $employeeIdApproval => $row) {
                    if (!in_array($employeeIdApproval, $inspectionConfirmer)) {
                        $modelProcurement->docApprovalMatrix()
                                        ->where('employee_id', $employeeId)
                                        ->where('company_id', $companyId)
                                        ->where('department_id', $departmentId)
                                        ->where('doc_type_id', '4')
                                        ->where('matrix_as', 'INSPECTION_CONFIRMER')
                                        ->where('matrix_order', '3')
                                        ->where('employee_id_approval', $employeeIdApproval)
                                        ->update(['is_active' => '0']);
                    }
                }
                // ADD INSPECTION CONFIMER NOT EXIST
                foreach ($inspectionConfirmer as $employeeIdApproval) {
                    if (!$existInspectionConfirmer->has($employeeIdApproval)) {
                        $dataApproval = [
                            'matrix_as' => 'INSPECTION_CONFIRMER',
                            'matrix_order' => '2',
                            'employee_id_approval' => $employeeIdApproval,
                            'is_active' => '1',
                        ];

                        $dataInsert = array_merge($dataInspector, $dataApproval);
                        $modelProcurement->docApprovalMatrix()->create($dataInsert);
                    }
                }
                // END INSPECTION CONFIMER

                $message = 'Successfully Update Inspection Flow';
                return response()->json(['message' => $message], 200);
            }
            catch (ValidationException $e) {
                return response()->json(['message' => 'Please fill the required form.', 'errors' => $e->errors()], 422)
                                ->setStatusCode(422, 'Please fill the required form.');
            }
        }
        else if($request->type == 'DISABLE_INSPECTOR' || $request->type == 'ENABLE_INSPECTOR' || $request->type == 'DELETE_INSPECTOR') {
            $departmentId = $token['d'];
            $dataUpdate = [
                'is_active' => ($request->type == 'DISABLE_INSPECTOR' || $request->type == 'DELETE_INSPECTOR') ? '0' : '1',
                'is_deleted' => ($request->type == 'DELETE_INSPECTOR') ? '1' : '0',
            ];

            $modelProcurement->docApprovalMatrix()
                            ->where('employee_id', $employeeId)
                            ->where('company_id', $companyId)
                            ->where('department_id', $departmentId)
                            ->where('doc_type_id', '4')
                            ->update($dataUpdate);

            $modelProcurement->multiDepartment()
                            ->where('employee_id', $employeeId)
                            ->where('company_id', $companyId)
                            ->where('department_id', $departmentId)
                            ->update($dataUpdate);

            $message = $request->type == 'DISABLE_INSPECTOR' ? 'Successfully Disabled Inspection Flow'
                        : (
                            $request->type == 'ENABLE_ORDER_FORM_APPLICANT' ? 'Successfully Enabled Inspection Flow' : 'Successfully Deleted Inspection Flow'
                        );

            return response()->json(['message' => $message], 200);
        }
    }

    public function addPoApprovalRule(Request $request) {
        $modelProcurement = new ProcurementPurchasingModel();
        try {
            $validateRules = [
                'filterPoRuleCompany' => 'required',
                'poApprovalRuleName' => 'required|string|max:255',
                'fieldRule.0' => 'required|string|max:255',
                'operatorRule.0' => 'required|string|max:255',
                'valueRule.0' => 'required|string|max:255',

                'selectPoApplicant.0' => 'required|string|max:255',
                'selectPoApprover.0' => 'required|string|max:255',
            ];
            $validateMessages = [
                'filterPoRuleCompany.required' => 'Company is required',
                'poApprovalRuleName.required' => 'Rule name is required',
                'fieldRule.0.required' => 'Field rule is required',
                'operatorRule.0.required' => 'Required',
                'valueRule.0.required' => 'Rule value is required',

                'selectPoApplicant.0.required' => 'PO Applicant is required',
                'selectPoApprover.0.required' => 'PO Approver is required',
            ];

            for($x = 0; $x < count($request->conditionId); $x++){
                $validateRules["fieldRule.{$x}"] = 'required|string|max:255';
                $validateRules["operatorRule.{$x}"] = 'required|string|max:5';
                $validateRules["valueRule.{$x}"] = 'required|string|max:255';

                $validateMessages["fieldRule.{$x}.required"] = 'Field rule required';
                $validateMessages["operatorRule.{$x}.required"] = 'Required';
                $validateMessages["valueRule.{$x}.required"] = 'Rule value is required';
            }

            $request->validate($validateRules, $validateMessages);

            $token = SafeToken::decode($request->filterPoRuleCompany);
            $companyId = $token['c'];

            $arrCondition = [];
            $groupCondition = 1;
            for($x = 0; $x < count($request->fieldRule); $x++){
                $valueId = null;
                $value = Str::replace(',', '', $request->valueRule[$x]);
                $getRuleField = $modelProcurement->masterFlowRuleField()
                                ->from('master_flow_rule_field as a')
                                ->leftJoin('master_flow_rule_field_option as b', function($join) use($value) {
                                    $join->on('b.field_id', 'a.field_id')
                                        ->where('a.value_type', '=', 'OPTION')
                                        ->where('b.field_option_id', '=', $value);
                                })
                                ->select('a.field_name', 'a.value_type', 'b.field_option_id', 'b.option_name')
                                ->where('a.field_id', $request->fieldRule[$x])
                                ->first();

                if($getRuleField) {
                    if($getRuleField->value_type == 'OPTION') {
                        $valueId = $value;
                        $value = $getRuleField->option_name;
                    }

                    $logicalOperator = null;
                    if($x < count($request->fieldRule) - 1) {
                        if($request->groupColor[$x] == $request->groupColor[($x + 1)] && $request->groupColor[$x] != '') {
                            $logicalOperator = "OR";
                        }
                        else if($x == 0 && $request->groupColor[$x] == '' && $request->groupColor[$x] != $request->groupColor[($x + 1)]) {
                            $logicalOperator = "AND";
                        }
                        else if($x == 0 && $request->groupColor[$x] != '' && $request->groupColor[$x] != $request->groupColor[($x + 1)]) {
                            $logicalOperator = "OR";
                        }
                        else {
                            $logicalOperator = "AND";
                        }
                    }

                    // REORDER GROUP_CONDITION
                    if($x > 0) {
                        if($request->groupColor[$x] != $request->groupColor[($x - 1)]) {
                            // GET PREV CONDITION
                            $groupCondition++;
                        }
                    }

                    $arrCondition[] = [
                                        'field_id' => $request->fieldRule[$x],
                                        'field' => $getRuleField->field_name,
                                        'operator' => $request->operatorRule[$x],
                                        'value_id' => $valueId,
                                        'value' => $value,
                                        'order' => ($x + 1),
                                        'logical_operator' => $logicalOperator,
                                        'group_condition' => $groupCondition,
                                        'group_color' => $request->groupColor[$x] != '' ? $request->groupColor[$x] : null,
                                    ];
                }
                else {
                    return response()->json(['message' => 'Rule type condition not exist', 'errors' => 'Rule type condition not exist'], 422)
                            ->setStatusCode(422, 'Rule type condition not exist');
                }
            }

            $arrAction = [];
            $poApprovalFlow = ['selectPoApplicant', 'selectPoReviewer', 'selectPoConfirmer1', 'selectPoReviewAdmin', 'selectPoConfirmer2', 'selectPoAcknowledger', 'selectPoApprover'];
            $x = 1;
            foreach ($request->actionType as $index => $actionTypeId) {
                $inputKey = $poApprovalFlow[$index];
                $actionValues = $request->input($inputKey, []);

                $getActionType = $modelProcurement->masterFlowRuleActionType()
                                            ->select('action_type')
                                            ->where('action_type_id', $actionTypeId)
                                            ->first();
                if (count($actionValues) > 0) {
                    foreach ($actionValues as $actionValue) {
                        $arrAction[] = [
                                            'action_type_id' => $actionTypeId,
                                            'action_type' => $getActionType->action_type,
                                            'action_value' => $actionValue,
                                            'order' => $x
                                        ];
                    }
                }
                else {
                    $arrAction[] = [
                                        'action_type_id' => $actionTypeId,
                                        'action_type' => $getActionType->action_type,
                                        'action_value' => null,
                                        'order' => $x
                                    ];
                }

                $x++;
            }

            if(count($arrCondition) == 0 || count($arrAction) == 0) {
                 return response()->json(['message' => 'Rule flow cannot be created', 'errors' => 'Rule flow cannot be created'], 422)
                            ->setStatusCode(422, 'Rule flow cannot be created');
            }

            for($i = 0; $i < count($request->selectPoApplicant); $i++) {
                $employeeId = $request->selectPoApplicant[$i];
                $ruleTypeId = '2'; // 2 = APPLICATION & PO FORM

                $lastRecord = $modelProcurement->masterFlowRuleGroup()
                    ->where('company_id', $companyId)
                    ->where('employee_id', $employeeId)
                    ->where('rule_type_id', $ruleTypeId)
                    ->where('is_active', '1')
                    ->where('is_deleted', '0')
                    ->orderBy('order', 'desc')
                    ->first();
                $nextOrder = $lastRecord ? $lastRecord->order + 1 : 1;

                $dataInsert = [
                    'company_id' => $companyId,
                    'order' => $nextOrder,
                    'employee_id' => $employeeId,
                    'rule_type_id' => $ruleTypeId,
                    'rule_group_name' => Str::trim($request->poApprovalRuleName),
                    'created_by' => $this->employeeId,
                    'created_at' => now(),
                ];
                $insertRuleGroup = $modelProcurement->masterFlowRuleGroup()->create($dataInsert);

                foreach ($arrCondition as $condition) {
                    $dataInsert = [
                                    'rule_group_id' => $insertRuleGroup->id,
                                    'field_id' => $condition['field_id'],
                                    'field' => $condition['field'],
                                    'operator' => $condition['operator'],
                                    'value_id' => $condition['value_id'],
                                    'value' => $condition['value'],
                                    'order' => $condition['order'],
                                    'logical_operator' => $condition['logical_operator'],
                                    'group_condition' => $condition['group_condition'],
                                    'group_color' => $condition['group_color'],
                                ];
                    $modelProcurement->masterFlowRuleCondition()->create($dataInsert);
                }

                foreach ($arrAction as $action) {
                    $insertAction = $modelProcurement->masterFlowRuleAction()
                                                    ->create([
                                                                'rule_group_id' => $insertRuleGroup->id,
                                                                'action_type_id' => $action['action_type_id'],
                                                                'action_type' => $action['action_type'],
                                                                'action_value' => $action['action_value'],
                                                                'order' => $action['order']
                                                            ]);

                    $modelProcurement->docApprovalMatrix()
                                    ->create([
                                                'company_id' => $companyId,
                                                'flow_rule_group_id' => $insertRuleGroup->id,
                                                'flow_rule_action_type_id' => $action['action_type_id'],
                                                'flow_rule_action_id' => $insertAction->id,
                                                'doc_type_id' => $ruleTypeId,
                                                'employee_id' => $employeeId,
                                                'matrix_as' => $action['action_type'],
                                                'employee_id_approval' => $action['action_value'],
                                                'matrix_order' => $action['order']
                                            ]);
                }
            }

            $message = 'Successfully Save PO Approval Rule';
            return response()->json(['message' => $message], 200);
        }
        catch (ValidationException $e) {
            return response()->json(['message' => 'Please fill the required form.', 'errors' => $e->errors()], 422)
                            ->setStatusCode(422, 'Please fill the required form.');
        }
    }

    public function updatePoApprovalRule(Request $request) {
        $modelProcurement = new ProcurementPurchasingModel();
        $token = SafeToken::decode($request->filterPoRuleCompany);
        $companyId = $token['c'];

        if($request->updateType == 'PRIORITY_ORDER') {
            // DECODE JSON FROM REQUEST
            $order = json_decode($request->order, true);
            if (!is_array($order)) {
                return response()->json([
                    'message' => 'Invalid order data format',
                ], 422);
            }

            foreach ($order as $item) {
                $modelProcurement->masterFlowRuleGroup()
                        ->where('rule_group_id', $item['id'])
                        ->where('employee_id', $item['employeeId'])
                        ->where('company_id', $companyId)
                        ->where('rule_type_id', '2')
                        ->update([
                            'order' => $item['newPriority'],
                            'is_active' => '1',
                        ]);
            }

            $message = 'Successfully Update Priority Rule';
            return response()->json(['message' => $message], 200);
        }
        else if($request->updateType == 'FORM') {
            $token = SafeToken::decode($request->tokenForm);
            $employeeId = $token['id'];
            $ruleGroupId = $token['i'];
            $ruleTypeId = '2'; // 2 = APPLICATION & PO FORM
            if($request->type == 'EDIT_PO_APPROVAL_RULE') {
                try {
                    $validateRules = [
                        'poApprovalRuleName' => 'required|string|max:255',

                        // 'fieldRule.0' => 'required|string|max:255',
                        // 'operatorRule.0' => 'required|string|max:255',
                        // 'valueRule.0' => 'required|string|max:255',

                        'selectPoApplicant.0' => 'required|string|max:255',
                        'selectPoApprover.0' => 'required|string|max:255',
                    ];
                    $validateMessages = [
                        'poApprovalRuleName.required' => 'Rule name is required',

                        // 'fieldRule.0.required' => 'Field rule is required',
                        // 'operatorRule.0.required' => 'Required',
                        // 'valueRule.0.required' => 'Rule value is required',

                        'selectPoApplicant.0.required' => 'PO Applicant is required',
                        'selectPoApprover.0.required' => 'PO Approver is required',
                    ];

                    for($x = 0; $x < count($request->conditionId); $x++){
                        $validateRules["fieldRule.{$x}"] = 'required|string|max:255';
                        $validateRules["operatorRule.{$x}"] = 'required|string|max:5';
                        $validateRules["valueRule.{$x}"] = 'required|string|max:255';

                        $validateMessages["fieldRule.{$x}.required"] = 'Field rule required';
                        $validateMessages["operatorRule.{$x}.required"] = 'Required';
                        $validateMessages["valueRule.{$x}.required"] = 'Rule value is required';
                    }

                    $request->validate($validateRules, $validateMessages);

                    $employeeId = $request->selectPoApplicant[0];
                    $dataUpdate = [
                        'rule_group_name' => $request->poApprovalRuleName,
                        'updated_by' => $this->employeeId,
                        'updated_at' => now(),
                    ];
                    $modelProcurement->masterFlowRuleGroup()
                                    ->where('rule_group_id', $ruleGroupId)
                                    ->where('employee_id', $employeeId)
                                    ->where('is_active', '1')
                                    ->update($dataUpdate);

                    $selectedCondition = [];
                    foreach ($request->conditionId as $row) {
                        if($row != '') {
                            $tokenConditionId = SafeToken::decode($row);
                            $selectedCondition[] = $tokenConditionId['i'];
                        }
                        else {
                            $selectedCondition[] = '';
                        }
                    }

                    $getRuleConditionExist = $modelProcurement->masterFlowRuleCondition()
                                                            ->from('master_flow_rule_condition as a')
                                                            ->select('a.condition_id')
                                                            ->where('a.rule_group_id', $ruleGroupId)
                                                            ->orderBy('a.order')
                                                            ->where('is_deleted', '0')
                                                            ->get()
                                                            ->keyBy('condition_id');

                    // DISABLED NON SELECTED RULE CONDITION
                    foreach ($getRuleConditionExist as $conditionId => $row) {
                        if (!in_array($conditionId, $selectedCondition)) {
                            $modelProcurement->masterFlowRuleCondition()
                                            ->where('condition_id', $conditionId)
                                            ->where('rule_group_id', $ruleGroupId)
                                            ->update(['is_active' => '0', 'order' => null, 'is_deleted' => '1']);
                        }
                    }
                    // UPDATE RULE CONDITION OR ADD CONDITION NOT EXIST
                    foreach ($selectedCondition as $index => $conditionId) {
                        $valueId = null;
                        $value = Str::replace(',', '', $request->valueRule[$index]);
                        $getRuleField = $modelProcurement->masterFlowRuleField()
                                        ->from('master_flow_rule_field as a')
                                        ->leftJoin('master_flow_rule_field_option as b', function($join) use($value) {
                                            $join->on('b.field_id', 'a.field_id')
                                                ->where('a.value_type', '=', 'OPTION')
                                                ->where('b.field_option_id', '=', $value);
                                        })
                                        ->select('a.field_name', 'a.value_type', 'b.field_option_id', 'b.option_name')
                                        ->where('a.field_id', $request->fieldRule[$index])
                                        ->first();

                        if($getRuleField) {
                            if($getRuleField->value_type == 'OPTION') {
                                $valueId = $value;
                                $value = $getRuleField->option_name;
                            }

                            $logicalOperator = null;
                            if($index < count($request->fieldRule) - 1) {
                                if($request->groupColor[$index] == $request->groupColor[($index + 1)] && $request->groupColor[$index] != '') {
                                    $logicalOperator = "OR";
                                }
                                else if($index == 0 && $request->groupColor[$index] == '' && $request->groupColor[$index] != $request->groupColor[($index + 1)]) {
                                    $logicalOperator = "AND";
                                }
                                else if($index == 0 && $request->groupColor[$index] != '' && $request->groupColor[$index] != $request->groupColor[($index + 1)]) {
                                    $logicalOperator = "OR";
                                }
                                else {
                                    $logicalOperator = "AND";
                                }
                            }

                            if ($getRuleConditionExist->has($conditionId)) {
                                // UPDATE RULE CONDITION
                                $dataUpdate = [
                                    'field_id' => $request->fieldRule[$index],
                                    'field' => $getRuleField->field_name,
                                    'operator' => $request->operatorRule[$index],
                                    'value_id' => $valueId,
                                    'value' => $value,
                                    'logical_operator' => $logicalOperator,
                                    'group_condition' => null,
                                    'group_color' => $request->groupColor[$index] != '' ? $request->groupColor[$index] : null,
                                    'order' => ($index + 1),
                                ];

                                $modelProcurement->masterFlowRuleCondition()
                                                ->where('condition_id', $conditionId)
                                                ->where('rule_group_id', $ruleGroupId)
                                                ->update($dataUpdate);
                            }
                            else {
                                // ADD RULE CONDITION
                                $dataInsert = [
                                    'rule_group_id' => $ruleGroupId,
                                    'field_id' => $request->fieldRule[$index],
                                    'field' => $getRuleField->field_name,
                                    'operator' => $request->operatorRule[$index],
                                    'value_id' => $valueId,
                                    'value' => $value,
                                    'logical_operator' => $logicalOperator,
                                    'group_condition' => null,
                                    'group_color' => $request->groupColor[$index] != '' ? $request->groupColor[$index] : null,
                                    'order' => ($index + 1),
                                ];
                                $modelProcurement->masterFlowRuleCondition()->create($dataInsert);
                            }
                        }
                    }

                    // REORDER GROUP_CONDITION
                    $groupCondition = 1;
                    $getRuleCondition = $modelProcurement->masterFlowRuleCondition()
                                                        ->from('master_flow_rule_condition as a')
                                                        ->select('a.condition_id','a.logical_operator','a.group_color')
                                                        ->where('a.rule_group_id', $ruleGroupId)
                                                        ->where('a.is_active', '1')
                                                        ->orderBy('a.order')
                                                        ->get();
                    foreach ($getRuleCondition as $index => $row) {
                        if($index > 0) {
                            if($row->group_color != $getRuleCondition[($index - 1)]->group_color) {
                                // GET PREV CONDITION
                                $groupCondition++;
                            }
                        }

                        $modelProcurement->masterFlowRuleCondition()
                                        ->where('condition_id', $row->condition_id)
                                        ->update(['group_condition' => $groupCondition]);
                    }

                    $poApprovalFlow = ['selectPoApplicant', 'selectPoReviewer', 'selectPoConfirmer1', 'selectPoReviewAdmin', 'selectPoConfirmer2', 'selectPoAcknowledger', 'selectPoApprover'];
                    $x = 1;
                    foreach ($request->actionType as $index => $actionTypeId) {
                        $inputKey = $poApprovalFlow[$index];
                        if($inputKey === 'selectPoApplicant') continue; // SKIP UPDATE AS APPLICANT
                        $actionValues = $request->input($inputKey, []);

                        $getActionType = $modelProcurement->masterFlowRuleActionType()
                                                        ->select('action_type')
                                                        ->where('action_type_id', $actionTypeId)
                                                        ->first();

                        if (count($actionValues) > 0) {
                            $actionValueArr = array_map('intval', (array) $actionValues);
                            $getActionValueExist = $modelProcurement->masterFlowRuleAction()
                                                            ->from('master_flow_rule_action as a')
                                                            ->select('a.action_value')
                                                            ->where('a.rule_group_id', $ruleGroupId)
                                                            ->where('a.action_type_id', $actionTypeId)
                                                            ->get()
                                                            ->keyBy('action_value');
                            // DISABLED NON SELECTED
                            foreach ($getActionValueExist as $employeeIdApproval => $row) {
                                if (!in_array($employeeIdApproval, $actionValueArr) || $employeeIdApproval == null) {
                                    $query = $modelProcurement->masterFlowRuleAction()
                                                    ->where('rule_group_id', $ruleGroupId)
                                                    ->where('action_type_id', $actionTypeId);
                                    if($employeeIdApproval == null) {
                                        $query = $query->whereNull('action_value');
                                    }
                                    else {
                                        $query = $query->where('action_value', $employeeIdApproval);
                                    }
                                    $query = $query->update(['is_active' => '0']);

                                    $query = $modelProcurement->docApprovalMatrix()
                                                            ->where('company_id', $companyId)
                                                            ->where('flow_rule_group_id', $ruleGroupId)
                                                            ->where('flow_rule_action_type_id', $actionTypeId);
                                    if($employeeIdApproval == null) {
                                        $query = $query->whereNull('employee_id_approval');
                                    }
                                    else {
                                        $query = $query->where('employee_id_approval', $employeeIdApproval);
                                    }
                                    $query = $query->where('doc_type_id', $ruleTypeId)
                                                    ->update(['is_active' => '0']);
                                }
                            }
                            // UPDATE SELECTED OR ADD NOT EXIST
                            foreach ($actionValueArr as $employeeIdApproval) {
                                if ($getActionValueExist->has($employeeIdApproval) || $employeeIdApproval == null) {
                                    // UPDATE
                                    $query = $modelProcurement->masterFlowRuleAction()
                                                            ->where('rule_group_id', $ruleGroupId)
                                                            ->where('action_type_id', $actionTypeId);
                                    if($employeeIdApproval == null) {
                                        $query = $query->whereNull('action_value');
                                    }
                                    else{
                                        $query = $query->where('action_value', $employeeIdApproval);
                                    }
                                    $query = $query->update(['is_active' => '1', 'order' => $x]);

                                    // DOC. APPROVAL MATRIX TABLE
                                    $query = $modelProcurement->docApprovalMatrix()
                                                            ->where('flow_rule_group_id', $ruleGroupId)
                                                            ->where('flow_rule_action_type_id', $actionTypeId);
                                    if($employeeIdApproval == null) {
                                        $query = $query->whereNull('employee_id_approval');
                                    }
                                    else{
                                        $query = $query->where('employee_id_approval', $employeeIdApproval);
                                    }
                                    $query = $query->where('doc_type_id', $ruleTypeId)
                                                ->first();

                                    if ($query) {
                                        $query = $modelProcurement->docApprovalMatrix()
                                                            ->where('flow_rule_group_id', $ruleGroupId)
                                                            ->where('flow_rule_action_type_id', $actionTypeId);
                                        if($employeeIdApproval == null) {
                                            $query = $query->whereNull('employee_id_approval');
                                        }
                                        else{
                                            $query = $query->where('employee_id_approval', $employeeIdApproval);
                                        }
                                        $query = $query->where('doc_type_id', $ruleTypeId)
                                                        ->update(['is_active' => '1', 'matrix_order' => $x]);
                                    }
                                    else if($employeeIdApproval != null) {
                                        $getAction = $modelProcurement->masterFlowRuleAction()
                                                                    ->from('master_flow_rule_action as a')
                                                                    ->select('a.action_id', 'a.order')
                                                                    ->where('a.rule_group_id', $ruleGroupId)
                                                                    ->where('a.action_type_id', $actionTypeId)
                                                                    ->where('a.action_value', $employeeIdApproval)
                                                                    ->first();

                                        $modelProcurement->docApprovalMatrix()->create([
                                            'company_id' => $companyId,
                                            'flow_rule_group_id' => $ruleGroupId,
                                            'flow_rule_action_type_id' => $actionTypeId,
                                            'flow_rule_action_id' => $getAction->action_id,
                                            'doc_type_id' => $ruleTypeId,
                                            'employee_id' => $employeeIdApproval,
                                            'matrix_as' => $getActionType->action_type,
                                            'employee_id_approval' => $employeeIdApproval,
                                            'matrix_order' => $getAction->order,
                                        ]);
                                    }
                                }
                                else {
                                    // ADD
                                    $insertAction = $modelProcurement->masterFlowRuleAction()->create([
                                                        'rule_group_id' => $ruleGroupId,
                                                        'action_type_id' => $actionTypeId,
                                                        'action_type' => $getActionType->action_type,
                                                        'action_value' => $employeeIdApproval,
                                                        'order' => $x,
                                                    ]);

                                    $modelProcurement->docApprovalMatrix()->create([
                                        'company_id' => $companyId,
                                        'flow_rule_group_id' => $ruleGroupId,
                                        'flow_rule_action_type_id' => $actionTypeId,
                                        'flow_rule_action_id' => $insertAction->id,
                                        'doc_type_id' => $ruleTypeId,
                                        'employee_id' => $employeeId,
                                        'matrix_as' => $getActionType->action_type,
                                        'employee_id_approval' => $employeeIdApproval,
                                        'matrix_order' => $x,
                                    ]);
                                }
                            }

                            $x++;
                        }
                        else {
                            $getActionNullExist = $modelProcurement->masterFlowRuleAction()
                                                        ->from('master_flow_rule_action as a')
                                                        ->select('action_id', 'is_active')
                                                        ->where('rule_group_id', $ruleGroupId)
                                                        ->where('action_type_id', $actionTypeId)
                                                        ->whereNull('action_value')
                                                        ->first();
                            if($getActionNullExist) {
                                if($getActionNullExist->is_active == '1') {
                                    $x++;
                                    continue;
                                }
                                else {
                                    // DISABLE ALL ACTIVE
                                    $modelProcurement->masterFlowRuleAction()
                                                    ->where('rule_group_id', $ruleGroupId)
                                                    ->where('action_type_id', $actionTypeId)
                                                    ->where('is_active', '1')
                                                    ->update(['is_active' => '0']);

                                    $modelProcurement->docApprovalMatrix()
                                                    ->where('flow_rule_group_id', $ruleGroupId)
                                                    ->where('flow_rule_action_type_id', $actionTypeId)
                                                    ->where('is_active', '1')
                                                    ->update(['is_active' => '1']);

                                    // UPDATE NULL VALUE TO ACTIVE
                                    $modelProcurement->masterFlowRuleAction()
                                                    ->where('rule_group_id', $ruleGroupId)
                                                    ->where('action_type_id', $actionTypeId)
                                                    ->whereNull('action_value')
                                                    ->update(['is_active' => '1']);

                                    $modelProcurement->docApprovalMatrix()
                                                    ->where('flow_rule_group_id', $ruleGroupId)
                                                    ->where('flow_rule_action_type_id', $actionTypeId)
                                                    ->whereNull('employee_id_approval')
                                                    ->update(['is_active' => '1']);
                                }
                            }
                            else {
                                // DISABLE ACTION VALUE FOR THIS ACTION TYPE
                                $dataUpdate = [
                                    'is_active' => '0',
                                ];
                                $modelProcurement->masterFlowRuleAction()
                                                ->where('rule_group_id', $ruleGroupId)
                                                ->where('action_type_id', $actionTypeId)
                                                ->where('is_active', '1')
                                                ->update($dataUpdate);

                                $modelProcurement->docApprovalMatrix()
                                                ->where('flow_rule_group_id', $ruleGroupId)
                                                ->where('flow_rule_action_type_id', $actionTypeId)
                                                ->where('is_active', '1')
                                                ->update($dataUpdate);

                                // INSERT NULL ACTION VALUE
                                $insertAction = $modelProcurement->masterFlowRuleAction()->create([
                                            'rule_group_id' => $ruleGroupId,
                                            'action_type_id' => $actionTypeId,
                                            'action_type' => $getActionType->action_type,
                                            'action_value' => null,
                                            'order' => $x
                                        ]);

                                $modelProcurement->docApprovalMatrix()->create([
                                    'company_id' => $companyId,
                                    'flow_rule_group_id' => $ruleGroupId,
                                    'flow_rule_action_type_id' => $actionTypeId,
                                    'flow_rule_action_id' => $insertAction->id,
                                    'doc_type_id' => $ruleTypeId,
                                    'employee_id' => $employeeId,
                                    'matrix_as' => $getActionType->action_type,
                                    'employee_id_approval' => null,
                                    'matrix_order' => $x,
                                ]);
                            }

                            $x++;
                        }
                    }

                    $message = 'Successfully Updated PO Approval Rule';
                    return response()->json(['message' => $message], 200);
                }
                catch (ValidationException $e) {
                    return response()->json(['message' => 'Please fill the required form.', 'errors' => $e->errors()], 422)
                                    ->setStatusCode(422, 'Please fill the required form.');
                }
            }
            else if($request->type == 'DISABLE_PO_APPROVAL_RULE' || $request->type == 'ENABLE_PO_APPROVAL_RULE' || $request->type == 'DELETE_PO_APPROVAL_RULE') {
                $dataUpdate = [
                    'is_active' => ($request->type == 'DISABLE_PO_APPROVAL_RULE' || $request->type == 'DELETE_PO_APPROVAL_RULE') ? '0' : '1',
                    'is_deleted' => ($request->type == 'DELETE_PO_APPROVAL_RULE') ? '1' : '0',
                ];

                if($request->type == 'DISABLE_PO_APPROVAL_RULE' || $request->type == 'DELETE_PO_APPROVAL_RULE') {
                    $dataOrder = [
                        'order' => null,
                    ];
                }
                else {
                    $lastRecord = $modelProcurement->masterFlowRuleGroup()
                        ->where('company_id', $companyId)
                        ->where('employee_id', $employeeId)
                        ->where('rule_type_id', $ruleTypeId)
                        ->where('is_active', '1')
                        ->where('is_deleted', '0')
                        ->orderBy('order', 'desc')
                        ->first();
                    $nextOrder = $lastRecord ? $lastRecord->order + 1 : 1;
                    $dataOrder = [
                        'order' => $nextOrder,
                    ];
                }

                $modelProcurement->masterFlowRuleGroup()
                                ->where('rule_group_id', $ruleGroupId)
                                ->update(array_merge($dataUpdate, $dataOrder));

                if($request->type == 'ENABLE_PO_APPROVAL_RULE') {
                    $matrixIds = $modelProcurement->masterFlowRuleAction()
                                                ->from('master_flow_rule_action as a')
                                                ->leftJoin('doc_approval_matrix as b', 'a.action_id', '=', 'b.flow_rule_action_id')
                                                ->where('a.rule_group_id', $ruleGroupId)
                                                ->where('a.is_active', '1')
                                                ->pluck('b.matrix_id');

                    $modelProcurement->docApprovalMatrix()
                                    ->whereIn('matrix_id', $matrixIds)
                                    ->where('flow_rule_group_id', $ruleGroupId)
                                    ->update(['is_active' => '1']);
                }
                else {
                    $modelProcurement->docApprovalMatrix()
                                    ->where('flow_rule_group_id', $ruleGroupId)
                                    ->update($dataUpdate);
                }

                $message = $request->type == 'DISABLE_PO_APPROVAL_RULE' ? 'Successfully Disabled PO Approval Rule'
                            : (
                                $request->type == 'ENABLE_PO_APPROVAL_RULE' ? 'Successfully Enabled PO Approval Rule' : 'Successfully Deleted PO Approval Rule'
                            );

                return response()->json(['message' => $message], 200);
            }
        }
    }

    public function updateOrderItem(Request $request) {
        $token = SafeToken::decode($request->tokenForm);
        $validator = Validator::make($token, [
            'id' => 'required',
            'a' => 'required',
            'b' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => 'Invalid token form', 'errors' => $validator->errors()], 422)
                            ->setStatusCode(422, 'Invalid token form');
        }

        $docApprovalId = $token['a'];
        $referenceId = $token['b'];

        $employeeId = $this->employeeId;
        $companyId = $this->companyId;
        $modelProcurement = new ProcurementPurchasingModel();

        try {
            $validatedData = $request->validate([
                'selectItemUnprocessed.*' => 'required|string|min:1',
                'reason' => 'required|string|max:255',
            ], [
                'selectItemUnprocessed.*.required' => 'Item to cancel is required',
                'selectItemUnprocessed.*.min' => 'Item to cancel is required',
                'reason.required' => 'Reason is required',
            ]);

            $datetimeAt = Carbon::now()->format('Y-m-d H:i:s');
            $dataInsert = [
                'order_form_id' => $referenceId,
                'company_id' => $companyId,
                'reason' => Str::trim($request->reason),
                'created_by' => $employeeId,
                'created_at' => $datetimeAt,
            ];
            $insertCanceled = $modelProcurement->docApprovalDetailOrderFormItemCanceled()->create($dataInsert);
            $canceledId = $insertCanceled->id;

            foreach ($validatedData['selectItemUnprocessed'] as $item) {
                $dataUpdate = [
                    'canceled_id' => $canceledId,
                ];

                $modelProcurement->documentApprovalDetailOrderItem()
                                ->where('doc_approval_id', $docApprovalId)
                                ->where('order_form_id', $referenceId)
                                ->where('order_detail_id', $item)
                                ->where('is_active', '1')
                                ->update($dataUpdate);
            }

            // CHECK ALL ITEM
            $response = collect();
            $allItems = '';
            $getAllItem = $modelProcurement->documentApprovalDetailOrderItem()
                                        ->select('appliance_item', 'comparison_id', 'application_id', 'vendor_id')
                                        ->where('doc_approval_id', $docApprovalId)
                                        ->where('order_form_id', $referenceId)
                                        ->where('unit_name', '<>', 'VAT')
                                        ->whereNull('canceled_id')
                                        ->where('is_active', '1')
                                        ->orderBy('order_detail_id', 'asc')
                                        ->get();

            // CHECK APAKAH TERDAPAT APPLICATION FORM YANG BELUM DISUBMIT
            $hasNullApplicationId = $getAllItem->contains('application_id', null);
            if($hasNullApplicationId) {
                // TERDAPAT APPLICATION FORM YANG BELUM DISUBMIT
                // CHECK APAKAH VENDOR ID NYA SEMUA SAMA ?
                $vendorId = $getAllItem->pluck('vendor_id')->unique();
                if ($vendorId->count() > 1) {
                    $selectedVendor = 'MULTIPLE VENDOR';
                }
                else {
                    $getVendor = $modelProcurement->masterVendor()
                                                ->from('master_vendor')
                                                ->select('vendor_name')
                                                ->where('vendor_id', $vendorId)
                                                ->first();
                    $selectedVendor = $getVendor->vendor_name;
                }

                if (count($getAllItem) === 1) {
                    $allItems = '<li>'.$getAllItem[0]->appliance_item.'</li>';
                }
                else {
                    $x = 1;
                    foreach ($getAllItem as $rowItem) {
                        $allItems .= '<li>'.$x.'. '.$rowItem->appliance_item.'</li>';
                        $x++;
                    }
                }

                $response['itemCompleted'] = false;
                $response['allItems'] = $allItems;
                $response['selectedVendor'] = $selectedVendor;
            }
            else {
                // APPLICATION FORM SUDAH DISUBMIT SEMUA
                $dataUpdate = [
                    'item_completed' => '1'
                ];

                $modelProcurement->documentApprovalDetailOrder()
                                ->where('doc_approval_id', $docApprovalId)
                                ->where('order_form_id', $referenceId)
                                ->where('is_active', '1')
                                ->update($dataUpdate);
                $response['itemCompleted'] = true;
            }

            // SEND EMAIL ITEM CANCELED
            $getEmployee = $modelProcurement->vwMasterEmployeeAll()
                                        ->from('vw_master_employee_all')
                                        ->select('employee_name', 'employee_email')
                                        ->where('employee_id', $employeeId)
                                        ->where('is_active', '1')
                                        ->first();

            $getHeader = $modelProcurement->documentApprovalHeader()
                                        ->from('doc_approval_header as a')
                                        ->select('a.doc_approval_id', 'a.employee_id','a.doc_type_id', 'a.doc_number', 'a.doc_status_id', 'a.department_name', 'b.employee_name', 'c.doc_name', 'b.employee_email')
                                        ->leftJoin('vw_master_employee_all as b', 'b.employee_id', 'a.employee_id')
                                        ->leftJoin('master_doc_types as c', 'c.doc_type_id', 'a.doc_type_id')
                                        ->where('a.doc_approval_id', $docApprovalId)
                                        ->where('a.is_active', '1')
                                        ->orderBy('a.doc_approval_id', 'asc')
                                        ->first();

            $getForm = $modelProcurement->documentApprovalDetailOrder()
                                            ->from('doc_approval_detail_order_form as a')
                                            ->select('a.order_form_id', 'a.grand_total', 'a.currency_code', 'a.created_at', 'b.location_name', 'c.description')
                                            ->leftJoin('master_locations as b', 'b.location_id', 'a.location_id')
                                            ->leftJoin('doc_approval_detail_order_form_purpose as c', 'c.order_form_id', 'a.order_form_id')
                                            ->where('a.doc_approval_id', $docApprovalId)
                                            ->where('a.order_form_id', $referenceId)
                                            ->where('a.is_active', '1')
                                            ->orderBy('a.order_form_id', 'desc')
                                            ->first();

            if ($getForm) {
                $accountId = 2; // ID master_email_accounts
                $mailSubject = $getHeader->doc_name.' '.$getHeader->doc_number;
                $mailBody = '';
                $mailBody .= '<table style="border-collapse:collapse;border:0;width:100%;font-size:13px;line-height:1.7;">
                                <tr>
                                    <th colspan="4" style="font-weight:normal;padding:10px 5px 20px 5px;text-align:left">Dear '.Str::upper($getHeader->employee_name).', <br/><br/>Here are the items that are CANCELED on your order form '.$getHeader->doc_number.' :</th>
                                </tr>
                            </table>

                            <table style="border-collapse:collapse;border: 1px solid #c7c7c7;width:100%;font-size:13px;">
                                <tr>
                                    <th style="width:10%;padding:5px;background-color:#c2d9fc;border-bottom: 1px solid #c7c7c7;font-weight:bold">NO.</td>
                                    <th style="width:70%;padding:5px;background-color:#c2d9fc;border-bottom: 1px solid #c7c7c7;font-weight:bold; text-align:left">APPLIANCE / ITEM</td>
                                    <th style="width:20%;padding:5px;background-color:#c2d9fc;border-bottom: 1px solid #c7c7c7;font-weight:bold; text-align:left">STATUS</td>
                                </tr>';

                $getItemCanceled = $modelProcurement->documentApprovalDetailOrderItem()
                                    ->select('appliance_item')
                                    ->where('doc_approval_id', $docApprovalId)
                                    ->where('order_form_id', $referenceId)
                                    ->where('unit_name', '<>', 'VAT')
                                    ->where('canceled_id', $canceledId)
                                    ->where('is_active', '1')
                                    ->orderBy('order_detail_id', 'asc')
                                    ->get();
                $x = 1;
                foreach ($getItemCanceled as $rowItemCanceled) {
                    $mailBody .= '<tr>
                                    <td style="padding:5px;border-bottom: 1px solid #c7c7c7; text-align:center">'.$x.'</td>
                                    <td style="padding:5px;border-bottom: 1px solid #c7c7c7;">'.$rowItemCanceled->appliance_item.'</td>
                                    <td style="padding:5px;border-bottom: 1px solid #c7c7c7;">CANCELED</td>
                                </tr>';
                    $x++;
                }

                $reason = nl2br(Str::trim($request->reason));
                $mailBody .= '</table>
                            <br>
                            <table style="border-collapse:collapse;border: none;width:100%;font-size:13px;">
                                <tr>
                                    <td valign="top" style="width:20%;padding:5px;border-bottom: 1px solid #c7c7c7;font-weight:bold">Canceled By</td>
                                    <td valign="top" style="width:1%;padding:5px;border-bottom: 1px solid #c7c7c7;font-weight:bold">:</td>
                                    <td valign="top" style="width:79%;padding:5px;border-bottom: 1px solid #c7c7c7;">'.Str::upper($getEmployee->employee_name).'</td>
                                </tr>
                                <tr>
                                    <td valign="top" style="width:20%;padding:5px;border-bottom: 1px solid #c7c7c7;font-weight:bold">Canceled At</td>
                                    <td valign="top" style="width:1%;padding:5px;border-bottom: 1px solid #c7c7c7;font-weight:bold">:</td>
                                    <td valign="top" style="width:79%;padding:5px;border-bottom: 1px solid #c7c7c7;">'.Carbon::parse($datetimeAt)->format('d-M-Y H:i').'</td>
                                </tr>
                                <tr>
                                    <td valign="top" style="width:20%;padding:5px;border-bottom: 1px solid #c7c7c7;font-weight:bold">Reason</td>
                                    <td valign="top" style="width:1%;padding:5px;border-bottom: 1px solid #c7c7c7;font-weight:bold">:</td>
                                    <td valign="top" style="width:79%;padding:5px;border-bottom: 1px solid #c7c7c7;">'.$reason.'</td>
                                </tr>
                            </table>';

                $mailBody .= "<br><div style='font-size:13px; margin-top:12px; display:block;font-weight:bold'>This information can also be found at https://app.logisteed.id menu Document Approval &#8594; My Request &#8594; History Tab.</div>
                            <div style='font-size:13px; margin-top:7px; margin-bottom:20px; display:block; font-style:italic'>This email was generated automatically by system, please don't reply this email.</div>";

                $cc = null;
                $bcc = null;
                dispatch(new SendEmailJob(
                    $accountId, // ACCOUNT ID
                    $getHeader->employee_email, // TO
                    $cc,
                    $bcc,
                    $mailSubject, // SUBJECT
                    $mailBody, // BODY
                    'normal', // PRIORITY = normal, low, high
                    [], // ATTACHMENT ARRAY
                ));
            }
            // END SEND EMAIL ITEM CANCELED

            $message = 'Successfully Canceled Item';
            return response()->json(['message' => $message, 'data' => $response], 200);
        }
        catch (ValidationException $e) {
            return response()->json(['message' => 'Please fill the required form.', 'errors' => $e->errors()], 422)
                            ->setStatusCode(422, 'Please fill the required form.');
        }
    }

    public function updatePoFormHistory(Request $request) {
        $token = SafeToken::decode($request->tokenForm);
        $validator = Validator::make($token, [
            'id' => 'required',
            'a' => 'required',
            'b' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => 'Invalid token form', 'errors' => $validator->errors()], 422)
                            ->setStatusCode(422, 'Invalid token form');
        }

        $docApprovalId = $token['a'];
        $referenceId = $token['b'];

        $employeeId = $this->employeeId;
        $companyId = $this->companyId;
        $modelProcurement = new ProcurementPurchasingModel();

        if($request->type == 'RECEIVED') {
            $dataUpdate = [
                'application_form_status' => '21',
                'received_order_by' => $employeeId,
                'received_order_at' => $request->receivedDateOrder,
            ];
        }
        else if($request->type == 'INVOICED') {
            $dataUpdate = [
                'application_form_status' => '22',
                'invoiced_order_by' => $employeeId,
                'invoiced_order_at' => Carbon::now()->format('Y-m-d H:i:s'),
            ];
        }

        $modelProcurement->docApprovalOrderApplication()
                        ->where('doc_approval_id', $docApprovalId)
                        ->where('application_id', $referenceId)
                        ->where('is_active', '1')
                        ->update($dataUpdate);

        $message = 'Successfully Updated Purchase Order Form';
        return response()->json(['message' => $message, 'data' => null], 200);
    }

    public function saveForm(Request $request) {
        $employeeId = $this->employeeId;
        $companyId = $this->companyId;
        $documentNumber = null;
        $model = new DocumentApprovalModel();
        $myData = $model->vwMasterEmployeeActive()->select('*')
                            ->where('employee_id', '=', $employeeId)
                            ->first();

        $token = SafeToken::decode($request->tokenForm);
        $validator = Validator::make($token, [
            'a' => 'required',
            'b' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => 'Invalid token form', 'errors' => $validator->errors()], 422)
                            ->setStatusCode(422, 'Invalid token form');
        }

        // dd($request->all());
        $docApprovalId = $token['a'];
        $referenceId = $token['b'];
        $selectedCurrency = null;
        $requestType = $request->requestType;

        try {
            $modelApproval = new DocumentApprovalModel();
            $modelProcurement = new ProcurementPurchasingModel();

            $documentTypeId = $request->documentType;
            $getDocumentType = $modelApproval->masterDocumentTypes()->select('doc_type_id', 'doc_name')
                                    ->where('doc_type_id', $documentTypeId)
                                    ->first();

            $getOrderFormHeader = $modelApproval->documentApprovalHeader()
                                                ->from('doc_approval_header as a')
                                                ->leftJoin('doc_approval_seq_number as b', 'b.seq_id', '=', 'a.seq_id')
                                                ->leftJoin('vw_master_employee_active as c', 'c.employee_id', '=', 'a.employee_id')
                                                ->leftJoin('master_company as d', 'd.company_id', '=', 'a.company_id')
                                                ->select('a.company_id', 'a.department_name', 'a.employee_id', 'a.department_id', 'a.location_id', 'b.seq_number', 'a.doc_number', 'a.seq_id', 'd.company_name')
                                                ->where('a.doc_approval_id', $docApprovalId)
                                                ->first();
            $companyId = $getOrderFormHeader->company_id;
            $companyName = $getOrderFormHeader->company_name;

            if($request->documentType === '6'){ // 6 =  COMPARISON FORM
                $validateRules = [
                    'documentType' => 'required|int',
                    'comparisonTitle' => 'required|string|max:255',
                    'comparisonDescription' => 'required|string|max:255',
                    'comparisonDate' => 'required|date_format:d-m-Y',
                    'yearForm' => 'required|string|max:4',
                    // 'discountType' => 'required|string|max:255',

                    'selectVendor.0' => 'required|string|max:255',
                    'validity.0' => 'required|date_format:d-m-Y',
                    // 'selectVendor.1' => 'required|string|max:255|different:selectVendor.0',

                    'unitPrice1.*' => 'required_with:colCriteriaId.*|string',
                    // 'unitPrice2.*' => 'required_with:colCriteriaId.*|string',

                    'selectedVendor' => 'required|string|max:255',
                    'vendorAddressSelected' => 'required|int',
                    'vendorPicSelected' => 'required|int',
                    'comparisonNote' => 'required|string',
                ];
                $validateMessages = [
                    'documentType.required' => 'Document type is required',
                    'comparisonTitle.required' => 'Comparison title is required',
                    'comparisonDescription.required' => 'Comparison description is required',
                    'comparisonDate.required' => 'Date is required',
                    // 'discountType' => 'Discount type is required',

                    'selectVendor.0.required' => 'Vendor 1 is required',
                    'validity.0.required' => 'Quotation validity 1 is required',
                    // 'selectVendor.1' => 'Vendor 2 is required',
                    // 'selectVendor.1.different' => 'Vendor 2 must be different',

                    'unitPrice1.*' => 'Unit price is required',
                    // 'unitPrice2.*' => 'Unit price is required',

                    'selectedVendor.required' => 'Selected vendor is required',
                    'vendorAddressSelected.required' => 'Selected vendor address is required',
                    'vendorPicSelected.required' => 'Selected vendor pic is required',
                    'comparisonNote.required' => 'Note is required',
                ];

                if ($request->input('selectVendor.1')) {
                    $validateRules['selectVendor.1'] = 'different:selectVendor.0';
                    $validateMessages['selectVendor.1.different'] = 'Vendor 2 must be different';

                    $validateRules['validity.1'] = 'required|date_format:d-m-Y';
                    $validateMessages['validity.1.required'] = 'Quotation validity 2 is required';

                    $validateRules['unitPrice2.*'] = 'required_with:colCriteriaId.*|string';
                    $validateMessages['unitPrice2.*'] = 'Unit price is required';
                }

                if ($request->input('selectVendor.2')) {
                    $validateRules['selectVendor.2'] = 'different:selectVendor.0,selectVendor.1';
                    $validateMessages['selectVendor.2.different'] = 'Vendor 3 must be different';

                    $validateRules['validity.2'] = 'required|date_format:d-m-Y';
                    $validateMessages['validity.2.required'] = 'Quotation validity 3 is required';

                    $validateRules['unitPrice3.*'] = 'required_with:colCriteriaId.*|string';
                    $validateMessages['unitPrice3.*'] = 'Unit price is required';
                }

                if(!$request->input('existAttachment.0')) {
                    $validateRules['attachmentFile.*'] = 'required|file|max:5120|mimetypes:application/pdf,image/jpeg,image/png,application/vnd.ms-excel,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet,application/vnd.openxmlformats-officedocument.wordprocessingml.document,application/vnd.ms-outlook,application/ms-tnef';
                    $validateMessages['attachmentFile.*.required'] = 'Quotation file is required';
                    $validateMessages['attachmentFile.*.mimetypes'] = 'Quotation must be a PDF, JPEG, PNG, Excel, Word or Outlook file.';
                    $validateMessages['attachmentFile.*.max'] = 'Quotation may not be greater than 5 MB.';
                }

                $validatedData = $request->validate($validateRules, $validateMessages);
                $datetime = Carbon::now()->format('Y-m-d H:i:s');
                $comparisonDate = Carbon::createFromFormat('d-m-Y', $request->comparisonDate)->format('Y-m-d');

                // $folderName = $getOrderFormHeader->department_name.'_'.$getOrderFormHeader->seq_number;
                // $documentName = str_replace(' ', '_', strtolower($getDocumentType->doc_name));
                $year = $request->yearForm;
                $applicationFlow = 'NEW';
                $docVersion = 1;

                // $folderName = Str::squish(Str::replace('&', ' ', Str::upper($getOrderFormHeader->department_name.' '.$getOrderFormHeader->seq_number)));
                // $documentName = Str::squish(Str::replace('&', ' ', Str::upper($getDocumentType->doc_name)));
                // $mainDirectory = "private/doc_approval";
                // $pathDetailForm = "/{$year}/purchasing/{$myData->company_code}/{$folderName}/";
                // $filenameForm = Str::squish(Str::replace('&', ' ', Str::upper($documentName.' '.$getOrderFormHeader->department_name.' '.$getOrderFormHeader->seq_number.' '.$docVersion.' '.uniqid())));

                $departmentFolder = sanitizeString($getOrderFormHeader->department_name);
                $documentNameExplode = Str::of($getOrderFormHeader->doc_number)->explode('-');
                $folderName = $documentNameExplode[2];
                $documentName = Str::upper(sanitizeString($getDocumentType->doc_name));
                $mainDirectory = "private/doc_approval";
                $pathDetailForm = "/{$year}/purchasing/{$companyId}/{$departmentFolder}/{$folderName}/";
                $filenameForm = Str::upper('COMPARISON FORM '.uniqid());

                // $filenameForm = $employeeId.'_'.time().'_'.uniqid().'.xlsx';
                // $filenameForm = $documentName.'_'.$getOrderFormHeader->department_name.'_'.$getOrderFormHeader->seq_number;

                $selectedVendorComparison = $latestComparisonId = null;
                if($requestType == 'REVISE' || $requestType == 'REVISE_COMPARISON_REVISE_APPLICATION') {
                    $getComparisonForm = $modelProcurement->docApprovalOrderComparison()
                                                    ->select('comparison_id', 'doc_version')
                                                    ->where('order_form_id', $referenceId)
                                                    ->where('comparison_id', $token['c'])
                                                    ->where('is_active', '1')
                                                    ->orderBy('comparison_id', 'desc')
                                                    ->first();
                    if($getComparisonForm) {
                        $latestComparisonId = $getComparisonForm->comparison_id;
                        $docVersion = $getComparisonForm->doc_version + 1;
                        $dataUpdate = [
                            'comparison_form_status' => '6',
                            'updated_by' => $employeeId,
                            'updated_at' => $datetime,
                        ];

                        $modelApproval->docApprovalOrderComparison()
                                    ->where('order_form_id', $referenceId)
                                    ->where('comparison_id', $latestComparisonId)
                                    ->whereNotIn('comparison_form_status', ['1','6','9','12','14'])
                                    ->where('is_active', 1)
                                    ->update($dataUpdate);
                    }

                    $dataUpdate = [
                        'item_completed' => '0',
                    ];
                    $modelApproval->documentApprovalDetailOrder()
                                ->where('doc_approval_id', $docApprovalId)
                                ->where('order_form_id', $referenceId)
                                ->where('is_active', 1)
                                ->update($dataUpdate);

                    // $dataUpdate = [
                    //     'is_revised' => '1'
                    // ];
                    // $modelProcurement->documentApprovalOrderGroup()
                    //                 ->where('order_form_id', $referenceId)
                    //                 ->update($dataUpdate);

                    $latestOrderFormId = null;
                    $getOrderFormAll = $modelApproval->documentApprovalDetailOrder()
                                                    ->from('doc_approval_detail_order_form')
                                                    ->select('order_form_id')
                                                    ->where('doc_approval_id', $docApprovalId)
                                                    ->orderBy('order_form_id', 'asc')
                                                    ->get();
                    foreach ($getOrderFormAll as $rowOrderFormAll) {
                        $getApplicationForm = $modelProcurement->docApprovalOrderApplication()
                                                        ->from('doc_approval_order_application as a')
                                                        ->leftJoin('doc_approval_order_group as b', function($join) {
                                                            $join->on('b.application_id', 'a.application_id')
                                                                ->where('b.is_active', '1')
                                                                ->orderBy('b.order_group_id', 'desc');
                                                        })
                                                        ->select('a.doc_approval_id', 'a.application_id', 'a.order_form_id', 'a.doc_version', 'b.order_group_id')
                                                        ->where('a.order_form_id', $rowOrderFormAll->order_form_id)
                                                        ->where('b.comparison_id', $latestComparisonId)
                                                        ->where('a.is_active', '1')
                                                        ->orderBy('a.application_id', 'desc')
                                                        ->first();
                        if($getApplicationForm) {
                            $dataUpdate = [
                                'application_form_status' => '6',
                                'final_decision_at' => $datetime,
                                'updated_by' => $employeeId,
                                'updated_at' => $datetime,
                            ];

                            $modelApproval->docApprovalOrderApplication()
                                        ->where('order_form_id', $referenceId)
                                        ->where('application_id', $getApplicationForm->application_id)
                                        ->whereNotIn('application_form_status', ['1','6','9','14'])
                                        ->where('is_active', 1)
                                        ->update($dataUpdate);

                            // UPDATE SIGNER
                            $dataUpdate = [
                                'status' => '16', // NOT SUBMITTED
                                'received_at' => null,
                            ];
                            $model->docApprovalFlowSign()
                                    ->where('doc_approval_id', $getApplicationForm->doc_approval_id)
                                    ->where('reference_id', $getApplicationForm->application_id)
                                    ->where('reference_group_id', $getApplicationForm->order_group_id)
                                    ->whereIn('status', ['3','13']) // 3 = RECEIVED, 13 = PENDING
                                    ->where('is_active', 1)
                                    ->update($dataUpdate);
                        }

                        $latestOrderFormId = $rowOrderFormAll->order_form_id;
                    }

                    // $getApplicationFormLatest = $modelProcurement->docApprovalOrderApplication()
                    //                                             ->from('doc_approval_order_application as a')
                    //                                             ->select('a.doc_approval_id', 'a.application_id', 'a.order_form_id', 'a.doc_version')
                    //                                             ->where('a.order_form_id', $latestOrderFormId)
                    //                                             ->where('a.is_active', '1')
                    //                                             ->orderBy('a.application_id', 'desc')
                    //                                             ->first();
                    // if($latestComparisonId) {
                    //     $getApplicationForm = $getApplicationForm->where('a.comparison_id', $latestComparisonId);
                    // }

                    // $getApplicationForm = $getApplicationForm->where('a.is_active', '1')
                    //                         ->orderBy('a.application_id', 'desc')
                    //                         ->first();
                    // if($getApplicationForm) {
                    //     $dataUpdate = [
                    //         'application_form_status' => '6',
                    //         'final_decision_at' => $datetime,
                    //         'updated_by' => $employeeId,
                    //         'updated_at' => $datetime,
                    //     ];

                    //     $modelApproval->docApprovalOrderApplication()
                    //                 ->where('order_form_id', $referenceId)
                    //                 ->where('application_id', $getApplicationForm->application_id)
                    //                 ->whereNotIn('application_form_status', ['1','6','9','12','14'])
                    //                 ->where('is_active', 1)
                    //                 ->update($dataUpdate);

                    //     // UPDATE SIGNER
                    //     $dataUpdate = [
                    //         'status' => '16', // NOT SUBMITTED
                    //         'received_at' => null,
                    //     ];
                    //     $model->docApprovalFlowSign()
                    //             ->where('doc_approval_id', $getApplicationForm->doc_approval_id)
                    //             ->where('reference_id', $getApplicationForm->application_id)
                    //             ->whereIn('status', ['3','13']) // 3 = RECEIVED, 13 = PENDING
                    //             ->where('is_active', 1)
                    //             ->update($dataUpdate);
                    // }
                }

                $dataInsert = [
                    'doc_approval_id' => $docApprovalId,
                    'company_id' => $companyId,
                    'order_form_id' => $referenceId,
                    'comparison_title' => $request->comparisonTitle,
                    'doc_version' => $docVersion,
                    'comparison_form_status' => '2',
                    'comparison_description' => $request->comparisonDescription,
                    'comparison_date' => $comparisonDate,
                    // 'validity' => $request->input('comparisonValidity', null),
                    'vendor_id_selected' => $request->selectedVendor,
                    'comparison_notes' => $request->comparisonNote,
                    'created_by' => $employeeId,
                    'created_at' => $datetime,
                    'path_detail' => $pathDetailForm,
                    'filename' => $filenameForm,
                    'year_form' => $year,
                ];
                $insertHeader = $modelProcurement->docApprovalOrderComparison()->create($dataInsert);
                $comparisonId = $insertHeader->id;

                $selectedVendorName = '';
                $comparisonVendorIdArr = [];
                $arrVendorComparison = [];
                for($x = 0; $x < count($request->selectVendor); $x++){
                    if($request->selectVendor[$x] != '') {
                        $vendorAddressId = $vendorAddress = $picId = $picName = null;
                        $getVendor = $modelProcurement->masterVendor()
                                                    ->select('vendor_name')
                                                    ->where('vendor_id', $request->selectVendor[$x])
                                                    ->first();
                        $vendorName = $getVendor->vendor_name;

                        if($request->selectVendor[$x] == $request->selectedVendor) {
                            $selectedVendorName = $getVendor->vendor_name;
                            $getVendorAddress = $modelProcurement->masterVendorAddress()
                                                            ->select('vendor_address')
                                                            ->where('vendor_id', $request->selectedVendor)
                                                            ->where('vendor_address_id', $request->vendorAddressSelected)
                                                            ->first();

                            $vendorAddressId = $request->vendorAddressSelected;
                            $vendorAddress = $getVendorAddress->vendor_address;

                            $getVendorPic = $modelProcurement->masterVendorPic()
                                                            ->select('pic_name')
                                                            ->where('vendor_id', $request->selectedVendor)
                                                            ->where('vendor_pic_id', $request->vendorPicSelected)
                                                            ->first();
                            $picId = $request->vendorPicSelected;
                            $picName = $getVendorPic->pic_name;
                        }

                        $validityDate = Carbon::createFromFormat('d-m-Y', $request->validity[$x])->format('Y-m-d');
                        $dataInsert = [
                            'comparison_id' => $comparisonId,
                            'company_id' => $companyId,
                            'doc_approval_id' => $docApprovalId,
                            'order_form_id' => $referenceId,
                            'vendor_id' => $request->selectVendor[$x],
                            'vendor_name' => $vendorName,
                            'vendor_address_id' => $vendorAddressId,
                            'vendor_address' => $vendorAddress,
                            'vendor_pic_id' => $picId,
                            'pic_name' => $picName,
                            'quotation_validity' => $validityDate,
                        ];

                        $insertVendor = $modelProcurement->docApprovalOrderComparisonVendor()->create($dataInsert);
                        $comparisonVendorIdArr[$x] = $insertVendor->id;

                        if($request->selectVendor[$x] == $request->selectedVendor) {
                            $selectedVendorComparison = $insertVendor->id;

                        }

                        $arrVendorComparison[] = [
                                                    'vendorName' => $vendorName,
                                                    'validity' => $validityDate,
                                                ];
                    }
                }

                $comparisonItemIdArr = [];
                $arrItem = [];

                // // INSERT ORDER ITEM GROUP
                // $dataInsert = [
                //     'company_id' => $companyId,
                //     'order_form_id' => $referenceId
                // ];
                // $insertItemGroup = $modelProcurement->documentApprovalDetailOrderItemGroup()->create($dataInsert);
                // $insertItemGroupId = $insertItemGroup->id;
                // // END INSERT ORDER ITEM GROUP

                // CREATE ORDER GROUP
                $dataInsert = [
                    'company_id' => $companyId,
                    'order_form_id' => $referenceId,
                    'comparison_id' => $comparisonId,
                ];
                $insertOrderGroup = $modelProcurement->documentApprovalOrderGroup()->create($dataInsert);
                $orderGroupId = $insertOrderGroup->id;
                // END CREATE ORDER GROUP
                for($x = 0; $x < count($request->colCriteria); $x++){
                    if($request->colCriteria[$x] != '') {

                        // if($request->colCriteria[$x] == 'VAT' || (($request->colCriteria[$x] == 'Discount' || $request->colCriteria[$x] == 'PPh') && $request->discountType == 'PERCENTAGE')) {
                        //     $currencyType = '%';
                        // }
                        // else {
                        //     $currencyType = $request->currency;
                        // }

                        $currencyType = $request->currency;
                        $colCriteriaId = $request->colCriteriaId[$x] != '' ? $request->colCriteriaId[$x] : null;
                        $dataInsert = [
                            'order_form_id' => $referenceId,
                            'company_id' => $companyId,
                            'order_detail_id' => $colCriteriaId,
                            'comparison_id' => $comparisonId,
                            'appliance_item' => $request->colCriteria[$x],
                            'unit_name' => $request->colUnit[$x] != '' ? $request->colUnit[$x] : null,
                            'unit_quantity' => $request->colQty[$x] != '' ? $request->colQty[$x] : null,
                            'currency' => $currencyType,
                        ];
                        $insertItem = $modelProcurement->docApprovalOrderComparisonItem()->create($dataInsert);
                        $comparisonItemId = $insertItem->id;
                        $comparisonItemIdArr[$x] = $comparisonItemId;

                        if($request->colCriteriaId[$x] != '') {
                            $dataUpdate = [
                                'order_group_id' => $orderGroupId,
                                'comparison_id' => $comparisonId,
                                'vendor_id' => $request->selectedVendor,
                                'comparison_item_id' => $comparisonItemId,
                                // 'application_id' => null,
                                'revise_order_detail_id' => $request->reviseOrderDetailId[$x] ?: null,
                            ];

                            $modelApproval->documentApprovalDetailOrderItem()
                                        ->where('order_detail_id',$request->colCriteriaId[$x])
                                        ->where('order_form_id', $referenceId)
                                        ->where('is_active', 1)
                                        ->update($dataUpdate);
                        }

                        $arrItemVendor = [];
                        for($a = 0; $a < count($request->selectVendor); $a++){
                            if($request->selectVendor[$a] != '') {
                                $dppOtherValue = null;
                                $unitPrice = 'unitPrice'.($a + 1);
                                $unitPrice = $request->$unitPrice;

                                $unitCurrency = 'currency'.($a + 1);
                                $unitCurrency = $request->$unitCurrency;
                                // dd($unitCurrency);

                                if($selectedCurrency == null && $unitCurrency[$x] != '') {
                                    $selectedCurrency = $unitCurrency[$x];
                                }


                                // for($b = 0; $b < count($unitPrice); $b++){
                                    if (array_key_exists($x, $unitPrice)) {
                                        $unitPriceValue = $unitPrice[$x] != '' ? $unitPrice[$x] : 0;
                                        $qty = $request->input("colQty.$x", 0);
                                        $unitPriceValue = str_replace(',', '', $unitPriceValue);
                                        $unitPriceValue = (float)$unitPriceValue;
                                        $totalPrice = $qty * $unitPriceValue;

                                        // dd($unitPriceValue);
                                        // dd($comparisonVendorIdArr[$a]);
                                        $dataInsert = [
                                            'company_id' => $companyId,
                                            'comparison_id' => $comparisonId,
                                            'comparison_item_id' => $comparisonItemId,
                                            'comparison_vendor_id' => $comparisonVendorIdArr[$a],
                                            'currency_code' => $unitCurrency[$x] != '' ? $unitCurrency[$x] : null,
                                            'unit_price' => $unitPriceValue,
                                            'dpp_other_value' => $dppOtherValue,
                                            'total_price' => $totalPrice,
                                        ];
                                        $insertItemVendor = $modelProcurement->docApprovalOrderComparisonItemVendor()->create($dataInsert);
                                    }
                                    else {
                                        if($request->colCriteria[$x] == 'Delivery Fee') {
                                            $unitPriceValue = $request->input("deliveryFee.$a", 0);
                                            if($unitPriceValue == 0) {
                                                $unitPriceValue = $totalPrice = null;
                                            }
                                            else {
                                                $unitPriceValue = str_replace(',', '', $unitPriceValue);
                                                $unitPriceValue = (float)$unitPriceValue;

                                                $totalPrice = $request->input("deliveryFeeTotal.$a", 0);
                                                $totalPrice = str_replace(',', '', $totalPrice);
                                                $totalPrice = (float)$totalPrice;
                                            }
                                        }
                                        else if($request->colCriteria[$x] == 'Discount') {
                                            $selectedCurrency = $request->input("discountType.$a", null);
                                            $unitPriceValue = $request->input("discount.$a", 0);
                                            if($unitPriceValue == 0) {
                                                $unitPriceValue = $totalPrice = null;
                                            }
                                            else {
                                                $unitPriceValue = str_replace(',', '', $unitPriceValue);
                                                $unitPriceValue = (float)$unitPriceValue;

                                                $totalPrice = $request->input("discountTotal.$a", 0);
                                                $totalPrice = str_replace(',', '', $totalPrice);
                                                $totalPrice = str_replace('(', '', $totalPrice);
                                                $totalPrice = str_replace(')', '', $totalPrice);
                                                $totalPrice = (float)$totalPrice;
                                            }
                                        }
                                        else if($request->colCriteria[$x] == 'Total Price') {
                                            $unitPriceValue = null;
                                            $totalPrice = $request->input("totalVendor.$a", 0);
                                            $totalPrice = str_replace(',', '', $totalPrice);
                                            $totalPrice = (float)$totalPrice;
                                        }
                                        else if($request->colCriteria[$x] == 'VAT') {
                                            $selectedCurrency = '%';
                                            $unitPriceValue = $request->input("vatRate.$a", 0);
                                            if($unitPriceValue == 0) {
                                                $unitPriceValue = $totalPrice = null;
                                            }
                                            else {
                                                if($unitPriceValue == '12x11/12') {
                                                    $unitPriceValue = 12;
                                                    $dppOtherValue = '11/12';
                                                }

                                                $totalPrice = $request->input("vat.$a", 0);
                                                $totalPrice = str_replace(',', '', $totalPrice);
                                                $totalPrice = (float)$totalPrice;
                                            }
                                        }
                                        else if($request->colCriteria[$x] == 'PPh') {
                                            $selectedCurrency = $request->input("pphType.$a", null);
                                            $unitPriceValue = $request->input("pph.$a", 0);
                                            if($unitPriceValue == 0) {
                                                $unitPriceValue = $totalPrice = null;
                                            }
                                            else {
                                                $unitPriceValue = str_replace(',', '', $unitPriceValue);
                                                $unitPriceValue = (float)$unitPriceValue;

                                                $totalPrice = $request->input("pphTotal.$a", 0);
                                                $totalPrice = str_replace(',', '', $totalPrice);
                                                $totalPrice = str_replace('(', '', $totalPrice);
                                                $totalPrice = str_replace(')', '', $totalPrice);
                                                $totalPrice = (float)$totalPrice;
                                            }
                                        }
                                        else if($request->colCriteria[$x] == 'Total Price + VAT - PPh') {
                                            $unitPriceValue = null;
                                            $totalPrice = $request->input("grandTotal.$a", 0);
                                            $totalPrice = str_replace(',', '', $totalPrice);
                                            $totalPrice = (float)$totalPrice;
                                        }

                                        $dataInsert = [
                                            'company_id' => $companyId,
                                            'comparison_id' => $comparisonId,
                                            'comparison_item_id' => $comparisonItemId,
                                            'comparison_vendor_id' => $comparisonVendorIdArr[$a],
                                            'currency_code' => $selectedCurrency,
                                            'unit_price' => $unitPriceValue,
                                            'dpp_other_value' => $dppOtherValue,
                                            'total_price' => $totalPrice,
                                        ];
                                        $insertItemVendor = $modelProcurement->docApprovalOrderComparisonItemVendor()->create($dataInsert);
                                    }

                                    $arrItemVendor[] = [
                                        'unitPriceValue' => $unitPriceValue,
                                        'dppOtherValue' => $dppOtherValue,
                                        'totalPrice' => $totalPrice,
                                        'comparisonVendorId' => $comparisonVendorIdArr[$a],
                                    ];

                                // }
                            }
                        }

                        $arrItem[] = [
                            'criteriaId' => $colCriteriaId,
                            'applianceItem' => $request->colCriteria[$x],
                            'unitQty' => $request->colQty[$x],
                            'unitName' => $request->colUnit[$x],
                            'currency' => $currencyType,
                            'arrItemVendor' => $arrItemVendor,
                        ];
                    }
                }

                // // UPDATE ORDER ITEM GROUP
                // $dataUpdate = [
                //     'comparison_id' => $comparisonId
                // ];
                // $modelApproval->documentApprovalDetailOrderItemGroup()
                //             ->where('order_form_id', $referenceId)
                //             ->where('company_id', $companyId)
                //             ->where('order_detail_group_id', $insertItemGroupId)
                //             ->where('is_active', 1)
                //             ->update($dataUpdate);
                // // UPDATE ORDER ITEM GROUP

                if($requestType == 'REVISE' || $requestType == 'REVISE_COMPARISON_REVISE_APPLICATION') {
                    $existAttachment = $request->input("existAttachment", []);
                    for($x = 0; $x < count($existAttachment); $x++){
                        $tokenAttachment = SafeToken::decode($request->existAttachment[$x]);
                        // $tokenFile = ['a' => 'DOC_APPROVAL', 'b' => $row->attachment_id];
                        $attachmentId = $tokenAttachment['b'];

                        $getAttachment = $model->docApprovalAttachment()
                                            ->select('company_id', 'doc_type_id', 'path_detail', 'filename', 'filename_original', 'converted', 'filename_converted', 'mime_type', 'downloadable')
                                            ->where('attachment_id', '=', $attachmentId)
                                            ->where('doc_approval_id', '=',$docApprovalId)
                                            ->where('is_active', '=', '1')
                                            ->first();
                        if($getAttachment){
                            $dataInsert = [
                                'doc_approval_id' => $docApprovalId,
                                'company_id' => $getAttachment->company_id,
                                'doc_type_id' => $getAttachment->doc_type_id,
                                'reference_id' => $comparisonId,
                                'path_detail' => $getAttachment->path_detail,
                                'filename' => $getAttachment->filename,
                                'filename_original' => $getAttachment->filename_original,
                                'converted' => $getAttachment->converted,
                                'filename_converted' => $getAttachment->filename_converted,
                                'mime_type' => $getAttachment->mime_type,
                                'downloadable' => $getAttachment->downloadable,
                            ];
                            $insertAttachment = $model->docApprovalAttachment()->create($dataInsert);
                        }
                    }
                }

                if($getDocumentType) {
                    // $folderName = $getOrderFormHeader->department_name.'_'.$getOrderFormHeader->seq_number;
                    $documentName = str_replace(' ', '_', strtolower($getDocumentType->doc_name));
                    $year = $request->yearForm;
                    if ($request->hasFile('attachmentFile')) {
                        foreach ($request->file('attachmentFile') as $file) {
                            $filenameOriginal = DecodeModSecurityPlaceholders::decodeFilenameStatic($file->getClientOriginalName());
                            // $mimeType = $file->getMimeType();
                            $mimeType = $this->getMimeTypeByExtension($filenameOriginal);

                            // $filenameOriginal = $file->getClientOriginalName();

                            // $filenameOriginal = preg_replace('/[^a-zA-Z0-9_\-\. \p{Hiragana}\p{Katakana}\p{Han}]/u', '_', $filenameOriginal);
                            // $filenameOriginal = substr($filenameOriginal, 0, 100);

                            $filename = Str::squish(Str::upper(Str::replace('&', ' ', $filenameForm.' ATTACHMENT '.uniqid()))).'.'.$file->getClientOriginalExtension();
                            $file->storeAs($mainDirectory.$pathDetailForm, $filename, 'local');
                            $converted = str_contains($mimeType, 'pdf') ? null : 0;

                            $dataInsert = [
                                'doc_approval_id' => $docApprovalId,
                                'company_id' => $companyId,
                                'doc_type_id' => $request->documentType,
                                'reference_id' => $comparisonId,
                                'path_detail' => $pathDetailForm,
                                'filename' => $filename,
                                'filename_original' => $filenameOriginal,
                                'converted' => $converted,
                                'mime_type' => $mimeType,
                                'downloadable' => '1',
                            ];
                            $insertAttachment = $modelApproval->docApprovalAttachment()->create($dataInsert);

                            // if (str_contains($mimeType, 'excel') || str_contains($mimeType, 'spreadsheetml') || str_contains($mimeType, 'wordprocessingml')) {
                            //     ConvertFileJob::dispatch($insertAttachment->id);
                            // }
                            if (!str_contains($mimeType, 'pdf')) {
                                ConvertFileJob::dispatch($insertAttachment->id);
                            }
                        }
                    }
                }

                $dataForm = collect([
                    'companyId' => $companyId,
                    'title' => $request->comparisonTitle,
                    'description' => $request->comparisonDescription,
                    'date' => $comparisonDate,
                    'vendorComparison' => $arrVendorComparison,
                    'itemForm' => $arrItem,
                    'selectedVendorComparison' => $selectedVendorComparison,
                    'selectedVendorName' => $selectedVendorName,
                    'comparisonNote' => $request->comparisonNote,
                    'filePath' => $pathDetailForm,
                    'filename' => $filenameForm,
                ]);

                $requestData = new Request();
                $requestData->replace([
                                        'documentType' => '6',
                                        'dataForm' => compact('dataForm'),
                                    ]);
                $documentApprovalController = new ControllersDocumentApprovalController();
                $documentApprovalController->generatePdf($requestData);

                if($requestType == 'REVISE' || $requestType == 'REVISE_COMPARISON_REVISE_APPLICATION') {
                    // $arrReviseOrderDetailId = array();
                    // $getComparisonItem = $modelProcurement->docApprovalOrderComparisonItem()
                    //                                     ->from('doc_approval_order_comparison_item as a')
                    //                                     ->leftJoin('doc_approval_detail_order_form_item as b', 'b.order_detail_id', 'a.order_detail_id', 'b.revise_order_detail_id')
                    //                                     ->select('a.comparison_item_id', 'a.order_detail_id', 'a.appliance_item', 'a.unit_name', 'a.unit_quantity', 'a.currency', 'b.revise_order_detail_id')
                    //                                     ->where('a.order_form_id', $referenceId)
                    //                                     ->where('a.comparison_id', $latestComparisonId)
                    //                                     ->where('a.is_active', '1')
                    //                                     ->orderBy('a.comparison_item_id', 'asc')
                    //                                     ->get();

                    // foreach ($getComparisonItem as $rowItem) {
                    //     $reviseOrderDetailId = $rowItem->revise_order_detail_id ?: $rowItem->order_detail_id;
                    //     if($rowItem->revise_order_detail_id && !in_array($rowItem->revise_order_detail_id, $arrReviseOrderDetailId)) {
                    //         $arrReviseOrderDetailId[] = $rowItem->revise_order_detail_id;
                    //     }
                    // }

                    $arrOrderGroupId = array();
                    for($x = 0; $x < count($request->reviseOrderDetailId); $x++) {
                        if($request->reviseOrderDetailId) {
                            // GET ORDER FORM BEFORE REVISION
                            // $getDetailOrderPrev = $modelProcurement->documentApprovalDetailOrderItem()
                            //                                     ->from('doc_approval_detail_order_form_item as a')
                            //                                     ->rightJoin('doc_approval_order_group as b', function($join) {
                            //                                         $join->on('b.order_group_id', 'a.order_group_id')
                            //                                             ->where('b.is_active', '1')
                            //                                             ->orderBy('b.order_group_id', 'desc');
                            //                                     })
                            //                                     ->select('b.order_group_id', 'b.comparison_id', 'b.application_id')
                            //                                     ->where('a.company_id', $companyId)
                            //                                     ->where('a.doc_approval_id', $docApprovalId)
                            //                                     ->where('a.order_detail_id', '=', $request->reviseOrderDetailId[$x])
                            //                                     ->where('a.is_active', '1')
                            //                                     ->first();
                            // if($getDetailOrderPrev) {
                            //     if($getDetailOrderPrev->order_group_id && !in_array($getDetailOrderPrev->order_group_id, $arrOrderGroupId)) {
                            //         $arrOrderGroupId[] = $getDetailOrderPrev->order_group_id;
                            //     }
                            // }

                            /////////////////////////////////////////
                            $getDetailOrderPrev = $modelProcurement->documentApprovalDetailOrderItem()
                                                                ->from('doc_approval_detail_order_form_item as a')
                                                                ->rightJoin('doc_approval_order_group as b', function($join) {
                                                                    $join->on('b.application_id', 'a.application_id')
                                                                        ->where('b.is_active', '1')
                                                                        ->orderBy('b.order_group_id', 'desc');
                                                                })
                                                                ->select('b.order_group_id', 'b.comparison_id', 'b.application_id')
                                                                ->where('a.company_id', $companyId)
                                                                ->where('a.doc_approval_id', $docApprovalId);

                            // if($rowItem->order_detail_id == $rowItem->revise_order_detail_id) {
                            $getDetailOrderPrev =  $getDetailOrderPrev->where('a.order_form_id', $referenceId)
                                                            // ->where('a.order_group_id', '<', $getComparisonForm->order_group_id)
                                                            ->whereNotNull('a.application_id');
                                                            // ->where('a.order_group_id', '<', $getComparisonForm->order_group_id);
                            // }
                            // else {
                            // // $getDetailOrderPrev =  $getDetailOrderPrev->where('a.order_detail_id', '=',  $rowItem->revise_order_detail_id);
                            // }

                            $getDetailOrderPrev =  $getDetailOrderPrev->where('a.order_detail_id', '=', $request->reviseOrderDetailId[$x]);
                            $getDetailOrderPrev =  $getDetailOrderPrev->orderBy('b.application_id', 'desc')
                                                                    ->orderBy('b.comparison_id', 'desc')
                                                                    ->first();

                            if($getDetailOrderPrev) {
                                $arrOrderGroupId[] = $getDetailOrderPrev->order_group_id;
                            }
                        }
                    }

                    // HERE
                    rsort($arrOrderGroupId);
                    $reviseApplication = false;
                    // for($x = 0; $x < count($arrOrderGroupId); $x++) {
                    //     // GET APPLICATION BEFORE REVISION
                    //     $getApplicationFormLatest = $modelProcurement->docApprovalOrderApplication()
                    //                                     ->from('doc_approval_order_application as a')
                    //                                     ->rightJoin('doc_approval_order_group as b', 'b.application_id', '=', 'a.application_id')
                    //                                     ->select('a.doc_approval_id', 'a.application_id', 'a.order_form_id', 'a.doc_version', 'a.comparison_id')
                    //                                     ->where('a.company_id', $companyId)
                    //                                     ->where('b.order_group_id', $arrOrderGroupId[$x])
                    //                                     ->where('b.is_active', '1')
                    //                                     ->orderBy('a.application_id', 'desc')
                    //                                     ->first();
                    //     if($getApplicationFormLatest) {
                    //         $reviseApplication = true;
                    //         break;
                    //     }
                    // }

                    for($x = 0; $x < count($arrOrderGroupId); $x++) {
                        // GET APPLICATION BEFORE REVISION
                        $getApplicationFormLatest = $modelProcurement->docApprovalOrderApplication()
                                                                ->from('doc_approval_order_application as a')
                                                                ->rightJoin('doc_approval_order_group as b', 'b.application_id', '=', 'a.application_id')
                                                                ->select('a.doc_approval_id', 'a.application_id', 'a.order_form_id', 'a.doc_version', 'a.comparison_id', 'b.order_group_id')
                                                                ->where('a.company_id', $companyId)
                                                                ->where('b.order_group_id', $arrOrderGroupId[$x])
                                                                ->where('b.is_active', '1')
                                                                ->orderBy('a.application_id', 'desc')
                                                                ->first();
                        if($getApplicationFormLatest) {
                            $reviseApplication = true;
                            // $tokenRevise = ['cid' => $companyId, 'id' => $employeeId,'a' => $getApplicationFormLatest->doc_approval_id, 'b' => $getApplicationFormLatest->application_id, 'c'=> $getApplicationFormLatest->comparison_id, 'd'=>null, 'g'=>$getApplicationFormLatest->order_group_id];
                            $tokenRevise = ['cid' => $companyId, 'id' => $employeeId,'a' => $getApplicationFormLatest->doc_approval_id, 'b' => $getApplicationFormLatest->application_id, 'c'=> $getApplicationFormLatest->comparison_id >= $comparisonId ?: $comparisonId, 'd'=>null, 'g'=>$getApplicationFormLatest->order_group_id];
                            $encodedToken = SafeToken::encode($tokenRevise);
                            break;
                        }
                    }

                    if($reviseApplication == true) {
                        $applicationFlow = 'REVISE';
                        // $token = ['cid' => $companyId, 'id' => $employeeId,'a' => $getApplicationFormLatest->doc_approval_id, 'b' => $getApplicationFormLatest->application_id,'c' => $comparisonId,'d'=>null];
                        // $jsonToken = json_encode($token);
                        // $encryptedToken = Crypt::encryptString($jsonToken);
                        // $encodedToken = rtrim(strtr(base64_encode($encryptedToken), '+/', '-_'), '=');
                    }
                    else{
                        $token = ['cid' => $companyId, 'id' => $employeeId,'a' => $docApprovalId, 'b' => $referenceId,'c' => $comparisonId,'d'=>null];
                        $encodedToken = SafeToken::encode($token);
                    }
                }
                else {
                    $token = ['cid' => $companyId, 'id' => $employeeId,'a' => $docApprovalId, 'b' => $referenceId,'c' => $comparisonId,'d'=>null];
                    $encodedToken = SafeToken::encode($token);
                }

                $message = 'Successfully Submit Comparison Form';
                return response()->json(['tokenForm' => $encodedToken, 'applicationFlow' => $applicationFlow, 'message' => $message], 200);
            }
            else if($request->documentType === '2'){ // 2 = APPLICATION FORM
                $comparisonId = array_key_exists('c', $token) ? $token['c'] : null;
                $validateRules = [
                    'compareItem' => 'required|string|in:true,false',
                    'documentType' => 'required|int',
                    // 'applicationHeader' => 'required|string|max:255',
                    'seqNumber' => 'required|string|max:255',
                    'titleApplication' => 'required|string|max:255',

                    'estimateApplication' => 'required|string|max:255|not_in:0,0.0',
                    'submittedDateApplication' => 'required|date_format:d-m-Y',

                    // 'vendorPicId' => 'required|string|max:255',
                    'vendorPicApplication' => 'required|string|max:255',

                    'deliveryTo' => 'required|string|max:255',
                    'invoiceTo' => 'required|string|max:255',

                    'deliveryApplication' => 'required|string|max:255',
                    'invoiceApplication' => 'required|string|max:255',

                    'currencyApplication' => 'required|string|max:255',
                    'vendorApplication' => 'required|string|max:255',
                    'vendorAddressApplication' => 'required|string|max:255',

                    // 'budgetNoApplication' => 'required|string|max:255',
                    // 'budgetAmountApplication' => 'required|string|max:255',
                    // 'budgetWithinApplication' => 'required|string|max:255',

                    // 'costCenterApplication.*' => 'required|string|max:255',
                    // 'costCenterPercentApplication.*' => 'required|string|max:255',

                    // 'reimburseApplication1' => 'required|string|max:255',
                    // 'reimburseApplication2' => 'required|string|max:255',
                    'shipDateApplication' => 'required|date_format:d-m-Y',
                    // 'remarkApplication' => 'required|string|max:255',

                    'purchaseTypeApplication' => 'required|string|max:255',
                    'paymentTypeApplication' => 'required|int',
                    'reasonApplication' => 'required|string|max:255',
                    'priorityApplication' => 'required|string|max:3',

                    'ruleId' => 'required|int',
                    'applicant' => 'required|string|max:50',
                    'approver' => 'required|string|max:50',

                    // 'attachmentFile.*' => Rule::when($request->compareItem == false, [
                    //                         'required',
                    //                         'file',
                    //                         'max:5120',
                    //                         'mimetypes:application/pdf,image/jpeg,image/png,application/vnd.ms-excel,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet,application/vnd.openxmlformats-officedocument.wordprocessingml.document,application/vnd.ms-outlook,application/ms-tnef'
                    //                     ]),
                ];
                $validateMessages = [
                    'documentType.required' => 'Document type is required',
                    // 'applicationHeader.required' => 'Application header is required',
                    'seqNumber.required' => 'Document number is required',
                    'titleApplication.required' => 'Application title is required',

                    'estimateApplication' => 'Estimate price is required',
                    'submittedDateApplication.required' => 'Submitted date is required',

                    // 'vendorPicId.required' => 'Attention to is required',
                    'vendorPicApplication.required' => 'Attention to is required',
                    'vendorApplication.required' => 'Paid To is required',
                    'vendorAddressApplication.required' => 'Paid To Address is required',
                    'currencyApplication.required' => 'Currency required',
                    // 'budgetNoApplication.required' => 'Budget Number is required',

                    // 'costCenterApplication.0' => 'Cost center required',
                    // 'costCenterPercentApplication.0' => 'required',

                    'shipDateApplication.required' => 'required',

                    'deliveryTo.required' => 'Delivery to is required',
                    'invoiceTo.required' => 'Invoice to is required',

                    'deliveryApplication.required' => 'Delivery address is required',
                    'purchaseTypeApplication.required' => 'Purchase type is required',
                    'paymentTypeApplication.required' => 'Payment type is required',
                    // 'remarkApplication.required' => 'Remark required',
                    'reasonApplication.required' => 'Reason is required',
                    'priorityApplication.required' => 'Priority level is required',

                    'ruleId.required' => 'Approval flow required',
                    'applicant.required' => 'Applied by is required',
                    'approver.required' => 'Approve by is required',
                    // 'attachmentFile.*.required' => 'Quotation is required',
                    // 'attachmentFile.*.mimetypes' => 'Attachment must be a PDF, JPEG, PNG, Excel, Word or Outlook file.',
                    // 'attachmentFile.*.max' => 'Attachment may not be greater than 5 MB.',
                ];

                // foreach ($request->colCriteriaId ?: [] as $key => $value) {
                //     if (!empty($value)) {
                //         $validateRules["budgetNoApplication"] = 'required|string';
                //         $validateMessages["budgetNoApplication.required"] = 'Unit price is required';
                //     }
                // }

                if($request->companyId == '322') {
                    $validateRules["termPayment"] = 'required|int';
                    $validateMessages["termPayment.required"] = 'Payment term is required';
                }

                $getPurchaseType = $modelProcurement->masterPurchaseType()
                                                ->from('master_purchase_type as a')
                                                ->select('a.purchase_type', 'a.budget_no_id')
                                                ->where('purchase_type_id', $request->purchaseTypeApplication)
                                                ->first();
                if($getPurchaseType) {
                    if($getPurchaseType->budget_no_id == '1') {
                        $validateRules["budgetApplication.*"] = 'required|string|max:255';
                        $validateRules["budgetAmountApplication.*"] = 'required|string|max:255';
                        $validateRules["budgetWithinInput.*"] = 'required|string|max:255';

                        $validateMessages["budgetApplication.*.required"] = 'Budget number is required';
                        $validateMessages["budgetAmountApplication.*.required"] = 'Amount is required';
                        $validateMessages["budgetWithinInput.*.required"] = 'Required';
                    }
                }

                for($x = 0; $x < count($request->costCenterApplication); $x++){
                    $validateRules["costCenterApplication.{$x}"] = 'required|string|max:255';
                    $validateRules["costCenterPercentApplication.{$x}"] = [
                        'required',
                        'numeric',
                        'regex:/^([0-9]{1,2}(\.\d{1,2})?|100(\.00?)?)$/'
                    ];

                    $validateMessages["costCenterApplication.{$x}.required"] = 'Cost center required';
                    $validateMessages["costCenterPercentApplication.{$x}.required"] = 'Required';
                    $validateMessages["costCenterPercentApplication.{$x}.max"] = 'Maximum 100%';
                }

                if($request->compareItem == 'false') {
                    $validateRules["attachmentFile.*"] = 'required|file|max:5120|mimetypes:application/pdf,image/jpeg,image/png,application/vnd.ms-excel,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet,application/vnd.openxmlformats-officedocument.wordprocessingml.document,application/vnd.ms-outlook,application/ms-tnef';
                    $validateMessages["attachmentFile.*.required"] = 'Quotation is required';
                    $validateMessages["attachmentFile.*.mimetypes"] = 'Attachment must be a PDF, JPEG, PNG, Excel, Word or Outlook file.';
                    $validateMessages["attachmentFile.*.max"] = 'Attachment may not be greater than 5 MB.';
                }

                if($request->ruleId) {
                    $getMatrix = $modelProcurement->docApprovalMatrix()
                                                ->from('doc_approval_matrix')
                                                ->select('matrix_as')
                                                ->where('flow_rule_group_id', $request->ruleId)
                                                ->where('employee_id', $employeeId)
                                                ->whereNotIn('matrix_as', ['APPLICANT','APPROVER'])
                                                ->whereNotNull('employee_id_approval')
                                                ->where('is_active', '1')
                                                ->orderBy('matrix_order', 'asc')
                                                ->get();
                    foreach ($getMatrix as $rowMatrix) {
                        if($rowMatrix->matrix_as == 'REVIEWER' && !$request->reviewer) {
                            $validateRules["reviewer"] = 'required|string|max:50';
                            $validateMessages["reviewer.required"] = 'Reviewer is required';
                        }
                        else if($rowMatrix->matrix_as == 'CONFIRMER_1' && !$request->confirmer_1) {
                            $validateRules["confirmer_1"] = 'required|string|max:50';
                            $validateMessages["confirmer_1.required"] = 'Confirmer is required';
                        }
                        else if($rowMatrix->matrix_as == 'REVIEW_ADMIN' && !$request->review_admin) {
                            $validateRules["review_admin"] = 'required|string|max:50';
                            $validateMessages["review_admin.required"] = 'Review admin is required';
                        }
                        else if($rowMatrix->matrix_as == 'CONFIRMER_2' && !$request->confirmer_2) {
                            $validateRules["confirmer_2"] = 'required|string|max:50';
                            $validateMessages["confirmer_2.required"] = 'Confirmer is required';
                        }
                        else if($rowMatrix->matrix_as == 'ACKNOWLEDGER' && !$request->acknowledger) {
                            $validateRules["acknowledger"] = 'required|string|max:50';
                            $validateMessages["acknowledger.required"] = 'Acknowledger by is required';
                        }
                    }
                }

                $validatedData = $request->validate($validateRules, $validateMessages);

                $applicantComment = $request->input('applicantComment', null);
                $arrCostCenter = [];
                for($x = 0; $x < count($request->costCenterApplication); $x++){
                    if(!in_array($request->costCenterApplication[$x], $arrCostCenter)) {
                        $arrCostCenter[] = $request->costCenterApplication[$x];
                    }
                    else {
                        return response()->json([
                                        'message' => 'Cost center has been selected',
                                        'errors' => ['costCenterApplication.'.$x => ['Cost center has been selected']]
                                    ], 422)
                        ->setStatusCode(422, 'Cost center has been selected');
                    }
                }

                $countPercentage = 0;
                for($x = 0; $x < count($request->costCenterPercentApplication); $x++){
                    $countPercentage += $request->costCenterPercentApplication[$x];
                }

                for($x = 0; $x < count($request->reimburseApplicationPercentage); $x++){
                    $countPercentage += $request->reimburseApplicationPercentage[$x];
                }

                if($countPercentage != 100) {
                    return response()->json([
                                    'message' => 'Total percentage cost center = '.$countPercentage.' (not valid)',
                                    'errors' => ['costCenterPercentApplication.'.$x => ['Total percentage not valid']]
                                ], 422)
                    ->setStatusCode(422, 'Total percentage cost center not valid');
                }

                $termPaymentId = $request->input('termPayment', null);
                $datetimeAt = Carbon::now()->format('Y-m-d H:i:s');
                $documentApprovalController = new ControllersDocumentApprovalController();

                $docVersion = 1;
                $docApprovalIdOrderForm = $orderFormId = null;
                if($requestType == 'REVISE' || $requestType == 'OLD_COMPARISON_REVISE_APPLICATION' || $requestType == 'DELETE_COMPARISON_REVISE_APPLICATION') {
                    $getApplicationForm = $modelProcurement->docApprovalOrderApplication()
                                                        ->from('doc_approval_order_application as a')
                                                        ->leftJoin('doc_approval_order_group as b', function($join) {
                                                            $join->on('b.application_id', 'a.application_id')
                                                                ->where('b.is_active', '1')
                                                                ->orderBy('b.order_group_id', 'desc');
                                                        })
                                                        ->leftJoin('doc_approval_detail_order_form as c', function($join) {
                                                            $join->on('c.order_form_id', 'a.order_form_id');
                                                        })
                                                        ->select('a.doc_approval_id', 'a.application_id', 'a.order_form_id', 'a.doc_version', 'b.order_group_id', 'c.doc_approval_id as doc_approval_id_order_form')
                                                        ->where('a.company_id', $companyId)
                                                        ->where('a.application_id', $referenceId)
                                                        ->where('a.is_active', '1')
                                                        ->orderBy('a.application_id', 'desc')
                                                        ->first();
                    if($getApplicationForm) {
                        $docApprovalIdOrderForm = $getApplicationForm->doc_approval_id_order_form;

                        // GET LATEST ORDER FORM
                        $getOrderFormLatest = $modelProcurement->documentApprovalDetailOrder()
                                                            ->from('doc_approval_detail_order_form as a')
                                                            ->select('a.doc_approval_id', 'a.order_form_id')
                                                            ->where('a.company_id', $companyId)
                                                            ->where('a.doc_approval_id', $docApprovalIdOrderForm)
                                                            ->where('a.is_active', '1')
                                                            ->orderBy('a.order_form_id', 'desc')
                                                            ->first();
                        $orderFormId = $getOrderFormLatest->order_form_id;

                        $dataUpdate = [
                            'status' => '16', // NOT SUBMITTED
                            'received_at' => null,
                        ];
                        $model->docApprovalFlowSign()
                                ->where('company_id', $companyId)
                                ->where('doc_approval_id', $getApplicationForm->doc_approval_id)
                                ->where('reference_id', $getApplicationForm->application_id)
                                ->where('reference_group_id', $getApplicationForm->order_group_id)
                                ->whereIn('status', ['3','13']) // 3 = RECEIVED, 13 = PENDING
                                ->where('is_active', 1)
                                ->update($dataUpdate);

                        // INSERT SIGN FLOW REVISE
                        $dataInsert = [
                            'company_id' => $companyId,
                            'employee_id' => $employeeId,
                            'flow_as' => 'APPLICANT',
                            'status' => '6',
                            'doc_approval_id' => $getApplicationForm->doc_approval_id,
                            'doc_type_id' => $request->documentType,
                            'reference_id' => $getApplicationForm->application_id,
                            'reference_group_id' => $getApplicationForm->order_group_id,
                            'received_at' => $datetimeAt,
                            'decision_at' => $datetimeAt,
                        ];
                        $insertFlowSign = $model->docApprovalFlowSign()->create($dataInsert);

                        // UPDATE FORM
                        $dataUpdate = [
                            'application_form_status' => '6',
                            'final_decision_at' => $datetimeAt,
                            'updated_by' => $employeeId,
                            'updated_at' => $datetimeAt,
                        ];

                        $modelApproval->docApprovalOrderApplication()
                                    ->where('application_id', $getApplicationForm->application_id)
                                    ->where('order_form_id', $getApplicationForm->order_form_id)
                                    // ->whereIn('application_form_status', ['2','5'])
                                    ->whereNotIn('application_form_status', ['1','6','9','14'])
                                    ->where('is_active', 1)
                                    ->update($dataUpdate);

                        $docVersion = $getApplicationForm->doc_version + 1;
                    }
                    else {
                        return response()->json(['message' => 'Application form not found', 'errors' => 'Application form not found'], 404)
                            ->setStatusCode(404, 'Application form not found');
                    }

                    $arrDocNumber = Str::of($request->seqNumber)->explode('-');
                    $seqNumber = $arrDocNumber[0];
                    $documentNumber = $request->seqNumber;
                    $seqId = $getOrderFormHeader->seq_id;
                }
                else {
                    $docApprovalIdOrderForm = $docApprovalId;
                    $applicationId = $token['d'];
                    $orderFormId = $referenceId;

                    // GET DOCUMENT NUMBER
                    $seqId = $seqNumber = $documentNumber = null;
                    if($request->actionType == 'NEW'){
                        $requestData = new Request();
                        $requestData->replace([
                                                'documentType' => $documentTypeId,
                                                'companyId' => SafeToken::encode(['c' => $getOrderFormHeader->company_id]),
                                                'seqNumber' => $request->input('seqNumber', null),
                                                'type' => 'INSERT_SEQ',
                                            ]);
                        $getDocumentNumber = $this->getDocumentNumber($requestData);
                        if($getDocumentNumber){
                            $seqId = $getDocumentNumber['seqId'];
                            $seqNumber = $getDocumentNumber['seqNumber'];
                            $documentNumber = $getDocumentNumber['documentNumber'];
                        }
                    }
                    // END GET DOCUMENT NUMBER

                }

                $year = $request->yearForm;
                $vendorAddress = $vendorPic = null;
                $getVendor = $modelProcurement->masterVendor()
                                                ->from('master_vendor')
                                                ->select('vendor_name')
                                                ->where('vendor_id', $request->vendorApplication)
                                                ->first();
                $vendorName = $getVendor->vendor_name;

                $getVendorAddress = $modelProcurement->masterVendorAddress()
                                                ->from('master_vendor_address as b')
                                                ->select('vendor_address', 'vendor_phone')
                                                ->where('vendor_id', $request->vendorApplication)
                                                ->where('vendor_address_id', $request->vendorAddressApplication)
                                                ->where('is_active', '1')
                                                ->first();
                if($getVendorAddress) {
                    $vendorAddress = $getVendorAddress->vendor_address;
                }

                $getVendorPic = $modelProcurement->masterVendorPic()
                                                ->from('master_vendor_pic as b')
                                                ->select('pic_name', 'pic_email')
                                                ->where('vendor_id', $request->vendorApplication)
                                                ->where('vendor_pic_id', $request->vendorPicApplication)
                                                ->where('is_active', '1')
                                                ->first();
                if($getVendorPic) {
                    $vendorPic = $getVendorPic->pic_name;
                }

                $getPurchaseType = $modelProcurement->masterPurchaseType()
                                                ->from('master_purchase_type')
                                                ->select('purchase_type')
                                                ->where('purchase_type_id', $request->purchaseTypeApplication)
                                                ->first();
                $purchaseType = $getPurchaseType->purchase_type;

                $deliveryApplication = '';
                $requestData = new Request();
                $requestData->replace([
                                        'search' => $request->deliveryTo,
                                        'data' => 'DETAIL_FORM',
                                        'type' => 'GOODS_DELIVERY',
                                    ]);
                $getDeliveryTo = $this->getDelivery($requestData);
                $getDeliveryTo = json_decode($getDeliveryTo->getContent(), true);
                if($getDeliveryTo){
                    $deliveryApplication = $getDeliveryTo[0]['deliveryName']."\r\n".Str::trim($request->deliveryApplication);
                }

                $invoiceApplication = '';
                $requestData = new Request();
                $requestData->replace([
                                        'search' => $request->invoiceTo,
                                        'data' => 'DETAIL_FORM',
                                        'type' => 'INVOICE',
                                    ]);
                $getInvoiceTo = $this->getDelivery($requestData);
                $getInvoiceTo = json_decode($getInvoiceTo->getContent(), true);
                if($getInvoiceTo){
                    $invoiceApplication = $getInvoiceTo[0]['companyName']."\r\n".$getInvoiceTo[0]['deliveryAddress'];
                }

                $submitDate = $request->input('submittedDateApplication', null);
                if($submitDate){
                    $submitDate = DateTime::createFromFormat('d-m-Y', $submitDate);
                    $submitDate = $submitDate->format('Y-m-d');
                }

                $shipDateApplication = $request->input('shipDateApplication', null);
                if($shipDateApplication){
                    $shipDateApplication = DateTime::createFromFormat('d-m-Y', $shipDateApplication);
                    $shipDateApplication = $shipDateApplication->format('Y-m-d');
                }

                $applicationEstimate = str_replace(',', '', $request->input('estimateApplication', '0'));
                $applicationEstimate = (float)$applicationEstimate;

                $grandTotal = str_replace(',', '', $request->input('grandTotal', '0'));
                $grandTotal = (float)$grandTotal;

                $getPriority = $modelApproval->masterPriorityLevel()
                                            ->select('priority_name')
                                            ->where('priority_id', '=', $request->input('priorityApplication', '3')) // 3 = NORMAL
                                            ->first();

                if($requestType == 'REVISE' || $requestType == 'OLD_COMPARISON_REVISE_APPLICATION' || $requestType == 'DELETE_COMPARISON_REVISE_APPLICATION') {
                    $docApprovalIdApplication = $docApprovalId;
                    $dataUpdate = [
                        'employee_id' => $employeeId,
                        'doc_number' => $documentNumber,
                        'doc_status_id' => '2',
                        'form_status_id' => '2',
                        'is_completed' => '0',
                        'final_decision_at' => null,
                        'submitted_at' => $datetimeAt,
                        'location_id' => null,
                        'priority' => $request->input('priorityApplication', '3'), // 3 = NORMAL
                        'priority_name' => $getPriority->priority_name,
                    ];
                    $updateAction = $model->documentApprovalHeader()
                                            ->where('doc_approval_id', $docApprovalId)
                                            ->where('is_active', 1)
                                            ->update($dataUpdate);
                }
                else {
                    $dataInsert = [
                        'company_id' => $companyId,
                        'company_name' => $companyName,
                        'employee_id' => $employeeId,
                        'doc_type_id' => $documentTypeId,
                        'doc_name' => $getDocumentType->doc_name,
                        'year' => $year,
                        'doc_number' => $documentNumber,
                        'seq_id' => $seqId,
                        'doc_status_id' => '2',
                        'final_decision_at' => null,
                        'form_status_id' => '2',
                        'department_id' => $getOrderFormHeader->department_id,
                        'department_name' => $getOrderFormHeader->department_name,
                        'keywords' => null,
                        'submitted_at' => $datetimeAt,
                        'is_completed' => '0',
                        'priority' => $request->input('priorityApplication', '3'), // 3 = NORMAL
                        'priority_name' => $getPriority->priority_name,
                    ];
                    $insertApproval = $modelProcurement->documentApprovalHeader()->create($dataInsert);
                    $docApprovalIdApplication = $insertApproval->id;
                }

                // $folderName = Str::squish(Str::replace('&', ' ', Str::upper($getOrderFormHeader->department_name.' '.$getOrderFormHeader->seq_number)));
                // $documentName = Str::squish(Str::replace('&', ' ', Str::upper($getDocumentType->doc_name)));
                // $mainDirectory = "private/doc_approval";
                // $pathDetail = "/{$year}/purchasing/{$myData->company_code}/{$folderName}/";
                // $formFilename = Str::squish(Str::replace('&', ' ', Str::upper($documentName.' '.$getOrderFormHeader->department_name.' '.$getOrderFormHeader->seq_number.' '.$docVersion.' '.uniqid())));

                $departmentFolder = sanitizeString($getOrderFormHeader->department_name);
                $documentNameExplode = Str::of($getOrderFormHeader->doc_number)->explode('-');
                $folderName = $documentNameExplode[2];
                $documentName = Str::upper(sanitizeString($getDocumentType->doc_name));
                $mainDirectory = "private/doc_approval";
                $pathDetail = "/{$year}/purchasing/{$companyId}/{$departmentFolder}/{$folderName}/";
                $formFilename = Str::upper(Str::replace('/', '-', $documentNumber));

                $dataInsert = [
                    'doc_approval_id' => $docApprovalIdApplication,
                    'company_id' => $companyId,
                    'order_form_id' => $orderFormId,
                    'comparison_id' => $comparisonId,
                    'application_form_status' => '2',
                    'doc_version' => $docVersion,
                    'application_header' => $request->applicationHeader,
                    'year' => $year,
                    'application_number' => $documentNumber,
                    'application_title' => Str::squish($request->titleApplication),
                    'application_estimate' => $grandTotal,
                    'application_submitted_date' => $submitDate,
                    'application_currency' => $request->currencyApplication,
                    'vendor_id' => $request->vendorApplication,
                    'vendor_name' => Str::squish($vendorName),
                    'vendor_pic_id' => $request->vendorPicApplication,
                    'vendor_pic' => $vendorPic,
                    'vendor_address_id' => $request->vendorAddressApplication,
                    'vendor_address' => $vendorAddress,
                    'delivery_to_id' => $request->deliveryTo,
                    'application_delivery' => $deliveryApplication,
                    'invoice_to_id' => $request->invoiceTo,
                    'application_invoice' => $invoiceApplication,
                    'application_grand_total' => $grandTotal,
                    'purchase_type_id' => $request->purchaseTypeApplication,
                    'purchase_type' => $purchaseType,
                    'form_type' => $request->formTypeApplication,
                    // 'budget_no' => $request->budgetNoApplication,
                    // 'budget_amount' => $budgetAmountApplication,
                    // 'budget_within_over' => $request->budgetWithinInput,
                    // 'budget_within' => $budgetWithin,
                    // 'reimburse_to' => $request->input('reimburseApplication1', null),
                    // 'reimburse_to_next' => $request->input('reimburseApplication2', null),
                    'payment_type' => $request->paymentTypeApplication,
                    'ship_date' => $shipDateApplication,
                    'application_remark' => $request->input('remarkApplication', null),
                    'application_reason' => $request->input('reasonApplication', null),
                    'path_detail' => $pathDetail,
                    'filename' => $formFilename,
                    'created_by' => $employeeId,
                    'created_at' => $datetimeAt,
                    'priority' => $request->input('priorityApplication', '3'), // 3 = NORMAL
                    'priority_name' => $getPriority->priority_name,
                    'term_payment_id' => $termPaymentId,
                ];
                $insertHeader = $modelProcurement->docApprovalOrderApplication()->create($dataInsert);
                $applicationId = $insertHeader->id;

                $arrBudget = [];
                if($request->budgetApplication[0] != '') {
                    foreach($request->budgetApplication as $index => $rowBudget) {
                        $budgetAmount = str_replace(',', '', $request->budgetAmountApplication[$index]);
                        $budgetAmount = str_replace(')', '', $budgetAmount);
                        $budgetAmount = str_replace('(', '-', $budgetAmount);

                        $budgetRemaining = str_replace(',', '', $request->budgetWithinApplication[$index]);
                        $budgetRemaining = str_replace(')', '', $budgetRemaining);
                        $budgetRemaining = str_replace('(', '-', $budgetRemaining);

                        $arrBudget[] = [
                            'budgetNo' => $rowBudget,
                            'budgetAmount' => $budgetAmount,
                            'budgetWithinOver' => $request->budgetWithinInput[$index],
                            'budgetRemaining' => $budgetRemaining,
                        ];

                        $dataInsert = [
                            'application_id' => $applicationId,
                            'company_id' => $companyId,
                            'budget_no' => $rowBudget,
                            'budget_amount' => ($budgetAmount == '') ? '0' : $budgetAmount,
                            'within_over' => $request->budgetWithinInput[$index],
                            'budget_remaining' => ($budgetRemaining == '') ? '0' : $budgetRemaining,
                            'is_active' => '1'
                        ];
                        $modelProcurement->docApprovalOrderApplicationBudget()->create($dataInsert);
                    }
                }

                $arrCostCenter = [];
                for($x = 0; $x < count($request->costCenterApplication); $x++){
                    $costCenter = $request->costCenterApplication[$x];
                    $arrCostCenter[] = [
                        'code' => $costCenter,
                        'percentage' => $request->costCenterPercentApplication[$x],
                    ];

                    $dataInsert = [
                        'application_id' => $applicationId,
                        'company_id' => $companyId,
                        'order_form_id' => $orderFormId,
                        'cost_center_id' => $request->costCenterApplication[$x],
                        'cost_center' => $costCenter,
                        'percentage' => $request->costCenterPercentApplication[$x],
                        'is_active' => '1'
                    ];
                    $modelProcurement->docApprovalOrderApplicationCostCenter()->create($dataInsert);
                }

                $arrReimburse = [];
                if($request->reimburseApplication[0] != '') {
                    foreach($request->reimburseApplication as $index => $rowReimburse) {
                        $arrReimburse[] = [
                            'reimburseTo' => $rowReimburse,
                            'reimbursePercentage' => $request->reimburseApplicationPercentage[$index],
                        ];

                        $dataInsert = [
                            'application_id' => $applicationId,
                            'company_id' => $companyId,
                            'reimburse_to' => $rowReimburse,
                            'percentage' => $request->reimburseApplicationPercentage[$index],
                            'is_active' => '1'
                        ];
                        $modelProcurement->docApprovalOrderApplicationReimburse()->create($dataInsert);
                    }
                }

                // $createOrderGroup = true;
                $getOrderGroup = $modelProcurement->documentApprovalOrderGroup()
                                                ->select('order_group_id', 'comparison_id', 'application_id')
                                                ->where('company_id', $companyId)
                                                ->where('order_form_id', $orderFormId)
                                                ->where('comparison_id', $comparisonId)
                                                ->where('is_active', '1')
                                                ->where('is_need_revision', '0')
                                                ->where('is_canceled', '0')
                                                ->where('is_rejected', '0')
                                                ->orderBy('order_group_id', 'desc')
                                                ->first();
                if($getOrderGroup) {
                    // if($getOrderGroup->comparison_id == $comparisonId) {
                        // $createOrderGroup = false;
                        $orderGroupId = $getOrderGroup->order_group_id;
                        $dataUpdate = [
                            'application_id' => $applicationId,
                            'is_need_revision' => '0',
                            'is_canceled' => '0',
                            'is_rejected' => '0',
                        ];

                        $modelProcurement->documentApprovalOrderGroup()
                                        ->where('order_form_id', $orderFormId)
                                        ->where('order_group_id', $orderGroupId)
                                        ->where('is_active', '1')
                                        ->update($dataUpdate);
                    // }
                }
                else {
                    // CREATE ORDER GROUP
                    $dataInsert = [
                        'company_id' => $companyId,
                        'order_form_id' => $referenceId,
                        'application_id' => $applicationId,
                        'is_need_revision' => '0',
                        'is_canceled' => '0',
                        'is_rejected' => '0',
                    ];
                    $insertOrderGroup = $modelProcurement->documentApprovalOrderGroup()->create($dataInsert);
                    $orderGroupId = $insertOrderGroup->id;
                    // END CREATE ORDER GROUP
                }

                $arrItem = [];
                for($x = 0; $x < count($request->colCriteria); $x++){
                    // if($request->colCriteria[$x] == 'Delivery Fee') {
                    //     $subTotalPrice = str_replace(',', '', $request->input("deliveryFeeTotal.0", '0'));
                    // }
                    // else if($request->colCriteria[$x] == 'Discount') {
                    //     $subTotalPrice = str_replace(',', '', $request->input("discountTotal.0", '0'));
                    // }
                    // else if($request->colCriteria[$x] == 'Total Price') {
                    //     $subTotalPrice = str_replace(',', '', $request->input("totalVendor.0", '0'));
                    // }
                    // else if($request->colCriteria[$x] == 'VAT') {
                    //     $subTotalPrice = str_replace(',', '', $request->input("vat.0", '0'));
                    // }
                    // else {
                    //     $subTotalPrice = str_replace(',', '', $request->input("subTotalPrice.$x", '0'));
                    // }

                    $dppOtherValue = null;
                    $subTotalPrice = str_replace(',', '', $request->input("subTotalPrice.$x", '0'));
                    $subTotalPrice = str_replace('(', '', $subTotalPrice);
                    $subTotalPrice = str_replace(')', '', $subTotalPrice);

                    if($request->colCriteria[$x] == 'Total Price' || ($request->colCriteriaId[$x] == '' && ($subTotalPrice == '' || $subTotalPrice == '0' || $subTotalPrice == null || $subTotalPrice == '0.00'))) {
                        continue;
                    }

                    $orderDetailId = $request->input("colCriteriaId.$x", null);

                    $unitPrice = str_replace(',', '', $request->input("unitPrice1.$x", '0'));
                    if($unitPrice == '12x11/12') {
                        $unitPrice = 12;
                        $dppOtherValue = '11/12';
                    }
                    else {
                        $unitPrice = (float)$unitPrice;
                    }

                    $subTotalPrice = (float)$subTotalPrice;

                    $dataInsert = [
                        'order_form_id' => $orderFormId,
                        'company_id' => $companyId,
                        'application_id' => $applicationId,
                        'order_detail_id' => $orderDetailId,
                        'appliance_item' => $request->colCriteria[$x],
                        'quantity' => $request->input("colQty.$x", null),
                        'currency' => $request->currency1[$x],
                        'unit_price' => $unitPrice,
                        'dpp_other_value' => $dppOtherValue,
                        'subtotal_price' => $subTotalPrice,
                    ];
                    $modelProcurement->docApprovalOrderApplicationItem()->create($dataInsert);

                    if($request->colCriteria[$x] != 'Delivery Fee' && $request->colCriteria[$x] != 'Discount' && $request->colCriteria[$x] != 'Total Price' && $request->colCriteria[$x] != 'VAT' && $request->colCriteria[$x] != 'PPh' && $request->colCriteria[$x] != 'Total Price + VAT - PPh'){
                        $itemNo = $x + 1;
                    }
                    else {
                        $itemNo = '';
                    }
                    $arrItem[] = [
                        'itemNo' =>  $itemNo,
                        'applianceItem' =>  $request->colCriteria[$x],
                        'quantity' => $request->input("colQty.$x", ''),
                        'unitPrice' => $unitPrice,
                        'dppOtherValue' => $dppOtherValue ?: '',
                        'currencySymbol' => $request->currency1[0],
                        'subtotalPrice' => $subTotalPrice,
                    ];

                    if($orderDetailId != null) {
                        $dataUpdate = [
                            'order_group_id' => $orderGroupId,
                            'application_id' => $applicationId,
                            'vendor_id' => $request->vendorApplication,
                        ];
                        $modelProcurement->documentApprovalDetailOrderItem()
                                ->where('doc_approval_id', $docApprovalIdOrderForm)
                                ->where('order_form_id', $orderFormId)
                                ->where('order_detail_id', $orderDetailId)
                                ->where('is_active', 1)
                                ->whereNull('canceled_id')
                                ->update($dataUpdate);

                        // if($orderGroupId != null) {
                        //     $getOrderGroup = $modelProcurement->documentApprovalOrderGroup()
                        //                                 ->select('order_group_id')
                        //                                 ->where('company_id', $companyId)
                        //                                 ->where('order_form_id', $orderFormId)
                        //                                 ->where('is_active', '1')
                        //                                 ->orderBy('order_group_id', 'desc')
                        //                                 ->first();
                        //     if($getOrderGroup) {
                        //         $orderGroupId = $getOrderGroup->order_group_id;
                        //     }
                        // }
                    }

                    if($x == count($request->colCriteria) - 1) {
                        $countNonCompleted = $modelApproval->documentApprovalDetailOrderItem()
                                                        ->from('doc_approval_detail_order_form_item')
                                                        ->select('application_id')
                                                        ->where('doc_approval_id', $docApprovalIdOrderForm)
                                                        ->where('order_form_id', $orderFormId)
                                                        ->where('unit_name', '<>', 'VAT')
                                                        ->whereNull('application_id')
                                                        ->whereNull('canceled_id')
                                                        ->where('is_rejected', '0')
                                                        ->where('is_active', '1')
                                                        ->count();
                        if($countNonCompleted == 0) {
                            $dataUpdate = [
                                            'item_completed' => '1',
                                        ];
                            $modelProcurement->documentApprovalDetailOrder()
                                        ->where('doc_approval_id', $docApprovalIdOrderForm)
                                        ->where('order_form_id', $orderFormId)
                                        ->where('is_active', 1)
                                        ->update($dataUpdate);
                        }
                    }
                }

                $year = $request->yearForm;
                if ($request->hasFile('attachmentFile')) {
                    foreach ($request->file('attachmentFile') as $file) {
                        // $mimeType = $file->getMimeType();
                        $filenameOriginal = DecodeModSecurityPlaceholders::decodeFilenameStatic($file->getClientOriginalName());
                        $mimeType = $this->getMimeTypeByExtension($filenameOriginal);
                        // $filenameOriginal = $file->getClientOriginalName();

                        // $filenameOriginal = preg_replace('/[^a-zA-Z0-9_\-\. \p{Hiragana}\p{Katakana}\p{Han}]/u', '_', $filenameOriginal);
                        // $filenameOriginal = substr($filenameOriginal, 0, 100);

                        $filename = Str::squish(Str::replace('&', ' ', Str::upper($documentName.' '.$getOrderFormHeader->department_name.' '.$getOrderFormHeader->seq_number.' '.$docVersion.' ATTACHMENT '.uniqid()))).'.'.$file->getClientOriginalExtension();
                        $file->storeAs($mainDirectory.$pathDetail, $filename, 'local');

                        $converted = str_contains($mimeType, 'pdf') ? null : 0;
                        // if (str_contains($mimeType, 'excel') || str_contains($mimeType, 'spreadsheetml') || str_contains($mimeType, 'wordprocessingml')) {
                        //     $converted = '0';
                        // }

                        $dataInsert = [
                            'doc_approval_id' => $docApprovalIdApplication,
                            'company_id' => $companyId,
                            'doc_type_id' => $request->documentType,
                            'reference_id' => $applicationId,
                            'path_detail' => $pathDetail,
                            'filename' => $filename,
                            'filename_original' => $filenameOriginal,
                            'converted' => $converted,
                            'mime_type' => $mimeType,
                            'downloadable' => '1',
                        ];
                        $insertAttachment = $modelApproval->docApprovalAttachment()->create($dataInsert);

                        // if (str_contains($mimeType, 'excel') || str_contains($mimeType, 'spreadsheetml') || str_contains($mimeType, 'wordprocessingml')) {
                        //     ConvertFileJob::dispatch($insertAttachment->id);
                        // }
                        if (!str_contains($mimeType, 'pdf')) {
                            ConvertFileJob::dispatch($insertAttachment->id);
                        }
                    }
                }

                $getEmployee = $model->vwMasterEmployeeActive()
                                    ->from('vw_master_employee_active as a')
                                    ->select('a.employee_id', 'a.employee_name', 'a.position_name', 'a.company_id', 'a.position_name')
                                    ->where('a.employee_id', '=', $request->applicant)
                                    ->first();
                $arrSigner[] = [
                    'signerId' => null,
                    'flowAs' => 'APPLICANT',
                    'company_id' => $getEmployee->company_id,
                    'employeeId' => $getEmployee->employee_id,
                    'employeeName' => Str::upper($getEmployee->employee_name),
                    'positionName' => $getEmployee->position_name,
                    'status' => '2',
                    'receivedAt' => $datetimeAt,
                    'decisionAt' => $datetimeAt,
                    'order' => '1',
                ];

                if($request->reviewer) {
                    $getEmployee = $model->vwMasterEmployeeActive()
                                    ->from('vw_master_employee_active as a')
                                    ->select('a.employee_id', 'a.employee_name', 'a.position_name', 'a.company_id')
                                    ->where('a.employee_id', '=', $request->reviewer)
                                    ->first();
                    $arrSigner[] = [
                        'flowAs' => 'REVIEWER',
                        'company_id' => $getEmployee->company_id,
                        'employeeId' => $getEmployee->employee_id,
                        'signerId' => null,
                        'employeeName' => Str::upper($getEmployee->employee_name),
                        'positionName' => $getEmployee->position_name,
                        'status' => '3', // 16 = NOT SUBMITTED, 3 = RECEIVED
                        'receivedAt' => $datetimeAt,
                        'decisionAt' => null,
                        'order' => '2',
                    ];
                }
                else {
                    $arrSigner[] = [
                        'flowAs' => 'REVIEWER',
                        'company_id' => null,
                        'employeeId' => null,
                        'signerId' => null,
                        'employeeName' => null,
                        'positionName' => null,
                        'status' => '16', // 16 = NOT SUBMITTED, 3 = RECEIVED
                        'receivedAt' => null,
                        'decisionAt' => null,
                        'order' => '2',
                    ];
                }

                if($request->confirmer_1) {
                    $status = '3';
                    if($request->reviewer) {
                        $status = '13';  // 3 = RECEIVED, 13 = PENDING
                    }

                    $getEmployee = $model->vwMasterEmployeeActive()
                                    ->from('vw_master_employee_active as a')
                                    ->select('a.employee_id', 'a.employee_name', 'a.position_name', 'a.company_id')
                                    ->where('a.employee_id', '=', $request->confirmer_1)
                                    ->first();
                    $arrSigner[] = [
                        'flowAs' => 'CONFIRMER_1',
                        'company_id' => $getEmployee->company_id,
                        'employeeId' => $getEmployee->employee_id,
                        'signerId' => null,
                        'employeeName' => Str::upper($getEmployee->employee_name),
                        'positionName' => $getEmployee->position_name,
                        'status' => $status,
                        'receivedAt' => $status == '3' ? $datetimeAt : null,
                        'decisionAt' => null,
                        'order' => '3',
                    ];
                }
                else {
                    $arrSigner[] = [
                        'flowAs' => 'CONFIRMER_1',
                        'company_id' => null,
                        'employeeId' => null,
                        'signerId' => null,
                        'employeeName' => null,
                        'positionName' => null,
                        'status' => '16', // 16 = NOT SUBMITTED, 3 = RECEIVED
                        'receivedAt' => null,
                        'decisionAt' => null,
                        'order' => '3',
                    ];
                }

                if($request->review_admin) {
                    $status = '3';
                    if($request->reviewer || $request->confirmer_1 || $request->confirmer_2) {
                        $status = '13';  // 3 = RECEIVED, 13 = PENDING
                    }

                    $getEmployee = $model->vwMasterEmployeeActive()
                                    ->from('vw_master_employee_active as a')
                                    ->select('a.employee_id', 'a.employee_name', 'a.position_name', 'a.company_id')
                                    ->where('a.employee_id', '=', $request->review_admin)
                                    ->first();
                    $arrSigner[] = [
                        'flowAs' => 'REVIEW_ADMIN',
                        'company_id' => $getEmployee->company_id,
                        'employeeId' => $getEmployee->employee_id,
                        'signerId' => null,
                        'employeeName' => Str::upper($getEmployee->employee_name),
                        'positionName' => $getEmployee->position_name,
                        'status' => $status,
                        'receivedAt' => $status == '3' ? $datetimeAt : null,
                        'decisionAt' => null,
                        'order' => '4',
                    ];
                }
                else {
                    $arrSigner[] = [
                        'flowAs' => 'REVIEW_ADMIN',
                        'company_id' => null,
                        'employeeId' => null,
                        'signerId' => null,
                        'employeeName' => null,
                        'positionName' => null,
                        'status' => '16', // 16 = NOT SUBMITTED, 3 = RECEIVED
                        'receivedAt' => null,
                        'decisionAt' => null,
                        'order' => '4',
                    ];
                }

                if($request->confirmer_2) {
                    $status = '3';
                    if($request->reviewer || $request->confirmer_1 || $request->review_admin) {
                        $status = '13';  // 3 = RECEIVED, 13 = PENDING
                    }

                    $getEmployee = $model->vwMasterEmployeeActive()
                                    ->from('vw_master_employee_active as a')
                                    ->select('a.employee_id', 'a.employee_name', 'a.position_name', 'a.company_id')
                                    ->where('a.employee_id', '=', $request->confirmer_2)
                                    ->first();
                    $arrSigner[] = [
                        'flowAs' => 'CONFIRMER_2',
                        'company_id' => $getEmployee->company_id,
                        'employeeId' => $getEmployee->employee_id,
                        'signerId' => null,
                        'employeeName' => Str::upper($getEmployee->employee_name),
                        'positionName' => $getEmployee->position_name,
                        'status' => $status,
                        'receivedAt' => $status == '3' ? $datetimeAt : null,
                        'decisionAt' => null,
                        'order' => '5',
                    ];
                }
                else {
                    $arrSigner[] = [
                        'flowAs' => 'CONFIRMER_2',
                        'company_id' => null,
                        'employeeId' => null,
                        'signerId' => null,
                        'employeeName' => null,
                        'positionName' => null,
                        'status' => '16', // 16 = NOT SUBMITTED, 3 = RECEIVED
                        'receivedAt' => null,
                        'decisionAt' => null,
                        'order' => '5',
                    ];
                }

                if($request->acknowledger) {
                    $status = '3';
                    if($request->reviewer || $request->confirmer_1 || $request->review_admin || $request->confirmer_2) {
                        $status = '13';  // 3 = RECEIVED, 13 = PENDING
                    }

                    $getEmployee = $model->vwMasterEmployeeActive()
                                    ->from('vw_master_employee_active as a')
                                    ->select('a.employee_id', 'a.employee_name', 'a.position_name', 'a.company_id')
                                    ->where('a.employee_id', '=', $request->acknowledger)
                                    ->first();
                    $arrSigner[] = [
                        'flowAs' => 'ACKNOWLEDGER',
                        'company_id' => $getEmployee->company_id,
                        'employeeId' => $getEmployee->employee_id,
                        'signerId' => null,
                        'employeeName' => Str::upper($getEmployee->employee_name),
                        'positionName' => $getEmployee->position_name,
                        'status' => $status,
                        'receivedAt' => $status == '3' ? $datetimeAt : null,
                        'decisionAt' => null,
                        'order' => '6',
                    ];
                }
                else {
                    $arrSigner[] = [
                        'flowAs' => 'ACKNOWLEDGER',
                        'company_id' => null,
                        'employeeId' => null,
                        'signerId' => null,
                        'employeeName' => null,
                        'positionName' => null,
                        'status' => '16', // 16 = NOT SUBMITTED, 3 = RECEIVED
                        'receivedAt' => null,
                        'decisionAt' => null,
                        'order' => '6',
                    ];
                }

                $status = '3';
                if($request->reviewer || $request->confirmer_1 || $request->review_admin || $request->confirmer_2 || $request->acknowledger) {
                    $status = '13';  // 3 = RECEIVED, 13 = PENDING
                }

                $getEmployee = $model->vwMasterEmployeeActive()
                                ->from('vw_master_employee_active as a')
                                ->select('a.employee_id', 'a.employee_name', 'a.position_name', 'a.company_id')
                                ->where('a.employee_id', '=', $request->approver)
                                ->first();
                $arrSigner[] = [
                    'signerId' => null,
                    'flowAs' => 'APPROVER',
                    'company_id' => $getEmployee->company_id,
                    'employeeId' => $getEmployee->employee_id,
                    'employeeName' => Str::upper($getEmployee->employee_name),
                    'positionName' => $getEmployee->position_name,
                    'status' => $status,
                    'receivedAt' => $status == '3' ? $datetimeAt : null,
                    'decisionAt' => null,
                    'order' => '7',
                ];
                $approverName = Str::upper($getEmployee->employee_name);
                $approverNamePosition = $getEmployee->position_name;

                $arrNotifications = [];
                for($x = 0; $x < count($arrSigner); $x++) {
                    if($arrSigner[$x]['employeeId']) {
                        $dataInsert = [
                            'doc_approval_id' => $docApprovalIdApplication,
                            'company_id' => $companyId,
                            'doc_type_id' => '2',
                            'reference_id' => $applicationId,
                            'reference_group_id' => $orderGroupId,
                            'employee_id' => $arrSigner[$x]['employeeId'],
                            'flow_as' => $arrSigner[$x]['flowAs'],
                            'received_at' => $arrSigner[$x]['receivedAt'],
                            'status' => $arrSigner[$x]['status'],
                            'decision_at' => $arrSigner[$x]['decisionAt'] ?? null,
                            'decision_source' => $arrSigner[$x]['decisionSource'] ?? null,
                            'order' => $arrSigner[$x]['order'] ?? null,
                        ];
                        $insertFlow = $modelProcurement->docApprovalFlowSign()->create($dataInsert);
                        $flowId = $insertFlow->id;

                        $arrSigner[$x]['signerId'] = $flowId;

                        if($arrSigner[$x]['flowAs'] == 'APPLICANT' && Str::trim($applicantComment)) {
                            $dataInsert = [
                                'sign_flow_id' => $flowId,
                                'company_id' => $companyId,
                                'reply_comment_id' => null,
                                'employee_id' => $arrSigner[$x]['employeeId'],
                                'comment' => Str::trim($applicantComment),
                                'comment_at' => $arrSigner[$x]['decisionAt'],
                                'is_reply' => 0,
                                'is_main' => 1,
                                'is_active' => 1,
                            ];
                            $model->docApprovalFlowComment()->create($dataInsert);
                        }

                        if($arrSigner[$x]['status'] == '3') {
                            $arrNotifications[$docApprovalIdApplication][] = [
                                                                                'docApprovalId' => $docApprovalIdApplication,
                                                                                'referenceId' => $applicationId,
                                                                                'signFlowId' => $flowId,
                                                                                'employeeId' => $arrSigner[$x]['employeeId'],
                                                                                'companyId' => $companyId,
                                                                                'flowAs' => $arrSigner[$x]['flowAs'],
                                                                                'notification' => 'NEED_APPROVAL'
                                                                            ];
                        }
                    }
                }

                $dataForm = collect([
                    'companyId' => $companyId,
                    'applicationHeader' => $request->applicationHeader,
                    'applicationNumber' => $documentNumber,
                    'applicationTitle' => Str::squish($request->titleApplication),
                    'applicationEstimate' => $grandTotal,
                    'applicationSubmittedDate' => $submitDate,
                    'vendorPicId' => Str::squish($request->vendorPicId),
                    'applicationVendorPic' => $vendorPic,
                    'applicationCurrency' => $request->currencyApplication,
                    'vendorName' => Str::squish($vendorName),
                    'vendorAddress' => $vendorAddress,
                    'applicationDelivery' => $deliveryApplication,
                    'applicationInvoice' => $invoiceApplication,
                    'applicationGrandTotal' => $grandTotal,
                    'purchaseType' => $purchaseType,
                    'formType' => $request->formTypeApplication,
                    'budget' => $arrBudget,
                    'reimburse' => $arrReimburse,
                    // 'budgetNo' => $request->budgetNoApplication,
                    // 'budgetAmount' => $budgetAmountApplication,
                    // 'budgetWithinOver' => $request->budgetWithinInput,
                    // 'budgetWithin' => $request->input('budgetWithinApplication', '-'),
                    // 'reimburseTo' => $request->input('reimburseApplication1', ''),
                    // 'reimburseToNext' => $request->input('reimburseApplication2', ''),
                    'paymentType' => $request->paymentTypeApplication,
                    'shipDate' => $shipDateApplication,
                    'applicationRemark' => $request->input('remarkApplication', ''),
                    'applicationReason' => $request->input('reasonApplication', ''),
                    'costCenter' => $arrCostCenter,
                    'itemForm' => $arrItem,
                    'signer' => $arrSigner,
                    'approverPo' => $approverName,
                    'approverPoPosition' => $approverNamePosition,
                    'filePath' => $pathDetail,
                    'filename' => $formFilename,
                    'submittedAt' => $datetimeAt,
                ]);

                // $dataForm = collect([
                //     'docNumber' => $documentNumber,
                //     'departmentName' => $departmentName,
                //     'locationName' => $getLocation->location_name,
                //     'createdDate' => Carbon::parse($dateTime)->format('d M Y'),
                //     'requestDate' => Carbon::parse($requestDate)->format('d M Y'),
                //     'currency' => $request->currencyOrder,
                //     'grandTotal' => number_format($grandTotalWithVat, 2, '.', ''),
                //     'itemForm' => $arrItem,
                //     'purpose' => $arrPurpose,
                //     'signer' => $arrSigner,
                //     'filePath' => $mainDirectory.$pathDetail.$formFilename,
                // ]);

                $requestData = new Request();
                $requestData->replace([
                                        'documentType' => $request->documentType,
                                        'dataForm' => compact('dataForm'),
                                    ]);
                $documentApprovalController->generatePdf($requestData);

                if(count($arrNotifications) > 0) {
                    $requestData = new Request();
                    $requestData->merge(['notifications' => $arrNotifications]);
                    $documentApprovalController->sendEmail($requestData);
                }

                $message = 'Successfully Submitted Application & PO Form';
                return response()->json(['message' => $message], 200);
            }
            // else if($request->documentType === '4') { // 4 = INSPECTION FORM
            //     $documentApprovalController = new ControllersDocumentApprovalController();
            //     $formId = $token['d'];
            //     $dataUpdate = [
            //         'inspection_form_status' => '2',
            //         'created_by' => $employeeId,
            //         'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
            //     ];

            //     $model->docApprovalOrderInspection()
            //             ->where('application_id', $referenceId)
            //             ->where('inspection_id', $formId)
            //             ->where('is_active', '1')
            //             ->update($dataUpdate);

            //     $arrNotifications[$docApprovalId][] = [
            //         'docApprovalId' => $docApprovalId,
            //         'referenceId' => $formId,
            //         'signFlowId' => null,
            //         'employeeId' => $getOrderFormHeader->employee_id,
            //         'companyId' => $companyId,
            //         'flowAs' => 'APPLICANT',
            //         'notification' => 'INSPECTION_FORM_SENT'
            //     ];
            //     $requestData = new Request();
            //     $requestData->merge(['notifications' => $arrNotifications]);
            //     $documentApprovalController->sendEmail($requestData);

            //     $message = 'Successfully Send Inspection Form';
            //     return response()->json(['message' => $message], 200);
            // }
        }
        catch (ValidationException $e) {
            return response()->json(['message' => 'Please fill the required form.', 'errors' => $e->errors()], 422)
                            ->setStatusCode(422, 'Please fill the required form.');
        }
    }

    public function poInspection(Request $request) {
        $employeeId = $this->employeeId;
        $companyId = SafeToken::decode($request->company);
        $companyId = $companyId['c'];

        $perPage = 100;
        $currentPage = $request->input('page', 1);
        $search = $request->input('search', null);
        $type = $request->input('type');
        $response = [];

        $countForm = null;
        $model = new DocumentApprovalModel();
        $getForm = $model->docApprovalOrderApplication()
                        ->from('doc_approval_order_application as a')
                        ->leftJoin('doc_approval_order_group as b', function($join) {
                            $join->on('b.application_id', '=', 'a.application_id')
                                ->where('b.is_active', '=', '1');
                        })
                        ->leftJoin('doc_approval_detail_order_form as c', function($join) {
                            $join->on('c.order_form_id', '=', 'b.order_form_id');
                        });

        if($type == 'PO_INSPECTION_RECEIVER') {
            $getForm = $getForm->leftJoin('doc_approval_matrix as d', function($join) {
                            $join->on('d.employee_id', '=', 'c.employee_id')
                                ->where('d.matrix_as', '=', 'INSPECTION_RECEIVER');
                        });
        }

        $getForm = $getForm->leftJoin('vw_master_employee_all as e', 'e.employee_id', '=', 'c.employee_id')
                        ->leftJoin('master_company as f', 'f.company_id', '=', 'a.company_id')
                        ->leftJoin('vw_master_department as g', 'g.department_id', '=', 'c.department_id')
                        ->leftJoin(DB::raw("(
                                SELECT application_id,
                                    GROUP_CONCAT(appliance_item ORDER BY application_item_id ASC SEPARATOR '## ') AS appliance_items
                                FROM doc_approval_order_application_item
                                WHERE is_active = 1 AND appliance_item NOT IN ('Delivery Fee', 'Discount', 'Total Price', 'VAT', 'PPh', 'Total Price + VAT - PPh')
                                GROUP BY application_id
                            ) as items"), 'items.application_id', '=', 'a.application_id')
                        ->select(
                                    'a.doc_approval_id',
                                    'a.application_id',
                                    'b.order_group_id',
                                    'c.order_form_id',
                                    'a.company_id',
                                    'f.company_name',
                                    'a.application_number',
                                    'a.application_title',
                                    'a.vendor_name',
                                    'a.priority_name',
                                    'g.department_name',
                                    'e.employee_name',
                                    'e.avatar',
                                    'a.open_order_at',
                                    DB::raw('IFNULL(items.appliance_items, "") as appliance_items')
                                )
                        ->where(function($query) use ($companyId) {
                            $query->whereNull('a.goods_received')
                                ->orWhere('a.goods_received', 'PARTIAL RECEIVED');
                        })
                        ->where('a.company_id', $companyId)
                        ->where('a.application_form_status', '24');

        if($type == 'PO_INSPECTION_RECEIVER') {
            $getForm = $getForm->where('d.employee_id_approval', $employeeId);
        }
        else {
            $getForm = $getForm->where('a.employee_id', $employeeId);
        }

        if($currentPage == 1) {
            $countForm = $getForm;
            $countForm = $countForm->count();
        }

        $getForm = $getForm->when($search, function ($query) use ($search) {
                            $query->where(function ($subQuery) use ($search) {
                                $subQuery->whereRaw('LOWER(a.application_number) LIKE ?', ["%{$search}%"])
                                    ->orWhereRaw('LOWER(a.vendor_name) LIKE ?', ["%{$search}%"])
                                    ->orWhereRaw('LOWER(items.appliance_items) LIKE ?', ["%{$search}%"])
                                    ->orWhereRaw('LOWER(e.employee_name) LIKE ?', ["%{$search}%"]);
                            });
                        });

        $getForm = $getForm->paginate($perPage, ['*'], 'page', $currentPage);
        foreach ($getForm as $rowForm) {
            $badgeStatus = '';
            // $badgeStatus = '<div class="float-end"><span class="badge bg-info fs-9 fw-semibold">OPEN ORDER</span></div>';
            $date = 'Open Order ' . $this->timeAgo($rowForm->open_order_at, false);

            $priority = '';
            if ($rowForm->priority_name == 'TOP URGENT' || $rowForm->priority_name == 'URGENT') {
                $priority = '<span class="badge bg-danger fs-9 fw-semibold badge-priority">' . $rowForm->priority_name . '</span>';
            }

             $avatar = '<div class="d-flex align-items-center">
                            <div class="avatar avatar-xs d-flex align-items-center me-1">
                                <img class="avatar-img" src="'.asset('storage/avatars/'.$rowForm->avatar).'" alt="">
                            </div>
                            <span class="avatar-name-card fs-8 fw-medium">'.Str::upper($rowForm->employee_name).'</span>
                        </div>';
            $bottomCard = '<div class="d-flex justify-content-between align-items-center">
                                '.$avatar.'
                                <div class="text-end">
                                    <small class="text-muted text-nowrap text-end">'.$date.'</small>
                                </div>
                            </div>';

            // Format appliance items dari hasil GROUP_CONCAT
            $itemOrder = '';
            $items = explode('## ', $rowForm->appliance_items);
            foreach ($items as $index => $value) {
                $itemOrder .= '<li>'.($index + 1).'. '.$value.'</li>';
            }

            $token = ['cid' => $companyId,'id' => $employeeId,'a' => $rowForm->doc_approval_id,'b' => $rowForm->application_id,'c' => null,'d' => null,'g'=>$rowForm->order_group_id];
            $encodedToken = SafeToken::encode($token);
            $response[] = [
                'html' => '<div class="card card-link-content mb-2-2" data-form="PO_INSPECTION" data-type="" data-token="'.$encodedToken.'">
                            <div class="card-header p-2-2 pb-0">
                                <div class="card-title m-0 fs-7">'.$rowForm->application_number.$badgeStatus.'</div>
                            </div>
                            <div class="card-body p-2-2 pt-1">
                                <div class="mb-1 data-details">
                                    <div class="data-row">
                                        <span class="data-label">Company</span>
                                        <span class="data-separator">:</span>
                                        <span class="data-value">'.$rowForm->company_name.'</span>
                                    </div>
                                    <div class="data-row">
                                        <span class="data-label">Department</span>
                                        <span class="data-separator">:</span>
                                        <span class="data-value">'.$rowForm->department_name.'</span>
                                    </div>
                                    <div class="data-row">
                                        <span class="data-label">Vendor</span>
                                        <span class="data-separator">:</span>
                                        <span class="data-value card_po_number">'.$rowForm->vendor_name.'</span>
                                    </div>
                                    <div class="data-row">
                                        <span class="data-label">Items</span>
                                        <span class="data-separator">:</span>
                                        <span class="data-value" style="line-height:1rem">'.$itemOrder.'</span>
                                    </div>
                                </div>
                                '.$bottomCard.'
                            </div>
                        </div>',
            ];
        }

        return response()->json([
            'status' => 200,
            'message' => 'success',
            'countPo' => $countForm,
            'data' => $response,
            'hasMorePages' => $getForm->hasMorePages() // Indicate if there are more pages to load
        ], 200);
    }

    public function purchasingHistory(Request $request) {
        $modelProcurement = new ProcurementPurchasingModel();
        $companyId = $request->company;

        // $employeeIdDefault = $request->applicantDefault;
        // if($employeeIdDefault !== 'ALL') {
        //     $decryptedEmployee = SafeToken::decode($request->applicantDefault);
        //     $employeeIdDefault = $decryptedEmployee['id'];
        // }

        $employeeId = $request->applicant;
        // if($employeeId !== 'ALL') {
        //     $decryptedEmployee = SafeToken::decode($request->applicant);
        //     $employeeId = $decryptedEmployee['id'];
        // }

        $arrEmployeeId = [];
        if($employeeId != $this->employeeId) {
            // CHECK USER SELECTION
            $checkSelectionRoles = $modelProcurement->masterRoleDetails()
                                        ->from('master_role_users as a')
                                        ->leftJoin('master_role_details as b', 'b.role_id', '=', 'a.role_id')
                                        ->select('b.ref_id')
                                        ->where('a.employee_id', $this->employeeId)
                                        ->where(function($query) {
                                            $query->whereNull('a.role_menu')
                                                ->orWhere('a.role_menu', '4');
                                        })
                                        ->where(function($query) {
                                            $query->whereNull('a.role_submenu')
                                                ->orWhere('a.role_submenu', '3');
                                        })
                                        ->where('a.role_type', 'SELECTION')
                                        ->where('a.role_action', '1')
                                        ->where('a.is_active', '1');

            if($employeeId == 'ALL') {
                $checkSelectionRoles = $checkSelectionRoles->get();
            }
            else {
                $checkSelectionRoles = $checkSelectionRoles->where('b.ref_id', $employeeId)
                                                        ->first();
            }

            if(empty($checkSelectionRoles)) {
                return response()->json([
                    'status' => 200,
                    'message' => 'Forbidden',
                    'recordsTotal' => 0,
                    'recordsFiltered' => 0,
                    'data' => [],
                    'disableOrdering' => false,
                ], 200);
            }
        }

        if($employeeId == 'ALL') {
            array_push($arrEmployeeId, $this->employeeId);
            foreach($checkSelectionRoles as $rowCheckSelectionRoles) {
                if(!in_array($rowCheckSelectionRoles->ref_id, $arrEmployeeId)) {
                    array_push($arrEmployeeId, $rowCheckSelectionRoles->ref_id);
                }
            }
        }

        $status = $request->input('status', 'ALL');
        $orderStartDate = $request->input('orderStartDate', '');
        $orderEndDate = $request->input('orderEndDate', '');
        $receivedStartDate = $request->input('receivedStartDate', '');
        $receivedEndDate = $request->input('receivedEndDate', '');
        $invStartDate = $request->input('invStartDate', '');
        $invEndDate = $request->input('invEndDate', '');
        $startAmount = $request->input('startAmount', '');
        $endAmount = $request->input('endAmount', '');
        $ccy = $request->input('ccy', 'IDR');
        $sortBy = $request->input('sortBy', 'LATEST');

        $query = $modelProcurement->docApprovalOrderApplication()
                                ->from('doc_approval_order_application as a')
                                // ->leftJoin('doc_approval_order_inspection as b', function($join) {
                                //     $join->on('b.application_id', 'a.application_id')
                                //         ->on('b.order_form_id', 'a.order_form_id');
                                // })
                                ->leftJoin('doc_approval_order_group as c', function($join) {
                                    $join->on('c.application_id', 'a.application_id');
                                })
                                ->leftJoin(DB::raw("(
                                    SELECT application_id, GROUP_CONCAT(invoice_number ORDER BY invoice_id ASC SEPARATOR ', ') AS all_invoice
                                    FROM doc_approval_order_invoice
                                    WHERE is_active = 1
                                    GROUP BY application_id
                                ) as invoice"), 'invoice.application_id', '=', 'a.application_id')
                                ->select(
                                    'a.company_id',
                                    'a.created_by',
                                    'a.application_id',
                                    'a.doc_approval_id',
                                    'a.order_form_id',
                                    'a.application_title as applicationTitle',
                                    'a.application_number as poNumber',
                                    'a.open_order_at as orderDate',
                                    'a.vendor_name as vendor',
                                    'a.application_grand_total as totalAmount',
                                    'a.application_form_status',
                                    'a.application_currency as currency',
                                    'a.invoice_number as invoiceNumber',
                                    'c.order_group_id',
                                    'c.comparison_id',
                                    'a.is_archived',
                                    'invoice.all_invoice')
                                ->where('a.is_active', '1');

        if($companyId != 'ALL') {
            $query->where('a.company_id', $companyId);
        }

        if($employeeId == 'ALL') {
            $query->whereIn('a.created_by', $arrEmployeeId);
        }
        else {
            $query->where('a.created_by', $employeeId);
        }

        if($status == 'ALL') {
            // $query->whereIn('a.application_form_status', ['22','9','12']);
            $query->where('a.is_archived', '1');
        }
        else {
            $query->where('a.application_form_status', $status)
                ->where('a.is_archived', '1');
        }

        if($orderStartDate != '' && $orderEndDate != '') {
            $query->whereDate('a.open_order_at', '>=', $orderStartDate)
                ->whereDate('a.open_order_at', '<=', $orderEndDate);
        }

        if($receivedStartDate != '' && $receivedEndDate != '') {
            $query->whereDate('a.received_order_at', '>=', $receivedStartDate)
                ->whereDate('a.received_order_at', '<=', $receivedEndDate);
        }

        if($invStartDate != '' && $invEndDate != '') {
            $query->whereDate('a.invoiced_order_at', '>=', $invStartDate)
                ->whereDate('a.invoiced_order_at', '<=', $invEndDate);
        }

        if($startAmount != '' && $endAmount != '') {
            $startAmount = Str::replace(',', '', $startAmount);
            $endAmount = Str::replace(',', '', $endAmount);
            $query->whereDate('a.application_grand_total', '>=', $startAmount)
                ->whereDate('a.application_grand_total', '<=', $endAmount)
                ->where('a.application_currency', $ccy);
        }

        if ($request->filled('search')) {
            $search = Str::squish($request->input('search'));
            $query->where(function($q) use ($search) {
                    $q->where('a.vendor_name', 'LIKE', "%{$search}%")
                        ->orWhere('a.application_title', 'LIKE', "%{$search}%")
                        ->orWhere('a.application_number', 'LIKE', "%{$search}%")
                        ->orWhere('a.invoice_number', 'LIKE', "%{$search}%")
                        ->orWhere('invoice.all_invoice', 'LIKE', "%{$search}%")
                        ->orWhere('a.application_remark', 'LIKE', "%{$search}%")
                        ->orWhere('a.application_reason', 'LIKE', "%{$search}%");
                });
        }

        if($sortBy == 'LATEST') {
            $query->latest('a.final_decision_at');
        }
        else if($sortBy == 'OLDEST') {
            $query->oldest('a.final_decision_at');
        }

        $query->latest('a.application_id');

        return DataTables::of($query)
        ->addIndexColumn()
        ->editColumn('orderDate', function($row) {
            return ($row->orderDate) ? Carbon::parse($row->orderDate)->format('d-M-Y') : '--';
        })
        ->editColumn('invoiceNumber', function($row) {
            $invoiceNumber = '--';
            if($row->invoiceNumber) {
                if($row->all_invoice) {
                    $invoiceNumber = $row->invoiceNumber.', '.$row->all_invoice;
                }
                else {
                    $invoiceNumber = $row->invoiceNumber;
                }
            }
            else if($row->all_invoice) {
                $invoiceNumber = $row->all_invoice;
            }

            return $invoiceNumber;
        })
        ->editColumn('totalAmount', function($row) {
            return $this->formatNumber($row->totalAmount).' '.$row->currency;
        })
        ->addColumn('action', function($row) {
            $token = ['cid' => $row->company_id,'id' => $row->created_by,'a' => $row->doc_approval_id,'b' => $row->application_id,'c' => null,'d' => null,'g'=>$row->order_group_id];
            $tokenForm = SafeToken::encode($token);

            $token = ['cid' => $row->company_id,'id' => $row->created_by,'a' => $row->doc_approval_id,'b' => $row->application_id, 'c' => $row->comparison_id, 'd'=> null, 'g'=>$row->order_group_id];
            $tokenAttachment = SafeToken::encode($token);

            $actionList = '<li><a class="dropdown-item" href="javascript:void(0);" onclick="actionTable('."'".$tokenForm."'".', '."'VIEW'".')"><i class="fa-regular fa-files me-2"></i> View PO Form</a></li>
            <li><a class="dropdown-item actionBtnHeader" href="javascript:void(0);" data-token="'.$tokenAttachment.'" data-form="APPLICATION_FORM" data-type="INVOICED" data-no="'.$row->poNumber.'" data-ccy="'.$row->currency.'"><i class="fa-regular fa-receipt me-2"></i> Add Invoice</a></li>
            <li><a class="dropdown-item actionBtnHeader" href="javascript:void(0);" data-token="'.$tokenAttachment.'" data-type="PRINT"><i class="fa-solid fa-print me-2"></i> Print Documents</a></li>
            <li><a class="dropdown-item actionBtnHeader" href="javascript:void(0);" data-token="'.$tokenAttachment.'" data-type="DOWNLOAD"><i class="fa-solid fa-arrow-down-to-line me-2"></i> Download Documents</a></li>';

            $action = '<div class="dropdown">
                                <button class="btn btn-sm btn-secondary dropdown-toggle" data-bs-boundary="viewport" id="dropdownMenuButton"
                                    type="button" data-coreui-toggle="dropdown"
                                    aria-expanded="false">Action</button>
                                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdownMenuButton1" style="">
                                    '.$actionList.'
                                </ul>
                            </div>';

            return $action;
        })
        ->editColumn('status', function($row) {
            $statuses = [
                '21' => '<i title="Goods Received" class="fa-solid fa-circle-check text-success fs-5"></i>',
                '22' => '<i title="Invoiced" class="fa-solid fa-circle-check text-success fs-5"></i>',
                '9' => '<i title="Rejected" class="fa-solid fa-circle-xmark text-danger fs-5"></i>',
                '12' => '<i title="Canceled" class="fa-solid fa-circle-xmark text-warning fs-5"></i>',
                '8' => '<i title="Canceled" class="fa-solid fa-circle-xmark text-warning fs-5"></i>',
                '24' => '<i title="Open Order" class="fa-solid fa-circle-check text-info fs-5"></i>',
            ];
            return $statuses[$row->application_form_status];
        })
        ->rawColumns(['action', 'status'])
        ->make(true);
    }

    public function exportData(Request $request) {
        $model = new ProcurementPurchasingModel();
        try {
            $validateRules = [
                'exportFormat' => 'required|in:xlsx,csv,pdf',
                'exportCompany' => 'required|string',
                'exportApplicant' => 'required|string',
                'exportPoStatus' => 'required|string',
                'exportSortBy' => 'required|string',
                'exportOrderDateStart' => 'nullable|date',
                'date-range-picker-end-date-exportOrderDate' => 'nullable|date',
                'exportReceivedDateStart' => 'nullable|date',
                'date-range-picker-end-date-exportReceivedDate' => 'nullable|date',
                'exportInvDateStart' => 'nullable|date',
                'date-range-picker-end-date-exportInvDate' => 'nullable|date',
            ];
            $validateMessages = [
                'exportFormat.required' => 'Format data is required',
                'exportCompany.required' => 'Company is required',
                'exportApplicant.required' => 'Applicant PO is required',
                'exportPoStatus.required' => 'PO Status is required',
                'exportSortBy.required' => 'Sort by is required',
            ];

            $request->validate($validateRules, $validateMessages);

            $data = null;
            $filters = $request->all();
            $fileName = 'procurement_purchases_' . now()->format('Ymd_His').'.'.$request->exportFormat;

            switch ($request->exportFormat) {
                case 'pdf':
                    // return Pdf::loadView('exports.procurement_purchases_pdf', [
                    //     'purchases' => (new ProcurementPurchaseExport($filters))->collection(),
                    //     'filters' => $filters
                    // ])->download($fileName);

                case 'csv':
                    return Excel::download(
                        new ProcurementPurchaseExport($filters),
                        $fileName,
                        \Maatwebsite\Excel\Excel::CSV,
                        [
                            'Content-Type' => 'text/csv', // Header penting untuk CSV
                        ]
                    );

                case 'xlsx':
                default:
                    return Excel::download(
                        new ProcurementPurchaseExport($filters),
                        $fileName,
                        \Maatwebsite\Excel\Excel::XLSX
                    );
            }
        }
        catch (ValidationException $e) {
            return response()->json(['message' => 'Please fill the required form.', 'errors' => $e->errors()], 422)
                            ->setStatusCode(422, 'Please fill the required form.');
        }
    }

    public function purchasingHistoryOld(Request $request) {
        $employeeId = $this->employeeId;
        $companyId = $this->companyId;

        $modelProcurement = new ProcurementPurchasingModel();
        $query = $modelProcurement->docApprovalOrderApplication()
                                ->from('doc_approval_order_application as a')
                                ->leftJoin('doc_approval_order_inspection as b', function($join) {
                                    $join->on('b.application_id', 'a.application_id')
                                        ->on('b.order_form_id', 'a.order_form_id');
                                })
                                ->leftJoin('doc_approval_order_group as c', function($join) {
                                    $join->on('c.application_id', 'a.application_id')
                                        ->where('b.is_active', '1');
                                })
                                ->select('a.application_id', 'a.doc_approval_id', 'a.order_form_id', 'a.application_number',
                                         'a.application_submitted_date', 'a.vendor_name', 'a.application_grand_total',
                                         'a.application_form_status', 'b.inspection_id', 'b.inspection_form_status',
                                         'a.application_currency', 'c.order_group_id')
                                ->where('a.application_form_status', '22')
                                ->where('a.company_id', $companyId)
                                ->where('a.is_active', '1')
                                ->orderBy('a.invoiced_order_at', 'desc');

        return DataTables::of($query)
            ->addColumn('checkbox', function($row){
                return '<input type="checkbox" class="row-checkbox" value="'.$row->application_id.'">';
            })
            ->addColumn('action', function($row){
                return '';  // Action column will be handled by context menu
            })
            ->editColumn('application_submitted_date', function($row) {
                return date('d M Y', strtotime($row->application_submitted_date));
            })
            ->editColumn('application_grand_total', function($row) {
                return number_format($row->application_grand_total, 2) . ' ' . $row->application_currency;
            })
            ->editColumn('application_form_status', function($row) {
                $statuses = [
                    '8' => '<span class="badge bg-success">Approved</span>',
                    '21' => '<span class="badge bg-warning">Pending</span>',
                    '22' => '<span class="badge bg-info">Processing</span>'
                ];
                return $statuses[$row->application_form_status] ?? $row->application_form_status;
            })
            ->rawColumns(['checkbox', 'action', 'application_form_status'])
            ->make(true);

    }

    public function purchasingVendorReference(Request $request) {
        $employeeId = $this->employeeId;
        $companyId = $this->companyId;
        $sortBy = $request->input('sortBy', 'VENDOR_NAME');
        $menu = $request->menu;

        $modelProcurement = new ProcurementPurchasingModel();
        $query = $modelProcurement->masterVendor()
                                ->from('master_vendor as a')
                                ->leftJoin('master_vendor_components as c', function($join) {
                                    $join->on('c.vendor_id', 'a.vendor_id')
                                        ->where('c.is_active', '1');
                                })
                                // ->leftJoin('master_vendor_purchase_type as d', function($join) {
                                //     $join->on('d.vendor_id', 'a.vendor_id')
                                //         ->where('d.is_active', '1');
                                // })
                                ->select(
                                    'a.vendor_id',
                                    'a.vendor_account',
                                    'a.vendor_name',
                                    'a.group',
                                    'a.currency',
                                    DB::raw('GROUP_CONCAT(DISTINCT c.component_name SEPARATOR ", ") as component_names')
                                    // DB::raw('GROUP_CONCAT(DISTINCT d.purchase_type_group_name SEPARATOR ", ") as purchase_type_names')
                                )
                                ->where(function($query) use ($companyId) {
                                    $query->whereNull('a.company_id')
                                        ->orWhere('a.company_id', $companyId);
                                })
                                ->where('a.is_active', '1');

        if ($request->filled('search')) {
            $search = Str::squish($request->input('search'));
            $query->leftJoin('master_vendor_pic as b', function($join) {
                    $join->on('b.vendor_id', 'a.vendor_id')
                        ->where('b.is_active', '1');
                })
                // ->leftJoin('master_vendor_site as e', function($join) {
                //     $join->on('e.vendor_id', 'a.vendor_id')
                //         ->where('e.is_active', '1');
                // })
                ->where(function($q) use ($search) {
                    $q->where('a.vendor_name', 'LIKE', "%{$search}%")
                        ->orWhere('a.vendor_account', 'LIKE', "%{$search}%")
                        ->orWhere('b.pic_name', 'LIKE', "%{$search}%")
                        ->orWhere('c.component_name', 'LIKE', "%{$search}%");
                });
        }

        $query->groupBy('a.vendor_id', 'a.vendor_name');
        if($sortBy == 'LATEST_CREATED') {
            $query = $query->orderBy('a.created_at', 'desc');
        }
        else if($sortBy == 'OLDEST_CREATED') {
            $query = $query->orderBy('a.created_at', 'asc');
        }
        if($sortBy == 'LATEST_UPDATED') {
            $query = $query->orderBy('a.updated_at', 'desc');
        }
        else if($sortBy == 'OLDEST_UPDATED') {
            $query = $query->orderBy('a.updated_at', 'asc');
        }
        else {
            $query = $query->orderBy('a.vendor_name', 'asc');
        }

        $dataTable = DataTables::of($query)
            ->editColumn('vendor_name', function($row) {
                return $row->vendor_name;
            })
            // ->editColumn('purchase_type_names', function($row) {
            //     return $row->purchase_type_names;
            // })
            ->editColumn('component_names', function($row) {
                return $row->component_names;
            });

            // Conditionally add checkbox column if menu exists
            if($menu == 'COMPARISON') {
                $dataTable->addColumn('checkbox', function($row) {
                    return '<input type="checkbox" class="align-middle row-checkbox rowVendorReference" value="'.$row->vendor_id.'" data-name="'.$row->vendor_name.'">';
                })
                ->rawColumns(['checkbox']);
            }
            if($menu == 'SETTINGS') {
                $dataTable->addIndexColumn()
                        ->addColumn('action', function($row) {
                            $token = ['id' => $row->vendor_id];
                            $tokenForm = SafeToken::encode($token);

                            $actionList = '<li><a class="dropdown-item actionBtnHeader" href="javascript:void(0);" data-token="'.$tokenForm.'" data-type="EDIT_VENDOR")">Edit Vendor</a></li>
                                            <li><a class="dropdown-item actionBtnHeader" href="javascript:void(0);" onclick="actionTable('."'".$tokenForm."'".', '."'DISABLE_VENDOR'".')">Disable Vendor</a></li>
                                            <li><a class="dropdown-item actionBtnHeader" href="javascript:void(0);" onclick="actionTable('."'".$tokenForm."'".', '."'DELETE_VENDOR'".')">Delete Vendor</a></li>';

                            $action = '<div class="dropdown">
                                                <button class="btn btn-sm btn-secondary dropdown-toggle" data-bs-boundary="viewport" id="dropdownMenuButton"
                                                    type="button" data-coreui-toggle="dropdown"
                                                    aria-expanded="false">Action</button>
                                                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdownMenuButton1" style="">
                                                    '.$actionList.'
                                                </ul>
                                            </div>';

                            return $action;
                        })
                        ->rawColumns(['action']);
            }

        return $dataTable->make(true);
    }

    public function getPurchaseTypeGroup(Request $request){
        $employeeId = $this->employeeId;
        $companyId = $this->companyId;

        $model = new ProcurementPurchasingModel();
        $getPurchaseTypeGroup = $model->masterPurchaseTypeGroup()
                                    ->from('master_purchase_type_group as a')
                                    ->select('a.purchase_type_group_id', 'a.purchase_type_group_name')
                                    ->where(function($query) use ($companyId) {
                                        $query->whereNull('a.company_id')
                                            ->orWhere('a.company_id', '=', $companyId);
                                    })
                                    ->where('a.is_active', '1')
                                    ->orderBy('a.purchase_type_group_name', 'asc')
                                    ->get();
        return $getPurchaseTypeGroup;
    }

    public function getPurchaseTypeComponents(Request $request){
        $companyId = $this->companyId;

        $search = Str::squish($request->input('term'));
        $page = (int) $request->input('page', 1);
        $perPage = 10;

        $model = new ProcurementPurchasingModel();
        $getComponent = $model->masterPurchaseComponents()
                            ->from('master_purchase_components as a')
                            ->select('a.component_id', 'a.component_name')
                            ->where(function($query) use ($companyId) {
                                $query->whereNull('a.company_id')
                                    ->orWhere('a.company_id', '=', $companyId);
                            })
                            ->where('a.is_active', '1');

        if(!empty($search)) {
            $getComponent->where(function($query) use ($search) {
                            $query->where('a.component_name', 'like', '%'.$search.'%');
                        });
        }
        $getComponent->orderBy('a.component_name', 'asc');
        $total = $getComponent->count();
        $results = $getComponent->skip(($page - 1) * $perPage)
            ->take($perPage)
            ->get()
            ->map(function ($row) {
                return [
                    'id' => $row->component_id,
                    'text' => $row->component_name,
                ];
            });

        return response()->json([
            'data' => $results,
            'more' => ($total > $page * $perPage)
        ]);
    }

    public function addPurchaseTypeComponents(Request $request) {
        $employeeId = $this->employeeId;
        $request->validate(['name' => 'required|string|max:100']);
        $componentName = Str::squish($request->name);
        $modelProcurement = new ProcurementPurchasingModel();
        $insert = $modelProcurement->masterPurchaseComponents()->create([
                                        'component_name' => $componentName,
                                        'created_by' => $employeeId,
                                        'created_at' => Carbon::now(),
                                    ]);

        return response()->json(['id' => $insert->id, 'text' => $componentName]);
    }

    public function orderFormApplicantTable(Request $request) {
        $employeeId = $this->employeeId;
        // $companyId = $this->companyId;
        $decryptedToken = SafeToken::decode($request->filterOrderFormCompany);
        $companyId = $decryptedToken['c'];

        $modelProcurement = new ProcurementPurchasingModel();
        $query = $modelProcurement->docApprovalMatrix()
                                ->from('doc_approval_matrix as a')
                                ->leftJoin('vw_master_employee_active as b', 'b.employee_id', '=', 'a.employee_id')
                                ->leftJoin('vw_master_department as c', 'c.department_id', '=', 'a.department_id')
                                ->select('a.employee_id as employeeId', 'b.employee_name as employeeName', 'a.department_id as departmentId', 'c.department_name as departmentName', 'a.is_active as status')
                                ->distinct()
                                ->where('a.doc_type_id', '1') // 1 = ORDER FORM
                                ->where('a.company_id', $companyId)
                                ->where('a.is_deleted', '0');

        if ($request->filled('search')) {
            $search = Str::squish($request->input('search'));
            $query->where(function($q) use ($search) {
                    $q->where('a.employee_id', 'LIKE', "%{$search}%")
                        ->orWhere('b.employee_name', 'LIKE', "%{$search}%")
                        ->orWhere('c.department_name', 'LIKE', "%{$search}%");
                });
        }

        $query = $query->orderBy('b.employee_name', 'asc')
                        ->orderBy('c.department_name', 'asc');
        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('action', function($row) use ($companyId) {
                $token = ['id' => $row->employeeId,'c' => $companyId,'d' => $row->departmentId];
                $tokenForm = SafeToken::encode($token);

                if($row->status == '1') {
                    $actionList = '<li><a class="dropdown-item actionBtnHeader" href="javascript:void(0);" data-token="'.$tokenForm.'" data-type="EDIT_ORDER_FORM_APPLICANT")">Edit Approval Flow</a></li>
                                    <li><a class="dropdown-item actionBtnHeader" href="javascript:void(0);" onclick="actionTable('."'".$tokenForm."'".', '."'DISABLE_ORDER_FORM_APPLICANT'".')">Disable Flow</a></li>
                                    <li><a class="dropdown-item actionBtnHeader" href="javascript:void(0);" onclick="actionTable('."'".$tokenForm."'".', '."'DELETE_ORDER_FORM_APPLICANT'".')">Delete Flow</a></li>';
                }
                else {
                    $actionList = '<li><a class="dropdown-item" href="javascript:void(0);" onclick="actionTable('."'".$tokenForm."'".', '."'ENABLE_ORDER_FORM_APPLICANT'".')">Enable Flow</a></li>
                    <li><a class="dropdown-item" href="javascript:void(0);" onclick="actionTable('."'".$tokenForm."'".', '."'DELETE_ORDER_FORM_APPLICANT'".')">Delete Flow</a></li>';
                }

                $action = '<div class="dropdown">
                                    <button class="btn btn-sm btn-secondary dropdown-toggle" data-bs-boundary="viewport" id="dropdownMenuButton"
                                        type="button" data-coreui-toggle="dropdown"
                                        aria-expanded="false">Action</button>
                                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdownMenuButton1" style="">
                                        '.$actionList.'
                                    </ul>
                                </div>';

                return $action;
            })
            ->editColumn('status', function($row) {
                $statuses = [
                    '1' => '<i title="Active" class="fa-solid fa-circle-check text-success fs-5"></i>',
                    '0' => '<i title="Non Active" class="fa-solid fa-circle-xmark text-danger fs-5"></i>',
                ];
                return $statuses[$row->status];
            })
            ->rawColumns(['action', 'status'])
            ->make(true);
    }

    public function poApprovalRuleTable(Request $request) {
        $decryptedToken = SafeToken::decode($request->filterPoRuleCompany);
        $companyId = $decryptedToken['c'];

        $modelProcurement = new ProcurementPurchasingModel();
        $query = $modelProcurement->masterFlowRuleGroup()
                                ->from('master_flow_rule_group as a')
                                ->leftJoin('vw_master_employee_active as b', function($join) {
                                    $join->on('b.employee_id', 'a.employee_id');
                                })
                                ->select(
                                    'a.rule_group_id as id',
                                    'a.employee_id as employeeId',
                                    'b.employee_name as employeeName',
                                    'a.rule_group_name as ruleGroupName',
                                    'a.order as priority',
                                    'a.is_active as status'
                                )
                                ->distinct()
                                ->where('a.rule_type_id', '2') // 2 = APPLICATION & PO FORM APPROVAL
                                ->where(function($query) {
                                    $query->where('a.created_by', $this->employeeId)
                                        ->orWhere('a.updated_by', $this->employeeId)
                                        ->orWhere('b.employee_id', $this->employeeId);
                                })
                                ->where('a.company_id', $companyId)
                                ->where('a.is_deleted', '0');

        if ($request->filled('search')) {
            $search = Str::squish($request->input('search'));
            $query->where(function($q) use ($search) {
                    $q->where('a.rule_group_name', 'LIKE', "%{$search}%")
                        ->orWhere('b.employee_name', 'LIKE', "%{$search}%");
                });
        }

        $query = $query->orderBy('b.employee_name', 'asc')
                        ->orderBy('a.employee_id', 'asc')
                        ->orderBy('a.is_active', 'desc')
                        ->orderBy('a.order', 'asc');
        return DataTables::of($query)
            ->addColumn('priorityOrder', function($row) {
                return '<div class="text-center position-relative" style="min-height:100%;">
                            <i class="fa-solid fa-grip-vertical text-secondary position-absolute"
                                style="top:49%; transform:translateY(-50%);"></i>
                            <span class="d-inline-block w-100">'.$row->priority.'</span>
                        </div>';
            })
            ->addColumn('action', function($row) {
                $token = ['id' => $row->employeeId, 'i' => $row->id];
                $tokenForm = SafeToken::encode($token);

                if($row->status == '1') {
                    $actionList = '<li><a class="dropdown-item actionBtnHeader" href="javascript:void(0);" data-token="'.$tokenForm.'" data-type="EDIT_PO_APPROVAL_RULE")">Edit Flow</a></li>
                                    <li><a class="dropdown-item actionBtnHeader" href="javascript:void(0);" onclick="actionTable('."'".$tokenForm."'".', '."'DISABLE_PO_APPROVAL_RULE'".')">Disable Flow</a></li>
                                    <li><a class="dropdown-item actionBtnHeader" href="javascript:void(0);" onclick="actionTable('."'".$tokenForm."'".', '."'DELETE_PO_APPROVAL_RULE'".')">Delete Flow</a></li>';
                }
                else {
                    $actionList = '<li><a class="dropdown-item" href="javascript:void(0);" onclick="actionTable('."'".$tokenForm."'".', '."'ENABLE_PO_APPROVAL_RULE'".')">Enable Flow</a></li>
                    <li><a class="dropdown-item" href="javascript:void(0);" onclick="actionTable('."'".$tokenForm."'".', '."'DELETE_PO_APPROVAL_RULE'".')">Delete Flow</a></li>';
                }

                $action = '<div class="dropdown">
                                    <button class="btn btn-sm btn-secondary dropdown-toggle" data-bs-boundary="viewport" id="dropdownMenuButton"
                                        type="button" data-coreui-toggle="dropdown"
                                        aria-expanded="false">Action</button>
                                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdownMenuButton1" style="">
                                        '.$actionList.'
                                    </ul>
                                </div>';

                return $action;
            })
            ->editColumn('id', function($row) {
                return Str::padLeft($row->id, 3, '0');
            })
            ->editColumn('status', function($row) {
                $statuses = [
                    '1' => '<i title="Active" class="fa-solid fa-circle-check text-success fs-5"></i>',
                    '0' => '<i title="Non Active" class="fa-solid fa-circle-xmark text-danger fs-5"></i>',
                ];
                return $statuses[$row->status];
            })
            ->rawColumns(['priorityOrder', 'action', 'id', 'status'])
            ->make(true);
    }

    public function getRuleFlow(Request $request) {
        $companyId = $this->companyId;
        $search = $request->input('term');
        $dataType = $request->dataType;
        $dataNik = $request->nik;
        $page = (int) $request->input('page', 1);
        $perPage = 10;

        $model = new ProcurementPurchasingModel();
        // $myData = $model->vwMasterEmployeeActive()->select('*')
        //                 ->where('employee_id', '=', $employeeId)
        //                 ->first();

        if($request->company) {
            $decryptedToken = SafeToken::decode($request->company);
            $companyId = $decryptedToken['c'];
        }

        $data = array();
        if($request->dataForm == 'APPLICATION_PO_FORM') {
            if($request->dataType == 'FIELD') {
                $getData = $model->masterFlowRuleField()
                                ->from('master_flow_rule_field as a')
                                ->select('a.field_id', 'a.field_name', 'a.field_description', 'a.value_type')
                                ->where(function($query) use ($companyId) {
                                    $query->whereNull('a.company_id')
                                        ->orWhere('a.company_id', '=', $companyId);
                                })
                                ->where(function($query) use ($search) {
                                    $query->where('a.field_name', 'like', '%'.$search.'%')
                                        ->orWhere('a.field_description', 'like', '%'.$search.'%');
                                })
                                ->where('a.is_admin', '0')
                                ->where('a.is_active', '1')
                                ->orderBy('a.field_name', 'asc');
                $total = $getData->count();
                $results = $getData->skip(($page - 1) * $perPage)
                    ->take($perPage)
                    ->get()
                    ->map(function ($row) {
                        return [
                            'id' => $row->field_id,
                            'text' => $row->field_name,
                            'description1' => $row->field_description,
                            'nextFlow' => $row->value_type,
                        ];
                    });

                return response()->json([
                    'data' => $results,
                    'more' => ($total > $page * $perPage)
                ]);
            }
            else if($request->dataType == 'FIELD_OPTION') {
                $getData = $model->masterFlowRuleFieldOption()
                                ->from('master_flow_rule_field_option as a')
                                ->select('a.field_option_id as fieldOptionId', 'a.option_name as optionName')
                                ->where('a.field_id', $request->fieldId)
                                ->where('a.is_active', '1')
                                ->orderBy('a.option_name', 'asc')
                                ->get();
                return response()->json([
                    'data' => $getData,
                    'status' => 200,
                ], 200);
            }
            else if($request->dataType == 'APPROVAL_RULE_MATRIX') {
                $companyId = $request->companyId;
                $purchaseType = $request->purchaseType;
                $paymentType = $request->paymentType;
                $totalAmount = $request->totalAmount;
                $employeeId = $this->employeeId;
                $ruleTypeId = '2'; // 2 = APPLICATION & PO FORM

                $data = [];
                if($purchaseType != '' && $paymentType != '' && $totalAmount != '') {
                    $getPurchaseTypeGroup = $model->masterPurchaseType()
                                                    ->from('master_purchase_type as a')
                                                    ->select('a.purchase_type_group')
                                                    ->where(function($query) use ($companyId) {
                                                        $query->whereNull('a.company_id')
                                                            ->orWhere('a.company_id',  $companyId);
                                                    })
                                                    ->where('a.purchase_type_id', $purchaseType)
                                                    ->where('a.is_active', '1')
                                                    ->first();

                    $conditionData = [
                        'PURCHASE TYPE' => $getPurchaseTypeGroup->purchase_type_group,
                        'PAYMENT TYPE' => $paymentType,
                        'TOTAL AMOUNT' => $totalAmount
                    ];

                    // GET FLOW RULE GROUP AND EVALUATE RULE CONDITION
                    $flowRuleService = new FlowRuleServiceModel();
                    $rules = $flowRuleService->getRules($employeeId, $ruleTypeId);

                    $ruleEngineService = new RuleEngineService();
                    $ruleGroupIdResult = $ruleEngineService->evaluateRules($conditionData, $rules);

                    if($ruleGroupIdResult) {
                        $data['ruleId'] = Str::padLeft($ruleGroupIdResult, 3, '0');
                        $getFlowRuleAction = $model->masterFlowRuleAction()
                                            ->from('master_flow_rule_action as a')
                                            ->leftJoin('doc_approval_matrix as b', 'a.action_id', '=', 'b.flow_rule_action_id')
                                            ->leftJoin('vw_master_employee_active as c', 'b.employee_id_approval', '=', 'c.employee_id')
                                            ->select('a.action_value', 'c.employee_name', 'b.matrix_as')
                                            ->where('a.rule_group_id', $ruleGroupIdResult)
                                            ->where('a.is_active', '1')
                                            ->where('b.is_active', '1')
                                            ->orderBy('b.matrix_order', 'asc')
                                            ->get();
                        foreach($getFlowRuleAction as $row) {
                            $data['signer'][$row->matrix_as][] = [
                                                                    'employeeId' => $row->action_value,
                                                                    'employeeName' => Str::upper($row->employee_name),
                                                                ];
                        }
                    }
                }

                return response()->json([
                    'data' => $data,
                    'status' => 200,
                ], 200);
            }
        }
    }

    public function inspectorTable(Request $request) {
        $employeeId = $this->employeeId;
        // $companyId = $this->companyId;
        $decryptedToken = SafeToken::decode($request->filterOrderFormCompany);
        $companyId = $decryptedToken['c'];

        $modelProcurement = new ProcurementPurchasingModel();
        $query = $modelProcurement->docApprovalMatrix()
                                ->from('doc_approval_matrix as a')
                                ->leftJoin('vw_master_employee_active as b', 'b.employee_id', '=', 'a.employee_id')
                                ->leftJoin('vw_master_department as c', 'c.department_id', '=', 'a.department_id')
                                ->select('a.employee_id as employeeId', 'b.employee_name as employeeName', 'a.department_id as departmentId', 'c.department_name as departmentName', 'a.is_active as status')
                                ->distinct()
                                ->where('a.doc_type_id', '4') // 4 = INSPECTION FORM
                                ->where('a.company_id', $companyId)
                                ->whereNotNull('b.employee_name')
                                ->where('a.matrix_as', '<>', 'INSPECTION_RECEIVER')
                                ->where('a.is_deleted', '0');

        if ($request->filled('search')) {
            $search = Str::squish($request->input('search'));
            $query->where(function($q) use ($search) {
                    $q->where('a.employee_id', 'LIKE', "%{$search}%")
                        ->orWhere('b.employee_name', 'LIKE', "%{$search}%")
                        ->orWhere('c.department_name', 'LIKE', "%{$search}%");
                });
        }

        $query = $query->orderBy('b.employee_name', 'asc')
                        ->orderBy('c.department_name', 'asc');
        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('action', function($row) use ($companyId) {
                $token = ['id' => $row->employeeId,'c' => $companyId,'d' => $row->departmentId];
                $tokenForm = SafeToken::encode($token);

                if($row->status == '1') {
                    $actionList = '<li><a class="dropdown-item actionBtnHeader" href="javascript:void(0);" data-token="'.$tokenForm.'" data-type="EDIT_INSPECTOR")">Edit Inspection Flow</a></li>
                                    <li><a class="dropdown-item actionBtnHeader" href="javascript:void(0);" onclick="actionTable('."'".$tokenForm."'".', '."'DISABLE_INSPECTOR'".')">Disable Flow</a></li>
                                    <li><a class="dropdown-item actionBtnHeader" href="javascript:void(0);" onclick="actionTable('."'".$tokenForm."'".', '."'DELETE_INSPECTOR'".')">Delete Flow</a></li>';
                }
                else {
                    $actionList = '<li><a class="dropdown-item" href="javascript:void(0);" onclick="actionTable('."'".$tokenForm."'".', '."'ENABLE_INSPECTOR'".')">Enable Flow</a></li>
                    <li><a class="dropdown-item" href="javascript:void(0);" onclick="actionTable('."'".$tokenForm."'".', '."'DELETE_INSPECTOR'".')">Delete Flow</a></li>';
                }

                $action = '<div class="dropdown">
                                    <button class="btn btn-sm btn-secondary dropdown-toggle" data-bs-boundary="viewport" id="dropdownMenuButton"
                                        type="button" data-coreui-toggle="dropdown"
                                        aria-expanded="false">Action</button>
                                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdownMenuButton1" style="">
                                        '.$actionList.'
                                    </ul>
                                </div>';

                return $action;
            })
            ->editColumn('status', function($row) {
                $statuses = [
                    '1' => '<i title="Active" class="fa-solid fa-circle-check text-success fs-5"></i>',
                    '0' => '<i title="Non Active" class="fa-solid fa-circle-xmark text-danger fs-5"></i>',
                ];
                return $statuses[$row->status];
            })
            ->rawColumns(['action', 'status'])
            ->make(true);
    }

    public function getDocumentNumber(Request $request) {
        $employeeId = $this->employeeId;
        $companyId = SafeToken::decode($request->companyId)['c'];
        $documentTypeId = $request->documentType;
        $type = $request->type;

        $model = new DocumentApprovalModel();
        $getDocumentType = $model->masterDocumentTypes()
            ->select('doc_number_config', 'doc_code', 'seq_number_min_digit', 'reset_seq_number')
            ->where('doc_type_id', $documentTypeId)
            ->where('is_active', '1')
            ->orderByDesc('doc_type_id')
            ->first();

        if (!$getDocumentType) {
            return null;
        }

        $docNumberConfig = json_decode($getDocumentType->doc_number_config, true);
        $minDigit = $getDocumentType->seq_number_min_digit;
        $month = Carbon::now()->format('m');
        $year = Carbon::now()->format('Y');
        $romanNumber = $this->romanNumber($month);
        $documentNumber = '';
        $formatNumber = '';
        $seqNumber = null;

        if ($type === 'INSERT_SEQ') {
            DB::beginTransaction();
            try {
                // Lock the row for update
                $getSeqNumber = $model->seqNumber()
                    ->where('company_id', $companyId)
                    ->where('doc_type_id', $documentTypeId)
                    ->orderByDesc('seq_id')
                    ->lockForUpdate()
                    ->first();

                if ($getSeqNumber) {
                    if ($getDocumentType->reset_seq_number === 'YEARLY') {
                        $seqNumber = ($getSeqNumber->year == $year) ? (int)$getSeqNumber->seq_number + 1 : 1;
                    }
                    elseif ($getDocumentType->reset_seq_number === 'MONTHLY') {
                        $seqNumber = ($getSeqNumber->year == $year && $getSeqNumber->month == $month) ? (int)$getSeqNumber->seq_number + 1 : 1;
                    }
                    else {
                        $seqNumber = (int)$getSeqNumber->seq_number + 1;
                    }
                }
                else {
                    $seqNumber = 1;
                }

                $seqNumberFormatted = sprintf("%0{$minDigit}d", $seqNumber);

                foreach ($docNumberConfig as $value) {
                    switch ($value) {
                        case 'seqNumber':
                            $documentNumber .= $seqNumberFormatted;
                            break;
                        case 'docCode':
                            $documentNumber .= $getDocumentType->doc_code;
                            $formatNumber .= $getDocumentType->doc_code;
                            break;
                        case 'companyId':
                            $documentNumber .= $companyId;
                            $formatNumber .= $companyId;
                            break;
                        case 'companyCode':
                            $companyCode = optional($model->masterCompany()
                                                        ->select('company_code')
                                                        ->where('company_id', $companyId)
                                                        ->first())->company_code;
                            $documentNumber .= $companyCode;
                            $formatNumber .= $companyCode;
                            break;
                        case 'deptCode':
                            $deptCode = optional($model->vwMasterDepartment()
                                                    ->select('department_code')
                                                    ->where('department_id', $request->departmentId)
                                                    ->first())->department_code;
                            $documentNumber .= $deptCode;
                            $formatNumber .= $deptCode;
                            break;
                        case 'locationCode':
                            $locCode = optional($model->masterLocations()
                                                    ->select('location_code')
                                                    ->where('location_id', $request->locationId)
                                                    ->first())->location_code;
                            $documentNumber .= $locCode;
                            $formatNumber .= $locCode;
                            break;
                        case 'romanNumber':
                            $documentNumber .= $romanNumber;
                            $formatNumber .= $romanNumber;
                            break;
                        case 'yearFull':
                            $documentNumber .= $year;
                            $formatNumber .= $year;
                            break;
                        case 'yearShort':
                            $shortYear = substr($year, -2);
                            $documentNumber .= $shortYear;
                            $formatNumber .= $shortYear;
                            break;
                        case 'version':
                            $documentNumber .= '1';
                            $formatNumber .= '1';
                            break;
                        default:
                            $documentNumber .= $value;
                            $formatNumber .= $value;
                            break;
                    }
                }

                $dataInsert = [
                    'doc_type_id' => $documentTypeId,
                    'company_id' => $companyId,
                    'directorat_id' => $request->input('directoratId'),
                    'division_id' => $request->input('divisionId'),
                    'department_id' => $request->input('departmentId'),
                    'seq_number' => $seqNumberFormatted,
                    'month' => (int)$month,
                    'year' => $year,
                    'document_number' => $documentNumber,
                ];

                $insert = $model->seqNumber()->create($dataInsert);

                DB::commit();

                return [
                    'seqId' => $insert->id,
                    'seqNumber' => $seqNumberFormatted,
                    'documentNumber' => $documentNumber,
                ];
            } catch (\Exception $e) {
                DB::rollBack();
                return null;
            }
        }
        else if ($type === 'DRAFT_SEQ') {
            foreach ($docNumberConfig as $value) {
                switch ($value) {
                    case 'seqNumber':
                        $documentNumber .= str_repeat('#', $minDigit); // placeholder
                        break;
                    case 'docCode':
                        $documentNumber .= $getDocumentType->doc_code;
                        $formatNumber .= $getDocumentType->doc_code;
                        break;
                    case 'companyId':
                        $documentNumber .= $companyId;
                        $formatNumber .= $companyId;
                        break;
                    case 'companyCode':
                        $companyCode = optional($model->masterCompany()
                                                    ->select('company_code')
                                                    ->where('company_id', $companyId)
                                                    ->first())->company_code;
                        $documentNumber .= $companyCode;
                        $formatNumber .= $companyCode;
                        break;
                    case 'deptCode':
                        $deptCode = optional($model->vwMasterDepartment()
                                                ->select('department_code')
                                                ->where('department_id', $request->departmentId)
                                                ->first())->department_code;
                        $documentNumber .= $deptCode;
                        $formatNumber .= $deptCode;
                        break;
                    case 'locationCode':
                        $locCode = optional($model->masterLocations()
                                                ->select('location_code')
                                                ->where('location_id', $request->locationId)
                                                ->first())->location_code;
                        $documentNumber .= $locCode;
                        $formatNumber .= $locCode;
                        break;
                    case 'romanNumber':
                        $documentNumber .= $romanNumber;
                        $formatNumber .= $romanNumber;
                        break;
                    case 'yearFull':
                        $documentNumber .= $year;
                        $formatNumber .= $year;
                        break;
                    case 'yearShort':
                        $shortYear = substr($year, -2);
                        $documentNumber .= $shortYear;
                        $formatNumber .= $shortYear;
                        break;
                    case 'version':
                        $documentNumber .= '1';
                        $formatNumber .= '1';
                        break;
                    default:
                        $documentNumber .= $value;
                        $formatNumber .= $value;
                        break;
                }
            }

            return [
                'documentTypeId' => $documentTypeId,
                'companyId' => $companyId,
                'directorat_id' => $request->input('directoratId'),
                'division_id' => $request->input('divisionId'),
                'department_id' => $request->input('departmentId'),
                'seqNumber' => null,
                'month' => $month,
                'year' => $year,
                'formatNumber' => $formatNumber,
                'documentNumber' => $documentNumber,
            ];
        }

        return null;
    }

    public function romanNumber($number){
        $arrRomanNumber = [
            1 => 'I',
            2 => 'II',
            3 => 'III',
            4 => 'IV',
            5 => 'V',
            6 => 'VI',
            7 => 'VII',
            8 => 'VIII',
            9 => 'IX',
            10 => 'X',
            11 => 'XI',
            12 => 'XII'
        ];

        if (!is_numeric((int)$number) || (int)$number < 1 || (int)$number > 12) {
            return null;
        }

        return $arrRomanNumber[(int)$number];
    }

    public function getFileIcon($mimeType, $fileName = null){
        $iconClass = '';
        $checkExtension = function (array $extensions) use ($fileName) {
            return collect($extensions)->contains(function ($ext) use ($fileName) {
                return str_ends_with(strtolower($fileName), $ext);
            });
        };

        if (str_contains($mimeType, 'pdf')) {
            $iconClass = 'far fa-file-pdf';
        } elseif (str_contains($mimeType, 'image')) {
            $iconClass = 'far fa-file-image';
        } elseif (str_contains($mimeType, 'excel') || str_contains($mimeType, 'spreadsheetml')) {
            $iconClass = 'far fa-file-excel';
        } elseif (str_contains($mimeType, 'wordprocessingml')) {
            $iconClass = 'far fa-file-word';
        } elseif (
            str_contains($mimeType, 'ms-outlook') ||
            str_contains($mimeType, 'ms-tnef') ||
            $mimeType === 'message/rfc822' ||
            str_contains($mimeType, 'vnd.ms-outlook') ||
            $checkExtension(['.msg', '.eml'])
        ) {
            $iconClass = 'far fa-envelope';
        } else {
            $iconClass = 'far fa-file';
        }

        return $iconClass;
    }

    public function timeAgo($dateTime, $full = false) {
        try {
            if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $dateTime)) {
                // ONLY DATE
                $now = new DateTime();
                $today = new DateTime($now->format('Y-m-d'));
                $ago = new DateTime($dateTime);
                $diff = $today->diff($ago);

                if ($diff->days === 0 && $diff->y === 0 && $diff->m === 0) {
                    return 'Today';
                }
            }
            elseif (preg_match('/^\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}$/', $dateTime)) {
                // DATE AND TIME
                $now = new DateTime();
                $ago = new DateTime($dateTime);
                $diff = $now->diff($ago);

                if ($diff->y === 0 && $diff->m === 0 && $diff->d === 0 &&
                    $diff->h === 0 && $diff->i === 0 && $diff->s === 0) {
                    return 'just now';
                }
            }
            else {
                return "Invalid date format";
            }

            $days = $diff->days;
            $weeks = floor($days / 7);
            $remainingDays = $days % 7;

            $string = [
                'y' => 'year',
                'm' => 'month',
                'w' => 'week',
                'd' => 'day',
                'h' => 'hour',
                'i' => 'minute',
                's' => 'second',
            ];

            $diffArray = [
                'y' => $diff->y,
                'm' => $diff->m,
                'w' => $weeks,
                'd' => $remainingDays,
                'h' => $diff->h,
                'i' => $diff->i,
                's' => $diff->s,
            ];

            foreach ($string as $k => &$v) {
                if ($diffArray[$k] > 0) {
                    $v = $diffArray[$k] . ' ' . $v . ($diffArray[$k] > 1 ? 's' : '');
                } else {
                    unset($string[$k]);
                }
            }

            if (!$full) {
                $string = array_slice($string, 0, 1);
            }

            return $string ? implode(', ', $string) . ' ago' : 'just now';

        }
        catch (Exception $e) {
            return "Error: " . $e->getMessage();
        }
    }

    private function formatNumber($number) {
        $absoluteNumber = abs($number);
        $formattedNumber = ($absoluteNumber == round($absoluteNumber))
            ? number_format($absoluteNumber, 0, '.', ',')
            : number_format($absoluteNumber, 2, '.', ',');

        return $number < 0 ? '-' . $formattedNumber : $formattedNumber;
    }

    private function formatCompanyName(string $text): string {
        return Str::of($text)
            ->trim()
            // ->title()
            ->replaceMatches('/\b(PT|CV|UD)\.(\S)/i', function ($match) {
                return strtoupper($match[1]) . ' ' . $match[2];
            })
            // Then handle cases like PT. INDOMAR → PT INDOMAR
            ->replaceMatches('/\b(PT|CV|UD)\.\s+/i', function ($match) {
                return strtoupper($match[1]) . ' ';
            })
            // Then handle cases with optional dot and space
            ->replaceMatches('/\b(PT|CV|UD)\.? /i', function ($match) {
                return strtoupper($match[1]) . ' ';
            })
            // Handle (Persero) formatting
            ->replaceMatches('/\(\s*(Persero)\s*\)/i', '(Persero)')
            // Standardize Tbk
            ->replaceMatches('/(?:^|\s)(TBK|Tbk)\.?(?=\s|$)/i', ' Tbk')
            // Clean up any remaining multiple spaces
            ->replaceMatches('/[ ]{2,}/', ' ')
            // Clean up newlines
            ->replaceMatches('/\n{2,}/', "\n")
            ->replaceMatches('/\n\s+/', "\n")
            ->toString();
    }

    private function getMimeTypeByExtension(string $filename) {
        $mimeTypes = [
            'txt' => 'text/plain',
            'html' => 'text/html',
            'htm' => 'text/html',
            'css' => 'text/css',
            'js' => 'application/javascript',
            'csv' => 'text/csv',
            'ics' => 'text/calendar',
            'md' => 'text/markdown',
            'markdown' => 'text/markdown',
            'xml' => 'application/xml',
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'png' => 'image/png',
            'gif' => 'image/gif',
            'bmp' => 'image/bmp',
            'svg' => 'image/svg+xml',
            'tif' => 'image/tiff',
            'tiff' => 'image/tiff',
            'webp' => 'image/webp',
            'ico' => 'image/x-icon',
            'mp3' => 'audio/mpeg',
            'aac' => 'audio/aac',
            'wav' => 'audio/wav',
            'oga' => 'audio/ogg',
            'mid' => 'audio/midi',
            'midi' => 'audio/midi',
            'weba' => 'audio/webm',
            'opus' => 'audio/opus',
            'mpeg' => 'video/mpeg',
            'mpg' => 'video/mpeg',
            'mp4' => 'video/mp4',
            'avi' => 'video/x-msvideo',
            'ogv' => 'video/ogg',
            'webm' => 'video/webm',
            '3gp' => 'video/3gpp',
            '3g2' => 'video/3gpp2',
            'pdf' => 'application/pdf',
            'zip' => 'application/zip',
            'json' => 'application/json',
            'doc' => 'application/msword',
            'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'xls' => 'application/vnd.ms-excel',
            'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'ppt' => 'application/vnd.ms-powerpoint',
            'pptx' => 'application/vnd.openxmlformats-officedocument.presentationml.presentation',
            'epub' => 'application/epub+zip',
            'jar' => 'application/java-archive',
            'swf' => 'application/x-shockwave-flash',
            'rar' => 'application/x-rar-compressed',
            '7z' => 'application/x-7z-compressed',
            'apk' => 'application/vnd.android.package-archive',
            'mpkg' => 'application/vnd.apple.installer+xml',
            'bin' => 'application/octet-stream',
        ];

        $expFilename = Str::of($filename)->explode('.');
        $extension = Str::lower($expFilename->last());
        return $mimeTypes[$extension] ?? 'application/octet-stream';
    }
}
