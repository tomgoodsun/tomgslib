<?php
/**
 * Language Exception
 * 
 * @package libraries
 * @subpackage Language
 * @author Tom Higuchi
 * @copyright tom-gs.com(http://www.tom-gs.com)
 * @date 2012.05.03
 */

namespace com\tom_gs\www\libraries\Language;

final class LanguageException extends \com\tom_gs\www\libraries\Exception\CustomException
{
    /**
     * constructor
     */
    public function __construct($message)
    {
        $this->message = $this->createMessage($message);
    }

    /**
     * getClassName() must be overridden
     * 
     * @return String  Class name of this instance
     */
    protected function getClassName()
    {
        return get_class($this);
    }
}
