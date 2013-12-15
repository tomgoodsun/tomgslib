<?php
/**
 * Request Variable Processor
 *
 * @date 2013.09.08
 * @package tomgslib
 * @subpackage libraries
 * @subpackage Data
 */

namespace com\tom_gs\www\libraries\Data;

class RequestVariable
{
    /**
     * $_GET
     */
    private $get_vars = array();
    
    /**
     * $_POST
     */
    private $post_vars = array();
    
    /**
     * Request Variables
     */
    private $request_vars = array();

    /**
     * Constructor
     *
     * @param string $priority  Merging priority
     */
    private function __construct($priority)
    {
        $this->get_vars  = $_GET;
        $this->post_vars = $_POST;
        if ($priority == 'g') {
            $this->request_vars = $_GET + $_POST;
        } else {
            $this->request_vars = $_POST + $_GET;
        }
    }

    /**
     * Singleton method
     *
     * @param string $priority = 'p'  Merging priority
     */
    public function getInstance($priority = 'p')
    {
        static $instance = null;
        if ($instance === null) {
            $class = __CLASS__;
            $instance = new $class($priority);
        }
        return $instance;
    }

    /**
     * Get $_GET variables
     */
    public function getGetVars()
    {
        return $this->get_vars;
    }

    /**
     * Get $_POST variables
     */
    public function getPostVars()
    {
        return $this->post_vars;
    }

    /**
     * Get request variables
     */
    public function getReqVars()
    {
        return $this->request_vars;
    }
    
    /**
     * Get variables
     *
     * @param string $label = null  Variable name
     */
    public function getVar($label = null)
    {
        if (array_key_exists($label, $this->request_vars)) {
            return $this->request_vars[$label];
        }
        return null;
    }
}
