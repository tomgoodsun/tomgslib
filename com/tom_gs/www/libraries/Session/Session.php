<?php
/**
 * Session
 * 
 * @date 2013.09.08
 * @package libraries
 * @subpackage Session
 * @author Tom Higuchi
 * @copyright tom-gs.com(http://www.tom-gs.com)
 */

namespace com\tom_gs\www\libraries\Session;

class Session extends SessionBase
{
    /**
     * Session name
     */
    private $session_name;

    /**
     * Session ID
     */
    private $session_id;

    /**
     * Previous session ID
     * If regenerate is false, previous session ID equals session ID
     */
    private $previous_session_id;

    /**
     * Session cache expire time
     */
    private $cache_expire;

    /**
     * Session cache Limiter
     */
    private $cache_limiter;

    /**
     * Session save handler
     */
    private $save_handler = 'file';

    /**
     * Session save path
     */
    private $save_path;

    /**
     * Is session started
     */
    private $started = false;

    /**
     * Session class is constructed as singleton pattern
     * 
     * @param $sesid
     * @param array $options
     * @return Session  Instance of Session class
     */
    public static function getInstance($sesid = null, array $options = array())
    {
        static $instance;
        if ($instance === null) {
            $class = __CLASS__;
            $instance = new $class($sesid, $options);
        }
        return $instance;
    }

    /**
     * Constructor
     * 
     * @param $sesid
     * @param array $options
     */
    private function __construct($sesid, array $options = array())
    {
        $options += array(
            'regenarate' => true,
            'cache_expire' => null,
            'cache_limiter' => null,
            'save_handler' => null,
            'save_path' => null,
        );

        if (!is_null($sesid)) {
            $this->session_name = session_name($sesid);
        }

        if (isset($options['regenerate'])) {
            $this->previous_session_id = session_id();
            $this->session_id = session_regenerate_id();
        } else {
            $this->session_id = $this->previous_session_id = session_id();
        }
        
        if (isset($options['cache_expire'])) {
            $this->cache_expire = session_cache_expire($options['cache_expire']);
        } else {
            $this->cache_expire = session_cache_expire();
        }

        if (isset($options['cache_limiter'])) {
            $this->cache_limiter = session_cache_limiter($options['cache_limiter']);
        } else {
            $this->cache_limiter = session_cache_limiter();
        }

        if (isset($options['save_handler'])) {
            ini_set('session.save_handler', $options['save_handler']);
            $this->save_handler = $options['save_handler'];
        }

        if (isset($options['save_path'])) {
            $this->save_path = session_save_path($options['save_path']);
        } else {
            $this->save_path = session_save_path();
        }
    }

    /**
     * Start session
     * 
     * @return Session  Instance of Session class
     */
    public function start()
    {
        session_start();
        $this->started = true;
        return $this;
    }

    /**
     * Get session name
     * 
     * @return The same result of session_name() function
     */
    public function getSessionName()
    {
        return session_name($this->session_name);
    }

    /**
     * Is session started
     * 
     * @return Boolean  TRUE: Started / FALSE: Not started
     */
    public function isStarted()
    {
        return $this->started;
    }

    /**
     * Reset all session values
     */
    public function kill()
    {
        session_unset();
    }

    /**
     * Destroy session
     */
    public function destroy()
    {
        session_destroy();
        $this->started = false;
    }

    /**
     * Get session from super global variable
     * 
     * @return mixed  Returns $_SESSION
     */
    public function &getGlobalSession()
    {
        return $_SESSION;
    }
}
