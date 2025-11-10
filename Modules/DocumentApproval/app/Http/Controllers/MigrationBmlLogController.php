<?php

namespace Modules\DocumentApproval\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Jobs\ConvertFileJob;
use App\Jobs\SendEmailJob;
use App\Services\SafeToken;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Modules\DocumentApproval\Models\MigrationBmlLogModel;
use Modules\DocumentApproval\Http\Controllers\DocumentApprovalController as ControllersDocumentApprovalController;

class MigrationBmlLogController extends Controller
{
    // public function migration(Request $request) {
    //     $model = new MigrationBmlLogModel();

    //     $companyId = '317';
    //     $companyName = 'PT BERDIRI MATAHARI LOGISTIK';

    //     $fromId = 1;
    //     $toId = 50;

    //     $getHeader = $model->documentApprovalHeaderOld()
    //                     ->from('doc_approval_header_old AS a')
    //                     ->select('a.*')
    //                     ->distinct()
    //                     ->oldest('a.doc_approval_id')
    //                     ->where('a.doc_approval_id', '>=', $fromId)
    //                     ->where('a.doc_approval_id', '<=', $toId)
    //                     ->get();
    //     foreach($getHeader as $rowHeader) {
    //         $dataInsert = [
    //                         'doc_approval_id_old' => $rowHeader->doc_approval_id,
    //                         'employee_id' => $rowHeader->employee_nik,
    //                         'company_id' => $companyId,
    //                         'company_name' => $companyName,
    //                         'doc_type_id' => $rowHeader->doc_type_id,
    //                         'doc_name' => $rowHeader->doc_name,
    //                         'year' => $rowHeader->year,
    //                         'seq_id' => $rowHeader->seqId,
    //                         'doc_number' => $rowHeader->doc_number,
    //                         'doc_status_id' => $rowHeader->doc_status_id,
    //                         'form_status_id' => $rowHeader->form_status_id,
    //                         'final_decision_at' => $rowHeader->final_decision_at,
    //                         'department_id' => $rowHeader->department_id,
    //                         'department_name' => $rowHeader->department_name,
    //                         'location_id' => $rowHeader->location_id,
    //                         'submitted_at' => $rowHeader->submitted_at,
    //                         'priority' => '3',
    //                         'priority_name' => 'NORMAL',
    //                         'is_completed' => $rowHeader->is_completed,
    //                         'is_active' => $rowHeader->is_active,
    //                     ];
    //         $insertHeader = $model->documentApprovalHeader()->create($dataInsert);
    //         $docApprovalIdNew = $insertHeader->id;

    //         if($rowHeader->doc_type_id == '1') { // 1 = ORDER FORM
    //             $getForm = $model->documentApprovalDetailOrderOld()
    //                             ->from('doc_approval_detail_order_form_old as a')
    //                             ->select('a.*')
    //                             ->where('a.doc_approval_id', '=', $rowHeader->doc_approval_id)
    //                             ->oldest('a.order_form_id')
    //                             ->get();

    //             foreach($getForm as $rowForm) {
    //                 $dataInsert = [
    //                     'order_form_id_old' => $rowForm->order_form_id,
    //                     'doc_approval_id' => $docApprovalIdNew,
    //                     'company_id' => $companyId,
    //                     'employee_id' => $rowForm->employee_nik,
    //                     'year' => $rowForm->year,
    //                     'order_form_number' => $rowForm->order_form_number,
    //                     'doc_version' => $rowForm->doc_version,
    //                     'order_form_status' => $rowForm->order_form_status,
    //                     'department_id' => $rowForm->final_decision_at,
    //                     'location_id' => $rowForm->location_id,
    //                     'request_date'=> $rowForm->request_date,
    //                     'currency_code' => $rowForm->currency_code,
    //                     'priority' => '3',
    //                     'priority_name' => 'NORMAL',
    //                     'all_items' => $rowForm->all_items,
    //                     'grand_total' => $rowForm->grand_total,
    //                     'vat_rate' => $rowForm->vat_rate,
    //                     'vat_value' => $rowForm->vat_value,
    //                     'created_at' => $rowForm->created_at,
    //                     'created_by' => $rowForm->created_by,
    //                     'path_detail' => Str::replace('purchasing', 'purchasing_old', $rowForm->path_detail),
    //                     'filename' => $rowForm->filename,
    //                     'final_decision_at' => $rowForm->final_decision_at,
    //                     'item_completed' => $rowForm->item_completed,
    //                     'updated_by' => $rowForm->updated_by,
    //                     'updated_at' => $rowForm->updated_at,
    //                     'is_archived' => $rowForm->is_archived,
    //                     'is_active' => $rowForm->is_active,
    //                 ];
    //                 $insertDetail = $model->documentApprovalDetailOrder()->create($dataInsert);
    //                 $orderFormIdNew = $insertDetail->id;

    //                 // ORDER ITEM
    //                 $getFormItem = $model->documentApprovalDetailOrderItemOld()
    //                                     ->from('doc_approval_detail_order_form_item_old as a')
    //                                     ->select('a.*')
    //                                     ->oldest('a.order_detail_id')
    //                                     ->where('a.doc_approval_id', '=', $rowHeader->doc_approval_id)
    //                                     ->where('a.order_form_id', '=', $rowForm->order_form_id)
    //                                     ->get();
    //                 foreach($getFormItem as $rowFormItem) {
    //                     $dataInsert = [
    //                         'order_detail_id_old' => $rowFormItem->order_detail_id,
    //                         'comparison_id_old' => $rowFormItem->comparison_id,
    //                         'application_id_old' => $rowFormItem->application_id,
    //                         'doc_approval_id' => $docApprovalIdNew,
    //                         'company_id' => $companyId,
    //                         'order_form_id' => $orderFormIdNew,
    //                         'cost_center_id' => null,
    //                         'cost_center' => $rowFormItem->cost_center,
    //                         'appliance_item' => $rowFormItem->appliance_item,
    //                         'brand_type' => $rowFormItem->brand_type,
    //                         'unit_id' => $rowFormItem->unit_id,
    //                         'unit_name' => $rowFormItem->unit_name,
    //                         'unit_quantity' => $rowFormItem->unit_quantity,
    //                         'currency_code' => $rowFormItem->currency_code,
    //                         'unit_price_estimated' => $rowFormItem->unit_price_estimated,
    //                         'total_price_estimated' => $rowFormItem->total_price_estimated,
    //                         'required_date' => $rowFormItem->required_date,
    //                         'created_by' => $rowFormItem->created_by,
    //                         'created_at' => $rowFormItem->created_at,
    //                         'is_active' => $rowFormItem->is_active,
    //                     ];
    //                     $model->documentApprovalDetailOrderItem()->create($dataInsert);
    //                 }
    //                 // END ORDER ITEM

    //                 // CANCEL ITEM
    //                 // $getCancelItem = $model->documentApprovalDetailOrderItemCancelOld()
    //                 //                     ->from('doc_approval_detail_order_form_item_canceled_old as a')
    //                 //                     ->select('a.*')
    //                 //                     ->where('a.doc_approval_id', '=', $rowHeader->doc_approval_id)
    //                 //                     ->where('a.order_form_id', '=', $rowForm->order_form_id)
    //                 //                     ->oldest('a.canceled_id')
    //                 //                     ->get();
    //                 // foreach($getCancelItem as $rowCancelItem) {
    //                 //     $dataInsert = [
    //                 //         'order_form_id' => $orderFormIdNew,
    //                 //         'company_id' => $companyId,
    //                 //         'reason' => $rowCancelItem->reason,
    //                 //         'created_by' => $employeeId,
    //                 //         'created_at' => $datetimeAt,
    //                 //     ];
    //                 //     $insertCanceled = $modelProcurement->docApprovalDetailOrderFormItemCanceled()->create($dataInsert);
    //                 //     $canceledId = $insertCanceled->id;
    //                 // }
    //                 // CANCEL ITEM

    //                 // PURPOSE
    //                 $getPurpose = $model->documentApprovalDetailOrderPurposeOld()
    //                                     ->from('doc_approval_detail_order_form_purpose_old as a')
    //                                     ->select('a.*')
    //                                     ->where('a.doc_approval_id', '=', $rowHeader->doc_approval_id)
    //                                     ->where('a.order_form_id', '=', $rowForm->order_form_id)
    //                                     ->oldest('a.order_purpose_id')
    //                                     ->get();
    //                 foreach($getPurpose as $rowPurpose) {
    //                     $dataInsert = [
    //                         'doc_approval_id' => $docApprovalIdNew,
    //                         'company_id' => $companyId,
    //                         'order_form_id' => $orderFormIdNew,
    //                         'purpose_id' => $rowPurpose->purpose_id,
    //                         'description' => $rowPurpose->description,
    //                         'reason_id' => $rowPurpose->reason_id,
    //                         'reason' => $rowPurpose->reason,
    //                         'remarks' => $rowPurpose->remarks,
    //                         'is_active' => $rowPurpose->is_active,
    //                     ];
    //                     $model->documentApprovalDetailOrderPurpose()->create($dataInsert);
    //                 }
    //                 // END PURPOSE

    //                 // ATTACHMENT
    //                 $getAttachment = $model->docApprovalAttachmentOld()
    //                                 ->from('doc_approval_attachment_old as a')
    //                                 ->select('a.*')
    //                                 ->oldest('a.attachment_id')
    //                                 ->where('a.doc_approval_id', '=', $rowHeader->doc_approval_id)
    //                                 ->where('a.doc_type_id', '=', $rowHeader->doc_type_id)
    //                                 ->where('a.reference_id', '=', $rowForm->order_form_id)
    //                                 ->get();
    //                 foreach($getAttachment as $rowAttachment) {
    //                     $dataInsert = [
    //                             'doc_approval_id' => $docApprovalIdNew,
    //                             'company_id' => '317',
    //                             'doc_type_id' => $rowAttachment->doc_type_id,
    //                             'reference_id' => $orderFormIdNew,
    //                             'path_detail' => Str::replace('purchasing', 'purchasing_old', $rowAttachment->path_detail),
    //                             'filename' => $rowAttachment->filename,
    //                             'filename_original' => $rowAttachment->filename_original,
    //                             'converted' => $rowAttachment->converted,
    //                             'filename_converted' => $rowAttachment->filename_converted,
    //                             'mime_type' => $rowAttachment->mime_type,
    //                             'downloadable' => $rowAttachment->downloadable,
    //                         ];
    //                     $insertAttachment = $model->docApprovalAttachment()->create($dataInsert);
    //                 }
    //                 // END ATTACHMENT

    //                 // SIGNER
    //                 $getSigner = $model->docApprovalFlowSignOld()
    //                                 ->from('doc_approval_flow_sign_old as a')
    //                                 ->select('a.*')
    //                                 ->whereNotNull('a.employee_nik')
    //                                 ->where('a.doc_approval_id', '=', $rowHeader->doc_approval_id)
    //                                 ->where('a.doc_type_id', '=', $rowHeader->doc_type_id)
    //                                 ->where('a.reference_id', '=', $rowForm->order_form_id)
    //                                 ->where('a.is_active', '1')
    //                                 ->oldest('a.sign_flow_id')
    //                                 ->get();
    //                 foreach($getSigner as $rowSigner) {
    //                     if($rowSigner->flow_as === 'RECEIVER') {
    //                         if($rowSigner->employee_nik !== '114000191') {
    //                             continue;
    //                         }
    //                     }

    //                     if($rowSigner->flow_as === 'CHECKER') {
    //                         $flowAs = 'CHECKER_'.$rowSigner->order_flow;
    //                     }
    //                     else {
    //                         $flowAs = $rowSigner->flow_as;
    //                     }

    //                     $dataInsert = [
    //                         'employee_id' => $rowSigner->employee_nik,
    //                         'company_id' => $companyId,
    //                         'flow_as' => $flowAs,
    //                         'status' => $rowSigner->status,
    //                         'doc_approval_id' => $docApprovalIdNew,
    //                         'doc_type_id' => $rowSigner->doc_type_id,
    //                         'reference_id' => $orderFormIdNew,
    //                         'received_at' => $rowSigner->received_at,
    //                         'decision_at' => $rowSigner->decision_at,
    //                         'is_active' => $rowSigner->is_active,
    //                     ];
    //                     $insertFlowSign = $model->docApprovalFlowSign()->create($dataInsert);
    //                     $signFlowIdNew = $insertFlowSign->id;

    //                     $getComment = $model->docApprovalFlowCommentOld()
    //                                         ->select('*')
    //                                         ->where('sign_flow_id', '=', $rowSigner->sign_flow_id)
    //                                         ->where('is_active', '1')
    //                                         ->oldest('comment_id')
    //                                         ->get();
    //                     foreach ($getComment as $rowComment) {
    //                         $dataInsert = [
    //                             'employee_id' => $rowSigner->employee_nik,
    //                             'company_id' => $companyId,
    //                             'sign_flow_id' => $signFlowIdNew,
    //                             'comment' => $rowComment->comment,
    //                             'comment_at' => $rowComment->comment_at,
    //                             'is_main' => $rowComment->is_main,
    //                             'is_reply' => $rowComment->is_reply,
    //                             'is_active' => $rowComment->is_active,
    //                         ];
    //                         $insertComment = $model->docApprovalFlowComment()->create($dataInsert);
    //                     }
    //                 }
    //                 // END SIGNER

    //                 // COMPARISON
    //                 $getComparison = $model->docApprovalOrderComparisonOld()
    //                                 ->from('doc_approval_order_comparison_old as a')
    //                                 ->leftJoin('master_vendor as b', 'b.vendor_id_old', '=', 'a.vendor_id_selected')
    //                                 ->select('a.*', 'b.vendor_id as vendor_id_new')
    //                                 ->oldest('a.comparison_id')
    //                                 ->where('a.doc_approval_id', '=', $rowHeader->doc_approval_id)
    //                                 ->where('a.order_form_id', '=', $rowForm->order_form_id)
    //                                 ->get();

    //                 foreach($getComparison as $rowComparison) {
    //                     $dataInsert = [
    //                         'comparison_id_old' => $rowComparison->comparison_id,
    //                         'doc_approval_id' => $docApprovalIdNew,
    //                         'company_id' => $companyId,
    //                         'order_form_id' => $orderFormIdNew,
    //                         'comparison_title' => $rowComparison->comparison_title,
    //                         'doc_version' => $rowComparison->doc_version,
    //                         'comparison_form_status' => $rowComparison->comparison_form_status,
    //                         'comparison_description' => $rowComparison->comparison_description,
    //                         'comparison_date' => $rowComparison->comparison_date,
    //                         'vendor_id_selected' => $rowComparison->vendor_id_new,
    //                         'comparison_notes' => $rowComparison->comparison_notes,
    //                         'created_by' => $rowComparison->created_by,
    //                         'created_at' => $rowComparison->created_at,
    //                         'updated_by' => $rowComparison->updated_by,
    //                         'updated_at' => $rowComparison->updated_at,
    //                         'path_detail' => Str::replace('purchasing', 'purchasing_old', $rowComparison->path_detail),
    //                         'filename' => $rowComparison->filename,
    //                         'year_form' => $rowComparison->year_form,
    //                         'is_active' => $rowComparison->is_active,
    //                     ];
    //                     $insertHeader = $model->docApprovalOrderComparison()->create($dataInsert);
    //                     $comparisonIdNew = $insertHeader->id;

    //                     // ATTACHMENT
    //                     $getAttachment = $model->docApprovalAttachmentOld()
    //                                     ->from('doc_approval_attachment_old as a')
    //                                     ->select('a.*')
    //                                     ->oldest('a.attachment_id')
    //                                     ->where('a.doc_approval_id', '=', $rowHeader->doc_approval_id)
    //                                     ->where('a.doc_type_id', '=', '6') // 6 = COMPARISON FORM
    //                                     ->where('a.reference_id', '=', $rowComparison->comparison_id)
    //                                     ->get();
    //                     foreach($getAttachment as $rowAttachment) {
    //                         $dataInsert = [
    //                                 'doc_approval_id' => $docApprovalIdNew,
    //                                 'company_id' => '317',
    //                                 'doc_type_id' => $rowAttachment->doc_type_id,
    //                                 'reference_id' => $comparisonIdNew,
    //                                 'path_detail' => Str::replace('purchasing', 'purchasing_old', $rowAttachment->path_detail),
    //                                 'filename' => $rowAttachment->filename,
    //                                 'filename_original' => $rowAttachment->filename_original,
    //                                 'converted' => $rowAttachment->converted,
    //                                 'filename_converted' => $rowAttachment->filename_converted,
    //                                 'mime_type' => $rowAttachment->mime_type,
    //                                 'downloadable' => $rowAttachment->downloadable,
    //                             ];
    //                         $insertAttachment = $model->docApprovalAttachment()->create($dataInsert);
    //                     }
    //                     // END ATTACHMENT

    //                     // CREATE ORDER GROUP
    //                     $dataInsert = [
    //                         'company_id' => $companyId,
    //                         'order_form_id' => $orderFormIdNew,
    //                         'comparison_id' => $comparisonIdNew,
    //                     ];
    //                     $insertOrderGroup = $model->documentApprovalOrderGroup()->create($dataInsert);
    //                     $orderGroupIdNew = $insertOrderGroup->id;
    //                     // END CREATE ORDER GROUP

    //                     $getComparisonItem = $model->docApprovalOrderComparisonItemOld()
    //                                             ->from('doc_approval_order_comparison_item_old as a')
    //                                             ->select('a.*')
    //                                             ->oldest('a.comparison_item_id')
    //                                             ->where('a.order_form_id', '=', $rowForm->order_form_id)
    //                                             ->where('a.comparison_id', '=', $rowComparison->comparison_id)
    //                                             ->get();
    //                     foreach($getComparisonItem as $rowComparisonItem) {
    //                         $dataInsert = [
    //                             'comparison_item_id_old' => $rowComparisonItem->comparison_item_id,
    //                             'order_form_id' => $orderFormIdNew,
    //                             'company_id' => $companyId,
    //                             'order_detail_id' => null,
    //                             'comparison_id' => $comparisonIdNew,
    //                             'appliance_item' => $rowComparisonItem->appliance_item,
    //                             'unit_name' => $rowComparisonItem->unit_name,
    //                             'unit_quantity' => $rowComparisonItem->unit_quantity,
    //                             'currency' => $rowComparisonItem->currency,
    //                             'is_active' => $rowComparisonItem->is_active,
    //                         ];
    //                         $insertItem = $model->docApprovalOrderComparisonItem()->create($dataInsert);
    //                         $comparisonItemIdNew = $insertItem->id;

    //                         $dataUpdate = [
    //                             'order_group_id' => $orderGroupIdNew,
    //                             'comparison_id' => $comparisonIdNew,
    //                             'vendor_id' => $rowComparison->vendor_id_new,
    //                             'comparison_item_id' => $comparisonItemIdNew,
    //                             // 'application_id' => null,
    //                             // 'revise_order_detail_id' => null,
    //                         ];

    //                         $model->documentApprovalDetailOrderItem()
    //                             ->where('order_detail_id_old', $rowComparisonItem->order_detail_id)
    //                             ->where('order_form_id', $orderFormIdNew)
    //                             ->where('comparison_id_old', $rowComparison->comparison_id)
    //                             ->update($dataUpdate);
    //                     }

    //                     $getComparisonVendor = $model->docApprovalOrderComparisonVendorOld()
    //                                                 ->from('doc_approval_order_comparison_vendor_old as a')
    //                                                 ->leftJoin('master_vendor as b', 'b.vendor_id_old', '=', 'a.vendor_id')
    //                                                 ->leftJoin('master_vendor_address as c', 'c.vendor_id', '=', 'b.vendor_id')
    //                                                 ->leftJoin('master_vendor_pic as d', 'd.vendor_id', '=', 'b.vendor_id')
    //                                                 ->select('a.*', 'b.vendor_id as vendor_id_new', 'c.vendor_address_id as vendor_address_id_new', 'd.vendor_pic_id as vendor_pic_id_new')
    //                                                 ->oldest('a.comparison_vendor_id')
    //                                                 ->where('a.comparison_id', '=', $rowComparison->comparison_id)
    //                                                 // ->where('a.comparison_id', '=', $rowComparison->comparison_id)
    //                                                 ->get();
    //                     foreach ($getComparisonVendor as $rowComparisonVendor) {
    //                         $dataInsert = [
    //                             'comparison_id' => $comparisonIdNew,
    //                             'company_id' => $companyId,
    //                             'doc_approval_id' => $docApprovalIdNew,
    //                             'order_form_id' => $orderFormIdNew,
    //                             'vendor_id' => $rowComparisonVendor->vendor_id_new,
    //                             'vendor_name' => $rowComparisonVendor->vendor_name,
    //                             'vendor_address_id' => $rowComparisonVendor->vendor_address_id_new,
    //                             'vendor_address' => $rowComparisonVendor->vendor_address,
    //                             'vendor_pic_id' => $rowComparisonVendor->vendor_pic_id_new,
    //                             'pic_name' => $rowComparisonVendor->pic_name,
    //                             'quotation_validity' => null,
    //                             'is_active' => $rowComparisonVendor->is_active,
    //                         ];
    //                         $insertVendor = $model->docApprovalOrderComparisonVendor()->create($dataInsert);
    //                         $comparisonVendorIdNew = $insertVendor->id;

    //                         $getComparisonItemVendor = $model->docApprovalOrderComparisonItemVendorOld()
    //                                                         ->from('doc_approval_order_comparison_item_vendor_old as a')
    //                                                         ->leftJoin('doc_approval_order_comparison_item as b', 'b.comparison_item_id_old', '=', 'a.comparison_item_id')
    //                                                         ->select('a.*', 'b.comparison_item_id as comparison_item_id_new')
    //                                                         ->oldest('a.comparison_item_vendor_id')
    //                                                         ->where('a.comparison_id', '=', $rowComparison->comparison_id)
    //                                                         // ->where('a.comparison_id', '=', $rowComparison->comparison_id)
    //                                                         ->get();
    //                         foreach($getComparisonItemVendor as $rowComparisonItemVendor) {
    //                             $dataInsert = [
    //                                         'company_id' => $companyId,
    //                                         'comparison_id' => $comparisonIdNew,
    //                                         'comparison_item_id' =>  $rowComparisonItemVendor->comparison_item_id_new,
    //                                         'comparison_vendor_id' => $comparisonVendorIdNew,
    //                                         'currency_code' => $rowComparisonItemVendor->currency_code,
    //                                         'unit_price' => $rowComparisonItemVendor->unit_price,
    //                                         'dpp_other_value' => $rowComparisonItemVendor->dpp_other_value,
    //                                         'total_price' => $rowComparisonItemVendor->total_price,
    //                                         'is_active' => $rowComparisonItemVendor->is_active,
    //                                     ];
    //                             $insertItemVendor = $model->docApprovalOrderComparisonItemVendor()->create($dataInsert);
    //                         }
    //                     }
    //                 }
    //                 // END COMPARISON
    //             }
    //         }
    //         else if($rowHeader->doc_type_id == '2') { // 2 = APPLICATION & PO FORM
    //             $getForm = $model->docApprovalOrderApplicationOld()
    //                             ->from('doc_approval_order_application_old as a')
    //                             ->leftJoin('doc_approval_detail_order_form as b', 'b.order_form_id_old', '=', 'a.order_form_id')
    //                             ->leftJoin('doc_approval_order_comparison as c', 'c.comparison_id_old', '=', 'a.comparison_id')
    //                             ->leftJoin('master_vendor as d', 'd.vendor_id_old', '=', 'a.vendor_id')
    //                             ->leftJoin('master_vendor_pic as e', 'e.vendor_id', '=', 'd.vendor_id')
    //                             ->leftJoin('master_vendor_address as f', 'f.vendor_id', '=', 'd.vendor_id')
    //                             ->leftJoin('doc_approval_order_group as g', function($join) {
    //                                             $join->on('g.comparison_id', '=', 'c.comparison_id');
    //                                             $join->on('g.order_form_id', '=', 'b.order_form_id');
    //                                         })
    //                             ->select('a.*', 'b.order_form_id as order_form_id_new', 'c.comparison_id as comparison_id_new', 'd.vendor_id as vendor_id_new', 'd.vendor_account', 'e.vendor_pic_id as vendor_pic_id_new', 'f.vendor_address_id as vendor_address_id_new', 'g.order_group_id')
    //                             ->oldest('a.application_id')
    //                             ->where('a.doc_approval_id', '=', $rowHeader->doc_approval_id)
    //                             ->get();

    //             foreach($getForm as $rowForm) {
    //                 $openOrderAt = null;
    //                 $isArchived = ($rowForm->application_form_status == '22' || $rowForm->application_form_status == '9') ? '1' : '0';
    //                 if($rowForm->application_form_status == '8' || $rowForm->application_form_status == '21' || $rowForm->application_form_status == '22') {
    //                     $openOrderAt = Carbon::parse($rowForm->final_decision_at)->format('Y-m-d');
    //                 }

    //                 $dataInsert = [
    //                     'application_id_old' => $rowForm->application_id,
    //                     'doc_approval_id' => $docApprovalIdNew,
    //                     'company_id' => $companyId,
    //                     'order_form_id' => $rowForm->order_form_id_new,
    //                     'comparison_id' => $rowForm->comparison_id_new,
    //                     'application_form_status' => $rowForm->application_form_status,
    //                     'final_decision_at' => $rowForm->final_decision_at,
    //                     'doc_version' => $rowForm->doc_version,
    //                     'application_header' => $rowForm->application_header,
    //                     'year' => $rowForm->year,
    //                     'application_number' => $rowForm->application_number,
    //                     'application_title' => $rowForm->application_title,
    //                     'application_estimate' => $rowForm->application_estimate,
    //                     'application_submitted_date' => $rowForm->application_submitted_date,
    //                     'application_currency' => $rowForm->application_currency,

    //                     'vendor_id' => $rowForm->vendor_id_new,
    //                     'vendor_account' => $rowForm->vendor_account,
    //                     'vendor_name' => $rowForm->vendor_name,
    //                     'vendor_pic_id' => $rowForm->vendor_pic_id_new,
    //                     'vendor_pic' => $rowForm->application_vendor_pic,
    //                     'vendor_address_id' => $rowForm->vendor_address_id_new,
    //                     'vendor_address' => $rowForm->vendor_address,
    //                     'delivery_to_id' => $rowForm->delivery_to_id,
    //                     'application_delivery' => $rowForm->application_delivery,

    //                     'invoice_to_id' => '18',
    //                     'application_invoice' => $rowForm->application_invoice,
    //                     'application_grand_total' => $rowForm->application_grand_total,
    //                     'purchase_type_id' => $rowForm->purchase_type_id,
    //                     'purchase_type' => $rowForm->purchase_type,
    //                     'form_type' => $rowForm->form_type,
    //                     'budget_no' => $rowForm->budget_no,
    //                     'budget_amount' => $rowForm->budget_amount,
    //                     'budget_within_over' => $rowForm->budget_within_over,
    //                     'budget_within' => $rowForm->budget_within,
    //                     'reimburse_to' => $rowForm->reimburse_to,
    //                     'reimburse_to_next' => $rowForm->reimburse_to_next,
    //                     'payment_type' => '1',
    //                     'ship_date' => $rowForm->ship_date,
    //                     'application_remark' => $rowForm->application_remark,
    //                     'application_reason' => $rowForm->application_reason,
    //                     'path_detail' => Str::replace('purchasing', 'purchasing_old', $rowForm->path_detail),
    //                     'filename' => $rowForm->filename,
    //                     'created_by' => $rowForm->created_by,
    //                     'created_at' => $rowForm->created_at,
    //                     'updated_by' => $rowForm->updated_by,
    //                     'updated_at' => $rowForm->updated_at,

    //                     'open_order_by' => $rowForm->received_order_by,
    //                     'open_order_at' => $openOrderAt,
    //                     'received_order_by' => $rowForm->received_order_by,
    //                     'received_order_at' => $rowForm->received_order_at,
    //                     'invoiced_order_by' => $rowForm->invoiced_order_by,
    //                     'invoiced_order_at' => $rowForm->invoiced_order_at,

    //                     'priority' => '3',
    //                     'priority_name' => 'NORMAL',
    //                     'is_archived' => $isArchived,
    //                     'is_active' => $rowForm->is_active,
    //                 ];
    //                 $insertHeader = $model->docApprovalOrderApplication()->create($dataInsert);
    //                 $applicationIdNew = $insertHeader->id;

    //                 // COST CENTER
    //                 $getCostCenter = $model->docApprovalOrderApplicationCostCenterOld()
    //                                     ->select('*')
    //                                     ->where('application_id', '=', $rowForm->application_id)
    //                                     ->oldest('application_cost_center_id')
    //                                     ->get();
    //                 foreach($getCostCenter as $rowCostCenter) {
    //                     $dataInsert = [
    //                         'application_id' => $applicationIdNew,
    //                         'company_id' => $companyId,
    //                         'order_form_id' => $rowForm->order_form_id_new,
    //                         'cost_center_id' => $rowCostCenter->cost_center,
    //                         'cost_center' => $rowCostCenter->cost_center,
    //                         'percentage' => $rowCostCenter->percentage,
    //                         'is_active' => $rowCostCenter->is_active,
    //                     ];
    //                     $model->docApprovalOrderApplicationCostCenter()->create($dataInsert);
    //                 }
    //                 // END COST CENTER

    //                 // APPLICATION ITEM
    //                 $createdGroup = false;
    //                 $getFormItem = $model->docApprovalOrderApplicationItemOld()
    //                                     ->from('doc_approval_order_application_item_old as a')
    //                                     ->leftJoin('doc_approval_detail_order_form_item as b', 'b.order_detail_id_old', '=', 'a.order_detail_id')
    //                                     ->select('a.*', 'b.order_detail_id as order_detail_id_new')
    //                                     ->where('a.application_id', '=', $rowForm->application_id)
    //                                     ->oldest('a.application_item_id')
    //                                     ->get();
    //                 foreach($getFormItem as $rowFormItem) {
    //                     $dataInsert = [
    //                         'order_form_id' => $rowForm->order_form_id_new,
    //                         'company_id' => $companyId,
    //                         'application_id' => $applicationIdNew,
    //                         'order_detail_id' => $rowFormItem->order_detail_id_new,
    //                         'appliance_item' => $rowFormItem->appliance_item,
    //                         'quantity' => $rowFormItem->quantity,
    //                         'currency' => $rowFormItem->currency,
    //                         'unit_price' => $rowFormItem->unit_price,
    //                         'dpp_other_value' => $rowFormItem->dpp_other_value,
    //                         'subtotal_price' => $rowFormItem->subtotal_price,
    //                         'is_active' => $rowFormItem->is_active,
    //                     ];
    //                     $model->docApprovalOrderApplicationItem()->create($dataInsert);

    //                     // if(!empty($rowFormItem->order_group_id_new)) {

    //                     //     $dataUpdate = [
    //                     //                     'application_id' => $applicationIdNew,
    //                     //                     // 'is_need_revision' => '0',
    //                     //                     // 'is_canceled' => '0',
    //                     //                     // 'is_rejected' => '0',
    //                     //                 ];

    //                     //     $modelProcurement->documentApprovalOrderGroup()
    //                     //                     ->where('order_form_id', $orderFormId)
    //                     //                     ->where('order_group_id', $orderGroupId)
    //                     //                     ->where('is_active', '1')
    //                     //                     ->update($dataUpdate);
    //                     // }
    //                     // else {
    //                     //     $createdGroup = false;
    //                     // }
    //                 }
    //                 // END APPLICATION ITEM

    //                 // ATTACHMENT
    //                 $getAttachment = $model->docApprovalAttachmentOld()
    //                                 ->from('doc_approval_attachment_old as a')
    //                                 ->select('a.*')
    //                                 ->oldest('a.attachment_id')
    //                                 ->where('a.doc_approval_id', '=', $rowHeader->doc_approval_id)
    //                                 ->where('a.doc_type_id', '=', $rowHeader->doc_type_id)
    //                                 ->where('a.reference_id', '=', $rowForm->application_id)
    //                                 ->get();
    //                 foreach($getAttachment as $rowAttachment) {
    //                     $dataInsert = [
    //                             'doc_approval_id' => $docApprovalIdNew,
    //                             'company_id' => '317',
    //                             'doc_type_id' => $rowAttachment->doc_type_id,
    //                             'reference_id' => $applicationIdNew,
    //                             'path_detail' => Str::replace('purchasing', 'purchasing_old', $rowAttachment->path_detail),
    //                             'filename' => $rowAttachment->filename,
    //                             'filename_original' => $rowAttachment->filename_original,
    //                             'converted' => $rowAttachment->converted,
    //                             'filename_converted' => $rowAttachment->filename_converted,
    //                             'mime_type' => $rowAttachment->mime_type,
    //                             'downloadable' => $rowAttachment->downloadable,
    //                         ];
    //                     $insertAttachment = $model->docApprovalAttachment()->create($dataInsert);
    //                 }
    //                 // END ATTACHMENT

    //                 // ORDER GROUP
    //                 $isNeedRevision = $isCanceled = $isRejected = 0;
    //                 if($rowForm->application_form_status == '5') {
    //                     $isNeedRevision = '1';
    //                 }
    //                 else if($rowForm->application_form_status == '9') {
    //                     $isRejected = '1';
    //                 }
    //                 else if($rowForm->application_form_status == '14') {
    //                     $isCanceled = '1';
    //                 }

    //                 if(!empty($rowForm->order_group_id)) {
    //                     $dataUpdate = [
    //                                     'application_id' => $applicationIdNew,
    //                                     'is_need_revision' => $isNeedRevision,
    //                                     'is_canceled' => $isCanceled,
    //                                     'is_rejected' => $isRejected,
    //                                 ];

    //                     $model->documentApprovalOrderGroup()
    //                         ->where('order_group_id', $rowForm->order_group_id)
    //                         ->update($dataUpdate);

    //                     $dataUpdate = [
    //                         'order_group_id' => $rowForm->order_group_id,
    //                     ];
    //                     $model->documentApprovalDetailOrderItem()
    //                         ->where('order_form_id', $rowForm->order_form_id_new)
    //                         ->where('comparison_id', $rowForm->comparison_id_new)
    //                         ->update($dataUpdate);
    //                 }
    //                 else {
    //                     $dataInsert = [
    //                         'company_id' => $companyId,
    //                         'order_form_id' => $rowForm->order_form_id_new,
    //                         'application_id' => $applicationIdNew,
    //                         'is_need_revision' => $isNeedRevision,
    //                         'is_canceled' => $isCanceled,
    //                         'is_rejected' => $isRejected,
    //                     ];
    //                     $insertOrderGroup = $model->documentApprovalOrderGroup()->create($dataInsert);
    //                     $orderGroupIdNew = $insertOrderGroup->id;

    //                     $dataUpdate = [
    //                         'order_group_id' => $orderGroupIdNew,
    //                         'vendor_id' => $rowForm->vendor_id_new,
    //                     ];
    //                     $model->documentApprovalDetailOrderItem()
    //                         ->where('order_form_id', $rowForm->order_form_id_new)
    //                         ->whereNull('comparison_id')
    //                         ->update($dataUpdate);
    //                 }
    //                  // END ORDER GROUP

    //                 // SIGNER
    //                 $getSigner = $model->docApprovalFlowSignOld()
    //                                 ->from('doc_approval_flow_sign_old as a')
    //                                 ->select('a.*')
    //                                 ->whereNotNull('a.employee_nik')
    //                                 ->where('a.doc_approval_id', '=', $rowHeader->doc_approval_id)
    //                                 ->where('a.doc_type_id', '=', $rowHeader->doc_type_id)
    //                                 ->where('a.reference_id', '=', $rowForm->application_id)
    //                                 ->where('a.is_active', '1')
    //                                 ->oldest('a.sign_flow_id')
    //                                 ->get();
    //                 foreach($getSigner as $rowSigner) {
    //                     if($rowSigner->flow_as === 'CONFIRMER') {
    //                         $flowAs = 'CONFIRMER_1';
    //                     }
    //                     else {
    //                         $flowAs = $rowSigner->flow_as;
    //                     }

    //                     $dataInsert = [
    //                         'employee_id' => $rowSigner->employee_nik,
    //                         'company_id' => $companyId,
    //                         'flow_as' => $flowAs,
    //                         'status' => $rowSigner->status,
    //                         'doc_approval_id' => $docApprovalIdNew,
    //                         'doc_type_id' => $rowSigner->doc_type_id,
    //                         'reference_id' => $applicationIdNew,
    //                         'received_at' => $rowSigner->received_at,
    //                         'decision_at' => $rowSigner->decision_at,
    //                         'is_active' => $rowSigner->is_active,
    //                     ];
    //                     $insertFlowSign = $model->docApprovalFlowSign()->create($dataInsert);
    //                     $signFlowIdNew = $insertFlowSign->id;

    //                     $getComment = $model->docApprovalFlowCommentOld()
    //                                         ->select('*')
    //                                         ->where('sign_flow_id', '=', $rowSigner->sign_flow_id)
    //                                         ->where('is_active', '1')
    //                                         ->oldest('comment_id')
    //                                         ->get();
    //                     foreach ($getComment as $rowComment) {
    //                         $dataInsert = [
    //                             'employee_id' => $rowSigner->employee_nik,
    //                             'company_id' => $companyId,
    //                             'sign_flow_id' => $signFlowIdNew,
    //                             'comment' => $rowComment->comment,
    //                             'comment_at' => $rowComment->comment_at,
    //                             'is_main' => $rowComment->is_main,
    //                             'is_reply' => $rowComment->is_reply,
    //                             'is_active' => $rowComment->is_active,
    //                         ];
    //                         $insertComment = $model->docApprovalFlowComment()->create($dataInsert);
    //                     }

    //                 }
    //                 // END SIGNER

    //                 // INSPECTION
    //                 $getInspection = $model->docApprovalOrderInspectionOld()
    //                                     ->from('doc_approval_order_inspection_old as a')
    //                                     ->leftJoin('master_vendor as b', 'b.vendor_id_old', '=', 'a.vendor_id')
    //                                     ->select('a.*', 'b.vendor_id as vendor_id_new')
    //                                     ->where('a.application_id', '=', $rowForm->application_id)
    //                                     ->where('a.order_form_id', '=', $rowForm->order_form_id)
    //                                     ->oldest('a.inspection_id')
    //                                     ->get();
    //                 foreach($getInspection as $rowInspection) {
    //                     $dataInsert = [
    //                         'inspection_id_old' => $rowInspection->inspection_id,
    //                         'application_id' => $applicationIdNew,
    //                         'comparison_id' => $rowForm->comparison_id_new,
    //                         'company_id' => $companyId,
    //                         'order_form_id' => $rowForm->order_form_id_new,
    //                         'inspection_form_status' => $rowInspection->inspection_form_status,
    //                         'final_decision_at' => null,
    //                         'doc_version' => '1',
    //                         'po_number' => $rowInspection->po_number,
    //                         'po_date' => $rowInspection->po_date,
    //                         'incoming_date' => $rowInspection->incoming_date,
    //                         'vendor_id' => $rowInspection->vendor_id_new,
    //                         'vendor_name' => $rowInspection->vendor_name,
    //                         'vendor_address' => $rowInspection->vendor_address,
    //                         'inspection_delivery' => $rowInspection->inspection_delivery,
    //                         'ship_date' => $rowInspection->ship_date,
    //                         'inspection_remarks' => $rowInspection->inspection_remarks,
    //                         'path_detail' => Str::replace('purchasing', 'purchasing_old', $rowInspection->path_detail),
    //                         'filename' => $rowInspection->filename,
    //                         'is_active' => $rowInspection->is_active,
    //                         'created_by' => $rowInspection->created_by,
    //                         'created_at' => $rowInspection->created_at,
    //                     ];
    //                     $insertInspection = $model->docApprovalOrderInspection()->create($dataInsert);
    //                     $inspectionIdNew = $insertInspection->id;

    //                     $getInspectionItem = $model->docApprovalOrderInspectionItemOld()
    //                                         ->from('doc_approval_order_inspection_item_old as a')
    //                                         ->leftJoin('doc_approval_detail_order_form_item as b', 'b.order_detail_id_old', '=', 'a.order_detail_id')
    //                                         ->select('a.*', 'b.order_detail_id as order_detail_id_new')
    //                                         ->where('a.application_id', '=', $rowForm->application_id)
    //                                         ->where('a.inspection_id', '=', $rowInspection->inspection_id)
    //                                         ->oldest('a.inspection_item_id')
    //                                         ->get();
    //                     foreach($getInspectionItem as $rowInspectionItem) {
    //                         $dataInsert = [
    //                             'inspection_id' => $inspectionIdNew,
    //                             'company_id' => $companyId,
    //                             'order_form_id' => $rowForm->order_form_id_new,
    //                             'application_id' => $applicationIdNew,
    //                             'order_detail_id' => $rowInspectionItem->order_detail_id_new,
    //                             'appliance_item' => $rowInspectionItem->appliance_item,
    //                             'quantity' => $rowInspectionItem->quantity,
    //                             'incoming_qty' => $rowInspectionItem->incoming_qty,
    //                             'remaining_qty' => $rowInspectionItem->remaining_qty,
    //                             'is_active' => $rowInspectionItem->is_active,
    //                         ];
    //                         $insertInspectionItem = $model->docApprovalOrderInspectionItem()->create($dataInsert);
    //                     }
    //                 }
    //                 // END INSPECTION
    //             }
    //         }

    //     }

    //     echo('Successful Migration');
    //     exit();
    // }

    public function migration_vendor(Request $request) {
        $model = new MigrationBmlLogModel();

        $companyId = '317';
        $companyName = 'PT BERDIRI MATAHARI LOGISTIK';

        $fromId = 1;
        $toId = 929;

        $getVendor = $model->masterVendorOld()
                        ->from('master_vendor_old AS a')
                        ->select('a.*')
                        ->oldest('a.vendor_id')
                        ->where('a.vendor_id', '>=', $fromId)
                        ->where('a.vendor_id', '<=', $toId)
                        ->get();
        foreach($getVendor as $rowVendor) {
            // CEK VENDOR EXIST
            $getVendorExist = $model->masterVendor()
                            ->select('vendor_id')
                            ->where('company_id', $companyId)
                            ->where('vendor_name', 'LIKE', "%{$rowVendor->vendor_name}%")
                            ->oldest('vendor_id')
                            ->first();
            if($getVendorExist) {
                $dataUpdate = [
                    'vendor_id_old' => $rowVendor->vendor_id,
                ];
                $model->masterVendor()
                    ->where('vendor_id', $getVendorExist->vendor_id)
                    ->update($dataUpdate);

                $vendorAddress =  Str::of($rowVendor->vendor_address)
                                        ->replaceMatches('/\n\s+/', "\n")                       // Hapus spasi setelah newline
                                        ->replaceMatches('/\s+\n/', "\n")                       // Hapus spasi sebelum newline
                                        ->replaceMatches('/[ ]{2,}/', ' ')                      // Ganti spasi ganda
                                        ->replaceMatches('/\n{2,}/', "\n")                      // Ganti newline berlebih
                                        ->trim();
                $dataUpdate = [
                    'vendor_address' => $vendorAddress,
                    'is_active' => $rowVendor->is_active,
                ];
                $model->masterVendorAddress()
                    ->where('vendor_id', $getVendorExist->vendor_id)
                    ->update($dataUpdate);

                $getVendorPic = $model->masterVendorPicOld()
                                    ->from('master_vendor_pic_old AS a')
                                    ->select('a.*')
                                    ->oldest('a.vendor_pic_id')
                                    ->where('a.vendor_id', '=', $rowVendor->vendor_id)
                                    ->get();
                foreach($getVendorPic as $rowVendorPic) {
                    $vendorPicName = Str::of($rowVendorPic->pic_name)->trim();

                    $dataInsert = [
                        'vendor_pic_id_old' => $rowVendor->vendor_pic_id,
                        'vendor_id' => $getVendorExist->vendor_id,
                        'pic_name' => $vendorPicName,
                        'is_active' => $rowVendor->is_active,
                    ];
                    $insertPic = $model->masterVendorPic()->create($dataInsert);
                }
            }
            else {
                $dataInsert = [
                    'vendor_id_old' => $rowVendor->vendor_id,
                    'company_id' => $companyId,
                    'vendor_account' => '-',
                    'vendor_name' => $this->formatCompanyName($rowVendor->vendor_name),
                    'group' => 'LOCAL',
                    'currency' => 'IDR',
                    'created_by' => $rowVendor->created_by,
                    'created_at' => $rowVendor->created_at,
                    'is_active' => $rowVendor->is_active,
                ];
                $insertVendor = $model->masterVendor()->create($dataInsert);

                $vendorAddress =  Str::of($rowVendor->vendor_address)
                                        ->replaceMatches('/\n\s+/', "\n")                       // Hapus spasi setelah newline
                                        ->replaceMatches('/\s+\n/', "\n")                       // Hapus spasi sebelum newline
                                        ->replaceMatches('/[ ]{2,}/', ' ')                      // Ganti spasi ganda
                                        ->replaceMatches('/\n{2,}/', "\n")                      // Ganti newline berlebih
                                        ->trim();
                $dataInsert = [
                    'vendor_id' => $insertVendor->id,
                    'vendor_address' => $vendorAddress,
                    'is_active' => $rowVendor->is_active,
                ];
                $insertAddress = $model->masterVendorAddress()->create($dataInsert);

                $getVendorPic = $model->masterVendorPicOld()
                                    ->from('master_vendor_pic_old AS a')
                                    ->select('a.*')
                                    ->oldest('a.vendor_pic_id')
                                    ->where('a.vendor_id', '=', $rowVendor->vendor_id)
                                    ->get();
                foreach($getVendorPic as $rowVendorPic) {
                    $vendorPicName = Str::of($rowVendorPic->pic_name)->trim();

                    $dataInsert = [
                        'vendor_pic_id_old' => $rowVendor->vendor_pic_id,
                        'vendor_id' => $insertVendor->id,
                        'pic_name' => $vendorPicName,
                        'is_active' => $rowVendor->is_active,
                    ];
                    $insertPic = $model->masterVendorPic()->create($dataInsert);
                }
            }
        }

        echo('Successful Migration');
        exit();
    }

    function formatCompanyName(string $text): string {
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

    public function migration_ord(Request $request) {
        $model = new MigrationBmlLogModel();

        $fromId = 51;
        $toId = 79;

    }

    public function migration(Request $request) {
        $model = new MigrationBmlLogModel();
        $fromId = $request->from;
        $toId = $request->to;
        $type = $request->type;
        // $fromId = 531;
        // $toId = 540;
        if($type == 'po') {
            $getForm = $model->docApprovalOrderApplication()
                            ->from('doc_approval_order_application as a')
                            ->leftJoin('doc_approval_header as b', 'b.doc_approval_id', '=', 'a.doc_approval_id')
                            ->select('a.*', 'b.department_name', 'b.location_id', 'b.doc_type_id')
                            ->where('a.application_id', '>=', $fromId)
                            ->where('a.application_id', '<=', $toId)
                            ->whereNull('a.application_id_old')
                            ->oldest('a.order_form_id')
                            ->get();

            foreach($getForm as $rowForm) {
                $arrItemForm = [];
                $getFormItem = $model->docApprovalOrderApplicationItem()
                                    ->from('doc_approval_order_application_item as a')
                                    ->select('a.*')
                                    ->where('a.application_id', '=', $rowForm->application_id)
                                    // ->oldest('a.application_item_id')
                                    ->orderByRaw('CASE WHEN a.order_detail_id IS NULL THEN 1 ELSE 0 END, a.order_detail_id ASC')
                                    ->get();
                $no = 0;
                foreach($getFormItem as $rowFormItem) {
                    // $applianceItem = Str::replace('&amp;', '&', $rowFormItem->appliance_item);
                    // $model->docApprovalOrderApplicationItem()
                    //     ->where('application_id', $rowFormItem->application_id)
                    //     ->update(['appliance_item' => $applianceItem]);

                    // if(!empty($rowFormItem->appliance_item_compare)) {
                    //     $applianceItem = $rowFormItem->appliance_item_compare;
                    // }
                    // else if(empty($rowFormItem->appliance_item_compare) && !empty($rowFormItem->appliance_item_order) ) {
                    //     $applianceItem = $rowFormItem->appliance_item_order;
                    // }
                    // else {
                    //     $applianceItem = $rowFormItem->appliance_item;
                    // }

                    $applianceItem = Str::replace('amp;', '', $rowFormItem->appliance_item);
                    $model->docApprovalOrderApplicationItem()
                        ->where('application_id', $rowFormItem->application_id)
                        ->where('application_item_id', $rowFormItem->application_item_id)
                        ->update(['appliance_item' => $applianceItem]);

                    if(empty($rowFormItem->order_detail_id)) {
                        $no = '';
                    }
                    else {
                        $no++;
                    }

                    $arrItemForm[] = [
                        "itemNo" => $no,
                        "applianceItem" => $applianceItem,
                        "quantity" => $rowFormItem->quantity,
                        "unitPrice" => $rowFormItem->unit_price,
                        "dppOtherValue" => $rowFormItem->dpp_other_value,
                        "currencySymbol" => $rowFormItem->currency,
                        "subtotalPrice" => $rowFormItem->subtotal_price,
                    ];
                }

                $arrBudget = [];
                $getBudgetSelected = $model->docApprovalOrderApplicationBudget()
                                            ->from('doc_approval_order_application_budget')
                                            ->select('budget_id', 'budget_no', 'budget_amount', 'within_over', 'budget_remaining')
                                            ->where('application_id', $rowForm->application_id)
                                            ->where('company_id', $rowForm->company_id)
                                            ->where('is_active', '1')
                                            ->orderBy('application_budget_id', 'asc')
                                            ->get();
                foreach ($getBudgetSelected as $rowBudgetSelected) {
                    $arrBudget[] = [
                        'budgetId' => $rowBudgetSelected->budget_id,
                        'budgetNo' => $rowBudgetSelected->budget_no,
                        'budgetAmount' => $rowBudgetSelected->budget_amount,
                        'budgetWithinOver' => $rowBudgetSelected->within_over,
                        'budgetRemaining' => $rowBudgetSelected->budget_remaining,
                    ];
                }

                $arrCostCenter = [];
                $getFormCostCenter = $model->docApprovalOrderApplicationCostCenter()
                                        ->from('doc_approval_order_application_cost_center')
                                        ->select('cost_center_id', 'cost_center', 'percentage')
                                        ->where('application_id', $rowForm->application_id)
                                        ->where('company_id', $rowForm->company_id)
                                        ->where('order_form_id', $rowForm->order_form_id)
                                        ->where('is_active', '1')
                                        ->orderBy('application_cost_center_id', 'asc')
                                        ->get();
                foreach ($getFormCostCenter as $rowCostCenter) {
                    $arrCostCenter[] = [
                        'costCenterId' => $rowCostCenter->cost_center_id,
                        'code' => $rowCostCenter->cost_center,
                        'percentage' => $rowCostCenter->percentage,
                    ];
                }

                $arrReimburse = [];
                $getFormReimburse = $model->docApprovalOrderApplicationReimburse()
                                        ->from('doc_approval_order_application_reimburse')
                                        ->select('application_reimburse_id', 'reimburse_to', 'percentage')
                                        ->where('application_id', $rowForm->application_id)
                                        ->where('is_active', '1')
                                        ->orderBy('application_reimburse_id', 'asc')
                                        ->get();
                foreach ($getFormReimburse as $rowReimburse) {
                    $arrReimburse[] = [
                        'reimburseId' => $rowReimburse->application_reimburse_id,
                        'reimburseTo' => $rowReimburse->reimburse_to,
                        'percentage' => $rowReimburse->percentage,
                    ];
                }

                $approverPo = $approverPoPosition = $approverPoEmail = '';
                $arrSigner = [];
                $getSigner = $model->docApprovalFlowSign()
                                ->from('doc_approval_flow_sign as a')
                                ->leftJoin('vw_master_employee_all as b', 'b.employee_id', '=', 'a.employee_id')
                                ->leftJoin('master_transaction_status as c', 'c.transaction_status_id', '=', 'a.status')
                                ->select('a.*', 'b.employee_name', 'b.position_name', 'c.transaction_status_name', 'b.employee_email')
                                ->whereNotNull('a.employee_id')
                                ->where('a.doc_approval_id', '=', $rowForm->doc_approval_id)
                                ->where('a.doc_type_id', '=', $rowForm->doc_type_id)
                                ->where('a.reference_id', '=', $rowForm->application_id)
                                ->whereNotIn('a.status', ['6','12','16'])
                                ->whereNotIn('a.flow_as', ['CC','RECEIVER'])
                                ->where('a.is_active', '1')
                                ->oldest('a.sign_flow_id')
                                ->get();
                foreach($getSigner as $rowSigner) {
                    $approverPo = $rowSigner->employee_name;
                    $approverPoPosition = $rowSigner->position_name;
                    $approverPoEmail = $rowSigner->employee_email;
                    $arrSigner[] = [
                        "signerId" => $rowSigner->sign_flow_id,
                        "flowAs" => $rowSigner->flow_as,
                        "company_id" => $rowSigner->company_id,
                        "employeeId" => $rowSigner->employee_id,
                        "employeeName" => $rowSigner->employee_name,
                        "positionName" => $rowSigner->position_name,
                        "status" => "2",
                        "decisionAt" => $rowSigner->decision_at,
                        "order" => $rowSigner->order,
                    ];
                }

                $requestData = new Request();
                $requestData->replace([
                    "documentType" => "2",
                    "docApprovalId" => $rowForm->doc_approval_id,
                    "dataForm" => [
                        "dataForm" => [
                            "companyId" => $rowForm->company_id,
                            "applicationHeader" => $rowForm->application_header,
                            "applicationNumber" => $rowForm->application_number,
                            "applicationTitle" => $rowForm->application_title,
                            "applicationEstimate" => $rowForm->application_estimate,
                            "applicationSubmittedDate" => $rowForm->application_submitted_date,
                            "vendorPicId" => $rowForm->vendor_pic_id,
                            "applicationVendorPic" => $rowForm->vendor_pic,
                            "applicationCurrency" => $rowForm->application_currency,
                            "vendorName" => $rowForm->vendor_name,
                            "vendorAddress" => $rowForm->vendor_address,
                            "applicationDelivery" => $rowForm->application_delivery,
                            "applicationInvoice" => $rowForm->application_invoice,
                            "applicationGrandTotal" => $rowForm->application_grand_total,
                            "purchaseType" => $rowForm->purchase_type,
                            "formType" => $rowForm->form_type,
                            "budgetNo" => $rowForm->budget_no,
                            "budgetAmount" => $rowForm->budget_amount,
                            "budgetWithinOver" => $rowForm->budget_within_over,
                            "budgetWithin" => $rowForm->budget_within,
                            "reimburseTo" => $rowForm->reimburse_to,
                            "reimburseToNext" => $rowForm->reimburse_to_next,
                            "paymentType" => $rowForm->payment_type,
                            "shipDate" => $rowForm->ship_date,
                            "applicationRemark" => $rowForm->application_remark,
                            "applicationReason" => $rowForm->application_reason,
                            "budget" => $arrBudget,
                            "itemForm" => $arrItemForm,
                            "signer" => $arrSigner,
                            "costCenter" => $arrCostCenter,
                            "reimburse" => $arrReimburse,
                            "approverPo" => $approverPo,
                            "approverPoPosition" => $approverPoPosition,
                            "approverPoEmail" => $approverPoEmail,
                            "filePath" => $rowForm->path_detail,
                            "filename" => $rowForm->filename,
                            "submittedAt" => $rowForm->created_at,
                            "approvedAt" => $rowForm->final_decision_at,
                        ]
                    ]
                ]);

                $documentApprovalController = new ControllersDocumentApprovalController();
                $documentApprovalController->generatePdf($requestData);
            }
        }
        else if($type == 'pr') {
            $getForm = $model->documentApprovalDetailOrder()
                            ->from('doc_approval_detail_order_form as a')
                            ->leftJoin('doc_approval_header as b', 'b.doc_approval_id', '=', 'a.doc_approval_id')
                            ->leftJoin('doc_approval_detail_order_form_purpose as c', 'c.order_form_id', '=', 'a.order_form_id')
                            ->select('a.*', 'b.department_name', 'b.location_id', 'c.description', 'c.reason', 'c.remarks', 'b.doc_type_id')
                            ->where('a.order_form_id', '>=', $fromId)
                            ->where('a.order_form_id', '<=', $toId)
                            ->whereNull('a.order_form_id_old')
                            ->oldest('a.order_form_id')
                            ->get();

            foreach($getForm as $rowForm) {
                $arrItemForm = [];
                $getFormItem = $model->documentApprovalDetailOrderItem()
                                    ->from('doc_approval_detail_order_form_item as a')
                                    ->select('a.*')
                                    ->where('a.doc_approval_id', '=', $rowForm->doc_approval_id)
                                    ->where('a.order_form_id', '=', $rowForm->order_form_id)
                                    ->oldest('a.order_detail_id')
                                    ->get();
                foreach($getFormItem as $rowFormItem) {
                    $applianceItem = Str::replace('amp;', '', $rowFormItem->appliance_item);
                    $brandType = Str::replace('amp;', '', $rowFormItem->brand_type);
                    $model->documentApprovalDetailOrderItem()
                        ->where('order_detail_id', $rowFormItem->order_detail_id)
                        ->update(['appliance_item' => $applianceItem, 'brand_type' => $brandType]);

                    $arrItemForm[] = [
                                        "costCenter" => $rowFormItem->cost_center,
                                        "applianceItem" => $applianceItem,
                                        "brandType" => $brandType,
                                        "unitName" => $rowFormItem->unit_name,
                                        "unitQuantity" => $rowFormItem->unit_quantity,
                                        "currency" => $rowFormItem->currency_code,
                                        "unitPriceEst" => $rowFormItem->unit_price_estimated,
                                        "totalPriceEst" => $rowFormItem->total_price_estimated,
                                        "requiredDate" => Carbon::parse($rowFormItem->required_date)->format('d-M-Y'),
                    ];
                }

                $itemForm = $arrItemForm;

                $arrSigner = [];
                $getSigner = $model->docApprovalFlowSign()
                                ->from('doc_approval_flow_sign as a')
                                ->leftJoin('vw_master_employee_active as b', 'b.employee_id', '=', 'a.employee_id')
                                ->leftJoin('master_transaction_status as c', 'c.transaction_status_id', '=', 'a.status')
                                ->select('a.*', 'b.employee_name', 'c.transaction_status_name')
                                ->whereNotNull('a.employee_id')
                                ->where('a.doc_approval_id', '=', $rowForm->doc_approval_id)
                                ->where('a.doc_type_id', '=', $rowForm->doc_type_id)
                                ->where('a.reference_id', '=', $rowForm->order_form_id)
                                ->whereNotIn('a.status', ['6', '12'])
                                ->whereNotIn('a.flow_as', ['CC','RECEIVER'])
                                ->where('a.is_active', '1')
                                ->oldest('a.sign_flow_id')
                                ->get();
                foreach($getSigner as $rowSigner) {
                    $decisionAt = ($rowSigner->decision_at) ? Carbon::parse($rowSigner->decision_at)->format('d-M-Y') : '';
                    $decisionAtTime = ($rowSigner->decision_at) ? Carbon::parse($rowSigner->decision_at)->format('d-M-Y H:i:s') : '';
                    $arrSigner[] = [
                                        "signerId" => $rowSigner->sign_flow_id,
                                        "employeeName" => $rowSigner->employee_name,
                                        "company_id" => $rowSigner->company_id,
                                        "flowAs" => $rowSigner->flow_as,
                                        "receivedAt" => Carbon::parse($rowSigner->received_at)->format('d-M-Y'),
                                        "statusName" => $rowSigner->transaction_status_name,
                                        "decisionAt" => $decisionAt,
                                        "decisionAtTime" => $decisionAtTime,
                                        "order" => $rowSigner->order,
                    ];
                }

                $signer = $arrSigner;
                $requestData = new Request();
                $requestData->replace([
                    "documentType" => "1",
                    "dataForm" => [
                        "dataForm" => [
                            "docNumber" => $rowForm->order_form_number,
                            "companyId" => $rowForm->company_id,
                            "departmentName" => $rowForm->department_name,
                            "locationName" => $rowForm->location_id,
                            "createdDate" => Carbon::parse($rowForm->created_at)->format('d M Y'),
                            "requestDate" =>Carbon::parse($rowForm->request_date)->format('d M Y'),
                            "currency" => $rowForm->currency_code,
                            "grandTotal" => $rowForm->grand_total,
                            "itemForm" => $itemForm,
                            "purpose" => [
                                [
                                    "description" => $rowForm->description,
                                    "reason" => $rowForm->reason,
                                    "remarks" => $rowForm->remarks,
                                ]
                            ],
                            "signer" => $signer,
                            "filePath" => $rowForm->path_detail,
                            "filename" => $rowForm->filename,
                        ]
                    ]
                ]);

                $documentApprovalController = new ControllersDocumentApprovalController();
                $documentApprovalController->generatePdf($requestData);
            }
        }
        else if($type == 'comparison') {
            $getForm = $model->docApprovalOrderComparison()
                            ->from('doc_approval_order_comparison as a')
                            ->leftJoin('master_vendor as b', 'b.vendor_id', '=', 'a.vendor_id_selected')
                            ->select('a.*', 'b.vendor_name')
                            ->where('a.comparison_id', '>=', $fromId)
                            ->where('a.comparison_id', '<=', $toId)
                            ->whereNull('a.comparison_id_old')
                            ->oldest('a.comparison_id')
                            ->get();

            foreach($getForm as $rowForm) {
                $comparisonNotes = Str::replace('amp;', '', $rowForm->comparison_notes);
                $model->docApprovalOrderComparison()
                    ->where('comparison_id', $rowForm->comparison_id)
                    ->update(['comparison_notes' => $comparisonNotes]);

                $selectedVendorComparison = null;
                $arrVendor = [];
                $getVendor = $model->docApprovalOrderComparisonVendor()
                                    ->from('doc_approval_order_comparison_vendor as a')
                                    ->select('a.*')
                                    ->where('a.doc_approval_id', '=', $rowForm->doc_approval_id)
                                    ->where('a.comparison_id', '=', $rowForm->comparison_id)
                                    ->oldest('a.comparison_vendor_id')
                                    ->get();
                foreach($getVendor as $rowVendor) {
                    $arrVendor[] = [
                        "vendorName" => $rowVendor->vendor_name,
                        "validity" => $rowVendor->quotation_validity,
                    ];

                    if($rowForm->vendor_id_selected == $rowVendor->vendor_id) {
                        $selectedVendorComparison = $rowVendor->comparison_vendor_id;
                    }
                }

                $arrItemForm = [];
                $getFormItem = $model->docApprovalOrderComparisonItem()
                                    ->from('doc_approval_order_comparison_item as a')
                                    ->select('a.*')
                                    ->where('a.comparison_id', '=', $rowForm->comparison_id)
                                    // ->oldest('a.application_item_id')
                                    ->orderByRaw('CASE WHEN a.order_detail_id IS NULL THEN 1 ELSE 0 END, a.order_detail_id ASC')
                                    ->oldest('a.comparison_item_id')
                                    ->get();
                foreach($getFormItem as $rowFormItem) {
                    $applianceItem = Str::replace('amp;', '', $rowFormItem->appliance_item);
                    $model->docApprovalOrderComparisonItem()
                        ->where('comparison_id', $rowFormItem->comparison_id)
                        ->where('comparison_item_id', $rowFormItem->comparison_item_id)
                        ->update(['appliance_item' => $applianceItem]);

                    $arrItemVendor = [];
                    $getFormItemVendor = $model->docApprovalOrderComparisonItemVendor()
                                        ->from('doc_approval_order_comparison_item_vendor as a')
                                        ->select('a.*')
                                        ->where('a.comparison_item_id', '=', $rowFormItem->comparison_item_id)
                                        ->where('a.comparison_id', '=', $rowForm->comparison_id)
                                        ->oldest('a.comparison_item_vendor_id')
                                        ->get();
                    foreach($getFormItemVendor as $rowFormItemVendor) {
                        $arrItemVendor[] = [
                            "unitPriceValue" => $rowFormItemVendor->unit_price,
                            "dppOtherValue" => $rowFormItemVendor->dpp_other_value,
                            "totalPrice" => $rowFormItemVendor->total_price,
                            "comparisonVendorId" => $rowFormItemVendor->comparison_vendor_id,
                        ];
                    }

                    $arrItemForm[] = [
                        "criteriaId" => $rowFormItem->order_detail_id,
                        "applianceItem" => $rowFormItem->appliance_item,
                        "unitQty" => $rowFormItem->unit_quantity,
                        "unitName" => $rowFormItem->unit_name,
                        "currency" => $rowFormItem->currency,
                        "arrItemVendor" => $arrItemVendor,
                    ];
                }

                $requestData = new Request();
                $requestData->replace([
                    "documentType" => "6",
                    "dataForm" => [
                        "dataForm" => [
                            "companyId" => $rowForm->company_id,
                            "title" => $rowForm->comparison_title,
                            "description" => $rowForm->comparison_description,
                            "date" => $rowForm->comparison_date,
                            "vendorComparison" => $arrVendor,
                            "itemForm" => $arrItemForm,
                            "selectedVendorComparison" => $selectedVendorComparison,
                            "selectedVendorName" => $rowForm->vendor_name,
                            "comparisonNote" => $rowForm->comparison_notes,
                            "filePath" => $rowForm->path_detail,
                            "filename" => $rowForm->filename,
                        ]
                    ]
                ]);

                // dd($requestData);
                $documentApprovalController = new ControllersDocumentApprovalController();
                $documentApprovalController->generatePdf($requestData);
            }
        }
        else if($type == 'inspection') {
            $getForm = $model->docApprovalOrderInspection()
                            ->from('doc_approval_order_inspection as a')
                            ->leftJoin('doc_approval_order_application as b', 'b.application_id', '=', 'a.application_id')
                            ->select('a.*', 'b.path_detail')
                            ->where('a.inspection_id', '>=', $fromId)
                            ->where('a.inspection_id', '<=', $toId)
                            ->whereNull('a.inspection_id_old')
                            ->oldest('a.inspection_id')
                            ->get();

            foreach($getForm as $rowForm) {
                $pathDetail = $rowForm->path_detail;
                $formFilename = Str::upper('INSPECTION FORM '.uniqid());

                if(Storage::exists('private/doc_approval'.$rowForm->path_detail.$rowForm->filename.'.xlsx')) {
                    Storage::delete('private/doc_approval'.$rowForm->path_detail.$rowForm->filename.'.xlsx');
                }

                $model->docApprovalOrderInspection()
                    ->where('inspection_id', $rowForm->inspection_id)
                    ->update(['path_detail' => $pathDetail, 'filename' => $formFilename]);

                $arrItemForm = [];
                $getFormItem = $model->docApprovalOrderInspectionItem()
                                    ->from('doc_approval_order_inspection_item as a')
                                    ->select('a.*')
                                    ->where('a.application_id', '=', $rowForm->application_id)
                                    ->where('a.inspection_id', '=', $rowForm->inspection_id)
                                    ->oldest('a.inspection_item_id')
                                    ->get();
                foreach($getFormItem as $rowFormItem) {
                    $applianceItem = Str::replace('amp;', '', $rowFormItem->appliance_item);
                    $model->docApprovalOrderInspectionItem()
                        ->where('inspection_item_id', $rowFormItem->inspection_item_id)
                        ->update(['appliance_item' => $applianceItem]);

                    $arrItemForm[] = [
                        "applicationItemId" => $rowFormItem->application_item_id,
                        "applianceItem" => $rowFormItem->appliance_item,
                        "qty" => $rowFormItem->quantity,
                        "incomingQty" => "",
                        "remainingQty" => "",
                    ];
                }

                $itemForm = $arrItemForm;

                $arrChecklist = [];
                $getChecklist = $model->docApprovalOrderInspectionItem()
                                    ->from('doc_approval_order_inspection_checklist')
                                    ->select('checklist_name', 'is_checked')
                                    ->where('inspection_id', $rowForm->inspection_id)
                                    ->get();
                foreach ($getChecklist as $rowChecklist) {
                    $arrChecklist[] = [
                        "checklist_name" => $rowChecklist->checklist_name,
                        "is_checked" => $rowChecklist->is_checked,
                    ];
                }

                $arrSigner = [];
                $getSigner = $model->docApprovalFlowSign()
                                ->from('doc_approval_flow_sign as a')
                                ->leftJoin('vw_master_employee_active as b', 'b.employee_id', '=', 'a.employee_id')
                                ->leftJoin('master_transaction_status as c', 'c.transaction_status_id', '=', 'a.status')
                                ->select('a.*', 'b.employee_name', 'b.position_name', 'c.transaction_status_name')
                                ->whereNotNull('a.employee_id')
                                ->where('a.doc_approval_id', '=', $rowForm->doc_approval_id)
                                ->where('a.doc_type_id', '=', '4')
                                ->where('a.reference_id', '=', $rowForm->inspection_id)
                                ->whereNotIn('a.status', ['6', '12'])
                                ->whereNotIn('a.flow_as', ['CC','RECEIVER'])
                                ->where('a.is_active', '1')
                                ->oldest('a.sign_flow_id')
                                ->get();
                foreach($getSigner as $rowSigner) {
                    $arrSigner[] = [
                                    "signerId" => $rowSigner->sign_flow_id,
                                    "employeeName" => $rowSigner->employee_name,
                                    "positionName" => $rowSigner->position_name,
                                    "flowAs" => $rowSigner->flow_as,
                                    "receivedAt" => $rowSigner->received_at,
                                    "statusName" => $rowSigner->transaction_status_name,
                                    "decisionAt" => $rowSigner->decision_at,
                                    "decisionAtTime" => $rowSigner->decision_at,
                                    "order" => $rowSigner->order,
                    ];
                }

                $requestData = new Request();
                $requestData->replace([
                    "documentType" => "4",
                    "dataForm" => [
                        "dataForm" => [
                            "companyId" => $rowForm->company_id,
                            "poNumber" => $rowForm->po_number,
                            "poDate" => $rowForm->po_date,
                            "incomingDate" => $rowForm->incoming_date,
                            "vendorId" => $rowForm->vendor_id,
                            "vendorName" => $rowForm->vendor_name,
                            "vendorAddress" => $rowForm->vendor_address,
                            "inspectionDelivery" => $rowForm->inspection_delivery,
                            "shipDate" => $rowForm->ship_date,
                            "inspectionRemarks" => $rowForm->inspection_remarks,
                            "itemForm" => $itemForm,
                            "filePath" => $pathDetail,
                            "filename" => $formFilename,
                            "checklist" => $arrChecklist,
                            "signer" => $arrSigner,
                        ]
                    ]
                ]);

                $documentApprovalController = new ControllersDocumentApprovalController();
                $documentApprovalController->generatePdf($requestData);
            }
        }
        else if($type == 'test') {
            $requestData = new Request();
            // $requestData->replace([
            //     "documentType" => "2",
            //     "docApprovalId" => "999999",
            //     "dataForm" => [
            //         "dataForm" => [
            //             "companyId" => '317',
            //             "applicationHeader" => "APPLICATION FORM",
            //             "applicationNumber" => "",
            //             "applicationTitle" => "",
            //             "applicationEstimate" => '353000.0',
            //             "applicationSubmittedDate" => "2025-07-03",
            //             "vendorPicId" => "",
            //             "applicationVendorPic" => "",
            //             "applicationCurrency" => "IDR",
            //             "vendorName" => "",
            //             "vendorAddress" => "",
            //             "applicationDelivery" => "",
            //             "applicationInvoice" => "",
            //             "applicationGrandTotal" => '353000.00',
            //             "purchaseType" => "",
            //             "formType" => "",
            //             "budgetNo" => null,
            //             "budgetAmount" => null,
            //             "budgetWithinOver" => "Within",
            //             "budgetWithin" => null,
            //             "reimburseTo" => null,
            //             "reimburseToNext" => null,
            //             "paymentType" => "1",
            //             "shipDate" => "2025-07-31",
            //             "applicationRemark" => "",
            //             "applicationReason" => "",
            //             "budget" => [
            //                 [
            //                     "budgetNo" => '',
            //                     "budgetAmount" => '',
            //                     "budgetWithinOver" => "Within",
            //                     "budgetRemaining" => '',
            //                 ],
            //             ],
            //             "itemForm" => [
            //                 [
            //                     "itemNo" => '1',
            //                     "applianceItem" => "",
            //                     "quantity" => "",
            //                     "unitPrice" => "100000.0",
            //                     "dppOtherValue" => "",
            //                     "currencySymbol" => "IDR",
            //                     "subtotalPrice" => "100000.0",
            //                 ],
            //             ],
            //             "signer" => [
            //                 [
            //                     "signerId" => "1",
            //                     "flowAs" => "APPLICANT",
            //                     "company_id" => "",
            //                     "employeeId" => "",
            //                     "employeeName" => "",
            //                     "positionName" => "",
            //                     "status" => "2",
            //                     "receivedAt" => "",
            //                     "decisionAt" => "",
            //                     "order" => "1",
            //                 ],
            //                 [
            //                     "signerId" => "2",
            //                     "flowAs" => "REVIEWER",
            //                     "company_id" => null,
            //                     "employeeId" => null,
            //                     "employeeName" => null,
            //                     "positionName" => null,
            //                     "status" => "16",
            //                     "receivedAt" => null,
            //                     "decisionAt" => null,
            //                     "order" => "2",
            //                 ],
            //                 [
            //                     "signerId" => "3",
            //                     "flowAs" => "CONFIRMER_1",
            //                     "company_id" => null,
            //                     "employeeId" => null,
            //                     "employeeName" => null,
            //                     "positionName" => null,
            //                     "status" => "16",
            //                     "receivedAt" => null,
            //                     "decisionAt" => null,
            //                     "order" => "3",
            //                 ],
            //                 [
            //                     "signerId" => "4",
            //                     "flowAs" => "REVIEW_ADMIN",
            //                     "company_id" => null,
            //                     "employeeId" => null,
            //                     "employeeName" => null,
            //                     "positionName" => null,
            //                     "status" => "16",
            //                     "receivedAt" => null,
            //                     "decisionAt" => null,
            //                     "order" => "4",
            //                 ],
            //                 [
            //                     "signerId" => "5",
            //                     "flowAs" => "CONFIRMER_2",
            //                     "company_id" => null,
            //                     "employeeId" => null,
            //                     "employeeName" => null,
            //                     "positionName" => null,
            //                     "status" => "16",
            //                     "receivedAt" => null,
            //                     "decisionAt" => null,
            //                     "order" => "5",
            //                 ],
            //                 [
            //                     "signerId" => "6",
            //                     "flowAs" => "ACKNOWLEDGER",
            //                     "company_id" => "322",
            //                     "employeeId" => "",
            //                     "employeeName" => "",
            //                     "positionName" => "",
            //                     "status" => "3",
            //                     "receivedAt" => "",
            //                     "decisionAt" => null,
            //                     "order" => "7",
            //                 ],
            //                 [
            //                     "signerId" => "7",
            //                     "flowAs" => "APPROVER",
            //                     "company_id" => "322",
            //                     "employeeId" => "313000009",
            //                     "employeeName" => "RIA ANGELIKA",
            //                     "positionName" => "HR & GA DM Manager",
            //                     "status" => "3",
            //                     "receivedAt" => "2025-07-03 11:20:53",
            //                     "decisionAt" => "2025-07-03 11:20:53",
            //                     "order" => "7",
            //                 ]
            //             ],
            //             "costCenter" => [
            //                 [
            //                     "code" => "",
            //                     "percentage" => "",
            //                 ],
            //                 [
            //                     "code" => "",
            //                     "percentage" => "",
            //                 ],
            //                 [
            //                     "code" => "",
            //                     "percentage" => "",
            //                 ],
            //                 [
            //                     "code" => "",
            //                     "percentage" => "",
            //                 ],
            //                 [
            //                     "code" => "",
            //                     "percentage" => "",
            //                 ],
            //                 [
            //                     "code" => "",
            //                     "percentage" => "",
            //                 ]
            //             ],
            //             "reimburse" => [
            //                 [
            //                     "reimburseTo" => '',
            //                     "reimbursePercentage" => '',
            //                 ]
            //             ],
            //             "approverPo" => "RIA ANGELIKA",
            //             "approverPoPosition" => "HR & GA DM Manager",
            //             "approverPoEmail" => "ria.angelika@logisteed.com",
            //             "filePath" => "/2025/purchasing/317/3PL Admin/2500000028/",
            //             "filename" => "PO-317-250000005-1",
            //             "submittedAt" => "2025-07-03 11:20:53",
            //             "approvedAt" => "2025-07-03 11:20:53",
            //         ]
            //     ]
            // ]);

            // INSPECTION FORM
            // $requestData->replace([
            //     "documentType" => "4",
            //     "dataForm" => [
            //         "dataForm" => [
            //             "companyId" => "317",
            //             "poNumber" => "",
            //             "poDate" => "",
            //             "incomingDate" => "",
            //             "vendorId" => "",
            //             "vendorName" => "",
            //             "vendorAddress" => "",
            //             "inspectionDelivery" => "",
            //             "shipDate" => "",
            //             "inspectionRemarks" => "",
            //             "itemForm" => [
            //                 [
            //                     "applicationItemId" => "",
            //                     "applianceItem" => "",
            //                     "qty" => "",
            //                     "incomingQty" => "",
            //                     "remainingQty" => "",
            //                 ],
            //             ],
            //             "filePath" => "/2025/purchasing/317/3PL Admin/2500000028/",
            //             "filename" => "INSPECTION FORM 6875905128E09",
            //             "checklist" => [
            //                 [
            //                     "checklist_name" => "Received quantity same as ordered",
            //                     "is_checked" => "0"
            //                 ],
            //                 [
            //                     "checklist_name" => "Received specification same as ordered",
            //                     "is_checked" => "0"
            //                 ],
            //                 [
            //                     "checklist_name" => "On-time delivery",
            //                     "is_checked" => "0"
            //                 ],
            //                 [
            //                     "checklist_name" => "No defect found on items",
            //                     "is_checked" => "0"
            //                 ],
            //                 [
            //                     "checklist_name" => "Vendor documents same as delivered item",
            //                     "is_checked" => "0"
            //                 ]
            //             ],
            //             "signer" => [
            //                 [
            //                     "signerId" => "6266",
            //                     "employeeName" => "",
            //                     "positionName" => "",
            //                     "company_id" => "317",
            //                     "flowAs" => "APPLICANT",
            //                     "receivedAt" => "",
            //                     "statusName" => "SUBMITTED",
            //                     "decisionAt" => "",
            //                     "decisionAtTime" => "",
            //                     "order" => "1"
            //                 ],
            //                 [
            //                     "signerId" => "6267",
            //                     "employeeName" => "",
            //                     "positionName" => "",
            //                     "flowAs" => "",
            //                     "receivedAt" => "",
            //                     "statusName" => "",
            //                     "decisionAt" => "",
            //                     "decisionAtTime" => "",
            //                 ],
            //                 [
            //                     "signerId" => "6268",
            //                     "employeeName" => "",
            //                     "positionName" => "",
            //                     "flowAs" => "",
            //                     "receivedAt" => null,
            //                     "statusName" => "",
            //                     "decisionAt" => "",
            //                     "decisionAtTime" => "",
            //                 ]
            //             ],
            //         ]
            //     ]
            // ]);

            // COMPARISON
            $requestData->replace([
                                "documentType" => "6",
                                "dataForm" => [
                                    "dataForm" => [
                                        "companyId" => '317',
                                        "title" => " ",
                                        "description" => " ",
                                        "date" => " ",
                                        "vendorComparison" => [
                                            [
                                                "vendorName" => "",
                                                "validity" => ""
                                            ]
                                        ],
                                        "itemForm" => [
                                            [
                                                "criteriaId" => "",
                                                "applianceItem" => "",
                                                "unitQty" => "",
                                                "unitName" => "",
                                                "currency" => "IDR",
                                                "arrItemVendor" => [
                                                    [
                                                        "unitPriceValue" => '100000.0',
                                                        "dppOtherValue" => null,
                                                        "totalPrice" => '100000.0',
                                                        "comparisonVendorId" => '',
                                                    ]
                                                ]
                                            ],
                                        ],
                                        "selectedVendorComparison" => '',
                                        "selectedVendorName" => "",
                                        "comparisonNote" => "",
                                        "filePath" => "/2025/purchasing/317/3PL Admin/2500000028/",
                                        "filename" => "COMPARISON FORM 686ADF5754F37",
                                    ]
                                ]
            ]);

            $documentApprovalController = new ControllersDocumentApprovalController();
            $documentApprovalController->generatePdf($requestData);
        }
        else if($type == 'job_convert') {
            // ConvertFileJob::dispatch('7109');
            $getFile = $model->docApprovalAttachment()
                            ->select('attachment_id')
                            ->where('mime_type', '<>', 'application/pdf')
                            ->where('converted', '0')
                            ->orderBy('attachment_id')
                            ->get();
            foreach ($getFile as $file) {
                ConvertFileJob::dispatch($file->attachment_id);
            }
        }
        else {
            echo 'Input type : pr / comparison / po';
        }
    }

    // public function migration(Request $request) {
    //     $employeeId = $request->employeeId;
    //     $reminderAbout = $request->about;
    //     $reminderAt = $request->h.':'.$request->m;
    //     $accountId = '4'; // ID master_email_accounts
    //     $model = new MigrationBmlLogModel();

    //     if ($reminderAbout == 'APPROVER_ONGOING') {
    //         $getApprovalReminder = $model->docApprovalFlowSign()
    //                                     ->from('doc_approval_flow_sign as a')
    //                                     ->join(DB::raw('(
    //                                         SELECT
    //                                             employee_id,
    //                                             MAX(sign_flow_id) as latest_sign_flow_id
    //                                         FROM doc_approval_flow_sign
    //                                         WHERE status = 3
    //                                             AND decision_at IS NULL
    //                                             AND is_active = 1
    //                                         GROUP BY employee_id
    //                                     ) as sub'), function($join) {
    //                                         $join->on('a.employee_id', '=', 'sub.employee_id')
    //                                             ->on('a.sign_flow_id', '=', 'sub.latest_sign_flow_id');
    //                                     })
    //                                     ->leftJoin('vw_master_employee_active as b', function($join) {
    //                                             $join->on('b.employee_id', '=', 'a.employee_id');
    //                                         })
    //                                     ->select(
    //                                         'a.*',
    //                                         'b.employee_name',
    //                                         'b.employee_email'
    //                                     )
    //                                     ->where('a.status', '=', '3') // 3 = RECEIVED
    //                                     ->whereNotNull('b.employee_name')
    //                                     ->whereNotIn('a.flow_as', ['APPLICANT','RECEIVER'])
    //                                     ->orderBy('a.sign_flow_id', 'asc')
    //                                     ->where('a.employee_id', '=', $employeeId)
    //                                     ->get();
    //     }
    //     else if ($reminderAbout == 'BATCH_APPROVAL_REQUEST') {
    //         $getApprovalReminder = $model->docApprovalReminder()->from('doc_approval_reminder as a')
    //                                     ->leftJoin('vw_master_employee_active as b', 'b.employee_id', 'a.employee_id')
    //                                     ->select('a.*', 'b.employee_name', 'b.employee_email')
    //                                     ->whereNotNull('b.employee_name')
    //                                     ->where('a.reminder_at', $reminderAt)
    //                                     ->where('a.is_active', '1')
    //                                     ->orderBy('a.reminder_id', 'asc')
    //                                     ->get();
    //     }

    //     foreach ($getApprovalReminder as $rowApprovalReminder) {
    //         $arrOngoing = [];
    //         $arrOngoingDocType = [];
    //         $getApprovalOngoing = $model->vwDocApprovalOngoing()
    //                                 ->select('*')
    //                                 ->where('employee_id', $rowApprovalReminder['employee_id'])
    //                                 ->orderBy('doc_type_id', 'asc')
    //                                 ->orderBy('received_at', 'desc')
    //                                 ->get();
    //         foreach ($getApprovalOngoing as $rowApprovalOngoing) {
    //             if($rowApprovalOngoing->doc_type_id == '1') {
    //                 // ORDER FORM
    //                 $getForm = $model->documentApprovalDetailOrder()
    //                                 ->from('doc_approval_detail_order_form as a')
    //                                 ->select('a.order_form_id', 'a.doc_version', 'a.grand_total', 'a.currency_code', 'a.created_at', 'b.location_name', 'c.description')
    //                                 ->leftJoin('master_locations as b', 'b.location_id', 'a.location_id')
    //                                 ->leftJoin('doc_approval_detail_order_form_purpose as c', 'c.order_form_id', 'a.order_form_id')
    //                                 ->where('a.doc_approval_id', $rowApprovalOngoing->doc_approval_id)
    //                                 ->where('a.order_form_id', $rowApprovalOngoing->reference_id)
    //                                 ->where('a.is_active', '1')
    //                                 ->orderBy('a.order_form_id', 'desc')
    //                                 ->first();

    //                 if ($getForm) {
    //                     $getFormItem = $model->documentApprovalDetailOrderItem()
    //                                         ->from('doc_approval_detail_order_form_item')
    //                                         ->select('appliance_item', 'brand_type', 'unit_name', 'unit_quantity')
    //                                         ->where('doc_approval_id', $rowApprovalOngoing->doc_approval_id)
    //                                         ->where('order_form_id', $getForm->order_form_id)
    //                                         ->where('unit_name', '!=', 'VAT')
    //                                         ->where('is_active', '1')
    //                                         ->whereNull('canceled_id')
    //                                         ->orderBy('order_detail_id', 'asc')
    //                                         ->get();

    //                     $items = '';
    //                     $no = 1;
    //                     foreach ($getFormItem as $rowItem) {
    //                         $items .= $items != '' ? '<br>' : '';
    //                         $items .= count($getFormItem) > 1 ? $no.'. '.$rowItem->appliance_item : $rowItem->appliance_item;
    //                         $no++;
    //                     }

    //                     $grandTotal = $this->formatNumber($getForm->grand_total).' '.$getForm->currency_code;
    //                     $docVersion = $revision = '';
    //                     if($getForm->doc_version > 1) {
    //                         $docVersion = $getForm->doc_version - 1;
    //                         $revision = ' (REVISION '.$docVersion.')';
    //                     }

    //                     if(!in_array($rowApprovalOngoing->doc_type_id, $arrOngoingDocType)) {
    //                         $arrOngoingDocType[] = $rowApprovalOngoing->doc_type_id;
    //                     }

    //                     $arrOngoing[$rowApprovalOngoing->doc_type_id][] = [
    //                         'documentType' => $rowApprovalOngoing->doc_name,
    //                         'employeeName' => Str::upper($rowApprovalOngoing->employee_name),
    //                         'departmentName' => $rowApprovalOngoing->department_name,
    //                         'docNumber' => $rowApprovalOngoing->doc_number,
    //                         'items' => $items,
    //                         'grandTotal' => $grandTotal,
    //                     ];
    //                 }
    //             }
    //             else if($rowApprovalOngoing->doc_type_id == '2') {
    //                 // APPLICATION & PO FORM
    //                 $getForm = $model->docApprovalOrderApplication()
    //                                 ->from('doc_approval_order_application as a')
    //                                 ->select('a.*')
    //                                 ->where('a.doc_approval_id', $rowApprovalOngoing->doc_approval_id)
    //                                 ->where('a.application_id', $rowApprovalOngoing->reference_id)
    //                                 ->where('a.is_active', '1')
    //                                 ->orderBy('a.application_id', 'desc')
    //                                 ->first();

    //                 if ($getForm) {
    //                     $getFormItem = $model->documentApprovalDetailOrderItem()
    //                                         ->from('doc_approval_order_application_item')
    //                                         ->select('appliance_item', 'quantity')
    //                                         ->where('application_id', $getForm->application_id)
    //                                         // ->where('appliance_item', '!=', 'VAT')
    //                                         ->whereNotNull('order_detail_id')
    //                                         ->where('is_active', '1')
    //                                         ->orderBy('application_item_id', 'asc')
    //                                         ->get();

    //                     $items = '';
    //                     $no = 1;
    //                     foreach ($getFormItem as $rowItem) {
    //                         $items .= $items != '' ? '<br>' : '';
    //                         $items .= count($getFormItem) > 1 ? $no.'. '.$rowItem->appliance_item : $rowItem->appliance_item;
    //                         $no++;
    //                     }

    //                     $grandTotal = $this->formatNumber($getForm->application_grand_total).' '.$getForm->application_currency;
    //                     $docVersion = $revision = '';
    //                     if($getForm->doc_version > 1) {
    //                         $docVersion = $getForm->doc_version - 1;
    //                         $revision = ' (REVISION '.$docVersion.')';
    //                     }

    //                     if(!in_array($rowApprovalOngoing->doc_type_id, $arrOngoingDocType)) {
    //                         $arrOngoingDocType[] = $rowApprovalOngoing->doc_type_id;
    //                     }

    //                     $arrOngoing[$rowApprovalOngoing->doc_type_id][] = [
    //                         'documentType' => $rowApprovalOngoing->doc_name,
    //                         'employeeName' => Str::upper($rowApprovalOngoing->employee_name),
    //                         'departmentName' => $rowApprovalOngoing->department_name,
    //                         'docNumber' => $rowApprovalOngoing->doc_number,
    //                         'items' => $items,
    //                         'grandTotal' => $grandTotal,
    //                     ];
    //                 }
    //             }
    //         }

    //         if($arrOngoing) {
    //             $mailTo = $rowApprovalReminder['employee_email'];
    //             $token = ['cid' => $rowApprovalReminder['company_id'], 'id' => $rowApprovalReminder['employee_id'],'a' => null,'b' => null,'c' =>null];
    //             $tokenApproval = SafeToken::encode($token);

    //             $mailSubject ='BATCH DOCUMENT APPROVAL';
    //             $mailBody = '';
    //             $mailBody .= '<table style="border-collapse:collapse;border:0;width:100%;font-size:13px;line-height:1.7;">
    //                                 <tr>
    //                                     <th colspan="4" style="font-weight:normal;padding:10px 5px 20px 5px;text-align:left">Dear '.Str::upper($rowApprovalReminder['employee_name']).', <br/><br/>You have received batch document to review.</th>
    //                                 </tr>
    //                             </table>';

    //             for($x = 0; $x < count($arrOngoingDocType); $x++) {
    //                 if($arrOngoingDocType[$x] == '1') { // 1 = ORDER FORM
    //                     $mailBody .= '<table style="border-collapse:collapse;border: 1px solid #c7c7c7;width:100%;font-size:13px;">
    //                                 <thead>
    //                                     <tr>
    //                                         <th style="background-color:#c2d9fc;border: 0;"></th>
    //                                         <th colspan="5" style="padding:5px;background-color:#c2d9fc;border: 0;text-align:left"> TYPE : '.$arrOngoing[$arrOngoingDocType[$x]][0]['documentType'].'</th>
    //                                     </tr>
    //                                     <tr>
    //                                         <th style="width:5%;padding:5px;background-color:#c2d9fc;border-bottom: 1px solid #c7c7c7;">NO.</th>
    //                                         <th style="width:20%;padding:5px;background-color:#c2d9fc;border-bottom: 1px solid #c7c7c7;text-align:left">FORM NO.</th>
    //                                         <th style="width:15%;padding:5px;background-color:#c2d9fc;border-bottom: 1px solid #c7c7c7;text-align:left">DEPARTMENT</th>
    //                                         <th style="width:15%;padding:5px;background-color:#c2d9fc;border-bottom: 1px solid #c7c7c7;text-align:left">APPLICANT</th>
    //                                         <th style="width:25%;padding:5px;background-color:#c2d9fc;border-bottom: 1px solid #c7c7c7;text-align:left">ITEMS</th>
    //                                         <th style="width:20%;padding:5px;background-color:#c2d9fc;border-bottom: 1px solid #c7c7c7;text-align:right">GRAND TOTAL (EST.)</th>
    //                                     </tr>
    //                                 </thead>
    //                                 <tbody>';

    //                     $no = 1;
    //                     foreach ($arrOngoing[$arrOngoingDocType[$x]] as $rowOngoing) {
    //                         $mailBody .= '<tr>
    //                                         <td valign="top" style="padding:5px;border-bottom: 1px solid #c7c7c7;text-align:center">'.$no.'</td>
    //                                         <td valign="top" style="padding:5px;border-bottom: 1px solid #c7c7c7;">'.$rowOngoing['docNumber'].'</td>
    //                                         <td valign="top" style="padding:5px;border-bottom: 1px solid #c7c7c7;">'.$rowOngoing['departmentName'].'</td>
    //                                         <td valign="top" style="padding:5px;border-bottom: 1px solid #c7c7c7;">'.$rowOngoing['employeeName'].'</td>
    //                                         <td valign="top" style="padding:5px;border-bottom: 1px solid #c7c7c7;">'.$rowOngoing['items'].'</td>
    //                                         <td valign="top" style="padding:5px;border-bottom: 1px solid #c7c7c7;text-align:right">'.$rowOngoing['grandTotal'].'</td>
    //                                     </tr>';
    //                         $no++;
    //                     }

    //                         $mailBody .= '</tbody>
    //                                 </table>';
    //                 }
    //                 else if($arrOngoingDocType[$x] == '2') { // 2 = APPLICATION & PO FORM
    //                     $mailBody .= '<table style="border-collapse:collapse;border: 1px solid #c7c7c7;width:100%;font-size:13px;">
    //                                 <thead>
    //                                     <tr>
    //                                         <th style="background-color:#c2d9fc;border: 0;"></th>
    //                                         <th colspan="5" style="padding:5px;background-color:#c2d9fc;border: 0;text-align:left"> TYPE : '.$arrOngoing[$arrOngoingDocType[$x]][0]['documentType'].'</th>
    //                                     </tr>
    //                                     <tr>
    //                                         <th style="width:5%;padding:5px;background-color:#c2d9fc;border-bottom: 1px solid #c7c7c7;">NO.</th>
    //                                         <th style="width:20%;padding:5px;background-color:#c2d9fc;border-bottom: 1px solid #c7c7c7;text-align:left">FORM NO.</th>
    //                                         <th style="width:15%;padding:5px;background-color:#c2d9fc;border-bottom: 1px solid #c7c7c7;text-align:left">DEPARTMENT</th>
    //                                         <th style="width:15%;padding:5px;background-color:#c2d9fc;border-bottom: 1px solid #c7c7c7;text-align:left">APPLIED BY</th>
    //                                         <th style="width:25%;padding:5px;background-color:#c2d9fc;border-bottom: 1px solid #c7c7c7;text-align:left">ITEMS</th>
    //                                         <th style="width:20%;padding:5px;background-color:#c2d9fc;border-bottom: 1px solid #c7c7c7;text-align:right">GRAND TOTAL (EST.)</th>
    //                                     </tr>
    //                                 </thead>
    //                                 <tbody>';

    //                     $no = 1;
    //                     foreach ($arrOngoing[$arrOngoingDocType[$x]] as $rowOngoing) {
    //                         $mailBody .= '<tr>
    //                                         <td valign="top" style="padding:5px;border-bottom: 1px solid #c7c7c7;text-align:center">'.$no.'</td>
    //                                         <td valign="top" style="padding:5px;border-bottom: 1px solid #c7c7c7;">'.$rowOngoing['docNumber'].'</td>
    //                                         <td valign="top" style="padding:5px;border-bottom: 1px solid #c7c7c7;">'.$rowOngoing['departmentName'].'</td>
    //                                         <td valign="top" style="padding:5px;border-bottom: 1px solid #c7c7c7;">'.$rowOngoing['employeeName'].'</td>
    //                                         <td valign="top" style="padding:5px;border-bottom: 1px solid #c7c7c7;">'.$rowOngoing['items'].'</td>
    //                                         <td valign="top" style="padding:5px;border-bottom: 1px solid #c7c7c7;text-align:right">'.$rowOngoing['grandTotal'].'</td>
    //                                     </tr>';
    //                         $no++;
    //                     }

    //                         $mailBody .= '</tbody>
    //                                 </table>';
    //                 }
    //                 else if($arrOngoingDocType[$x] == '7') {

    //                 }
    //             }

    //             $mailBody .= '<table style="border-collapse: collapse; border: 0; width: 80%; margin : 40px auto 40px auto;">
    //                             <tr>
    //                                 <td valign="middle" style="text-align:center">
    //                                 <!--[if mso]>
    //                                     <v:roundrect xmlns:v="urn:schemas-microsoft-com:vml" xmlns:w="urn:schemas-microsoft-com:office:word" href="'.url('/doc_approval/view?source=EMAIL&token='.$tokenApproval).'" style="height:40px; v-text-anchor:middle; width: 270px;" arcsize="1" stroke="f" fillcolor="#115fad">
    //                                         <w:anchorlock/>
    //                                         <center style="color:#ebebeb; font-family:sans-serif; font-size:13px; font-weight:bold; text-decoration: none">VIEW ALL DOCUMENT</center>
    //                                     </v:roundrect>
    //                                 <![endif]-->
    //                                 <!--[if !mso]> <!-->
    //                                     <a href="'.url('/doc_approval/view?source=EMAIL&token='.$tokenApproval).'" target="_blank" style="font-size: 13px; border-radius: 10px; color: #ebebeb; background-color: #115fad; font-weight: bold; font-family:sans-serif; text-decoration: none; color: #ebebeb; padding: 8px 15px 8px 15px;">VIEW ALL DOCUMENT</a>
    //                                 <!-- <![endif]-->
    //                                 </td>
    //                             </tr>
    //                         </table>';
    //             $mailBody .= "<div style='font-size:13px; margin-top:12px; display:block;font-weight:bold'>This information can also be found at https://app.logisteed.id menu Document Approval &#8594; My Approval.</div>
    //             <div style='font-size:13px; margin-top:7px; margin-bottom:20px; display:block; font-style:italic'>This email was generated automatically by system, please don't reply this email.</div>";

    //             $cc = null;
    //             $bcc = 'krisman.silalahi.pnb@logisteed.com';
    //             dispatch(new SendEmailJob(
    //                 $accountId, // ACCOUNT ID
    //                 $mailTo, // TO
    //                 $cc,
    //                 $bcc,
    //                 $mailSubject, // SUBJECT
    //                 $mailBody, // BODY
    //                 'normal', // PRIORITY = normal, low, high
    //                 [], // ATTACHMENT ARRAY
    //             ));
    //         }
    //     }
    // }

    private function formatNumber($number) {
        return $number == round($number) ?
            number_format($number, 0, '.', ',') :
            number_format($number, 2, '.', ',');
    }
}
