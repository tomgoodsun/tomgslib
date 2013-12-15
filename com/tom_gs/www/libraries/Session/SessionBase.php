<?php
/**
 * Session base class
 * 
 * @date 2013.09.08
 * @package libraries
 * @subpackage Session
 * @author Tom Higuchi
 * @copyright tom-gs.com(http://www.tom-gs.com)
 */

namespace com\tom_gs\www\libraries\Session;

abstract class SessionBase implements SessionManagable
{
    /**
     * Separator of path
     */
    protected $array_separator = '.';

    /**
     * Set value to $_SESSION
     * 
     * @param $key
     * @param $value
     */
    public function set($key, $value)
    {
        $session =& $this->getGlobalSession();
        $session[$key] = $value;
    }

    /**
     * Get value from $_SESSION
     * 
     * @param $label = null
     */
    public function get($label = null)
    {
        $matcher = sprintf('/(\w|\d)\%s(\w|\d)/', $this->array_separator);
        if (preg_match($matcher, $label)) {
            return $this->getSessionByPath($label);
        }
        $session =& $this->getGlobalSession();
        if (is_null($label)) {
            return $session;
        }
        
        if (array_key_exists($label, $session)) {
            return $session[$label];
        }
        return null;
    }

    /**
     * Clear session values
     * 
     * @param $label = null
     */
    public function clear($label = null)
    {
        $session =& $this->getGlobalSession();
        if (is_null($label)) {
            unset($session);
            $this->kill();
            //$this->destroy();
        } else {
            if (array_key_exists($label, $session)) {
                unset($session[$label]);
            }
        }
    }

    /**
     * Delete session value
     * 
     * @param $label = null
     */
    public function delete($label = null)
    {
        $this->clear($label);
    }

    /**
     * Get session value with path
     * 
     * @param $label
     */
    private function getSessionByPath($label)
    {
        $paths = explode($this->array_separator, $label);
        $session =& $this->getGlobalSession();
        foreach ($paths as $key) {
            if (array_key_exists($key, $session)) {
                $session = $session[$key];
            } else {
                return null;
            }
        }
        return $session;
    }
}
