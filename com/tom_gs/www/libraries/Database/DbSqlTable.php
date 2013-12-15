<?php

class DbSqlTable {
    /**
     * Column delimiter of SELECT statement
     */
    const COLUMN_DELIMITER = ', ';

    /**
     * Operator to cancatenate the column names
     */
    const COLUMN_ALIAS_OPERATER = '.';

    /**
     * Alias name
     */
    private $alias = null;

    /**
     * Table name
     */
    private $table_name = null;

    /**
     * Column names
     */
    private $columns = array();

    /**
     * Constructor
     *
     * @param array $columns        List of column names
     * @param string $table_name    Table name
     * @param string $alias = NULL  Alias name to table name
     */
    public function __construct(array $columns, $table_name, $alias = NULL) {
        $this->columns = $columns;
        $this->table_name = $table_name;
        $this->alias = $alias;
    }

    /**
     * Get alias name of table name
     *
     * @return string  Alias name of table name
     */
    public function getAlias() {
        return $this->alias;
    }

    /**
     * Get table name
     *
     * @param $eith_alias = true  Return value added with alias
     * @return string  Table name
     */
    public function getTableName($with_alias = true) {
        $table_name = $this->table_name;
        if ($with_alias && !is_null($this->alias)) {
            $table_name .= ' ' . $this->alias;
        }
        return $table_name;
    }

    /**
     * Get columns
     *
     * @param $eith_alias = true  Return value added with alias
     * @return array  List of column names
     */
    public function getColumns($with_alias = true) {
        $columns = array();
        for ($i = 0; $i < count($this->columns); $i++) {
            $column = '';
            if ($with_alias && !is_null($this->alias)) {
                $column .= $this->alias . self::COLUMN_ALIAS_OPERATER;
            }
            $columns[] = $column . $this->columns[$i];
        }
        return $columns;
    }

    /**
     * Get SELECT-FROM statement
     *
     * @param $eith_alias = true  Return value added with alias
     * @return string  SELECT-FROM statement
     */
    public function getSelectStatement($with_alias = true) {
        $columns = $this->getColumns($with_alias);
        $table_name = $this->getTableName($with_alias);
        return sprintf('SELECT %s FROM %s', implode(self::COLUMN_DELIMITER, $columns), $table_name);
    }

    /**
     * Create JOIN table
     *
     * @param string $join_mode               Join-mode is LEFT/RIGHT OUTER JOIN, INNER JOIN
     * @param string $join_key                Join key column name
     * $param string $target_join_key = NULL  Join key column name of target table
     * @param $eith_alias = true  Return value added with alias
     * @return string  SELECT-FROM statement
     */
    public function createJoinTable($join_mode, $join_key, $target_join_key = null) {
        return new DbSqlJoinTable($this, $join_mode, $join_key, $target_join_key);
    }
}
