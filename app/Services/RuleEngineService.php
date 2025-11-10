<?php

namespace App\Services;

class RuleEngineService
{
    /**
     * EVALUATE RULES AND RETURN MATCHING RULE GROUP ID
     * 
     * @param array $data - DATA TO BE EVALUATED (KEY-VALUE PAIRS)
     * @param array $rules - RULES FROM DATABASE
     * @return int|null - MATCHING RULE GROUP ID OR NULL
     */

    // // ====================================
    // // USAGE EXAMPLES WITH PROPER FIELD MAPPING
    // // ====================================

    // // Example 1: Purchase Flow (matching your database structure)
    // $data = [
    //     'PAYMENT TYPE' => 'Leasing',          // matches b.field = 'PAYMENT TYPE'
    //     'TOTAL AMOUNT' => 800000,             // matches b.field = 'TOTAL AMOUNT'
    //     'PURCHASE TYPE' => 'Tax'              // matches b.field = 'PURCHASE TYPE'
    // ];

    // // Example 2: Get rules from database (using your model structure)
    // $rules = $this->getRules($employeeId, $ruleTypeId); // returns the query result as array

    // // Example 3: Debug structure to see priority and conditions
    // $ruleEngine = new RuleEngineService();
    // $debugInfo = $ruleEngine->debugConditionStructure($rules);
    // // This will show:
    // // - order_rule_group priority (1 = highest priority)
    // // - order_condition within each rule group
    // // - condition grouping by group_condition field

    // // Example 4: Evaluate rules
    // $result = $ruleEngine->evaluateRules($data, $rules);
    // // Should return rule_group_id 10 (order=3) before rule_group_id 6 (order=5)

    public function evaluateRules(array $data, array $rules)
    {
        // GROUP RULES BY RULE_GROUP_ID
        $groupedRules = $this->groupRulesByRuleGroup($rules);

        // SORT RULE GROUPS BY order_rule_group (ASCENDING - SMALLER ORDER = HIGHER PRIORITY)
        uasort($groupedRules, function($a, $b) {
            $orderA = $a['order_rule_group'] ?? 999;
            $orderB = $b['order_rule_group'] ?? 999;

            // Handle null values - put them at the end
            if ($orderA === null) $orderA = 999;
            if ($orderB === null) $orderB = 999;

            return $orderA - $orderB;
        });

        // CHECK EACH RULE GROUP IN ORDER OF PRIORITY
        foreach ($groupedRules as $ruleGroupId => $ruleGroup) {
            if ($this->evaluateRuleGroup($ruleGroup, $data)) {
                return $ruleGroupId;
            }
        }
        
        return null;
    }

    /**
     * GROUP RULES BY RULE_GROUP_ID
     */
    private function groupRulesByRuleGroup(array $rules)
    {
        $grouped = [];
        
        foreach ($rules as $rule) {
            $ruleGroupId = $rule['rule_group_id'] ?? $rule->rule_group_id;
            
            if (!isset($grouped[$ruleGroupId])) {
                // Get order_rule_group from master_flow_rule_group.order
                $orderRuleGroup = $rule['order_rule_group'] ?? $rule->order_rule_group ?? 999;

                $grouped[$ruleGroupId] = [
                    'order_rule_group' => $orderRuleGroup,
                    'conditions' => []
                ];
            }
            
            $conditionId = $rule['condition_id'] ?? $rule->condition_id;
            if ($conditionId) {
                $grouped[$ruleGroupId]['conditions'][] = $rule;
            }
        }
        
        // SORT CONDITIONS WITHIN EACH GROUP BY order_condition (master_flow_rule_condition.order)
        foreach ($grouped as &$group) {
            usort($group['conditions'], function($a, $b) {
                $orderA = $a['order_condition'] ?? $a->order_condition ?? 999;
                $orderB = $b['order_condition'] ?? $b->order_condition ?? 999;

                // Handle null values
                if ($orderA === null) $orderA = 999;
                if ($orderB === null) $orderB = 999;

                return $orderA - $orderB;
            });
        }
        
        return $grouped;
    }

    /**
     * EVALUATE A SINGLE RULE GROUP WITH PROPER CONDITION GROUPING
     */
    private function evaluateRuleGroup(array $ruleGroup, array $data)
    {
        $conditions = $ruleGroup['conditions'];
        if (empty($conditions)) {
            return false;
        }

        // GROUP CONDITIONS BY group_condition
        $conditionGroups = $this->groupConditionsByGroup($conditions);

        // EVALUATE EACH CONDITION GROUP
        $groupResults = [];
        foreach ($conditionGroups as $groupId => $groupConditions) {
            $groupResults[$groupId] = $this->evaluateConditionGroup($groupConditions, $data);
        }

        // COMBINE GROUP RESULTS BASED ON LOGICAL OPERATORS
        return $this->combineGroupResults($groupResults, $conditionGroups);
    }

    /**
     * GROUP CONDITIONS BY group_condition FIELD
     */
    private function groupConditionsByGroup(array $conditions)
    {
        $groups = [];

        foreach ($conditions as $condition) {
            $groupId = $condition['group_condition'] ?? $condition->group_condition ?? 1;

            if (!isset($groups[$groupId])) {
                $groups[$groupId] = [];
            }

            $groups[$groupId][] = $condition;
        }

        // SORT GROUPS BY KEY
        ksort($groups);

        return $groups;
    }

    /**
     * EVALUATE CONDITIONS WITHIN A SINGLE GROUP
     */
    private function evaluateConditionGroup(array $conditions, array $data)
    {
        if (empty($conditions)) {
            return false;
        }

        $result = null;
        $currentLogicalOperator = null;

        foreach ($conditions as $condition) {
            $conditionResult = $this->evaluateCondition($condition, $data);

            if ($result === null) {
                $result = $conditionResult;
            } else {
                if ($currentLogicalOperator === 'AND') {
                    $result = $result && $conditionResult;
                } elseif ($currentLogicalOperator === 'OR') {
                    $result = $result || $conditionResult;
                }
            }

            // GET LOGICAL OPERATOR FOR NEXT ITERATION
            $currentLogicalOperator = $condition['logical_operator'] ?? $condition->logical_operator ?? null;
        }

        return $result;
    }

    /**
     * COMBINE RESULTS FROM DIFFERENT CONDITION GROUPS
     */
    private function combineGroupResults(array $groupResults, array $conditionGroups)
    {
        if (empty($groupResults)) {
            return false;
        }

        // IF ONLY ONE GROUP, RETURN ITS RESULT
        if (count($groupResults) === 1) {
            return reset($groupResults);
        }

        // DETERMINE HOW TO COMBINE GROUPS BASED ON LOGICAL OPERATORS BETWEEN GROUPS
        $combinedResult = null;
        $groupIds = array_keys($groupResults);

        foreach ($groupIds as $index => $groupId) {
            $groupResult = $groupResults[$groupId];

            if ($combinedResult === null) {
                $combinedResult = $groupResult;
            } else {
                // GET THE LOGICAL OPERATOR FROM THE LAST CONDITION OF PREVIOUS GROUP
                $previousGroupId = $groupIds[$index - 1];
                $previousGroupConditions = $conditionGroups[$previousGroupId];
                $lastCondition = end($previousGroupConditions);

                $logicalOperator = $lastCondition['logical_operator'] ?? $lastCondition->logical_operator ?? 'AND';

                if ($logicalOperator === 'AND') {
                    $combinedResult = $combinedResult && $groupResult;
                } elseif ($logicalOperator === 'OR') {
                    $combinedResult = $combinedResult || $groupResult;
                }
            }
        }

        return $combinedResult;
    }

    /**
     * EVALUATE A SINGLE CONDITION
     */
    private function evaluateCondition($condition, array $data)
    {
        $field = $condition['field'] ?? $condition->field;
        $operator = $condition['operator'] ?? $condition->operator;
        $value = $condition['value'] ?? $condition->value;

        // PRIORITY FOR VALUE SELECTION
        if (isset($condition['option_ref_id']) && $condition['option_ref_id'] !== null) {
            $value = $condition['option_ref_id'];
        } elseif (isset($condition['value_id']) && $condition['value_id'] !== null) {
            $value = $condition['value_id'];
        }
        
        // GET FIELD VALUE FROM DATA ARRAY
        $fieldValue = $data[$field] ?? null;
        return $this->compareValues($fieldValue, $operator, $value);
    }

    /**
     * COMPARE TWO VALUES BASED ON OPERATOR
     */
    private function compareValues($fieldValue, $operator, $compareValue)
    {
        // HANDLE NULL VALUES
        if ($fieldValue === null && $compareValue !== null) {
            return false;
        }
        
        // CONVERT TO APPROPRIATE TYPES FOR COMPARISON
        if (is_numeric($fieldValue) && is_numeric($compareValue)) {
            $fieldValue = (float) $fieldValue;
            $compareValue = (float) $compareValue;
        }
        
        switch ($operator) {
            case '=':
            case '==':
                return $fieldValue == $compareValue;
            case '<>':
            case '!=':
                return $fieldValue != $compareValue;
            case '>':
                return $fieldValue > $compareValue;
            case '<':
                return $fieldValue < $compareValue;
            case '>=':
                return $fieldValue >= $compareValue;
            case '<=':
                return $fieldValue <= $compareValue;
            case 'LIKE':
                return strpos(strtolower((string)$fieldValue), strtolower((string)$compareValue)) !== false;
            case 'IN':
                $values = is_array($compareValue) ? $compareValue : explode(',', (string)$compareValue);
                return in_array($fieldValue, array_map('trim', $values));
            default:
                return false;
        }
    }

    /**
     * DEBUG METHOD - GET READABLE CONDITION STRUCTURE WITH BOTH PRIORITY LEVELS
     */
    public function debugConditionStructure(array $rules)
    {
        $groupedRules = $this->groupRulesByRuleGroup($rules);

        // SORT BY PRIORITY SAME AS EVALUATION (by order_rule_group)
        uasort($groupedRules, function($a, $b) {
            $orderA = $a['order_rule_group'] ?? 999;
            $orderB = $b['order_rule_group'] ?? 999;

            if ($orderA === null) $orderA = 999;
            if ($orderB === null) $orderB = 999;

            return $orderA - $orderB;
        });

        $debug = [];

        foreach ($groupedRules as $ruleGroupId => $ruleGroup) {
            $conditionGroups = $this->groupConditionsByGroup($ruleGroup['conditions']);

            $groupStructure = [
                'rule_group_id' => $ruleGroupId,
                'order_rule_group' => $ruleGroup['order_rule_group'],
                'condition_groups' => []
            ];

            foreach ($conditionGroups as $groupId => $conditions) {
                $conditionDetails = [];
                foreach ($conditions as $condition) {
                    $field = $condition['field'] ?? $condition->field;
                    $operator = $condition['operator'] ?? $condition->operator;
                    $value = $condition['value'] ?? $condition->value;
                    $orderCondition = $condition['order_condition'] ?? $condition->order_condition ?? 999;
                    $logicalOp = $condition['logical_operator'] ?? $condition->logical_operator ?? '';

                    $conditionDetails[] = [
                        'order_condition' => $orderCondition,
                        'condition' => "$field $operator $value",
                        'logical_operator' => $logicalOp
                    ];
                }

                $groupStructure['condition_groups'][$groupId] = $conditionDetails;
            }

            $debug[] = $groupStructure;
        }

        return $debug;
    }
}