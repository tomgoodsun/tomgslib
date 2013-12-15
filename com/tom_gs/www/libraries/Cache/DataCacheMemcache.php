<?php
/**
 * Memcache DataCache Controller
 * Implements Interface DataCacheControllable
 *
 * @date 2012.03.26
 * @package tomgslib
 * @subpackage libraries
 * @subpackage Cache
 * @copyright tom-gs.com
 * @author tomgoodsun@gmail.com
 */

namespace com\tom_gs\www\libraries\Cache;

class DataCacheMemcache implements DataCacheControllable
{
    /**
     * @const Default host
     */
    const DEFAULT_HOST = 'localhost';

    /**
     * @const Default port
     */
    const DEFUTAL_PORT = '11211';

    /**
     * Host address
     */
    private $host;

    /**
     * Port No
     */
    private $port;

    /**
     * Memcache object
     */
    private $memcache;

    /**
     * Singleton
     * This class is used as singleton.
     *
     * @param $host = self::DEFAULT_HOST         Default host is localhost
     * @param $port = self::DEFUTAL_PORT         Default port is 11211
     * @param array $memcahce_options = array()  Memcache methods
     *    $memcahce_options = array('method_name' => array(arg1, arg2...));
     * @return DataCache_Memcache object
     */
    public static function getInstance(
        $host = self::DEFAULT_HOST,
        $port = self::DEFUTAL_PORT,
        array $memcahce_options = array()
    ) {
        static $instance;
        if ($instance === null) {
            $class_name = __CLASS__;
            $instance = new $class_name($host, $port, $memcahce_options);
        }
        return $instance;
    }

    /**
     * Constructor
     * Constructor connects Memcache server automatically
     *
     * @param $host = self::DEFAULT_HOST         Default host is localhost
     * @param $port = self::DEFUTAL_PORT         Default port is 11211
     * @param array $memcahce_options = array()  Memcache methods
     *    $memcahce_options = array('method_name' => array(arg1, arg2...));
     */
    private function __construct(
        $host = self::DEFAULT_HOST,
        $port = self::DEFUTAL_PORT,
        array $memcahce_options = array()
    ) {
        $this->host = $host;
        $this->port = $port;
        $this->memcache = new Memcache();
        $this->memcache->connect($host, $port);
        foreach ($memcahce_options as $method => $options) {
            if (method_exists($this->memcache, $method)) {
                call_user_func_array(array($this->memcache, $method), $options);
            }
        }
    }

    /**
     * Get memcache object
     *
     * @return Memcache object
     */
    public function getMemcache()
    {
        return $this->memcache;
    }

    /**
     * Get value
     *
     * @param $key  Key specifying value
     * @return mixed
     */
    public function get($key)
    {
        return $this->memcache->get($key);
    }

    /**
     * Set value
     *
     * @param $key    Key specifying value
     * @param $value  Value
     * @param $life   Life that the value will have been kept in second
     */
    public function set($key, $value, $life = null)
    {
        $this->memcache->set($key, $value, false, $life);
    }

    /**
     * Check existence
     *
     * @param $key  Key specifying value
     * @return boolean TRUE:exists / FALSE:doesn't exist
     */
    public function exists($key)
    {
        if ($this->get($key)) {
            return true;
        }
        return false;
    }

    /**
     * Delete value
     *
     * @param $key  Key specifying value
     */
    public function delete($key)
    {
        $this->memcache->delete($key);
    }
}
