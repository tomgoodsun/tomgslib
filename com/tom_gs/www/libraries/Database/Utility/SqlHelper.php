<?php
/**
 * SQL Helper
 * 
 * @package tomlib
 * @subpackage Database
 * @author Tom Higuchi
 * @copyright tom-gs.com(http://www.tom-gs.com)
 * @date 2012.12.24
 */

namespace com\tom_gs\www\libraries\Database\Utility;

class SqlHelper
{
    /**
     * Prefix for bindable placeholder
     */
    const PLACEHOLDER_PREFIX = ':';

    /**
     * Create bindable WHERE conditions
     * 
     * @param array $data          Data for condition
     * @param array $fields        Field list
     * @param array &$sql_params   SQL parameters
     * @param mixed $alias = NULL  Alias name of table
     * @return array WHERE conditions array
     */
    final public static function createBindableWhereCondition(
        array $data,
        array $fields,
        array &$sql_params,
        $alias = null
    ) {
        $where = array();
        foreach ($data as $key => $value) {
            if (in_array($key, $fields)) {
                $alias_str = $placeholder_str = '';
                if ($alias !== null) {
                    $alias_str = $alias . '.';
                } else {
                    $placeholder_str = $alias . '_';
                }
                $field_name = $alias_str . $key;
                $placeholder = self::PLACEHOLDER_PREFIX . $placeholder_str . $key;
                $where[] = sprintf('`%s` = %s', $field_name, $placeholder);
                $sql_params[$placeholder] = $value;
            }
        }
        return $where;
    }

    /**
     * Create bindable SET statement
     * 
     * @param array $data          Data for SET statement
     * @param array $fields        Field list
     * @param array &$sql_params   SQL parameters
     * @return string Statement for UPDATE-SET
     */
    final public static function createBindableSetSatement(
        array $data,
        array $fields,
        array &$sql_params
    ) {
        $set = array();
        foreach ($data as $key => $value) {
            if (in_array($key, $fields)) {
                $placeholder = self::PLACEHOLDER_PREFIX . $key;
                $set[] = sprintf('`%s` = %s', $key, $placeholder);
                $sql_params[$placeholder] = $value;
            }
        }
        return join(', ', $set);
    }

    /**
     * Create bindable INSERT-VALUES statement
     * 
     * @param array $data          Data for INSERT statement
     * @param array $fields        Field list
     * @param array &$sql_params   SQL parameters
     * @return array Statements for INSERT and VALUES statement
     */
    final public static function createBindableInsertSatement(
        array $data,
        array $fields,
        array &$sql_params
    ) {
        $insert = $values = array();
        foreach ($data as $key => $value) {
            if (in_array($key, $fields)) {
                $placeholder = self::PLACEHOLDER_PREFIX . $key;
                $insert[] = $key;
                $values[] = $placeholder;
                $sql_params[$placeholder] = $value;
            }
        }
        return array(
            'insert' => join(', ', $insert),
            'values' => join(', ', $values)
        );
    }

    /**
     * Create bindable ORDER BY statement
     * 
     * @param array $data          Data for INSERT statement
     * @param array $fields        Field list
     * @param mixed $alias = NULL  Alias name of table
     * @return string ORDER BY statement
     */
    final public static function createOrderByStatement(
        array $data,
        array $fields,
        $alias = null
    ) {
        $orderby = array();
        foreach ($data as $key => $order) {
            if (in_array($key, $fields)) {
                $field_name = ($alias === null ? '' : $alias . '.') . $key;
                $orderby[] = sprintf('`%s` %s', $field_name, $order);
            }
        }
        return count($orderby) > 0 ? ' ORDER BY ' . join(', ', $orderby) : '';
    }
}
