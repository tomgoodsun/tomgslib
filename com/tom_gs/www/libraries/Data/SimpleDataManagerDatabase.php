<?php 
/**
 * Database Simple Data Manager
 * 
 * @package tomgslib
 * @subpackage libraries
 * @subpackage Data
 * @author Tom Higuchi
 * @copyright tom-gs.com(http://www.tom-gs.com)
 * @date 2012.10.04
 */

namespace com\tom_gs\www\libraries\Data;

use \com\tom_gs\www\libraries\Database\Driver;

abstract class SimpleDataManagerDatabase extends SimpleDataManager
{
    /**
     * Database engine
     */
    private $db_engine;

    /**
     * Table name
     */
    private $table_name;

    /**
     * List of column names
     */
    private $columns = array();

    /**
     * DbSqlTable object
     */
    private $table;

    /**
     * Constructor
     *
     * @param mixed $db_engine  Database engine, this must be instance of some object
     */
    public function __construct(Driver\Base $db_engine)
    {
        $this->db_engine = $db_engine;
        $this->id_alias = $this->detectIdAlias();
        $this->table_name = $this->detectTableName();
        $this->columns = $this->detectColumns();
        $this->table = new DbSqlTable($this->columns, $this->table_name);
        parent::__construct();
    }

    /**
     * Get ID alias name
     *
     * @param string  Alias name of ID
     */
    final public function getIdAlias()
    {
        return $this->id_alias;
    }

    /**
     * Get table name
     *
     * @param string  Table name
     */
    final public function getTableName()
    {
        return $this->table_name;
    }

    /**
     * Get list of column namaes
     *
     * @param string  List of column names
     */
    final public function getColumns()
    {
        $this->columns;
    }

    /**
     * Detect data
     * @Override
     *
     * @return string  List of data
     */
    protected function detectData()
    {
        return $this->db_engine->query($this->table->getSelectStatement(true));
    }

    /**
     * Detect table name
     *
     * @return string  Table name
     */
    abstract protected function detectTableName();

    /**
     * Detect column names
     *
     * @return string  List of column names
     */
    abstract protected function detectColumns();
}
