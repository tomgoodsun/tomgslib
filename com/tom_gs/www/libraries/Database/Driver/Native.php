<?php
/**
 * Database.php
 * 
 * This class for databse
 */

namespace com\tom_gs\www\libraries\Database\Driver;

use \com\tom_gs\www\libraries\Database\Exception;

abstract class Native extends Base
{
    /**
     * Check availability of database vendor
     * 
     * @param boolean $return = false  Use return or not (exception)
     */
    protected function isAvailable($return = false)
    {
        if ($return) {
            return function_exists($this->getVendorSign() . '_connect');
        }
        if (!function_exists($this->getVendorSign() . '_connect')) {
            throw new Exception\DatabaseException(
                'This server\'s PHP does not have commands. [%s]'
            );
        }
    }

    /**
     * Rollback transaction
     * 
     * @access    public
     */
    public function rollback()
    {
        return $this->simpleQuery('ROLLBACK');
    }

    /**
     * Commit transaction
     * 
     * @access    public
     */
    public function commit()
    {
        return $this->simpleQuery('COMMIT');
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
        $resultset = array();
        while ($row = $this->fetchArray($result, $vendorResultType)) {
            $resultset[] = $row;
        }
        $this->freeResult($result);
        return $resultset;
    }

    /**
     * Execute simple query
     *
     * @param string $query
     */
    abstract public function simpleQuery($query);

    /**
     * Fetch result in array
     * 
     * @param resource &$result   The pointer to the resource of result
     * @param string $resultType  Result type of returned array
     *     [ASSOC]
     *         Default. 2-level numbered-associated array.
     *     [NUM]
     *         Default. 2-level numbered-numbered array.
     */
    abstract protected function fetchArray(&$result, $resultType);
}
