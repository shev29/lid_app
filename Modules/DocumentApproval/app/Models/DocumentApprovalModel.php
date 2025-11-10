<?php

namespace Modules\DocumentApproval\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\DocumentApproval\Database\Factories\DocumentApprovalModelFactory;

class DocumentApprovalModel extends Model
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

    public function documentApprovalDetailOrder(){
        $this->setTableAttributes('doc_approval_detail_order_form', []);
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

    public function documentApprovalDetailOrderPurpose(){
        $this->setTableAttributes('doc_approval_detail_order_form_purpose', []);
        return $this;
    }

    public function docApprovalFlowSign(){
        $this->setTableAttributes('doc_approval_flow_sign', []);
        return $this;
    }

    public function docApprovalFlowComment(){
        $this->setTableAttributes('doc_approval_flow_comment', []);
        return $this;
    }

    public function docApprovalDetailOrderFormItemCanceled()
    {
        $this->setTableAttributes('doc_approval_detail_order_form_item_canceled', []);
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

    public function docApprovalOrderApplicationItem() {
        $this->setTableAttributes('doc_approval_order_application_item', []);
        return $this;
    }

    public function docApprovalOrderApplicationCostCenter() {
        $this->setTableAttributes('doc_approval_order_application_cost_center', []);
        return $this;
    }

    public function docApprovalOrderComparison()
    {
        $this->setTableAttributes('doc_approval_order_comparison', []);
        return $this;
    }

    public function docApprovalOrderComparisonItem()
    {
        $this->setTableAttributes('doc_approval_order_comparison_item', []);
        return $this;
    }

    public function docApprovalOrderComparisonVendor()
    {
        $this->setTableAttributes('doc_approval_order_comparison_vendor', []);
        return $this;
    }

    public function docApprovalOrderComparisonItemVendor()
    {
        $this->setTableAttributes('doc_approval_order_comparison_item_vendor', []);
        return $this;
    }

    public function docApprovalOrderInspection() {
        $this->setTableAttributes('doc_approval_order_inspection', []);
        return $this;
    }

    public function docApprovalOrderInspectionItem() {
        $this->setTableAttributes('doc_approval_order_inspection_item', []);
        return $this;
    }

    public function docApprovalOrderInspectionChecklist() {
        $this->setTableAttributes('doc_approval_order_inspection_checklist', []);
        return $this;
    }

    public function docApprovalOrderInvoice() {
        $this->setTableAttributes('doc_approval_order_invoice', []);
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

    public function docApprovalOrderPartner(){
        $this->setTableAttributes('doc_approval_order_partner', []);
        return $this;
    }

    public function docApprovalOrderPartnerDetail(){
        $this->setTableAttributes('doc_approval_order_partner_detail', []);
        return $this;
    }

    public function masterRoleUsers() {
        $this->setTableAttributes('master_role_users', []);
        return $this;
    }

    public function masterRoleDetails() {
        $this->setTableAttributes('master_role_details', []);
        return $this;
    }


}
