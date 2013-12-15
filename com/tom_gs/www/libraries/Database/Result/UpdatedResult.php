<?php
namespace com\tom_gs\www\libraries\Database\Result;

class UpdatedResult implements ResultHandlable
{
    /**
     * Updating type 'INSERT'
     */
    const INSERT = 'insert';

    /**
     * Updating type 'UPDATE'
     */
    const UPDATE = 'update';

    /**
     * Updating type 'DELETE'
     */
    const DELETE = 'delete';

    /**
     * SQL type updating data on database
     */
    private $type;

    /**
     * The main key of data
     */
    private $primary_key;

    /**
     * Number of rows to be updated
     */
    private $affected_rows;

    /**
     * Constructor
     *
     * @param string $type        SQL type updating data on database
     * @param mixed $primary_key  The main key of data
     * @param affected_rows       Number of rows to be updated
     */
    public function __construct($type, $primary_key, $affected_rows)
    {
        $this->type = $type;
        $this->primary_key = $primary_key;
        $this->affected_rows = $affected_rows;
    }

    /**
     * Get type
     *
     * @return string $type  SQL type updating data on database
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Get primary key
     *
     * @return mixed  The main key of data
     */
    public function getPrimaryKey()
    {
        return $this->primary_key;
    }

    /**
     * Get affcted rows
     *
     * @return int  Number of rows to be updated
     */
    public function getAffectedRows()
    {
        return $this->affected_rows;
    }

    /**
     * Get result, as primary key
     *
     * @return mixed  The main key of data
     */
    public function getResult()
    {
        return $this->getPrimaryKey();
    }
}
