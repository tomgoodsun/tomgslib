<?php
/**
 * HTML Utility
 * 
 * @package tomgslib
 * @subpackage libraries
 * @subpackage Utility
 */

namespace com\tom_gs\www\libraries\Utility;

final class HtmlUtility
{
    /**
     * Create style values
     * 
     * @access  protected
     * @param array $styles
     * @return string Style values
     */
    public static function createStyleValues(array $styles)
    {
        $style = '';
        foreach ($styles as $property => $value) {
            $style .= sprintf('%s:%s;', $property, $value);
        }
        return $style;
    }
}
