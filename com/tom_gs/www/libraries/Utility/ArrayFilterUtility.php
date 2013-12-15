<?php
/**
 * Array Filter Utility
 * 
 * @package tomgslib
 * @subpackage libraries
 * @subpackage Utility
 */

namespace com\tom_gs\www\libraries\Utility;

final class ArrayFilterUtility
{
    /**
     * Filter data by conditions
     *
     * @param array $data        Target data
     * @param array $conditions  Condition to filter
     * @return array  Filtered data
     */
    public static function filterByConditions(array $data, array $conditions)
    {
        $result = array();
        foreach ($data as $item) {
            $compare_result = self::compareWithConditions($item, $conditions);
            if ($compare_result) {
                $result[] = $item;
            }
        }
        return $result;
    }

    /**
     * Condition modes
     */
    const AND_CONDITION = 'AND';
    const OR_CONDITION = 'OR';

    /**
     * Compare with conditions
     *
     * @param array $data        Target data
     * @param array $conditions  Condition to filter
     * @return boolean  Compared result
     */
    public static function compareWithConditions(array $data, array $conditions)
    {
        $result = true;
        foreach ($conditions as $type => $condition) {
            if ($type === self::AND_CONDITION) {
                $result = $result && self::compareWithConditions($data, $condition);
            } elseif ($type === self::OR_CONDITION) {
                $result = $result || self::compareWithConditions($data, $condition);
            } else {
                if (count($condition) >= 1 && count($condition) <= 4) {
                    list($mode, $name, $operator, $value) = self::detectConditionParameters($condition);
                    $result = self::compareWithCondition($mode, $result, $data, $name, $operator, $value);
                } else {
                    continue;
                }
            }
        }
        return $result;
    }

    /**
     * Compare value with normal condition
     * 
     * @param string $mode       OR / AND condition
     * @param boolean $result    Last result of comparision
     * @param array $data        Data that has target value to compare with $value
     * @param string $name       Name of data
     * @param string $operator   Operator of condition
     * @param mixed $value       Value to be compared with
     * @return boolean TRUE: Matches with condition / FALSE:Mismatches with condition
     */
    public static function compareWithCondition($mode, $result, array $data, $name, $operator, $value)
    {
        if ($mode == self::AND_CONDITION) {
            $result = $result && self::compare($data, $name, $operator, $value);
        } elseif ($mode == self::OR_CONDITION) {
            $result = $result || self::compare($data, $name, $operator, $value);
        }
        return $result;
    }

    /**
     * Detect condition parameters from condition definition
     *
     * @param array $conditions  Condition to filter
     * @return array $data, $name, $operator, $value
     */
    public static function detectConditionParameters(array $condition)
    {
        if (count($condition) == 1) {
            $mode = $condition[0];
            $name = null;
            $operator = $condition[1];
            $value = null;
        } elseif (count($condition) == 2) {
            $mode = self::AND_CONDITION;
            $name = null;
            $operator = $condition[0];
            $value = null;
        } elseif (count($condition) == 3) {
            $mode = self::AND_CONDITION;
            $name = $condition[0];
            $operator = $condition[1];
            $value = $condition[2];
        } elseif (count($condition) == 4) {
            $mode = $condition[0];
            $name = $condition[1];
            $operator = $condition[2];
            $value = $condition[3];
        }
        return array($mode, $name, $operator, $value);
    }

    /**
     * Operators
     */
    const EQUAL = '==';
    const EQ = '=';
    const NOT_EQUAL = '!=';
    const NOT_EQ = '<>';
    const GT = '>';
    const LT = '<';
    const GT_EQ = '>=';
    const LT_EQ = '<=';
    const BETWEEN = 'BETWEEN';
    const IN = 'IN';
    const NOT_IN = 'NOT_IN';
    const IS_NULL = 'IS_NULL';
    const IS_NOT_NULL = 'IS_NOT_NULL';

    /**
     * Operator group: relational operators
     */
    private static $RELATIONAL_OPERATORS = array(
        self::EQUAL,
        self::EQ,
        self::NOT_EQUAL,
        self::NOT_EQ,
        self::GT,
        self::LT,
        self::GT_EQ,
        self::LT_EQ,
    );

    /**
     * Operator group: keyword operators
     */
    private static $KEYWORD_OPERATORS = array(
        self::BETWEEN,
        self::IN,
        self::NOT_IN,
        self::IS_NULL,
    );

    /**
     * Compare value
     * 
     * @param array $data        Data that has target value to compare with $value
     * @param string $name       Name of data
     * @param string $operator   Operator of condition
     * @param mixed $value       Value to be compared with
     * @return boolean TRUE: Matches with condition / FALSE:Mismatches with condition
     */
    public static function compare(array $data, $name, $operator, $value)
    {
        if ($name !== null && $value !== null) {
            if (!array_key_exists($name, $data)) {
                return false;
            }
        }
        $target_value = $data[$name];
        return self::compareByOperator($target_value, $operator, $value);
    }
    
    /**
     * Compare value by operator
     * 
     * @param mixed $target_value  Target value to compare with $value
     * @param string $operator     Operator of condition
     * @param mixed $value         Value to be compared with
     * @return boolean TRUE: Matches with condition / FALSE:Mismatches with condition
     */
    public static function compareByOperator($target_value, $operator, $value)
    {
        if (in_array($operator, self::$RELATIONAL_OPERATORS)) {
            return self::compareByRelationalOperator($target_value, $operator, $value);
        } elseif (in_array($operator, self::$KEYWORD_OPERATORS)) {
            return self::compareByKeywordOperator($target_value, $operator, $value);
        }
        return false;
    }

    /**
     * Compare value by relational operator
     * 
     * @param mixed $target_value  Target value to compare with $value
     * @param string $operator     Operator of condition
     * @param mixed $value         Value to be compared with
     * @return boolean TRUE: Matches with condition / FALSE:Mismatches with condition
     */
    public static function compareByRelationalOperator($target_value, $operator, $value)
    {
        switch ($operator) {
            case self::EQUAL:
            case self::EQ:
                return $target_value == $value;
            case self::NOT_EQUAL:
            case self::NOT_EQ:
                return $target_value != $value;
            case self::GT:
                return $target_value > $value;
            case self::LT:
                return $target_value < $value;
            case self::GT_EQ:
                return $target_value >= $value;
            case self::LT_EQ:
                return $target_value <= $value;
            default:
                return false;
        }
    }
    
    /**
     * Compare value by keyword operator
     * 
     * @param mixed $target_value  Target value to compare with $value
     * @param string $operator     Operator of condition
     * @param mixed $value         Value to be compared with
     * @return boolean TRUE: Matches with condition / FALSE:Mismatches with condition
     */
    public static function compareByKeywordOperator($target_value, $operator, $value)
    {
        switch ($operator) {
            case self::BETWEEN:
                if (!is_array($value)) {
                    return false;
                }
                return  $value[0] <= $target_value && $target_value <= $value[1];
            case self::IN:
                if (!is_array($value)) {
                    return false;
                }
                return in_array($target_value, $value);
            case self::NOT_IN:
                if (!is_array($value)) {
                    return false;
                }
                return !in_array($target_value, $value);
            case self::IS_NULL:
                return is_null($value);
            default:
                return false;
        }
    }
}
