<?php 
/**
 * Simple Data Manager
 * 
 * @package tomgslib
 * @subpackage libraries
 * @subpackage Data
 * @author Tom Higuchi
 * @copyright tom-gs.com(http://www.tom-gs.com)
 * @date 2012.10.04
 */

namespace com\tom_gs\www\libraries\Data;

abstract class SimpleDataManager
{
    /**
     * Alias name of unique key
     */
    private $id_alias = null;

    /**
     * Data to be managed by this class
     */
    private $data = null;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->id_alias = $this->detectIdAlias();
    }

    /**
     * Get data to be managed by this class
     *
     * @return array  Managed data
     */
    public function getData()
    {
        if ($this->data === null) {
            $this->data = $this->detectData();
        }
        return $this->data;
    }

    /**
     * Get by ID identified by alias name
     *
     * @param mixed $identifier  Unique key
     * @return mixed  Data identified by ID
     */
    public function getById($identifier)
    {
        $data = $this->getData();
        foreach ($data as $value) {
            if (array_key_exists($this->id_alias, $value)) {
                if ($identifier == $value[$this->id_alias]) {
                    return $value;
                }
            }
        }
        return array();
    }

    /**
     * Get filtered data with conditions
     *
     * @param array $conditions  Setting for ArrayFilterUtility::filterByConditions()
     * @return array  Filtered result with conditions
     */
    public function getFiltered(array $conditions)
    {
        return ArrayFilterUtility::filterByConditions($this->data, $conditions);
    }

    /**
     * Detect ID alias name
     *
     * @return string  Alias name
     */
    abstract protected function detectIdAlias();

    /**
     * Detect data
     *
     * @return string  List of data
     */
    abstract protected function detectData();
}
