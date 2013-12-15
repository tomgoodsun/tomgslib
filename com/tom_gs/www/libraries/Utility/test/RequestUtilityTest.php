<?php
/**
 * Request Utility
 * 
 * @package tomgslib
 * @subpackage libraries
 * @subpackage Utility
 */

namespace com\tom_gs\www\libraries\Utility\test;

use \com\tom_gs\www\libraries\Utility\RequestUtility;

require_once 'PHPUnit/Autoload.php';
require_once __DIR__ . '/../../include.php';

class RequestUtilityTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Add query parameters to URL
     *
     * @param string $url        URL to be added query parameters
     * @param array $parameters  Data for query parameter
     * @return string Query parameter string
     */
    public function testAddQueryParameters()
    {
        $url = 'http://www.example.com/';
        $test_data = array(
            array(
                $url . '?a=1&b=1&c[]=1&c[]=2&d[e][]=1&d[e][]=2&f[g]=1&f[h]=2&i[0][]=1&i[0][]=2&i[1][]=3&i[1][]=4',
                array(
                    'a' => 1,
                    'b' => 1,
                    'c' => array(1, 2),
                    'd' => array('e' => array(1, 2)),
                    'f' => array('g' => 1, 'h' => 2),
                    'i' => array(
                         array(1, 2),
                         array(3, 4),
                    ),
                ),
            ),
        );
        foreach ($test_data as $data) {
            $result = RequestUtility::addQueryParameters($url, $data[1]);
            $this->assertEquals($data[0], $result);
        }
    }

//    /**
//     * Create query parameters from array
//     *
//     * @param array $parameters  Data for query parameter
//     * @return string Query parameter string
//     */
//    public function createQueryParameters(array $parameters)
//    {
//        $param_set = array();
//        foreach ($parameters as $index => $value) {
//            $index = urlencode($index);
//            if (is_array($value)) {
//                self::createParameters($index, $value, $param_set);
//            } else {
//                $param_set[] = sprintf('%s=%s', $index, urlencode($value));
//            }
//        }
//        return implode($param_set, '&');
//    }
//
//    /**
//     * Create sub query parameters
//     *
//     * @param string $parent_key_name  Key name of parent
//     * @param array $parameters        Data for query parameter
//     * @param array &$param_set        Parameter result set
//     */
//    private function createParameters($parent_key_name, array $parameters, array &$param_set)
//    {
//        foreach ($parameters as $index => $value) {
//            if (is_array($value)) {
//                $name = sprintf('%s[%s]', $parent_key_name, urlencode($index));
//                self::createParameters($name, $value, $param_set);
//            } else {
//                // To make the clean format, rename the array index
//                $label = preg_match('/^[0-9]{1,}$/', $index) ? '' : urlencode($index);
//                $name = sprintf('%s[%s]', $parent_key_name, $label);
//                $param_set[] = sprintf('%s=%s', $name, urlencode($value));
//            }
//        }
//    }
}
