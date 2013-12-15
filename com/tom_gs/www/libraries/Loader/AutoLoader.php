<?php
/**
 * Auto Loager
 * 
 * @package libraries
 * @subpackage Loader
 * @author Tom Higuchi
 * @copyright tom-gs.com(http://www.tom-gs.com)
 * @date 2012.05.03
 */
namespace com\tom_gs\www\libraries\Loader;

class AutoLoader
{
    private $basepath = null;

    private $modules = array();

    public static function getInstance($basepath = null)
    {
        static $instance;
        if ($instance === null) {
            $instance = new self($basepath);
        }
        return $instance;
    }

    public function __construct($basepath)
    {
        $this->basepath = $basepath;
    }

    /**
     * Include include files
     *
     * @param string $basepath  Base path of package
     * @param array $paths      Lists of package names
     */
    public function load($basepath, array $paths)
    {
        $last_ds_pos = strripos($basepath, DS);
        if ($last_ds_pos === false) {
            $last_ds_pos = 0;
        }
        if ($last_ds_pos + 1 < strlen($basepath)) {
            $basepath .= DS;
        }
        foreach ($paths as $path) {
            self::loadIncluder($basepath . $path);
        }
    }

    /**
     * Include file
     *
     * @param string $path  Path to include directory
     */
    public function loadIncluder($path)
    {
        $last_ds_pos = strripos($path, DS);
        if ($last_ds_pos === false) {
            $last_ds_pos = 0;
        }
        if ($last_ds_pos + 1 < strlen($path)) {
            $path .= DS;
        }
        include_once($path . 'include.php');
    }

    /**
     * Register packages to autoloader
     * 
     * @param string $ns  Namespace
     */
    public function register($ns)
    {
        if (!array_key_exists($ns, $this->modules)) {
            $this->modules[$ns] =
                new SplClassLoader($ns, $this->basepath);
            $this->modules[$ns]->register();
        }
    }
}
