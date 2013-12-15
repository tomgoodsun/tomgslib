<?php
/**
 * Array Utility
 * 
 * @package tomgslib
 * @subpackage libraries
 * @subpackage Utility
 */

namespace com\tom_gs\www\libraries\Utility\test;

use \com\tom_gs\www\libraries\Utility\ArrayUtility;

require_once 'PHPUnit/Autoload.php';
require_once __DIR__ . '/../../include.php';

final class ArrayUtilityTest extends \PHPUnit_Framework_TestCase
{
    private $test_data = array(
        array('user_id' => 1, 'name' => 'Arbert'),
        array('user_id' => 2, 'name' => 'Bob'),
        array('user_id' => 3, 'name' => 'Charlie'),
        array('user_id' => 3, 'name' => 'Edward'),
        array('user_id' => 4, 'name' => 'Frank'),
        array('user_id' => 5, 'name' => 'George'),
        array('user_id' => 6, 'name' => 'George'),
    );

    /**
     * Make array indexed by specified indexed value
     * 
     * @param array $data                The 2-level array, 2nd level is must be the associated array.
     * @param string $index              Index name of associated array.
     * @param boolean $overwrite = true  Overwrite if the specified indexed value is duplicate
     * @return array  Grouped by $index array.
     */
    public function testReindex()
    {
        $assert_results = array(
            'user_id' => array(
                array(
                    1 => array('user_id' => 1, 'name' => 'Arbert'),
                    2 => array('user_id' => 2, 'name' => 'Bob'),
                    3 => array('user_id' => 3, 'name' => 'Charlie'),
                    4 => array('user_id' => 4, 'name' => 'Frank'),
                    5 => array('user_id' => 5, 'name' => 'George'),
                    6 => array('user_id' => 6, 'name' => 'George'),
                ),
                array(
                    1 => array('user_id' => 1, 'name' => 'Arbert'),
                    2 => array('user_id' => 2, 'name' => 'Bob'),
                    3 => array('user_id' => 3, 'name' => 'Edward'),
                    4 => array('user_id' => 4, 'name' => 'Frank'),
                    5 => array('user_id' => 5, 'name' => 'George'),
                    6 => array('user_id' => 6, 'name' => 'George'),
                ),
            ),
            'name' => array(
                array(
                    'Arbert'  => array('user_id' => 1, 'name' => 'Arbert'),
                    'Bob'     => array('user_id' => 2, 'name' => 'Bob'),
                    'Charlie' => array('user_id' => 3, 'name' => 'Charlie'),
                    'Edward'  => array('user_id' => 3, 'name' => 'Edward'),
                    'Frank'   => array('user_id' => 4, 'name' => 'Frank'),
                    'George'  => array('user_id' => 5, 'name' => 'George'),
                ),
                array(
                    'Arbert'  => array('user_id' => 1, 'name' => 'Arbert'),
                    'Bob'     => array('user_id' => 2, 'name' => 'Bob'),
                    'Charlie' => array('user_id' => 3, 'name' => 'Charlie'),
                    'Edward'  => array('user_id' => 3, 'name' => 'Edward'),
                    'Frank'   => array('user_id' => 4, 'name' => 'Frank'),
                    'George'  => array('user_id' => 6, 'name' => 'George'),
                ),
            ),
        );
        foreach ($assert_results as $key => $data) {
            $result = ArrayUtility::reindex($this->test_data, $key, false);
            $this->assertEquals($data[0], $result);
            $result = ArrayUtility::reindex($this->test_data, $key);
            $this->assertEquals($data[1], $result);
        }
    }

    /**
     * Make array grouped by specified index
     * 
     * @param array $data     The 2-level array, 2nd level is must be the associated array.
     * @param mixed $indexes  Index name of associated array.
     * @return array  Grouped by $index array.
     */
    public function testGroupBy()
    {
        $assert_results = array(
            1 => array(
                array('user_id' => 1, 'name' => 'Arbert')
            ),
            2 => array(
                array('user_id' => 2, 'name' => 'Bob')
            ),
            3 => array(
                array('user_id' => 3, 'name' => 'Charlie'),
                array('user_id' => 3, 'name' => 'Edward')
            ),
            4 => array(
                array('user_id' => 4, 'name' => 'Frank')
            ),
            5 => array(
                array('user_id' => 5, 'name' => 'George')
            ),
            6 => array(
                array('user_id' => 6, 'name' => 'George')
            ),
        );
        $result = ArrayUtility::groupBy($this->test_data, array('user_id'));
        $this->assertEquals($assert_results, $result);
    }

//    /**
//     * Extract values identified by filters
//     * If string or integer is given in filter, result must be a single array.
//     * If array given, result must be a mutiple array.
//     * If no pointer in 2nd level, that'll be set null.
//     * 
//     * @param array $data    The 2-level array, 2nd level is must be the associated array.
//     * @param mixed $filter  Index name of associated array.
//     * @return array  Extracted data.
//     */
//    public function extractBy(array $data, $filters)
//    {
//        $result = array();
//        $is_single_filter = false;
//        if (is_scalar($filters)) {
//            $filters = array($filters);
//            $is_single_filter = true;
//        }
//        if (count($filters) <= 0) {
//            return $result;
//        }
//        foreach ($data as $value) {
//            foreach ($filters as $filter) {
//                $item = array();
//                if (array_key_exists($filter, $value)) {
//                    $item = $value[$filter];
//                } else {
//                    if (!$is_single_filter) {
//                        $item = null;
//                    }
//                }
//                if ($is_single_filter) {
//                    $result[] = $item;
//                } else {
//                    if (!array_key_exists($filter, $result)) {
//                        $result[$filter] = array();
//                    }
//                    $result[$filter][] = $item;
//                }
//            }
//        }
//        return $result;
//    }
//
//    /**
//     * JSON Suffixes
//     */
//    const JSON_DECODE_SUFFIX = '_jsondecoded';
//    const JSON_ENCODE_SUFFIX = '_jsonencoded';
//
//    /**
//     * Decoding JSON for indentified fields
//     *
//     * @param array &$data           Target data
//     * @param mixed $identifiers     String or list of index in target data
//     * @param suffix = null          Index suffix of newly created field, but null will overwrite.
//     * @param boolean $assoc = true  This is one of the arguments of json_decode()
//     * @param int $depth = 512       This is one of the arguments of json_decode()
//     */
//    public function decodeJsonFields(array &$data, $identifiers, $suffix = null, $assoc = true, $depth = 512)
//    {
//        foreach ($data as &$value) {
//            self::decodeJsonField($value, $identifiers, $suffix, $assoc, $depth);
//        }
//    }
//
//    /**
//     * Decoding JSON for indentified field
//     *
//     * @param array &$data           Target data
//     * @param mixed $identifiers     String or list of index in target data
//     * @param suffix = null          Index suffix of newly created field, but null will overwrite.
//     * @param boolean $assoc = true  This is one of the arguments of json_decode()
//     * @param int $depth = 512       This is one of the arguments of json_decode()
//     */
//    public function decodeJsonField(array &$data, $identifiers, $suffix = null, $assoc = true, $depth = 512)
//    {
//        if ($suffix !== null && strlen($suffix) <= 0) {
//            $suffix = self::JSON_DECODE_SUFFIX;
//        }
//        if (!is_array($identifiers)) {
//            $identifiers = array($identifiers);
//        }
//        foreach ($data as $label => $value) {
//            $new_label = $label . $suffix;
//            if (in_array($label, $identifiers)) {
//                $value = json_decode($value, $assoc, $depth);
//                if ($suffix === null) {
//                    $data[$label] = $value;
//                } else {
//                    $data[$new_label] = $value;
//                }
//            }
//        }
//    }
//
//    /**
//     * Encoding JSON for indentified fields
//     *
//     * @param array &$data        Target data
//     * @param mixed $identifiers  String or list of index in target data
//     * @param suffix = null       Index suffix of newly created field, but null will overwrite.
//     * @param int $options = 0    This is one of the arguments of json_encode()
//     */
//    public function encodeJsonFields(array &$data, $identifiers, $suffix = null, $options = 0)
//    {
//        foreach ($data as &$value) {
//            self::encodeJsonField($value, $identifiers, $suffix, $options);
//        }
//    }
//
//    /**
//     * Encoding JSON for indentified field
//     *
//     * @param array &$data        Target data
//     * @param mixed $identifiers  String or list of index in target data
//     * @param suffix = null       Index suffix of newly created field, but null will overwrite.
//     * @param int $options = 0    This is one of the arguments of json_encode()
//     */
//    public function encodeJsonField(array &$data, $identifiers, $suffix = null, $options = 0)
//    {
//        if ($suffix !== null && strlen($suffix) <= 0) {
//            $suffix = self::JSON_ENCODE_SUFFIX;
//        }
//        if (!is_array($identifiers)) {
//            $identifiers = array($identifiers);
//        }
//        foreach ($data as $label => $value) {
//            $new_label = $label . $suffix;
//            if (in_array($label, $identifiers)) {
//                $value = json_encode($value, $options);
//                if ($suffix === null) {
//                    $data[$label] = $value;
//                } else {
//                    $data[$new_label] = $value;
//                }
//            }
//        }
//    }
}
