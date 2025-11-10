<?php

namespace Modules\DocumentApproval\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MigrationBmlLogModel extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $table;

    // protected $fillable = [];
    protected $guarded = [];

    /**
     * Set the table name and guard attributes dynamically
     */
    public function setTableAttributes($table, $guard) {
        $this->table = $table;
        $this->guard = $guard;
    }

    public function docApprovalReminder() {
        $this->setTableAttributes('doc_approval_reminder', []);
        return $this;
    }

    public function documentApprovalHeader(){
        $this->setTableAttributes('doc_approval_header', []);
        return $this;
    }

    public function documentApprovalHeaderOld(){
        $this->setTableAttributes('doc_approval_header_old', []);
        return $this;
    }

    public function documentApprovalDetailOrder(){
        $this->setTableAttributes('doc_approval_detail_order_form', []);
        return $this;
    }

    public function documentApprovalDetailOrderOld(){
        $this->setTableAttributes('doc_approval_detail_order_form_old', []);
        return $this;
    }

    public function documentApprovalOrderGroup(){
        $this->setTableAttributes('doc_approval_order_group', []);
        return $this;
    }

    public function documentApprovalDetailOrderItem(){
        $this->setTableAttributes('doc_approval_detail_order_form_item', []);
        return $this;
    }

    public function documentApprovalDetailOrderItemOld(){
        $this->setTableAttributes('doc_approval_detail_order_form_item_old', []);
        return $this;
    }

    public function documentApprovalDetailOrderItemCancel(){
        $this->setTableAttributes('doc_approval_detail_order_form_item_canceled', []);
        return $this;
    }

    public function documentApprovalDetailOrderItemCancelOld(){
        $this->setTableAttributes('doc_approval_detail_order_form_item_canceled_old', []);
        return $this;
    }

    public function documentApprovalDetailOrderPurpose(){
        $this->setTableAttributes('doc_approval_detail_order_form_purpose', []);
        return $this;
    }

    public function documentApprovalDetailOrderPurposeOld(){
        $this->setTableAttributes('doc_approval_detail_order_form_purpose_old', []);
        return $this;
    }

    public function docApprovalFlowSign(){
        $this->setTableAttributes('doc_approval_flow_sign', []);
        return $this;
    }

    public function docApprovalFlowSignOld(){
        $this->setTableAttributes('doc_approval_flow_sign_old', []);
        return $this;
    }

    public function docApprovalFlowComment(){
        $this->setTableAttributes('doc_approval_flow_comment', []);
        return $this;
    }

    public function docApprovalFlowCommentOld(){
        $this->setTableAttributes('doc_approval_flow_comment_old', []);
        return $this;
    }

    public function masterDocumentTypes()
    {
        $this->setTableAttributes('master_doc_types', []);
        return $this;
    }

    public function masterCurrency()
    {
        $this->setTableAttributes('master_currency', []);
        return $this;
    }

    public function masterPriorityLevel()
    {
        $this->setTableAttributes('master_priority_level', []);
        return $this;
    }

    public function masterUnit()
    {
        $this->setTableAttributes('master_unit_categories', []);
        return $this;
    }

    public function masterLocations()
    {
        $this->setTableAttributes('master_locations', []);
        return $this;
    }

    public function masterDocPurpose()
    {
        $this->setTableAttributes('master_doc_purpose', []);
        return $this;
    }

    public function masterDocPurposeReason()
    {
        $this->setTableAttributes('master_doc_purpose_reason', []);
        return $this;
    }

    public function masterDocPurposeReasonOption()
    {
        $this->setTableAttributes('master_doc_purpose_reason_option', []);
        return $this;
    }

    public function masterEmployee()
    {
        $this->setTableAttributes('master_employees', []);
        return $this;
    }

    public function vwMasterEmployeeAll()
    {
        $this->setTableAttributes('vw_master_employee_all', []);
        return $this;
    }

    public function vwMasterEmployeeActive()
    {
        $this->setTableAttributes('vw_master_employee_active', []);
        return $this;
    }

    public function vwMasterLocation()
    {
        $this->setTableAttributes('vw_master_location', []);
        return $this;
    }

    public function vwMasterCostCenter()
    {
        $this->setTableAttributes('vw_master_cost_center', []);
        return $this;
    }

    public function vwOrgStructure()
    {
        $this->setTableAttributes('vw_org_structure', []);
        return $this;
    }

    public function vwDocApprovalOngoing()
    {
        $this->setTableAttributes('vw_doc_approval_sign_ongoing', []);
        return $this;
    }

    public function vwDocApprovalRequestHistory()
    {
        $this->setTableAttributes('vw_doc_approval_request_history', []);
        return $this;
    }

    public function vwDocApprovalHistory()
    {
        $this->setTableAttributes('vw_doc_approval_sign_history', []);
        return $this;
    }

    public function masterGoods()
    {
        $this->setTableAttributes('master_goods', []);
        return $this;
    }

    public function masterGoodsType()
    {
        $this->setTableAttributes('master_goods_type', []);
        return $this;
    }

    public function masterDepartment()
    {
        $this->setTableAttributes('master_department', []);
        return $this;
    }

    public function vwMasterDepartment()
    {
        $this->setTableAttributes('vw_master_department', []);
        return $this;
    }

    public function multiDepartment()
    {
        $this->setTableAttributes('doc_approval_multi_department', []);
        return $this;
    }

    public function masterVat()
    {
        $this->setTableAttributes('master_vat', []);
        return $this;
    }

    public function seqNumber()
    {
        $this->setTableAttributes('doc_approval_seq_number', []);
        return $this;
    }

    public function masterCompany()
    {
        $this->setTableAttributes('master_company', []);
        return $this;
    }

    public function costCenter()
    {
        $this->setTableAttributes('master_cost_center', []);
        return $this;
    }

    public function docApprovalMatrix()
    {
        $this->setTableAttributes('doc_approval_matrix', []);
        return $this;
    }

    public function vwDocApprovalMatrix()
    {
        $this->setTableAttributes('vw_doc_approval_matrix', []);
        return $this;
    }

    public function docApprovalAttachment() {
        $this->setTableAttributes('doc_approval_attachment', []);
        return $this;
    }

    public function docApprovalAttachmentOld() {
        $this->setTableAttributes('doc_approval_attachment_old', []);
        return $this;
    }

    public function docApprovalGroupFlow() {
        $this->setTableAttributes('doc_approval_group_flow', []);
        return $this;
    }

    public function vwOngoingOrderForm() {
        $this->setTableAttributes('vw_ongoing_order_form', []);
        return $this;
    }

    public function docApprovalOrderApplication() {
        $this->setTableAttributes('doc_approval_order_application', []);
        return $this;
    }

    public function docApprovalOrderApplicationOld() {
        $this->setTableAttributes('doc_approval_order_application_old', []);
        return $this;
    }

    public function docApprovalOrderApplicationItem() {
        $this->setTableAttributes('doc_approval_order_application_item', []);
        return $this;
    }

    public function docApprovalOrderApplicationItemOld() {
        $this->setTableAttributes('doc_approval_order_application_item_old', []);
        return $this;
    }

    public function docApprovalOrderApplicationCostCenter() {
        $this->setTableAttributes('doc_approval_order_application_cost_center', []);
        return $this;
    }

    public function docApprovalOrderApplicationCostCenterOld() {
        $this->setTableAttributes('doc_approval_order_application_cost_center_old', []);
        return $this;
    }

    public function docApprovalOrderComparison()
    {
        $this->setTableAttributes('doc_approval_order_comparison', []);
        return $this;
    }

    public function docApprovalOrderComparisonOld()
    {
        $this->setTableAttributes('doc_approval_order_comparison_old', []);
        return $this;
    }

    public function docApprovalOrderComparisonItem()
    {
        $this->setTableAttributes('doc_approval_order_comparison_item', []);
        return $this;
    }

    public function docApprovalOrderComparisonItemOld()
    {
        $this->setTableAttributes('doc_approval_order_comparison_item_old', []);
        return $this;
    }

    public function docApprovalOrderComparisonVendor()
    {
        $this->setTableAttributes('doc_approval_order_comparison_vendor', []);
        return $this;
    }

    public function docApprovalOrderComparisonVendorOld()
    {
        $this->setTableAttributes('doc_approval_order_comparison_vendor_old', []);
        return $this;
    }

    public function docApprovalOrderComparisonItemVendor()
    {
        $this->setTableAttributes('doc_approval_order_comparison_item_vendor', []);
        return $this;
    }

    public function docApprovalOrderComparisonItemVendorOld()
    {
        $this->setTableAttributes('doc_approval_order_comparison_item_vendor_old', []);
        return $this;
    }

    public function docApprovalOrderInspection() {
        $this->setTableAttributes('doc_approval_order_inspection', []);
        return $this;
    }

    public function docApprovalOrderInspectionOld() {
        $this->setTableAttributes('doc_approval_order_inspection_old', []);
        return $this;
    }

    public function docApprovalOrderInspectionItem() {
        $this->setTableAttributes('doc_approval_order_inspection_item', []);
        return $this;
    }

    public function docApprovalOrderInspectionItemOld() {
        $this->setTableAttributes('doc_approval_order_inspection_item_old', []);
        return $this;
    }

    public function docApprovalOther() {
        $this->setTableAttributes('doc_approval_other', []);
        return $this;
    }

    public function masterFlowRuleGroup(){
        $this->setTableAttributes('master_flow_rule_group', []);
        return $this;
    }

    public function masterFlowRuleAction(){
        $this->setTableAttributes('master_flow_rule_action', []);
        return $this;
    }

    public function masterEmployeesMultiCompany(){
        $this->setTableAttributes('master_employees_multi_company', []);
        return $this;
    }

    public function masterVendor()
    {
        $this->setTableAttributes('master_vendor', []);
        return $this;
    }

    public function masterVendorOld()
    {
        $this->setTableAttributes('master_vendor_old', []);
        return $this;
    }

    public function masterVendorAddress()
    {
        $this->setTableAttributes('master_vendor_address', []);
        return $this;
    }

    public function masterVendorComponents()
    {
        $this->setTableAttributes('master_vendor_components', []);
        return $this;
    }

    public function masterVendorPic()
    {
        $this->setTableAttributes('master_vendor_pic', []);
        return $this;
    }

    public function masterVendorPicOld()
    {
        $this->setTableAttributes('master_vendor_pic_old', []);
        return $this;
    }

    public function docApprovalOrderApplicationReimburse()
    {
        $this->setTableAttributes('doc_approval_order_application_reimburse', []);
        return $this;
    }

    public function docApprovalOrderApplicationBudget()
    {
        $this->setTableAttributes('doc_approval_order_application_budget', []);
        return $this;
    }
}
