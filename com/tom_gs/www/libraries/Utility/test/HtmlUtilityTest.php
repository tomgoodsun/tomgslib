<?php
/**
 * HTML Utility
 * 
 * @package tomgslib
 * @subpackage libraries
 * @subpackage Utility
 */

namespace com\tom_gs\www\libraries\Utility\test;

use \com\tom_gs\www\libraries\Utility\HtmlUtility;

require_once 'PHPUnit/Autoload.php';
require_once __DIR__ . '/../../include.php';

class HtmlUtilityTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Create style values
     * 
     * @access  protected
     * @param array $styles
     * @return string Style values
     */
    public function testCreateStyleValues()
    {
        $test_data = array(
            array(
                'border:1px solid #ccc;margin-top:10px;',
                array(
                    'border' => '1px solid #ccc',
                    'margin-top' => '10px',
                ),
            ),
        );
        foreach ($test_data as $data) {
            $result = HtmlUtility::createStyleValues($data[1]);
            $this->assertEquals($data[0], $result);
        }
    }
}
