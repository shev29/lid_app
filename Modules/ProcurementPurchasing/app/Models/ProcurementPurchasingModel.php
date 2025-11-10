<?php

namespace Modules\ProcurementPurchasing\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Modules\ProcurementPurchasing\Database\Factories\ProcurementPurchasingModelFactory;

class ProcurementPurchasingModel extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $table;

    // protected $fillable = [];
    protected $guarded = [];

    public function setTableAttributes($table, $guard) {
        $this->table = $table;
        $this->guard = $guard;
    }

    public function masterDepartment()
    {
        $this->setTableAttributes('master_department', []);
        return $this;
    }

    public function multiDepartment()
    {
        $this->setTableAttributes('doc_approval_multi_department', []);
        return $this;
    }

    public function masterLocations()
    {
        $this->setTableAttributes('master_locations', []);
        return $this;
    }

    public function vwMasterCostCenter()
    {
        $this->setTableAttributes('vw_master_cost_center', []);
        return $this;
    }

    public function masterVendor()
    {
        $this->setTableAttributes('master_vendor', []);
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

    public function masterVendorPurchaseType()
    {
        $this->setTableAttributes('master_vendor_purchase_type', []);
        return $this;
    }

    public function masterVendorSite()
    {
        $this->setTableAttributes('master_vendor_site', []);
        return $this;
    }

    public function masterDelivery()
    {
        $this->setTableAttributes('master_purchase_delivery', []);
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

    public function docApprovalOrderApplication()
    {
        $this->setTableAttributes('doc_approval_order_application', []);
        return $this;
    }

    public function docApprovalOrderApplicationItem()
    {
        $this->setTableAttributes('doc_approval_order_application_item', []);
        return $this;
    }

    public function docApprovalOrderApplicationCostCenter()
    {
        $this->setTableAttributes('doc_approval_order_application_cost_center', []);
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

    public function masterBudget()
    {
        $this->setTableAttributes('master_budget', []);
        return $this;
    }

    public function docApprovalFlowComment(){
        $this->setTableAttributes('doc_approval_flow_comment', []);
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

    public function docApprovalDetailOrderFormPurpose(){
        $this->setTableAttributes('doc_approval_detail_order_form_purpose', []);
        return $this;
    }

    public function docApprovalDetailOrderFormItemCanceled()
    {
        $this->setTableAttributes('doc_approval_detail_order_form_item_canceled', []);
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

    public function employeeAll()
    {
        return $this->belongsTo(ProcurementPurchasingModel::class, 'employee_id')
            ->vwMasterEmployeeAll();
    }

    public function vwMasterEmployeeActive()
    {
        $this->setTableAttributes('vw_master_employee_active', []);
        return $this;
    }

    public function employeeActive()
    {
        return $this->belongsTo(ProcurementPurchasingModel::class, 'created_by')
            ->vwMasterEmployeeActive();
    }

    public function vwOngoingApplication()
    {
        $this->setTableAttributes('vw_ongoing_application', []);
        return $this;
    }

    public function vwOngoingApplicationCompleted()
    {
        $this->setTableAttributes('vw_ongoing_application_completed', []);
        return $this;
    }

    public function masterCurrency()
    {
        $this->setTableAttributes('master_currency', []);
        return $this;
    }

    public function masterUnit()
    {
        $this->setTableAttributes('master_unit_categories', []);
        return $this;
    }

    public function masterCostCenter()
    {
        $this->setTableAttributes('master_cost_center', []);
        return $this;
    }

    public function masterPurchaseType()
    {
        $this->setTableAttributes('master_purchase_type', []);
        return $this;
    }

    public function masterPurchaseTypeGroup()
    {
        $this->setTableAttributes('master_purchase_type_group', []);
        return $this;
    }

    public function masterPurchaseComponents()
    {
        $this->setTableAttributes('master_purchase_components', []);
        return $this;
    }

    public function masterCompany()
    {
        $this->setTableAttributes('master_company', []);
        return $this;
    }

    public function company()
    {
        return $this->belongsTo(ProcurementPurchasingModel::class, 'company_id')
            ->masterCompany();
    }

    public function multiCompany()
    {
        $this->setTableAttributes('master_employees_multi_company', []);
        return $this;
    }

    public function masterPriorityLevel()
    {
        $this->setTableAttributes('master_priority_level', []);
        return $this;
    }

    public function masterTermPayment()
    {
        $this->setTableAttributes('master_term_payment', []);
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

    public function docApprovalFlowSign(){
        $this->setTableAttributes('doc_approval_flow_sign', []);
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

    public function masterFlowRuleGroup() {
        $this->setTableAttributes('master_flow_rule_group', []);
        return $this;
    }

    public function masterFlowRuleCondition() {
        $this->setTableAttributes('master_flow_rule_condition', []);
        return $this;
    }

    public function masterFlowRuleAction() {
        $this->setTableAttributes('master_flow_rule_action', []);
        return $this;
    }

    public function masterFlowRuleActionType() {
        $this->setTableAttributes('master_flow_rule_action_type', []);
        return $this;
    }

    public function masterFlowRuleField() {
        $this->setTableAttributes('master_flow_rule_field', []);
        return $this;
    }

    public function masterFlowRuleFieldOption() {
        $this->setTableAttributes('master_flow_rule_field_option', []);
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
