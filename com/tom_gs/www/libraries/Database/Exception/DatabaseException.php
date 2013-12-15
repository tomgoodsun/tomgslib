<?php
/**
 * DatabaseException.php
 */

namespace com\tom_gs\www\libraries\Database\Exception;

use \com\tom_gs\www\libraries\Exception;

final class DatabaseException extends Exception\CustomException
{
    /**
     * constructor
     */
    public function __construct($message, $error = null)
    {
        if (!is_null($error)) {
            $message .= sprintf(' [%s]', $error);
        }
        $this->message = $this->createMessage($message);
    }

    /**
     * getClassName() must be overridden
     */
    protected function getClassName()
    {
        return get_class($this);
    }
}
