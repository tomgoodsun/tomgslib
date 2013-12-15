<?php
/**
 * Stacked value debug class
 * 
 * @package libraries
 * @subpackage Debug
 * @author Tomohito Higuchi
 * @copyright http://www.tom-gs.com/
 * @date 2011.05.01
 */

namespace com\tom_gs\www\libraries\Debug;

class BatchValueDumper extends Dumper
{
    /**
     * @var array Dumping data
     */
    private $dumper = null;

    /**
     * @var array Dumping data
     */
    private $dump_data = array();

    /**
     * @var Border styles
     */
    private $borderStyles = array(
        'border-bottom' => '1px dotted #999999',
        'margin' => '1em 1em 1em 1em'
    );

    /**
     * Constructor
     */
    private function __construct()
    {
        $this->dumper = new ValueDumper();
    }

    /**
     * This class is singleton
     */
    public function getInstance()
    {
        static $instance;
        if ($instance === null) {
            $instance = new BatchValueDumper();
        }
        return $instance;
    }

    /**
     * Add new ValueDumper instance
     * 
     * @param $text
     * @param array $debug_info = array()
     */
    public function addDumper($text, array $debug_info = array())
    {
        $this->register($text, $debug_info);
    }

    /**
     * Register ValueDumper instance
     *
     * @param $text
     * @param array $debug_info = array()
     */
    public function register($text, array $debug_info = array())
    {
        array_push($this->dump_data, array('text' => $text, 'debug_info' => $debug_info));
    }

    /**
     * Set border styles
     */
    public function setBorderStyles(array $styles)
    {
        parent::setStyles($styles, $this->borderStyles);
    }

    /**
     * Get dumped string with CSS
     * 
     * @param $text  This is dummy
     * @param Boolean $return = false
     * @return String
     */
    public function dump($text, $return = false)
    {
        $dumpingString = '';
        foreach ($this->dump_data as $data) {
            $dumpingString .= $this->dumper->dump($data['text'], $return, $data['debug_info']) . "\n";
            $dumpingString .= sprintf('<div style=""></div>', $this->createStyleValues($this->borderStyles)) . "\n";
        }
        return $dumpingString;
    }
}
