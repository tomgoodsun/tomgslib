<?php 
namespace com\tom_gs\www\libraries\Database\Table;

use \com\tom_gs\www\libraries\Database\Driver;
use com\tom_gs\www\libraries\Database\Utility;

abstract class Module
{
    /**
     * Database engine
     */
    private $database;

    /**
     * Constructor
     *
     * @param Database $database  Database engine
     */
    public function __construct(Driver\Base $database)
    {
        $this->database = $database;
    }

    /**
     * Get database engine
     * 
     * @return Database
     */
    public function getDatabase()
    {
        return $this->database;
    }

    /**
     * Create bindable primary key prameters
     *
     * @param mixed $pkCond  Primary key conditions
     * @return array Bindable parameters
     */
    protected function createPkBindParams($pkCond)
    {
        $bindOptions = array();
        $pkCond = (array)$pkCond;
        $pks = $this->getPrimaryKeys();
        $len = count($pkCond);
        for ($i = 0; $i < $len; $i++) {
            $label = $pks[$i];
            $bindOptions[$label] = $pkCond[$i];
        }
        return $bindOptions;
    }

    /**
     * Create column name list
     *
     * @param array $fields  Selecting column names
     * @return array Column name list
     */
    protected function createFields(array $fields)
    {
        $result = array();
        $columns = $this->getColumNames();
        if (count($fields) == 0) {
            return $columns;
        }
        foreach ($fields as $field) {
            if (in_array($field, $columns)) {
                $result[] = $field;
            }
        }
        return $result;
    }

    /**
     * Select data
     *
     * @param array $options = array()  Search parameters
     * @param int $page = null          Page No.
     * @param int $count = null         Number of items per page
     * @param array $fields = array()   Select fields
     * @return array Select result
     */
    public function select(
        array $options = array(),
        $page = null,
        $count = null,
        array $fields = array()
    ) {
        $bindParams = array();
        $sql = 'SELECT %s FROM %s';
        $columns = $this->createFields($fields);
        $tableName = $this->getTableName();
        
        $column = '`' . join('`, `', $columns) . '`';
        $wheres = Utility\SqlHelper::createBindableWhereCondition(
            $options,
            $this->getColumNames(),
            $bindParams
        );
        
        $sql = sprintf($sql, $column, $tableName);
        $sql .= (count($wheres) > 0 ? ' WHERE '. join(' AND ', $wheres) : '');
        return $this->database->query($sql, $bindParams, $page, $count);
    }

    /**
     * Select one record which can be detected by primary key
     *
     * @param mixed $pkCond             Primary key conditions
     * @param array $options = array()  Search parameters
     * @param array $fields = array()   Select fields
     * @return array Select result, single layer array
     */
    public function selectOne(
        $pkCond,
        array $options = array(),
        array $fields = array()
    ) {
        $bindOptions = $this->createPkBindParams($pkCond);
        $result = $this->select($bindOptions, null, null, $fields);
        return count($result) > 0 ? $result[0] : array();
    }

    /**
     * Register data, insert if nothing is updated
     *
     * @param array $data        Saving data
     * @param mixed $pkCond      Primary key conditions
     * @param mixed &$id = null  Modified primary key
     * @return int Affected rows
     */
    public function register(array $data, $pkCond, &$id = null)
    {
        $affectedRows = $this->update($data, $pkCond, $id);
        if ($affectedRows == 0) {
            $affectedRows = $this->insert($data, $id);
        }
        return $affectedRows;
    }

    /**
     * Update data
     *
     * @param array $data        Saving data
     * @param mixed $pkCond      Primary key conditions
     * @param mixed &$id = null  Modified primary key
     * @return int Affected rows
     */
    public function update(array $data, $pkCond, &$id = null)
    {
        $bindParams = array();
        $columns = $this->getColumNames();
        $tableName = $this->getTableName();
        $bindOptions = $this->createPkBindParams($pkCond);

        $wheres = Utility\SqlHelper::createBindableWhereCondition(
            $bindOptions,
            $columns,
            $bindParams
        );
        $setStatement = Utility\SqlHelper::createBindableSetSatement(
            $data,
            $columns,
            $bindParams
        );
        $sql = 'UPDATE %s SET %s';
        $sql = sprintf($sql, $tableName, $setStatement);
        $sql .= (count($wheres) > 0 ? ' WHERE '. join(' AND ', $wheres) : '');
        $affectedRows = $this->database->execute($sql, $bindParams);
        if ($affectedRows > 0) {
            $id = $pkCond;
        }
        return $affectedRows;
    }

    /**
     * Insert data
     *
     * @param array $data        Saving data
     * @param mixed &$id = null  Modified primary key
     * @return int Affected rows
     */
    public function insert(array $data, &$id = null)
    {
        $bindParams = array();
        $columns = $this->getColumNames();
        $tableName = $this->getTableName();
        
        $stmt = Utility\SqlHelper::createBindableInsertSatement(
            $data,
            $columns,
            $bindParams
        );
        $sql = 'INSERT INTO %s (%s) VALUES (%s)';
        $sql = sprintf($sql, $tableName, $stmt['insert'], $stmt['values']);
        $affectedRows = $this->database->execute($sql, $bindParams);
        if ($affectedRows > 0) {
            $id = $this->database->lastInsertId($affectedRows);
        }
        return $affectedRows;
    }

    /**
     * Get column names in array
     *
     * @return array Array of column names
     */
    abstract public function getColumNames();

    /**
     * Get table name
     *
     * @return string Table name
     */
    abstract public function getTableName();

    /**
     * Get primary keys in array
     *
     * @return array Array of primary keys
     */
    abstract public function getPrimaryKeys();
}
