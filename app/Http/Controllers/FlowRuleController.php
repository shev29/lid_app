<?php

namespace App\Http\Controllers;

use App\Services\FlowRuleServiceModel;
use App\Services\RuleEngineService;
use Exception;
use Illuminate\Http\Request;

class FlowRuleController extends Controller
{
    protected $ruleEngineService;
    protected $flowRuleServiceModel;

    public function __construct(RuleEngineService $ruleEngineService, FlowRuleServiceModel $flowRuleServiceModel)
    {
        $this->ruleEngineService = $ruleEngineService;
        $this->flowRuleServiceModel = $flowRuleServiceModel;
    }

    /**
     * CHECK FLOW RULE BASED ON PROVIDED DATA
     */
    public function checkFlowRule(Request $request)
    {
        try {
            $employeeId = $request->input('employeeId');
            $ruleTypeId = $request->input('ruleTypeId');
            $data = $request->input('data', []); // GENERIC DATA ARRAY

            // GET RULES FROM DATABASE
            $rules = $this->flowRuleServiceModel->getRules($employeeId, $ruleTypeId);

            // EVALUATE RULES
            $matchingRuleGroupId = $this->ruleEngineService->evaluateRules($data, $rules);

            return response()->json([
                'success' => true,
                'rule_group_id' => $matchingRuleGroupId,
                'message' => $matchingRuleGroupId ? 'RULE MATCHED' : 'NO MATCHING RULE FOUND'
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'ERROR EVALUATING RULES: ' . $e->getMessage()
            ], 500);
        }
    }
}