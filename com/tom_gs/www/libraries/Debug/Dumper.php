<?php
/**
 * Abstract class of debug dumper
 * 
 * @package libraries
 * @subpackage Debug
 * @author Tomohito Higuchi
 * @copyright http://www.tom-gs.com/
 * @date 2013.09.08
 */

namespace com\tom_gs\www\libraries\Debug;

abstract class Dumper
{
    /**
     * CSS for Info bar
     */
    private $infoBarStyles = array(
        'background' => '#999',
        'color' => '#eee',
        'font-family' => 'Verdana',
        'font-size' => '10px',
        'font-weight' => 'bold',
        'margin' => '0'
    );

    /**
     * CSS for Label in Info bar
     */
    private $labelStyles = array(
        'color' => '#ccc',
    );

    /**
     * CSS for Original Message
     */
    private $viewAreaStyles = array(
        'background' => '#fff',
        'border' => '1px solid #999',
        'color' => '#999',
        'font-size' => '12px',
        'font-family' => 'Courier New',
        'margin' => '0 0 1em 0',
        'overflow-x' => 'scroll'
    );

    /**
     * Set Styles
     * 
     * @access public
     * @param array $styles
     * @param array &$optional_styles = array()
     */
    public function setStyles(array $styles, array &$original_styles = array())
    {
        if (count($original_styles) <= 0) {
            foreach ($styles as $property => $value) {
                $this->viewAreaStyles[$property] = $value;
            }
        } else {
            foreach ($styles as $property => $value) {
                $original_styles[$property] = $value;
            }
        }
    }

    /**
     * Create style values
     * 
     * @access protected
     * @param array $styles
     * @return string Style values
     */
    protected function createStyleValues(array $styles)
    {
        $style = '';
        foreach ($styles as $property => $value) {
            $style .= sprintf('%s:%s;', $property, $value);
        }
        return $style;
    }

    /**
     * Print debug message
     * 
     * @param $text
     * @param Boolean $return = false
     * @param array $debug_info = array()
     */
    public function write($text, $return = false, array $debug_info = array())
    {
        echo sprintf(
            $this->getViewAreaFormat(),
            $this->createStyleValues($this->viewAreaStyles),
            $this->dump($text, $return, $debug_info)
        );
    }

    /**
     * Get view area format
     */
    protected function getViewAreaFormat()
    {
        return '<pre style="%s">%s</pre>';
    }

    /**
     * Get info bar format
     */
    protected function getInfoBarFormat()
    {
        $style_text = $this->createStyleValues($this->labelStyles);
        return '<div style="%s">%s <span style="' . $style_text . '">on line </span>%s</div>';
    }

    /**
     * Get debug info
     *
     * @param array $debug_info = array()
     */
    protected function createDebugInfo(array $debug_info = array())
    {
        $debug_info += array(
            'file' => 'undefined',
            'line' => 'undefined',
        );
        return sprintf(
            $this->getInfoBarFormat(),
            $this->createStyleValues($this->infoBarStyles),
            $debug_info['file'],
            $debug_info['line']
        );
    }

    /**
     * Get view area styles
     *
     * @return array View area styles
     */
    protected function getViewAreaStyles()
    {
        return $this->viewAreaStyles;
    }

    /**
     * Get dumped string with CSS
     * 
     * @param $text
     * @param Boolean $return = false
     * @return String
     */
    abstract public function dump($text, $return = false);
}
