<?php
/**
 * DataCacheManager
 *
 * @date 2012.03.26
 * @package tomgslib
 * @subpackage libraries
 * @subpackage Cache
 * @copyright tom-gs.com
 * @author tomgoodsun@gmail.com
 */

namespace com\tom_gs\www\libraries\Cache;

class DataCacheManager
{
    /**
     * Formats and splitters
     */
    const KEY_FORMAT = '%s::%s';
    const IMPL_FORMAT = '%s_%s';
    const ARG_FORMAT = '%s:%s';
    const ARRAY_FORMAT = 'array(%s)';
    const ARG_SPRITTER = ', ';

    /**
     * Default caching life 
     */
    const DEFAULT_LIFE = 120; // 30 min.

    /**
     * Handler that cache data is managed
     */
    private $handler = null;
    private $prefix = '';

    /**
     * This class is singleton
     *
     * @param DataCacheControllable $handler = null
     * @param $prefix = ''  If no string, the SERVER_NAME is used.
     */
    public function getInstance(DataCacheControllable $handler = null, $prefix = '')
    {
        static $instance;
        if ($instance === null) {
            $class_name = __CLASS__;
            $instance = new $class_name($handler, $prefix);
        }
        return $instance;
    }

    /**
     * Constructor
     *
     * @param DataCacheControllable $handler = null
     * @param $prefix = ''  If no string, the SERVER_NAME is used.
     */
    protected function __construct(DataCacheControllable $handler = null, $prefix = '')
    {
        $this->handler = $handler;
        if (strlen($prefix) == 0) {
            $prefix = $_SERVER['SERVER_NAME'] . '::';
        }
        $this->prefix = $prefix;
    }

    /**
     * Get data from cache
     * If data are not found in cache, this method will be return null.
     * If implements and arguments are null, this method use debug_backtrace() to create key.
     *
     * @param mixed $arg1 = null  Definition of implement
     * @param mixed $arg2 = null  Arguments of implement
     */
    public function getCache($arg1 = null, $arg2 = null)
    {
        if ($arg1 === null || $arg2 === null) {
            $debug_backtrace = debug_backtrace();
            $this->defineArgument($debug_backtrace, $arg1, $arg2);
        }
        $key = $this->createKey($arg1, $arg2);
        return $this->handler->get($key);
    }

    /**
     * Set data into cache system
     * If implements and arguments are null, this method use debug_backtrace() to create key.
     *
     * @param mixed $arg1 = null  Caching data
     * @param mixed $arg2 = null  Life second of cache
     * @param mixed $arg3 = null  Definition of implement
     * @param mixed $arg4 = null  Arguments of implement
     */
    public function setCache($arg1 = null, $arg2 = null, $arg3 = null)
    {
        $impl = $args = array();
        if ($arg1 !== null) {
            $impl = $arg1;
        }
        if ($arg1 === null) {
            $debug_backtrace = debug_backtrace();
            $this->defineArgument($debug_backtrace, $impl, $args);
        }
        if ($arg3 === null) {
            $arg3 = self::DEFAULT_LIFE;
        }
        $key = $this->createKey($impl, $args);
        $this->handler->set($key, $arg2, $arg3);
    }

    /**
     * Create key to detect values
     *
     * @param mixed $impl  Array means callback, scalar means __FUNCTION__ or __METHOD__.
     * @param mixed $args  This must be array as of func_get_args().
     */
    private function createKey($impl, $args)
    {
        if (is_array($impl)) {
            $implement = sprintf(self::IMPL_FORMAT, $impl[0], $impl[1]);
        } else {
            $implement = $impl;
        }
        $key = $this->prefix . sprintf(
            self::KEY_FORMAT,
            $implement,
            $this->createKeyFromArguments($args)
        );
        return $key;
    }

    /**
     * Create key string from arguments
     *
     * @param mixed $impl  Array means callback, scalar means __FUNCTION__ or __METHOD__.
     * @param mixed $args  This must be array as of func_get_args().
     */
    private function createKeyFromArguments($args)
    {
        $arg_str = '';
        foreach ($args as $no => $value) {
            if (is_scalar($value)) {
                $arg_str .= sprintf(
                    self::ARG_FORMAT,
                    $no,
                    $value
                ) . self::ARG_SPRITTER;
            } else {
                $arg_str .= sprintf(
                    self::ARG_FORMAT,
                    $no,
                    $this->parseNonScalarArgument($value)
                ) . self::ARG_SPRITTER;
            }
        }
        return trim($arg_str, self::ARG_SPRITTER);
    }

    /**
     * Parse non-scaler argument
     * TODO: This method must be handle for the objects
     *
     * @param mixed $arg_value  
     */
    private function parseNonScalarArgument($arg_value)
    {
        return sprintf(self::ARRAY_FORMAT, $this->createKeyFromArguments($arg_value));
    }

    /**
     * Define get and set cache method arguments
     *
     * @param mixed $debug_backtrace
     * @param &$impl
     * @param &$args
     */
    private function defineArgument($debug_backtrace, &$impl, &$args)
    {
        if (isset($debug_backtrace[1])) {
            $debug_backtrace = $debug_backtrace[1];
            if (isset($debug_backtrace['class'])) {
                $impl = array($debug_backtrace['class'], $debug_backtrace['function']);
            } else {
                $impl = $debug_backtrace['function'];
            }
            $args = $debug_backtrace['args'];
        }
        $args = array();
    }
}
