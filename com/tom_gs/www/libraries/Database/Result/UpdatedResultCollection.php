<?php
namespace com\tom_gs\www\libraries\Database\Result;

class UpdatedResultCollection extends \ArrayObject implements ResultHandlable
{
    /**
     * Adds result
     * This method creates new instance of DatabaseUpdatedResult
     *
     * @param string $type        SQL type updating data on database
     * @param mixed $primary_key  The main key of data
     * @param affected_rows       Number of rows to be updated
     */
    public function addResult($type, $primary_key, $affected_rows)
    {
        $this->append(
            new DatabaseUpdatedResult($type, $primary_key, $affected_rows)
        );
    }

    /**
     * Returns all result
     *
     * @return array  Collection of DatabaseUpdatedResult
     */
    public function getResult()
    {
        return $this->getArrayCopy();
    }

    /**
     * Detects and returns result by type
     *
     * @param string $type                        SQL type updating data on database
     * @param boolean $include_effective = false  Return result won't include data whose affected_rows is 0
     * @return array  Filtered result
     */
    public function getResultByType($type, $include_effective = false)
    {
        $orig_data = $this->getArrayCopy();
        $result = array();
        foreach ($orig_data as $data) {
            if ($data->getType() == $type) {
                if ($include_effective) {
                    if ($data->getAffectedRows() > 0) {
                        $result[] = $data;
                    }
                } else {
                    $result[] = $data;
                }
            }
        }
        return $result;
    }

    /**
     * Returns inserted result
     *
     * @return array  Filtered collection of DatabaseUpdatedResult
     */
    public function getInsertedResult($include_effective = false)
    {
        return $this->getResultByType(
            UpdatedResult::INSERT,
            $include_effective
        );
    }

    /**
     * Returns updated result
     *
     * @return array  Filtered collection of DatabaseUpdatedResult
     */
    public function getUpdatedResult($include_effective = false)
    {
        return $this->getResultByType(
            UpdatedResult::UPDATE,
            $include_effective
        );
    }

    /**
     * Returns deleted result
     *
     * @return array  Filtered collection of DatabaseUpdatedResult
     */
    public function getDeletedResult($include_effective = false)
    {
        return $this->getResultByType(
            UpdatedResult::DELETE,
            $include_effective
        );
    }
}
