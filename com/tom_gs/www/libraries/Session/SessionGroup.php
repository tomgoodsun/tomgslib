<?php
/**
 * SessionGroup.php
 * 
 * SessionGroup must be overridden.
 * This class makes session group for user's custom module.
 * You must create and start instance of Session before creating this class.
 * 
 * @date 2013.09.08
 * @package libraries
 * @subpackage Session
 * @author Tom Higuchi
 * @copyright tom-gs.com(http://www.tom-gs.com)
 */

namespace com\tom_gs\www\libraries\Session;

abstract class SessionGroup extends SessionBase
{
    /**
     * Check group key already exists or session is started.
     * 
     * @param $group_key
     */
    public static function isCreatable($group_key)
    {
        if (!Session::getInstance()->isStarted()) {
            return false;
        }
        if (array_key_exists($group_key, $_SESSION)) {
            return false;
        }
        return true;
    }

    /**
     * Singleton
     * 
     * Smaple script for singleton
     */
    public static function getInstance()
    {
        static $instance;
        if ($instance === null) {
            $class = __CLASS__;
            $instance = new $class();
        }
        return $instance;
    }

    /**
     * Constructor
     * 
     * Extending this abstract class is strongly suggested to construct as singleton pattern.
     */
    final protected function __construct()
    {
        $group_key = $this->getGroupKey();
        if (!self::isCreatable($group_key)) {
            $msg = sprintf(
                "Failed to create SessionGroup object: session is not started or group key '%s' already exists.",
                $group_key
            );
            throw new \Exception($msg);
        }
        if (!array_key_exists($group_key, $_SESSION)) {
            Session::getInstance()->set($group_key, array());
        }
    }

    /**
     * Get global $_SESSION
     *
     * @return mixed  Group of session
     */
    public function &getGlobalSession()
    {
        $group_key = $this->getGroupKey();
        if (array_key_exists($group_key, $_SESSION)) {
            return $_SESSION[$group_key];
        }
        return null;
    }

    /**
     * [abstract] Get group key
     */
    abstract public function getGroupKey();
}
