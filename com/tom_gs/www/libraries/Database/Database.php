<?php
/**
 * Database.php
 * 
 * This class for databse
 */

namespace com\tom_gs\www\libraries\Database;

use com\tom_gs\www\libraries\Database\Driver;

class Database
{
    /**
     * Parse DSN into vendor and source
     *
     * @param string $dsn  DSN string
     * @return array Parsed result
     */
    public static function parseDsnByVendor($dsn)
    {
        $vendor = $source = '';
        $colon_pos = strpos($dsn, ':');
        if ($colon_pos > 0) {
            $vendor = substr($dsn, 0, $colon_pos);
            $source = substr($dsn, $colon_pos + 1, strlen($dsn));
        }
        return array($vendor, $source);
    }

    /**
     * Parse DSN
     *
     * @param string $dsn  DSN string
     * @return array Parsed result
     */
    public static function parseDsn($dsn)
    {
        $params = array();
        list($vendor, $source) = self::parseDsnByVendor($dsn);
        $params['vendor'] = $vendor;
        $settings = explode(';', $source);
        foreach ($settings as $setting) {
            list($label, $value) = explode('=', $setting);
            $params[$label] = $value;
        }
        ksort($params);
        return $params;
    }

    /**
     * Factory method
     * 
     * @param string $dsn                DSN
     * @param string $username           Username
     * @param string $password           Password
     * @param array $dbconfig = array()  Array of database configurations
     */
    public static function factory($dsn, $username, $password, array $dbconfig = array())
    {
        list($vendor) = self::parseDsnByVendor($dsn);
        //return new Driver\PdoSql($dsn, $username, $password, $dbconfig);
        switch(strtolower($vendor)) {
            case 'pdo_mysql':
                return new Driver\PdoMySQL($dsn, $username, $password, $dbconfig);
            case 'mysql':
                return new Driver\MySQL($dsn, $username, $password, $dbconfig);
            case 'pgsql':
            case 'postgresql':
                return new Driver\PostgreSQL($dsn, $username, $password, $dbconfig);
            default:
                return null;
        }
    }

    /**
     * Detect query type
     * 
     * @param string $query  SQL
     * @return string  Query type
     */
    public static function detectQueryType($query)
    {
        $query_types = array(
            'select', 'show', 'describe', 'explain',
            'update', 'insert', 'delete', 'alter table', 'create', 'drop'
        );
        foreach ($query_types as $type) {
            if (preg_match('/\bselect\b/i', $query)) {
                return $type;
            }
        }
        return null;
    }

    /**
     * Compute offset/limit
     * 
     * @param int $page = null         Page No.
     * @param int $itemPerPage = null  Number of items per page
     * @return array (offset/row count)
     */
    public static function computeOffset($page = null, $itemPerPage = null)
    {
        $offset = $rowCount = null;
        if ($page !== null && $itemPerPage !== null) {
            $offset = intval($page) <= 0 ? 0 : (intval($page) - 1) * intval($itemPerPage);
            $rowCount = intval($itemPerPage);
        }
        return array($offset, $rowCount);
    }
}
