<?php
/**
 * PostgreSQL.php
 * 
 * This class for PostgreSQL
 */

namespace com\tom_gs\www\libraries\Database\Driver;

use \com\tom_gs\www\libraries\Database\Exception;

final class PostgreSQL extends Native
{
    /**
     * Vendor's result type static variables
     */
    const DB_ASSOC = PGSQL_ASSOC;
    const DB_NUM   = PGSQL_NUM;

    /**
     * $vendorSign must be used to check the existance of function
     */
    private $vendorSign = 'pg';

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
            'port' => '5432',
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
            pg_free_result($result);
        }
    }

    /**
     * Connect to PostgreSQL server
     */
    public function connect()
    {
        if (!$this->connection) {
            $host = $this->host . ':' . $this->port;
            $constr = 'host=' . $this->host . ' port=' . $this->port;
            if (isset($this->dbname) && strlen($this->dbname) != 0) {
                $constr .= ' dbname=' . $this->dbname;
            }
            if (isset($this->username) && strlen($this->username) != 0) {
                $constr .= ' user=' . $this->username;
            }
            if (isset($this->password) && strlen($this->password) != 0) {
                $constr .= ' password=' . $this->password;
            }

            $this->connection = pg_connect($constr);
            if (!$this->connection) {
                throw new Exception\DatabaseException('Connection error.', pg_last_error());
            }
        }
        return $this;
    }

    /**
     * Disconnect from PostgreSQL server
     */
    public function close()
    {
        $this->connection = pg_close($this->connection);
    }

    /**
     * Execute DCL query statement
     * 
     * @param $query  SQL statement
     * @return boolean  Result of connection
     */
    public function simpleQuery($query)
    {
        $result = pg_query($this->connection, $query);
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
        return pg_query($this->connection, $query);
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
        $query .= sprintf(' OFFSET %d, LIMIT %d', $offset, $rowCount);
        return $query;
    }

    /**
     * Wrapper functions of native function
     */
    protected function fetchArray(&$result, $resultType)
    {
        return pg_fetch_array($result, $resultType);
    }

    /**
     * Get affected rows
     *
     * @param mixed $result
     * @return int
     */
    protected function affectedRows($result)
    {
        return pg_affected_rows($result);
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
