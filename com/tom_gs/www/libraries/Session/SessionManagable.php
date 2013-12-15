<?php
/**
 * Interface to manage session
 * 
 * @date 2013.09.08
 * @package libraries
 * @subpackage Session
 * @author Tom Higuchi
 * @copyright tom-gs.com(http://www.tom-gs.com)
 */

namespace com\tom_gs\www\libraries\Session;

interface SessionManagable
{
    /**
     * [abstract] Get global $_SESSION
     */
    public function &getGlobalSession();

    /**
     * [abstract] Set value to $_SESSION
     * 
     * @param $key
     * @param $value
     */
    public function set($key, $value);

    /**
     * [abstract] Get value from $_SESSION
     * 
     * @param $label = null
     */
    public function get($label = null);

    /**
     * [abstract] Clear session values
     * 
     * @param $label = null
     */
    public function clear($label = null);

    /**
     * [abstract] Delete session value
     * 
     * @param $label = null
     */
    public function delete($label = null);
}
