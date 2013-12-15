<?php
/**
 * Value Dumper
 * 
 * @package libraries
 * @subpackage Debug
 * @author Tomohito Higuchi
 * @copyright http://www.tom-gs.com/
 * @date 2013.09.08
 */

namespace com\tom_gs\www\libraries\Debug;

class ValueDumper extends Dumper
{
    /**
     * @const Style targets
     */
    const VIEW    = 'view';
    const OPERAND = 'operand';
    const STRING  = 'string';
    const BOOLEAN = 'boolean';
    const NUMBER  = 'number';

    /**
     * @var Replacing formats(regexp, replace, styles)
     */
    private $formats = array();

    /**
     * ValueDumper class is constructed as singleton pattern
     * 
     * @return ValueDumper  Instance of ValueDumper class
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
     * @param $return = true
     */
    public function __construct()
    {
        $this->formats = array(
            self::OPERAND => array(
                'regexp' => '/(\'.*\') \=&gt;/',
                'replace' => '<span style="%s">$1</span> =&gt;',
                'styles' => array('color' => '#FF6600')
            ),
            self::STRING  => array(
                'regexp' => '/\=&gt; (\'.*\')/',
                'replace' => '=&gt; <span style="%s">$1</span>',
                'styles' => array('color' => '#3366CC')
            ),
            self::BOOLEAN => array(
                'regexp' => '/\=&gt; (true|false|null)/',
                'replace' => '=&gt; <span style="%s">$1</span>',
                'styles' => array('color' => '#009966')
            ),
            self::NUMBER  => array(
                'regexp' => '/\=&gt; ((\-|\+)?\d+(\.\d+)?)/',
                'replace' => '=&gt; <span style="%s">$1</span>',
                'styles' => array('color' => '#CC3333')
            )
        );
    }

    /**
     * Adjusting string of dump data
     * 
     * @param $text
     * @return String
     */
    protected function adjustStyle($text)
    {
        $text = preg_replace('/(\n\r|\n|\r)(\s+)([a-zA-Z].)/', '$3', $text);
        foreach ($this->formats as $value) {
            $styled = sprintf(
                $value['replace'],
                $this->createStyleValues($value['styles'])
            );
            $text = preg_replace(
                $value['regexp'],
                $styled,
                $text
            );
        }
        return $text;
    }

    /**
     * Set Styles
     */
    public function setStyles($type, array $styles)
    {
        if ($type == self::VIEW) {
            parent::setStyles($styles);
        } else {
            if (array_key_exists($type, $this->formats)) {
                parent::setStyles($styles, $this->formats[$type]['styles']);
            }
        }
    }

    /**
     * Write all strings with CSS
     * 
     * @param $text
     * @param Boolean $return = false
     * @param array $debug_info = array()
     */
    public function write($text, $return = false, array $debug_info = array())
    {
        parent::write($text, $return, $debug_info);
    }

    /**
     * Get dumped string with CSS
     * 
     * @param $text
     * @param Boolean $return = false
     * @param array $debug_info = array()
     * @return String
     */
    public function dump($text, $return = false, array $debug_info = array())
    {
        $text = htmlspecialchars(var_export($text, $return));
        $result = $this->createDebugInfo($debug_info);
        $result .= $this->adjustStyle($text) . "\n";
        return $result;
    }
}
