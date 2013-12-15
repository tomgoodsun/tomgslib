<?php
/**
 * MySQL.php
 * 
 * This class for MySQL
 */

namespace com\tom_gs\www\libraries\Database\Driver;

use \com\tom_gs\www\libraries\Database\Exception;

final class MySQL extends Native
{
    /**
     * Vendor's result type static variables
     */
    const DB_ASSOC = MYSQL_ASSOC;
    const DB_NUM   = MYSQL_NUM;

    /**
     * $vendorSign must be used to check the existance of function
     */
    private $vendorSign = 'mysql';

    /**
     * MySQL class constructor
     * MySQL class is created one instance only
     * 
     * @param string $dsn                DSN
     * @param string $username           Username
     * @param string $password           Password
     * @param array $dbconfig = array()  Array of database configurations
     */
    public function __construct($dsn, $username, $password, array $dbconfig = array())
    {
        $dbconfig += array(
            'port' => '3306',
        );
        parent::initialize($dsn, $username, $password, $dbconfig);
    }

    /**
     * Release kept memory of result
     * 
     * @param resource &$result  The pointer to the resource of result
     */
    public function freeResult(&$result)
    {
        parent::freeResult();
        if ($result !== false) {
            mysql_free_result($result);
        }
    }

    /**
     * Connect to MySQL server
     */
    public function connect()
    {
        if (!$this->connection) {
            $host = $this->host . ':' . $this->port;
            $this->connection =
                mysql_connect($host, $this->username, $this->password);
            mysql_select_db($this->dbname, $this->connection);
            if (!$this->connection) {
                throw new Exception\DatabaseException('Connection error.', mysql_error());
            }
        }
        return $this;
    }

    /**
     * Disconnect from MySQL server
     */
    public function close()
    {
        $this->connection = mysql_close($this->connection);
    }

    /**
     * Execute DCL query statement
     * 
     * @param $query  SQL statement
     * @return boolean  Result of connection
     */
    public function simpleQuery($query)
    {
        $result = mysql_query($query, $this->connection);
        if ($result === false) {
            return false;
        }
        return true;
    }

    /**
     * Execute temporary set query
     * Every query is executed by this function
     * 
     * @param array $params = array()  Bind parameters
     */
    public function executeQuery(array $params = array())
    {
        $query = $this->bindValues($this->getTemporarySql(), $params);
        return mysql_query($query, $this->connection);
    }

    /**
     * Get last instert ID
     *
     * @param string $name = null  Name of sequence object
     * @return int Last insert ID
     */
    public function lastInsertId($name = null)
    {
        return mysql_insert_id();
    }

    /**
     * Start MySQL transaction
     */
    public function begin()
    {
        $this->simpleQuery('BEGIN');
    }

    /**
     * Start MySQL transaction
     */
    public function startTransaction()
    {
        $this->simpleQuery('START TRANSACTION');
    }

    /**
     * Create offset-limit query
     *
     * @param string $query  SQL
     * @param int $offset    Offset
     * @param int $rowCount  Limit
     * @return string  SQL
     */
    protected function createLimitQuery($query, $offset, $rowCount)
    {
        $query .= sprintf(' LIMIT %d, %d', $offset, $rowCount);
        $query = preg_replace('/\bselect\b/i', 'SELECT SQL_CALC_FOUND_ROWS ', $query);
        return $query;
    }

    /**
     * Wrapper functions of native function
     */
    protected function fetchArray(&$result, $resultType)
    {
        return mysql_fetch_array($result, $resultType);
    }

    /**
     * Get affected rows
     *
     * @param mixed $result
     * @return int
     */
    protected function affectedRows($result)
    {
        return mysql_affected_rows();
    }

    /**
     * Get database vendor name
     *
     * @return string  Vendor name
     */
    public function getVendorSign()
    {
        return $this->vendorSign;
    }

    /**
     * Get result type for associated array
     */
    public function getResultTypeAssoc()
    {
        return self::DB_ASSOC;
    }

    /**
     * Get result type for numbered array
     */
    public function getResultTypeNum()
    {
        return self::DB_NUM;
    }
}
