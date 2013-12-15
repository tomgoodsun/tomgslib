<?php
/**
 * Simple Database Manager
 * 
 * @package tomlib
 * @subpackage Database
 * @author Tom Higuchi
 * @copyright tom-gs.com(http://www.tom-gs.com)
 * @date 2012.12.24
 */

abstract class SimpleDatabaseManager
{
    /**
     * Database engine
     */
    private $database;

    /**
     * List of names of table columns
     */
    protected $fields = array();

    /**
     * Name of table
     */
    protected $table_name;

    /**
     * Alias name of table
     */
    protected $table_alias;

    /**
     * Name of column for primary key (Only single key)
     */
    protected $primary_key;

    /**
     * Constructor
     *
     * @param Database $database  Database engine
     */
    public function __construct(Database $database)
    {
        $this->fields = $this->getFields();
        $this->table_name = $this->getTableName();
        $this->table_alias = $this->getTableAlias();
        $this->primary_key = $this->getPrimaryKey();
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
     * Select from all data
     * 
     * @param int $page = NULL   Page No
     * @param int $count = NULL  Item count per page
     * @return array Database result in 2-level array
     */
    public final function select($page = NULL, $count = NULL)
    {
        $sql_params = array();
        $alias = ($this->table_alias === NULL ? '' : $alias . '.');
        $sql = sprintf('SELECT %s FROM %s', $alias . join(', ' . $alias, $this->fields), $this->table_name);
        return $this->database->query($sql, $sql_params, $page, $count);
    }

    /**
     * Select from primary-key-detected data
     * 
     * @param mixed $id  ID which can be detected as primary key
     * @return array Database result in single array
     */
    public final function selectOne($id)
    {
        $sql_params = array();
        $conditions = array($this->primary_key => $id);
        $alias = ($this->table_alias === NULL ? '' : $alias . '.');
        $where_conditions = SqlHelper::createBindableWhereCondition($conditions, $this->fields, $sql_params, $this->table_alias);
        $sql = sprintf('SELECT %s FROM %s', $alias . join(', ' . $alias, $this->fields), $this->table_name);
        $sql .= (count($where_conditions) > 0 ? ' WHERE ' . join(' AND ', $where_conditions) : '');
        $result = $this->database->query($sql, $sql_params);
        return count($result) > 0 ? $result[0] : array();
    }

    /**
     * Insert or update data at once
     * 
     * @param array $data                     Data to be registered
     * @param array &$modified_ids = array()  Inserted or updated primary keys to be stored as result
     * @return int Affected rows
     */
    public final function registerAtOnce(array $data, array &$modified_ids = array())
    {
        $modified_ids = array();
        $affected_rows = 0;
        foreach ($data as $value) {
            $id = $modified_id = NULL;
            if (array_key_exists($this->primary_key, $value)) {
                $id = $data[$this->primary_key];
            }
            $affected_rows += $this->register($id, $value, $modified_id);
            $modified_ids[] = $modified_id;
        }
        return $affected_rows;
    }

    /**
     * Insert or update data
     * 
     * @param mixed $id                   ID which can be detected as primary key
     * @param array $data                 Data to be registered
     * @param mixed &$modified_id = NULL  Inserted or updated primary key to be stored as result
     * @return int Affected rows
     */
    public final function register($id, array $data, &$modified_id = NULL)
    {
        $affected_rows = 0;
        $affected_rows = $this->update($id, $data, $modified_id);
        if ($affected_rows <= 0) {
            $affected_rows = $this->insert($data, $modified_id);
        }
        return $affected_rows;
    }

    /**
     * Insert data
     * 
     * @param array $data                 Data to be registered
     * @param mixed &$modified_id = NULL  Inserted or updated primary key to be stored as result
     * @return int Affected rows
     */
    public final function insert(array $data, &$modified_id = NULL)
    {
        $sql_params = array();
        $statements = SqlHelper::createBindableInsertSatement($data, $this->fields, $sql_params);
        $sql = sprintf('INSERT INTO %s (%s) VALUES (%s)', $this->table_name, $statements['insert'], $statements['values']);
        $affected_rows = $this->database->execute($sql, $sql_params);
        if ($affected_rows > 0) $modified_id = $this->lastInsertId($affected_rows);
        return $affected_rows;
    }

    /**
     * Update data
     * 
     * @param mixed $id                   ID which can be detected as primary key
     * @param array $data                 Data to be registered
     * @param mixed &$modified_id = NULL  Inserted or updated primary key to be stored as result
     * @return int Affected rows
     */
    public final function update($id, array $data, &$modified_id = NULL)
    {
        $sql_params = array();
        $conditions = array($this->primary_key => $id);
        $where_conditions = SqlHelper::createBindableWhereCondition($conditions, $this->fields, $sql_params);
        $set_statement = SqlHelper::createBindableSetSatement($data, $this->fields, $sql_params);
        $sql = sprintf('UPDATE %s SET %s', $this->table_name, $set_statement);
        $sql .= (count($where_conditions) > 0 ? ' WHERE ' . join(' AND ', $where_conditions) : '');
        $affected_rows = $this->database->execute($sql, $sql_params);
        if ($affected_rows > 0) $modified_id = $id;
        return $affected_rows;
    }

    /**
     * Delete data
     * 
     * @param mixed $id                   ID which can be detected as primary key
     * @param mixed &$modified_id = NULL  Inserted or updated primary key to be stored as result
     * @return int Affected rows
     */
    public final function delete($id, &$modified_id = NULL)
    {
        $sql_params = array();
        $conditions = array($this->primary_key => $id);
        $where_conditions = SqlHelper::createBindableWhereCondition($conditions, $this->fields, $sql_params);
        $sql = sprintf('DELETE FROM %s', $this->table_name);
        $sql .= (count($where_conditions) > 0 ? ' WHERE ' . join(' AND ', $where_conditions) : '');
        $affected_rows = $this->database->execute($sql, $sql_params);
        if ($affected_rows > 0) $modified_id = $id;
        return $affected_rows;
    }

    /**
     * Detect last insert ID of table
     * 
     * @param int $count = 1  Number of items
     * @return int Affected rows
     */
    public final function lastInsertId($count = 1)
    {
        $order_conditions = array($this->primary_key => 'DESC');
        $orderby = SqlHelper::createOrderByStatement($order_conditions, $this->fields, $sql_params);
        $sql = sprintf('SELECT %s FROM %s %s LIMIT 0, %s', $this->primary_key, $this->table_name, $orderby, $count);
        $result = $this->database->query($sql, array());
        if ($count <= 1) {
            return count($result) > 0 ? $result[0][$this->primary_key] : NULL;
        }

        $ids = array();
        for ($i = count($result) - 1; $i >= 0; $i--) {
            $ids[] = $result[$i][$this->primary_key];
        }
        return $ids;
    }

    /**
     * Get definition of fields
     * 
     * @return array Field list
     */
    abstract protected function getFields();

    /**
     * Get table name
     * 
     * @return string Table name
     */
    abstract protected function getTableName();
    
    /**
     * Get alias of table name
     * 
     * @return string Alias of table name
     */
    protected function getTableAlias() {
        return NULL;
    }

    /**
     * Get column name of primary key
     * 
     * @return string Column name of primary key
     */
    abstract protected function getPrimaryKey();
}
