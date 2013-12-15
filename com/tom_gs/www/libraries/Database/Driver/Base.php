<?php
/**
 * Database.php
 * 
 * This class for databse
 */

namespace com\tom_gs\www\libraries\Database\Driver;

use \com\tom_gs\www\libraries\Database\Database;
use \com\tom_gs\www\libraries\Database\Result;
use \com\tom_gs\www\libraries\Database\Exception;

abstract class Base
{
    /**
     * Database configuration must be kept in instance variables
     */
    protected $host     = '';
    protected $dbname   = '';
    protected $username = '';
    protected $password = '';
    protected $port     = '';
    protected $tablePrefix     = '';
    protected $tablePrefixSign = '';

    /**
     * MySQL class original variable
     * To use transaction because MySQL default setting of transaction is turned off
     */
    private $autoTransaction = false;

    /**
     * Connection must 
     */
    protected $connection = null;

    /**
     * The SQL is kept temporarily before execute
     */
    private $temporarySql = '';

    /**
     * This function must be called by subclass
     * 
     * @param string $dsn                DSN
     * @param string $username           Username
     * @param string $password           Password
     * @param array $dbconfig = array()  Array of database configurations
     */
    protected function initialize($dsn, $username, $password, array $dbconfig = array())
    {
        $this->dsn = $dsn;
        $dbconfig += Database::parseDsn($this->dsn);
        $dbconfig += array(
            'host'              => 'localhost',
            'dbname'            => '',
            'username'          => '',
            'password'          => '',
            'port'              => '',
            'table_prefix'      => '',
            'table_prefix_sign' => '#__',
            'auto_transaction'  => false,
        );
        $this->isAvailable(false);
        $this->host     = $dbconfig['host'];
        $this->dbname   = $dbconfig['dbname'];
        $this->username = $username;
        $this->password = $password;
        $this->port     = $dbconfig['port'];
        $this->tablePrefix     = $dbconfig['table_prefix'];
        $this->tablePrefixSign = $dbconfig['table_prefix_sign'];
        $this->autoTransaction = $dbconfig['auto_transaction'];
    }

    /**
     * Quote value for SQL
     * 
     * @param array $params  Bind parameters
     * @return array  Quoted bind prameters
     */
    public function quoteParams(array $params)
    {
        foreach ($params as &$param) {
            $param = $this->quote($param);
        }
        return $params;
    }

    /**
     * Quote value for SQL
     * 
     * @param mixed $value  Bind parameter
     * @return string  Quoted value
     */
    public function quote($value)
    {
        if ($value === null) {
            return 'NULL';
        }
        return $this->escapeString($value);
    }

    /**
     * Escape string
     * 
     * @param mixed $value  Bind parameter
     * @return string  Quoted value
     */
    public function escapeString($value)
    {
        return "'" . addslashes((string)$value) . "'";
    }

    /**
     * Bind values to SQL
     * *CAUTION* This method DOES NOT work for some vendor.
     * 
     * @param $query
     * @param array $params
     * @return String  Value-binded SQL statement
     */
    public function bindValues($query, array $params)
    {
        foreach ($params as $key => $value) {
            if (is_array($value)) {
                $value = implode(', ', $value);
            } else {
                $value = $this->quote($value);
            }
            //$pattern = '/\b' . preg_quote($key) . '\b/';
            $pattern = '/' . preg_quote($key) . '\b/';
            if (preg_match($pattern, $query)) {
                $query = preg_replace($pattern, $value, $query);
                unset($params[$key]);
            }
        }
        if (count($params) > 0) {
            throw new Exception\DatabaseException(
                'Number of bind parameters does not match to specified ones on query.'
            );
        }
        return $query;
    }

    /**
     * Prepare SQL and database before executing query
     * 
     * @param string $query            SQL statement
     * @param int $page = null         Page No.
     * @param int $itemPerPage = null  Number of items per page
     */
    protected function prepare($query, $page = null, $itemPerPage = null)
    {
        if ($page !== null && $itemPerPage !== null) {
            list($offset, $rowCount) = Database::computeOffset($page, $itemPerPage);
            $query = $this->createLimitQuery($query, $offset, $rowCount);
        }
        $this->setQuery($query);
    }

    /**
     * Export binded SQL statement
     * 
     * @param $query                   SQL statement
     * @param int $page = null         Page No.
     * @param int $itemPerPage = null  Number of items per page
     * @return string Binded SQL statement
     */
    public function exportBindSql($query, array $params, $page = null, $itemPerPage = null)
    {
        if ($page !== null && $itemPerPage !== null) {
            list($offset, $rowCount) = Database::computeOffset($page, $itemPerPage);
            $query = $this->createLimitQuery($query, $offset, $rowCount);
        }
        $query = $this->bindValues($query, $params);
        return $query;
    }

    /**
     * Create offset-limit query
     *
     * @param string $query  SQL statement
     * @param int $offset    Offset
     * @param int $rowCount  Limit
     * @return string  SQL
     */
    protected function createLimitQuery($query, $offset, $rowCount)
    {
        return $query;
    }

    /**
     * Set and convert sql temporarily into instance variables
     * 
     * @param string $query  SQL statement
     */
    protected function setQuery($query)
    {
        $this->temporarySql =
            str_replace($this->tablePrefixSign, $this->tablePrefix . '_', $query);
    }

    /**
     * Get temporary query
     */
    public function getTemporarySql()
    {
        return $this->temporarySql;
    }

    /**
     * Get hostname
     *
     * @return string  Hostname
     */
    public function getHostName()
    {
        return $this->hostname;
    }

    /**
     * Get port no
     *
     * @return int  Port no
     */
    public function getPort()
    {
        return $this->port;
    }

    /**
     * Free result on memory and set empty the temporary sql variable
     */
    public function freeResult()
    {
        $this->temporarySql = '';
    }

    /**
     * Execute DML query statement
     * 
     * @param $query                   SQL statement
     * @param int $page = null         Page No.
     * @param int $itemPerPage = null  Number of items per page
     * @param $resultType              Type of result array
     */
    public function query(
        $query,
        array $params = array(),
        $page = null,
        $itemPerPage = null,
        $resultType = 'ASSOC'
    ) {
        $data = $this->fetchResult($query, $params, $page, $itemPerPage, $resultType);
        $count = null;
        if ($page !== null && $itemPerPage !== null) {
            $counter = $this->query('SELECT FOUND_ROWS() AS count');
            $count = $counter[0]['count'];
        }
        $result = new Result\Result($data, $page, $itemPerPage, $count);
        return $result;
    }

    /**
     * Execute DDL query statement
     * 
     * @param string $query           SQL statement
     * @param array $param = array()  Bind parameters
     * @return array / object  Resultset
     */
    public function exec($query, array $params = array())
    {
        try {
            $result = $this->execute($query, $params);
            if (!$result) {
                throw new Exception\Exception\DatabaseException(
                    'Unavailable result set is return [%s]'
                );
            }
        } catch (\Exception $e) {
            $this->freeResult($result);
            $e->printAll();
        }
        return $result;
    }

    /**
     * Execute DDL query statement
     * 
     * @param string $query           SQL statement
     * @param array $param = array()  Bind parameters
     * @return array / object  Resultset
     */
    public function execute($query, array $params = array())
    {
        $this->prepare($query);
        $result = null;
        if ($this->autoTransaction) {
            $this->startTransaction();
            try {
                $result = $this->executeQuery($params);
                $this->commit();
            } catch (\Exception $e) {
                $this->rollback();
            }
        } else {
            $result = $this->executeQuery($params);
        }
        return $this->affectedRows($result);
    }

    /**
     * Get last instert ID
     *
     * @param string $name = null  Name of sequence object
     * @return int Last insert ID
     */
    public function lastInsertId($name = null)
    {
        throw new Exception\Exception\DatabaseException(
            sprintf('%s::%s() is not implemented.', __CLASS__, __METHOD__)
        );
    }

    /**
     * Fetch result
     * 
     * @param string $query            SQL statement
     * @param array $param = array()   Bind parameters
     * @param int $page = null         Page No.
     * @param int $itemPerPage = null  Number of items per page
     * @param $resultType              Type of result array
     */
    protected function fetchResult(
        $query,
        array $params = array(),
        $page = null,
        $itemPerPage = null,
        $resultType = 'ASSOC'
    ) {
        $this->prepare($query, $page, $itemPerPage);
        $result = $this->executeQuery($params);
        try {
            if (!$result) {
                throw new Exception\DatabaseException(
                    'Unexecutable query is set. [%s]'
                );
            }
            $vendorResultType = $this->getResultType($resultType);
            return $this->fetchResultByType($result, $vendorResultType);
        } catch (\Exception $e) {
            $this->freeResult($result);
            $e->printAll();
        }
    }

    /**
     * Get result type for vendors
     * 
     * @param string $resultType  Result type of returned array
     *     [ASSOC]
     *         Default. 2-level numbered-associated array.
     *     [NUM]
     *         Default. 2-level numbered-numbered array.
     */
    protected function getResultType($resultType)
    {
        switch($resultType) {
            case 'NUM':
                return $this->getResultTypeNum();
            case 'ASSOC':
            default:
                return $this->getResultTypeAssoc();
        }
    }

    /**
     * Check availability of database vendor
     * 
     * @param boolean $return = false  Use return or not (exception)
     */
    abstract protected function isAvailable($return = false);

    /**
     * Connect to database
     */
    abstract public function connect();

    /**
     * Disconnect to database
     */
    abstract public function close();

    /**
     * Start transaction
     */
    abstract public function startTransaction();

    /**
     * Commit transaction
     */
    abstract public function commit();

    /**
     * Roll back transaction
     */
    abstract public function rollback();

    /**
     * Execute temporary set query
     * Every query is executed by this function
     */
    abstract public function executeQuery(array $params = array());

    /**
     * Create data set for each result type to return to caller
     * 
     * @param resource &$result         The pointer to the resource of result
     * @param string $vendorResultType  Result type of returned array
     *     [ASSOC]
     *         Default. 2-level numbered-associated array.
     *     [NUM]
     *         Default. 2-level numbered-numbered array.
     */
    abstract protected function fetchResultByType(&$result, $vendorResultType);

    /**
     * Get affected rows
     *
     * @param mixed $result
     * @return int
     */
    abstract protected function affectedRows($result);

    /**
     * Get database vendor name
     */
    abstract public function getVendorSign();

    /**
     * Get result type for associated array
     */
    abstract public function getResultTypeAssoc();

    /**
     * Get result type for numbered array
     */
    abstract public function getResultTypeNum();
}
