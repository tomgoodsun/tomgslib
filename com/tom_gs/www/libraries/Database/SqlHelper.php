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

class SqlHelper {
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
    public static final function createBindableWhereCondition(array $data, array $fields, array &$sql_params, $alias = NULL) {
        $where = array();
        foreach ($data as $key => $value) {
            if (in_array($key, $fields)) {
                $field_name = ($alias === NULL ? '' : $alias . '.') . $key;
                $placeholder = self::PLACEHOLDER_PREFIX . ($alias === NULL ? '' : $alias . '_') . $key;
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
    public static final function createBindableSetSatement(array $data, array $fields, array &$sql_params) {
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
     * @return array Statements for INSERT (detectable with 'insert') and VALUES (detectable with 'values') statement
     */
    public static final function createBindableInsertSatement(array $data, array $fields, array &$sql_params) {
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
    public static final function createOrderByStatement(array $data, array $fields, $alias = NULL) {
        $orderby = array();
        foreach ($data as $key => $order) {
            if (in_array($key, $fields)) {
                $field_name = ($alias === NULL ? '' : $alias . '.') . $key;
                $orderby[] = sprintf('`%s` %s', $field_name, $order);
            }
        }
        return count($orderby) > 0 ? ' ORDER BY ' . join(', ', $orderby) : '';
    }
}
