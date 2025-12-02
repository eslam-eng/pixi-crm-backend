<?php

namespace App\Services\Tenant\Automation;

use Illuminate\Support\Arr;

class ConditionService
{
    /**
     * Evaluate a condition
     */
    public function evaluateCondition(string $fieldName, string $operator, $expectedValue, array $context): bool
    {
        // Get actual value from context using dot notation
        $actualValue = Arr::get($context, $fieldName);

        return $this->compareValues($actualValue, $operator, $expectedValue);
    }

    /**
     * Compare two values based on operator
     */
    private function compareValues($actual, string $operator, $expected): bool
    {
        return match($operator) {
            'equals', '=', '==' => $actual == $expected,
            'not_equals', '!=', '<>' => $actual != $expected,
            'greater_than', '>' => $actual > $expected,
            'greater_than_or_equal', '>=' => $actual >= $expected,
            'less_than', '<' => $actual < $expected,
            'less_than_or_equal', '<=' => $actual <= $expected,
            'contains' => is_string($actual) && str_contains($actual, $expected),
            'not_contains' => is_string($actual) && !str_contains($actual, $expected),
            'starts_with' => is_string($actual) && str_starts_with($actual, $expected),
            'ends_with' => is_string($actual) && str_ends_with($actual, $expected),
            'in' => is_array($expected) && in_array($actual, $expected),
            'not_in' => is_array($expected) && !in_array($actual, $expected),
            'is_empty' => empty($actual),
            'is_not_empty' => !empty($actual),
            'is_null' => is_null($actual),
            'is_not_null' => !is_null($actual),
            'between' => is_array($expected) && count($expected) === 2 && $actual >= $expected[0] && $actual <= $expected[1],
            default => false,
        };
    }

    /**
     * Evaluate multiple conditions with logical operator
     */
    public function evaluateConditions(array $conditions, array $context, string $logicalOperator = 'AND'): bool
    {
        if (empty($conditions)) {
            return true;
        }

        $results = array_map(function($condition) use ($context) {
            return $this->evaluateCondition(
                $condition['field_name'],
                $condition['operator'],
                $condition['value'],
                $context
            );
        }, $conditions);

        return $logicalOperator === 'OR' 
            ? in_array(true, $results) 
            : !in_array(false, $results);
    }

    /**
     * Validate condition configuration
     */
    public function validateCondition(array $condition): array
    {
        $errors = [];

        if (empty($condition['field_name'])) {
            $errors[] = 'Field name is required';
        }

        if (empty($condition['operator'])) {
            $errors[] = 'Operator is required';
        }

        $validOperators = [
            'equals', '=', '==', 'not_equals', '!=', '<>',
            'greater_than', '>', 'greater_than_or_equal', '>=',
            'less_than', '<', 'less_than_or_equal', '<=',
            'contains', 'not_contains', 'starts_with', 'ends_with',
            'in', 'not_in', 'is_empty', 'is_not_empty',
            'is_null', 'is_not_null', 'between',
        ];

        if (!in_array($condition['operator'], $validOperators)) {
            $errors[] = 'Invalid operator';
        }

        return $errors;
    }
}

