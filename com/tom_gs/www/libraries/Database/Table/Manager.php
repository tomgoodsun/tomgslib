<?php 
namespace com\tom_gs\www\libraries\Database\Table;

use \com\tom_gs\www\libraries\Database\Driver;

abstract class Manager
{
    /**
     * Database engine
     */
    private $database;

    /**
     * Table names and class names
     */
    private $tableNames = array();

    /**
     * Instances of tables objects
     */
    private $tables = array();

    /**
     * Constructor
     *
     * @param \Databsee $database
     * @param array $tableNames
     */
    protected function __construct(Driver\Base $database)
    {
        $this->database = $database;
        $this->tableNames = $this->getTableNames();
    }

    /**
     * Get database engine
     * 
     * @param \Databsee $database
     * @return Instance of DatabaseTableManager
     */
    public static function getInstance(Driver\Base $database = null)
    {
        static $instance;
        if ($instance === null) {
            $instance = new self($database);
        }
        return $instance;
    }

    /**
     * Create bindable primary key prameters
     *
     * @param mixed $pk_cond  Primary key conditions
     * @return array Bindable parameters
     */
    public function getTable($tableName)
    {
        if (!isset($this->tables[$tableName])) {
            if (!isset($this->tableNames[$tableName])) {
                throw new Exception("Table '$tableName' is defined.");
            }
            $this->tables[$tableName] =
                new $this->tableNames[$tableName]($this->database);
        }
        return $this->tables[$tableName];
    }

    abstract protected function getTableNames();
}
