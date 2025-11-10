<?php

namespace Modules\DocumentApproval\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Validation\ValidationException;
use Modules\DocumentApproval\Models\DocumentApprovalModel;

use App\Models\MasterEmailAccount;
use App\Services\SafeToken;
use Illuminate\Support\Str;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mailer\Transport\Smtp\EsmtpTransport;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;

use Modules\DocumentApproval\Http\Controllers\DocumentApprovalController as ControllersDocumentApprovalController;

class ApiExternalController extends Controller
{
    public function getPoCarton(Request $req) {
        // $orderPartnerId = $req->query('id');
        // $orderPartnerId = $req->query('id');
        $model = new DocumentApprovalModel();
        $data = [];
        foreach ($req->ids as $row) {
            $orderPartnerId = $row;
            $header = $detail = array();
            $getForm = $model->docApprovalOrderApplication()
                            ->from('doc_approval_order_application as a')
                            ->leftJoin('doc_approval_detail_order_form as b', 'b.order_form_id', '=', 'a.order_form_id')
                            ->leftJoin('doc_approval_order_partner as c', 'c.order_partner_id', '=', 'b.order_partner_id')
                            ->leftJoin('master_term_payment as d', 'd.term_payment_id', '=', 'a.term_payment_id')
                            ->leftJoin('master_transaction_status as e', 'e.transaction_status_id', '=', 'a.application_form_status')
                            ->where('c.order_partner_id', $orderPartnerId)
                            ->where('a.is_active', '1')
                            ->select('a.application_id', 'a.application_number', 'a.application_grand_total', 'a.application_currency', 'a.application_remark', 'a.created_at', 'a.final_decision_at', 'a.ship_date', 'a.term_payment_id', 'a.vendor_id', 'a.vendor_name', 'd.term_payment', 'c.order_number', 'e.transaction_status_id', 'e.transaction_status_name')
                            ->latest('a.application_id')
                            ->first();
            if($getForm) {
                if ($getForm->application_form_status == '16') {
                    $getOrderPartner = $model->docApprovalOrderPartner()
                                            ->select('order_number', 'status')
                                            ->where('order_partner_id', $orderPartnerId)
                                            ->where('is_active', '1')
                                            ->first();
                    if($getOrderPartner) {
                        $header = [
                            "poStatusId" => 16,
                            "poStatusName" => "NOT SUBMITTED",
                            "prePoNo" => $getOrderPartner->order_number,
                            "poNo" => null,
                            "poDate" => null,
                            "supplierId" => null,
                            "supplierName" => null,
                            "estDeliveryDate" => null,
                            "termPaymentId" => null,
                            "termPayment" => null,
                            "totalQty" => null,
                            "totalItem" => null,
                            "totalAmount" => null,
                            "taxRate" => null,
                            "taxDpp" => null,
                            "taxValue" => null,
                            "grandTotal" => null,
                            "remark" => null,
                        ];
                    }

                    $getOrderPartnerDetail = $model->docApprovalOrderPartnerDetail()
                                                ->select('item_detail_id', 'category_id', 'category_name', 'item_id', 'item_name', 'quantity', 'unit_name')
                                                ->where('order_partner_id', $orderPartnerId)
                                                ->where('is_active', '1')
                                                ->get();
                    foreach ($getOrderPartnerDetail as $rowDetail) {
                        $detail[] = [
                            "prePoDetailId" => $rowDetail->item_detail_id,
                            "catId" => $rowDetail->category_id,
                            "catName" => $rowDetail->category_name,
                            "itemId" => $rowDetail->item_id,
                            "itemName" => $rowDetail->item_name,
                            "qty" => $rowDetail->quantity,
                            "unit" => $rowDetail->unit_name,
                            "price" => null,
                            "totalPrice" => null,
                        ];
                    }
                }
                else {
                    $totalQty = $totalItem = $taxValue = 0;
                    $taxRate = $taxDpp = null;
                    $getItem = $model->docApprovalOrderApplicationItem()
                                    ->from('doc_approval_order_application_item as a')
                                    ->leftJoin('doc_approval_detail_order_form_item as b', 'b.order_detail_id', '=', 'a.order_detail_id')
                                    ->leftJoin('doc_approval_order_partner_detail as c', 'b.order_detail_id', '=', 'a.order_detail_id')
                                    ->select('a.appliance_item', 'a.quantity', 'a.unit_price', 'a.subtotal_price', 'b.order_partner_detail_id', 'c.category_id', 'b.unit_name', 'a.dpp_other_value', 'a.order_detail_id')
                                    ->where('a.application_id', $getForm->application_id)
                                    ->where('a.is_active', '1')
                                    ->oldest('a.application_item_id')
                                    ->get();
                    foreach ($getItem as $rowItem) {
                        if($rowItem->appliance_item == 'VAT') {
                            $value = $rowItem->unit_price;
                            $taxRate = ($value == floor($value)) ? (int) $value : number_format($value, 2, '.', '');
                            $taxDpp = $rowItem->dpp_other_value;
                            $taxValue = $rowItem->subtotal_price;
                        }
                        else {
                            $qty = $unit = null;
                            if($rowItem->appliance_item != 'Delivery Fee' && $rowItem->appliance_item != 'Discount' && $rowItem->appliance_item != 'PPh') {
                                $qty = $rowItem->quantity;
                                $totalQty += $qty;
                                $totalItem++;
                                $unit = $rowItem->unit_name;
                            }

                            $detail[] = [
                                "prePoDetailId" => $rowItem->order_partner_detail_id,
                                "catId" => $rowItem->category_id,
                                "catName" => $rowItem->category_name,
                                "itemId" => $rowItem->item_id,
                                "itemName" => $rowItem->appliance_item,
                                "qty" => $qty,
                                "unit" => $unit,
                                "price" => $rowItem->unit_price,
                                "totalPrice" => $rowItem->subtotal_price,
                            ];
                        }
                    }

                    $header = [
                        "poStatusId" => $getForm->transaction_status_id,
                        "poStatusName" => $getForm->transaction_status_name,
                        "prePoNo" => $getForm->order_number,
                        "poNo" => $getForm->application_number,
                        "poDate" => $getForm->created_at,
                        "supplierId" => $getForm->vendor_id,
                        "supplierName" => $getForm->vendor_name,
                        "estDeliveryDate" => $getForm->ship_date,
                        "termPaymentId" => $getForm->term_payment_id,
                        "termPayment" => $getForm->term_payment,
                        "totalQty" => $totalQty,
                        "totalItem" => $totalItem,
                        "totalAmount" => $getForm->application_estimate,
                        "taxRate" => $taxRate,
                        "taxDpp" => $taxDpp,
                        "taxValue" => $taxValue,
                        "grandTotal" => $getForm->application_grand_total,
                        "remark" => $getForm->application_remark,
                    ];
                }
            }
            else {
                $getOrderPartner = $model->docApprovalOrderPartner()
                                        ->select('order_number', 'status')
                                        ->where('order_partner_id', $orderPartnerId)
                                        ->where('is_active', '1')
                                        ->first();
                if($getOrderPartner) {
                    $header = [
                        "poStatusId" => 13,
                        "poStatusName" => "PENDING",
                        "prePoNo" => $getOrderPartner->order_number,
                        "poNo" => null,
                        "poDate" => null,
                        "supplierId" => null,
                        "supplierName" => null,
                        "estDeliveryDate" => null,
                        "termPaymentId" => null,
                        "termPayment" => null,
                        "totalQty" => null,
                        "totalItem" => null,
                        "totalAmount" => null,
                        "taxRate" => null,
                        "taxDpp" => null,
                        "taxValue" => null,
                        "grandTotal" => null,
                        "remark" => null,
                    ];
                }

                $getOrderPartnerDetail = $model->docApprovalOrderPartnerDetail()
                                            ->select('item_detail_id', 'category_id', 'category_name', 'item_id', 'item_name', 'quantity', 'unit_name')
                                            ->where('order_partner_id', $orderPartnerId)
                                            ->where('is_active', '1')
                                            ->get();
                foreach ($getOrderPartnerDetail as $rowDetail) {
                    $detail[] = [
                        "prePoDetailId" => $rowDetail->item_detail_id,
                        "catId" => $rowDetail->category_id,
                        "catName" => $rowDetail->category_name,
                        "itemId" => $rowDetail->item_id,
                        "itemName" => $rowDetail->item_name,
                        "qty" => $rowDetail->quantity,
                        "unit" => $rowDetail->unit_name,
                        "price" => null,
                        "totalPrice" => null,
                    ];
                }
            }

            $data[] = [
                "header" => $header,
                "details" => $detail,
            ];
        }

        return response()->json([
            "status" => "success",
            "data" => $data,
        ]);
    }

    public function storePoCarton(Request $req) {
        try {
            $validateRules = [
                'header.prePoSource' => 'required|int',
                'header.reqDate' => 'required|date_format:Y-m-d',
                'header.reqNo'=> 'required|string|max:50',
                'header.reqById' => 'required|string|max:50',

                'details' => 'required|array|min:1',
                'details.*.prePoDetailId' => 'required|int',
                'details.*.catId' => 'required|int',
                'details.*.catName' => 'required|string|max:255',
                'details.*.itemId' => 'required|int',
                'details.*.itemName' => 'required|string|max:255',
                'details.*.unit' => 'required|string|max:50',
                'details.*.qty' => 'required|numeric|min:1',
            ];
            $validateMessages = [
                'header.prePoSource.required' => 'Pre PO source is required',
                'header.reqDate.required' => 'Request date is required',
                'header.reqNo.required'=> 'Request number is required',
                'header.reqById.required' => 'Request by is required',

                'details.required' => 'Detail item list is required',
                'details.*.prePoDetailId.required' => 'Pre PO Detail ID is required in each detail item',
                'details.*.catId.required' => 'Category ID is required in each detail item',
                'details.*.itemId.required' => 'Item ID is required in each detail item',
                'details.*.qty.required' => 'Qty is required in each detail item',
                'details.*.qty.min' => 'Qty must be at least 1',
            ];

            $req->validate($validateRules, $validateMessages);

            $employeeId = $req->header['reqById'];
            // $employeeId = '219000014';
            $model = new DocumentApprovalModel();
            $getEmployee = $model->vwMasterEmployeeActive()
                                ->select('department_id', 'department_name', 'employee_name')
                                ->where('employee_id', $employeeId)
                                // ->where('company_id', '322') // VANTEC
                                ->first();
            if($getEmployee) {
                $getOrderPartner = $model->docApprovalOrderPartner()
                                        ->select('order_partner_id', 'status')
                                        ->where('order_number', $req->header['reqNo'])
                                        ->where('is_active', '1')
                                        ->first();
                if($getOrderPartner) {
                    $orderPartnerId = $getOrderPartner->order_partner_id;
                    return response()->json([
                        "status" => "Record already exist",
                        "data" => ["orderId" => $orderPartnerId, "orderNumber" => $req->header['reqNo']]
                    ], 403);

                    if($getOrderPartner->status == '1') { // 1 = DRAFT
                        $dataUpdate = [
                                        'order_type_id' => $req->header['prePoSource'],
                                        'order_date' => $req->header['reqDate'],
                                        'order_number' => $req->header['reqNo'],
                                        'employee_id' => $req->header['reqById'],
                                        'department_id' => $getEmployee->department_id,
                                        'department_name' => $getEmployee->department_name,
                                        'order_remarks' => $req->header['remark'],
                                        'status' => '1',
                                        'updated_at' => Carbon::now()->format('Y-m-d H:i:s'),
                                    ];
                        $model->docApprovalOrderPartner()
                            ->where('order_partner_id', $orderPartnerId)
                            ->update($dataUpdate);

                        $model->docApprovalOrderPartnerDetail()
                            ->where('order_partner_id', $orderPartnerId)
                            ->update(['is_active' => '0']);

                        foreach($req->details as $rowDetails) {
                            $dataInsert = [
                                            'order_partner_id' => $orderPartnerId,
                                            'category_id' => $rowDetails['catId'],
                                            'category_name' => $rowDetails['catName'],
                                            'item_detail_id' => $rowDetails['prePoDetailId'],
                                            'item_id' => $rowDetails['itemId'],
                                            'item_name' => $rowDetails['itemName'],
                                            'unit_name' => $rowDetails['unit'],
                                            'quantity' => $rowDetails['qty'],
                                        ];
                            $model->docApprovalOrderPartnerDetail()->create($dataInsert);
                        }
                    }

                    $getMatrix = $model->docApprovalMatrix()
                                    ->from('doc_approval_matrix as a')
                                    ->select('a.employee_id_approval', 'a.matrix_as')
                                    ->where('a.employee_id', $employeeId)
                                    // ->where('a.company_id', '322')
                                    ->where('a.department_id', $getEmployee->department_id)
                                    ->where('a.doc_type_id', '1') // 1 = ORDER FORM
                                    ->where('a.is_active', '1')
                                    ->orderBy('a.matrix_id', 'asc')
                                    ->get();
                    if($getMatrix->isNotEmpty()) {
                        $approvalMatrixStatus = true;
                    }
                    else {
                        $approvalMatrixStatus = false;
                    }

                    return response()->json([
                        "status" => "success",
                        "data" => ["orderId" => $orderPartnerId, "employeeExist" => true, "approvalMatrix" => $approvalMatrixStatus]
                    ], 200);
                }
                else {
                    $dataInsert = [
                                    'order_type_id' => $req->header['prePoSource'],
                                    'order_date' => $req->header['reqDate'],
                                    'order_number' => $req->header['reqNo'],
                                    'employee_id' => $employeeId,
                                    'department_id' => $getEmployee->department_id,
                                    'department_name' => $getEmployee->department_name,
                                    'order_remarks' => $req->header['remark'],
                                    'status' => '1',
                                    'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                                ];
                    $insertHeader = $model->docApprovalOrderPartner()->create($dataInsert);
                    $orderPartnerId = $insertHeader->id;

                    foreach($req->details as $rowDetails) {
                        $dataInsert = [
                                        'order_partner_id' => $orderPartnerId,
                                        'category_id' => $rowDetails['catId'],
                                        'category_name' => $rowDetails['catName'],
                                        'item_detail_id' => $rowDetails['prePoDetailId'],
                                        'item_id' => $rowDetails['itemId'],
                                        'item_name' => $rowDetails['itemName'],
                                        'unit_name' => $rowDetails['unit'],
                                        'quantity' => $rowDetails['qty'],
                                    ];
                        $model->docApprovalOrderPartnerDetail()->create($dataInsert);
                    }

                    $getMatrix = $model->docApprovalMatrix()
                                    ->from('doc_approval_matrix as a')
                                    ->select('a.employee_id_approval', 'a.matrix_as')
                                    ->where('a.employee_id', $employeeId)
                                    // ->where('a.company_id', '322')
                                    ->where('a.department_id', $getEmployee->department_id)
                                    ->where('a.doc_type_id', '1') // 1 = ORDER FORM
                                    ->where('a.is_active', '1')
                                    ->orderBy('a.matrix_id', 'asc')
                                    ->get();
                    if($getMatrix->isNotEmpty()) {
                        $approvalMatrixStatus = true;
                        // TRIGGER GENERATE ORDER FORM APPROVAL
                        // $reqData = new Request();
                        // $reqData->replace([
                        //     'id' => $orderPartnerId,
                        // ]);
                        // $this->generateOrderForm($reqData);
                        // END TRIGGER GENERATE ORDER FORM APPROVAL
                    }
                    else {
                        // MATRIX APPROVAL NOT EXIST, SEND EMAIL TO ADMINISTRATOR
                        $approvalMatrixStatus = false;
                        $accountId = '3';
                        $to = 'it.cfb@logisteed.com';
                        $subject = 'Approval Matrix Order Form - Does not Exist';
                        $body = '<table style="border-collapse:collapse;border:0;width:100%;font-size:13px;line-height:1.7;">
                                    <tr>
                                        <th colspan="4" style="font-weight:normal;padding:10px 5px 20px 5px;text-align:left">Approval Matrix Order Form Employee '.$getEmployee->employee_name.' ('.$employeeId.') does not exist, please check.</th>
                                    </tr>
                                </table>';
                        $body .= "<div style='font-size:13px; margin-top:7px; margin-bottom:20px; display:block; font-style:italic'>This email was generated automatically by system, please don't reply this email.</div>";

                        $emailAccount = MasterEmailAccount::findOrFail($accountId);

                        // Create transport and mailer
                        $transport = new EsmtpTransport($emailAccount->smtp_host, $emailAccount->smtp_port, $emailAccount->smtp_encryption);
                        $transport->setUsername($emailAccount->username);
                        $transport->setPassword($emailAccount->password);

                        $mailer = new Mailer($transport);

                        // Create the email
                        $email = (new Email())
                            ->from(new Address($emailAccount->username, $emailAccount->display_name)) // Set email and display name correctly
                            ->to($to)
                            ->subject($subject)
                            ->html($body);

                        try {
                            // Send the email
                            $mailer->send($email);
                        }
                        catch (TransportExceptionInterface $e) {
                            // return response()->json(['message' => 'Failed to send email', 'error' => $e->getMessage()], 500);
                        }
                    }

                    return response()->json([
                        "status" => "success",
                        "data" => ["orderId" => $orderPartnerId, "employeeExist" => true, "approvalMatrix" => $approvalMatrixStatus]
                    ], 200);
                }
            }
            else {
                // MATRIX APPROVAL NOT EXIST, SEND EMAIL TO ADMINISTRATOR
                $dataInsert = [
                                'order_type_id' => $req->header['prePoSource'],
                                'order_date' => $req->header['reqDate'],
                                'order_number' => $req->header['reqNo'],
                                'employee_id' => $employeeId,
                                'order_remarks' => $req->header['remark'],
                                'status' => '1',
                                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                            ];
                $insertHeader = $model->docApprovalOrderPartner()->create($dataInsert);
                $orderPartnerId = $insertHeader->id;

                foreach($req->details as $rowDetails) {
                    $dataInsert = [
                                    'order_partner_id' => $orderPartnerId,
                                    'category_id' => $rowDetails['catId'],
                                    'category_name' => $rowDetails['catName'],
                                    'item_detail_id' => $rowDetails['prePoDetailId'],
                                    'item_id' => $rowDetails['itemId'],
                                    'item_name' => $rowDetails['itemName'],
                                    'unit_name' => $rowDetails['unit'],
                                    'quantity' => $rowDetails['qty'],
                                ];
                    $model->docApprovalOrderPartnerDetail()->create($dataInsert);
                }

                $accountId = '3';
                $to = 'it.cfb@logisteed.com';
                $subject = 'Employee ID Pre PO Carton - Does not Exist';
                $body = '<table style="border-collapse:collapse;border:0;width:100%;font-size:13px;line-height:1.7;">
                            <tr>
                                <th colspan="4" style="font-weight:normal;padding:10px 5px 20px 5px;text-align:left">Employee ID ('.$employeeId.') Pre PO Carton does not exist, please check.</th>
                            </tr>
                        </table>';
                $body .= "<div style='font-size:13px; margin-top:7px; margin-bottom:20px; display:block; font-style:italic'>This email was generated automatically by system, please don't reply this email.</div>";

                $emailAccount = MasterEmailAccount::findOrFail($accountId);

                // Create transport and mailer
                $transport = new EsmtpTransport($emailAccount->smtp_host, $emailAccount->smtp_port, $emailAccount->smtp_encryption);
                $transport->setUsername($emailAccount->username);
                $transport->setPassword($emailAccount->password);

                $mailer = new Mailer($transport);

                // Create the email
                $email = (new Email())
                    ->from(new Address($emailAccount->username, $emailAccount->display_name)) // Set email and display name correctly
                    ->to($to)
                    ->subject($subject)
                    ->html($body);

                try {
                    // Send the email
                    $mailer->send($email);
                }
                catch (TransportExceptionInterface $e) {
                    // return response()->json(['message' => 'Failed to send email', 'error' => $e->getMessage()], 500);
                }

                return response()->json([
                        "status" => "success",
                        "data" => ["orderId" => $orderPartnerId, "employeeExist" => false, "approvalMatrix" => false],
                    ], 200);
            }
        }
        catch (ValidationException $e) {
            return response()->json(["message" => "Please fill the required form.", "errors" => $e->errors()], 422)
                            ->setStatusCode(422, "Please fill the required form.");
        }
    }

    public function generateOrderForm(Request $req) {
        $orderPartnerId = $req->query('id');
        $model = new DocumentApprovalModel();
        $approvalMatrixStatus = false;
        $getOrderPartner = $model->docApprovalOrderPartner()
                                    ->select('*')
                                    ->where('order_partner_id', $orderPartnerId)
                                    ->where('is_active', '1')
                                    ->first();
        if($getOrderPartner) {
            $employeeId = $getOrderPartner->employee_id;
            $getMatrix = $model->docApprovalMatrix()
                                    ->from('doc_approval_matrix as a')
                                    ->select('a.employee_id_approval', 'a.matrix_as')
                                    ->where('a.employee_id', $employeeId)
                                    // ->where('a.company_id', '322')
                                    ->where('a.department_id', $getOrderPartner->department_id)
                                    ->where('a.doc_type_id', '1') // 1 = ORDER FORM
                                    ->where('a.is_active', '1')
                                    ->orderBy('a.matrix_id', 'asc')
                                    ->get();
            if($getMatrix->isNotEmpty()) {
                $approvalMatrixStatus = true;
                $checker_1 = $approver = null;
                foreach($getMatrix as $rowMatrix) {
                    if($rowMatrix->matrix_as == 'CHECKER_1') {
                        $checker_1 = $rowMatrix->employee_id_approval;
                    }
                    else if($rowMatrix->matrix_as == 'APPROVER') {
                        $approver = $rowMatrix->employee_id_approval;
                    }
                }

                $codeOrder = $itemOrder = $typeOrder = $qtyOrder = $unitOrder = $unitPriceOrder = $subTotalPriceOrder = $requiredDateOrder = array();
                $orderPartnerDetailId = array();
                $getOrderPartnerDetail = $model->docApprovalOrderPartnerDetail()
                                            ->select('*')
                                            ->where('order_partner_id', $orderPartnerId)
                                            ->where('is_active', '1')
                                            ->oldest('order_partner_detail_id')
                                            ->get();
                foreach ($getOrderPartnerDetail as $rowDetails) {
                    $orderPartnerDetailId[] = $rowDetails->order_partner_detail_id;
                    $codeOrder[] = '50202000';
                    $itemOrder[] = $rowDetails->item_name;
                    $typeOrder[] = $rowDetails->category_name;
                    $qtyOrder[] = $rowDetails->quantity;
                    $unitOrder[] = 2; // 2 = PCS
                    $unitPriceOrder[] = 0;
                    $subTotalPriceOrder[] = 0;
                    $requiredDateOrder[] = Carbon::parse($getOrderPartner->order_date)->format('d-M-Y');
                }

                $reqData = new Request();
                $reqData->replace([
                                        'requestType' => 'NEW',
                                        'documentType' => '1',
                                        'preparedBy' => SafeToken::encode(['id' => $employeeId]),
                                        'companyOrder' => SafeToken::encode(['c' => '322']),
                                        'departmentOrder' => SafeToken::encode(['d' => $getOrderPartner->department_id]),
                                        'locationOrder' => 'HEAD OFFICE',
                                        'seqNumber' => "PR-322-".Carbon::now()->format('y')."########",
                                        'requestDate' => Carbon::parse($getOrderPartner->order_date)->format('d-M-Y'),
                                        'currencyOrder' => 'IDR',
                                        'priorityLevelOrder' => '3', // 3 = NORMAL
                                        'checker_1' => $checker_1,
                                        'checker_2' => null,
                                        'approver' => $approver,
                                        'cc' => null,
                                        'orderPartnerId' => $orderPartnerId,
                                        'orderPartnerDetailId' => $orderPartnerDetailId,
                                        'codeOrder' => $codeOrder,
                                        'itemOrder' => $itemOrder,
                                        'typeOrder' => $typeOrder,
                                        'qtyOrder' => $qtyOrder,
                                        'unitOrder' => $unitOrder,
                                        'unitPriceOrder' => $unitPriceOrder,
                                        'subTotalPriceOrder' => $subTotalPriceOrder,
                                        'requiredDateOrder' => $requiredDateOrder,
                                        'purposeId' => ['3'], // 3 = Routine purchase
                                        'reasonOrder' => ['Pre PO Carton'],
                                        'remarksOrder' => [''],
                                        'actionType' => 'SUBMIT',
                                        'attachmentFile' => [],
                                    ]);
                $documentApprovalController = new ControllersDocumentApprovalController();
                $documentApprovalController->saveForm($reqData);
            }
            else {
                // MATRIX APPROVAL NOT EXIST, SEND EMAIL TO ADMINISTRATOR
                 $getEmployee = $model->vwMasterEmployeeActive()
                                    ->select('department_id', 'department_name', 'employee_name')
                                    ->where('employee_id', $employeeId)
                                    // ->where('company_id', '322') // VANTEC
                                    ->first();
                $approvalMatrixStatus = false;
                $accountId = '3';
                $to = 'it.cfb@logisteed.com';
                $subject = 'Approval Matrix Order Form - Does not Exist';
                $body = '<table style="border-collapse:collapse;border:0;width:100%;font-size:13px;line-height:1.7;">
                            <tr>
                                <th colspan="4" style="font-weight:normal;padding:10px 5px 20px 5px;text-align:left">Approval Matrix Order Form Employee '.$getEmployee->employee_name.' ('.$employeeId.') does not exist, please check.</th>
                            </tr>
                        </table>';
                $body .= "<div style='font-size:13px; margin-top:7px; margin-bottom:20px; display:block; font-style:italic'>This email was generated automatically by system, please don't reply this email.</div>";

                $emailAccount = MasterEmailAccount::findOrFail($accountId);

                // Create transport and mailer
                $transport = new EsmtpTransport($emailAccount->smtp_host, $emailAccount->smtp_port, $emailAccount->smtp_encryption);
                $transport->setUsername($emailAccount->username);
                $transport->setPassword($emailAccount->password);

                $mailer = new Mailer($transport);

                // Create the email
                $email = (new Email())
                    ->from(new Address($emailAccount->username, $emailAccount->display_name)) // Set email and display name correctly
                    ->to($to)
                    ->subject($subject)
                    ->html($body);

                try {
                    // Send the email
                    $mailer->send($email);
                }
                catch (TransportExceptionInterface $e) {
                    // return response()->json(['message' => 'Failed to send email', 'error' => $e->getMessage()], 500);
                }
            }
        }

        return response()->json([
            "status" => "success",
            "data" => ["orderId" => $orderPartnerId, "employeeExist" => true, "approvalMatrix" => $approvalMatrixStatus]
        ], 200);
    }
}
