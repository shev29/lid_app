<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

class FlowRuleServiceModel
{
    protected $db;

    public function __construct($connection = null)
    {

    }

    // GET RULES FROM DATABASE
    public function getRules($employeeId, $ruleTypeId)
    {
        $getRuleFlow = DB::table('master_flow_rule_group as a')
                        ->leftJoin('master_flow_rule_condition as b', function($join) {
                            $join->on('b.rule_group_id', '=', 'a.rule_group_id')
                                ->where('b.is_active', '=', '1');
                        })
                        ->leftJoin('master_flow_rule_field_option as c', function($join) {
                            $join->on('b.field_id', '=', 'c.field_id')
                                ->on('b.value_id', '=', 'c.field_option_id');
                        })
                        ->select(
                            'a.rule_group_id',
                            'a.order as order_rule_group',
                            'b.condition_id',
                            'b.order as order_condition',
                            'b.field_id',
                            'b.field',
                            'b.operator',
                            'b.value_id',
                            'c.option_ref_id',
                            'b.value',
                            'b.logical_operator',
                            'b.group_condition',
                            'b.group_color'
                        )
                        ->where('a.employee_id', $employeeId)
                        ->where('a.rule_type_id', $ruleTypeId)
                        ->where('a.is_active', '1')
                        ->where('a.is_deleted', '0')
                        ->orderBy('a.order', 'asc')
                        ->orderBy('b.order', 'asc')
                        ->get();

        // Convert each result row to array
        return array_map(function($item) {
            return (array)$item;
        }, $getRuleFlow->all());
    }
}