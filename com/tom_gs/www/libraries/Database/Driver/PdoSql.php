<?php
/**
 * Database.php
 * 
 * This class for databse
 */

namespace com\tom_gs\www\libraries\Database\Driver;

class PdoSql extends Base
{
    private $driverOptions = array();
    protected $pdo = null;
    protected $statement = null;

    /**
     * Vendor's result type static variables
     */
    const DB_ASSOC = \PDO::FETCH_ASSOC;
    const DB_NUM   = \PDO::FETCH_NUM;

    /**
     * $vendorSign must be used to check the existance of function
     */
    private $vendorSign = 'mysql';

    public function __construct($dsn, $username, $password, array $dbconfig)
    {
        $dbconfig += array(
            'options'  => array(),
        );
        $this->dsn = $dsn;
        $this->driverOptions = $dbconfig['options'];
        parent::initialize($dsn, $username, $password, $dbconfig);
    }

    /**
     * Check availability of database vendor
     * 
     * @param boolean $return = false  Use return or not (exception)
     */
    protected function isAvailable($return = false)
    {
        if ($return) {
            return class_exists('\PDO');
        }
        if (!class_exists('\PDO')) {
            throw new Exception\DatabaseException(
                'This server\'s PHP does not have commands. [%s]'
            );
        }
    }

    /**
     * Connect to MySQL server
     */
    public function connect()
    {
        if (!$this->pdo) {
            try {
                $this->pdo = new \PDO(
                    $this->dsn,
                    $this->username,
                    $this->password,
                    $this->driverOptions
                );
            } catch (\PDOException $e) {
                throw new Exception\DatabaseException('Connection error.', $e->getMessage());
            }
        }
        return $this;
    }

    /**
     * Disconnect from MySQL server
     */
    public function close()
    {
        $this->pdo = null;
    }

    /**
     * Start MySQL transaction
     */
    public function begin()
    {
        $this->startTransaction();
    }

    /**
     * Start MySQL transaction
     */
    public function startTransaction()
    {
        $this->pdo->startTransaction();
    }

    /**
     * Rollback transaction
     * 
     * @access    public
     */
    public function rollback()
    {
        return $this->pdo->rollBack();
    }

    /**
     * Commit transaction
     * 
     * @access    public
     */
    public function commit()
    {
        return $this->pdo->commit();
    }

    /**
     * Prepare SQL and database before executing query
     * 
     * @param $query  SQL statement
     */
    protected function prepare($query, $page = null, $itemPerPage = null)
    {
        parent::prepare($query, $page, $itemPerPage);
        $this->statement = $this->pdo->prepare($this->getTemporarySql());
    }

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
    protected function fetchResultByType(&$result, $vendorResultType)
    {
        return $result->fetchAll($vendorResultType);
    }

    /**
     * Execute temporary set query
     * Every query is executed by this function
     */
    public function executeQuery(array $params = array())
    {
        $params = $this->quoteParams($params);
        $this->statement->execute($params);
        $result = $this->statement;
        return $result;
    }

    /**
     * Get last instert ID
     *
     * @param string $name = null  Name of sequence object
     * @return int Last insert ID
     */
    public function lastInsertId($name = null)
    {
        return $this->statement->lastInsertId($name);
    }

    /**
     * Get affected rows
     *
     * @param mixed $result
     * @return int
     */
    protected function affectedRows($result)
    {
        return $result->rowCount();
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
