<?php
/**
 * Array Utility
 * 
 * @package tomgslib
 * @subpackage libraries
 * @subpackage Utility
 */

namespace com\tom_gs\www\libraries\Utility;

final class ArrayUtility
{
    /**
     * Make array indexed by specified indexed value
     * 
     * @param array $data                The 2-level array, 2nd level is must be the associated array.
     * @param string $index              Index name of associated array.
     * @param boolean $overwrite = true  Overwrite if the specified indexed value is duplicate
     * @return array  Grouped by $index array.
     */
    public static function reindex(array $data, $index, $overwrite = true)
    {
        $result = array();
        foreach ($data as $value) {
            $label = null;
            if (array_key_exists($index, $value)) {
                $label = $value[$index];
                if (array_key_exists($label, $result)) {
                    if ($overwrite) {
                        $result[$label] = $value;
                    }
                } else {
                    $result[$label] = $value;
                }
            }
        }
        return $result;
    }

    /**
     * Make array grouped by specified index
     * 
     * @param array $data     The 2-level array, 2nd level is must be the associated array.
     * @param mixed $indexes  Index name of associated array.
     * @return array  Grouped by $index array.
     */
    public static function groupBy(array $data, $indexes)
    {
        $result = array();
        if (!is_array($indexes)) {
            $indexes = array($indexes);
        }
        foreach ($data as $value) {
            self::subGroupBy($value, $data, $indexes, 0, $result);
        }
        return $result;
    }

    /**
     * Make sub group of specified index
     * 
     * @param mixed $value    Value of data
     * @param array $data     Target data
     * @param array $indexes  Array of grouping indexes
     * @param int $idx        Index number of indexes
     * @param array &$result  Result of grouped data
     */
    public static function subGroupBy($value, array $data, array $indexes, $idx, array &$result)
    {
        $filtered_data = array();
        if (!isset($indexes[$idx])) {
            $result = $data;
            return;
        }
        $index = $indexes[$idx];
        $label = $value[$index];
        if (!array_key_exists($label, $result)) {
            $result[$label] = array();
        }
        foreach ($data as $item) {
            if (array_key_exists($index, $item) && $item[$index] == $label) {
                $filtered_data[] = $item;
            }
        }
        self::subGroupBy($value, $filtered_data, $indexes, $idx + 1, $result[$label]);
    }

    /**
     * Extract values identified by filters
     * If string or integer is given in filter, result must be a single array.
     * If array given, result must be a mutiple array.
     * If no pointer in 2nd level, that'll be set null.
     * 
     * @param array $data    The 2-level array, 2nd level is must be the associated array.
     * @param mixed $filter  Index name of associated array.
     * @return array  Extracted data.
     */
    public static function extractBy(array $data, $filters)
    {
        $result = array();
        $is_single_filter = false;
        if (is_scalar($filters)) {
            $filters = array($filters);
            $is_single_filter = true;
        }
        if (count($filters) <= 0) {
            return $result;
        }
        foreach ($data as $value) {
            foreach ($filters as $filter) {
                $item = array();
                if (array_key_exists($filter, $value)) {
                    $item = $value[$filter];
                } else {
                    if (!$is_single_filter) {
                        $item = null;
                    }
                }
                if ($is_single_filter) {
                    $result[] = $item;
                } else {
                    if (!array_key_exists($filter, $result)) {
                        $result[$filter] = array();
                    }
                    $result[$filter][] = $item;
                }
            }
        }
        return $result;
    }

    /**
     * JSON Suffixes
     */
    const JSON_DECODE_SUFFIX = '_jsondecoded';
    const JSON_ENCODE_SUFFIX = '_jsonencoded';

    /**
     * Decoding JSON for indentified fields
     *
     * @param array &$data           Target data
     * @param mixed $identifiers     String or list of index in target data
     * @param suffix = null          Index suffix of newly created field, but null will overwrite.
     * @param boolean $assoc = true  This is one of the arguments of json_decode()
     * @param int $depth = 512       This is one of the arguments of json_decode()
     */
    public static function decodeJsonFields(array &$data, $identifiers, $suffix = null, $assoc = true, $depth = 512)
    {
        foreach ($data as &$value) {
            self::decodeJsonField($value, $identifiers, $suffix, $assoc, $depth);
        }
    }

    /**
     * Decoding JSON for indentified field
     *
     * @param array &$data           Target data
     * @param mixed $identifiers     String or list of index in target data
     * @param suffix = null          Index suffix of newly created field, but null will overwrite.
     * @param boolean $assoc = true  This is one of the arguments of json_decode()
     * @param int $depth = 512       This is one of the arguments of json_decode()
     */
    public static function decodeJsonField(array &$data, $identifiers, $suffix = null, $assoc = true, $depth = 512)
    {
        if ($suffix !== null && strlen($suffix) <= 0) {
            $suffix = self::JSON_DECODE_SUFFIX;
        }
        if (!is_array($identifiers)) {
            $identifiers = array($identifiers);
        }
        foreach ($data as $label => $value) {
            $new_label = $label . $suffix;
            if (in_array($label, $identifiers)) {
                $value = json_decode($value, $assoc, $depth);
                if ($suffix === null) {
                    $data[$label] = $value;
                } else {
                    $data[$new_label] = $value;
                }
            }
        }
    }

    /**
     * Encoding JSON for indentified fields
     *
     * @param array &$data        Target data
     * @param mixed $identifiers  String or list of index in target data
     * @param suffix = null       Index suffix of newly created field, but null will overwrite.
     * @param int $options = 0    This is one of the arguments of json_encode()
     */
    public static function encodeJsonFields(array &$data, $identifiers, $suffix = null, $options = 0)
    {
        foreach ($data as &$value) {
            self::encodeJsonField($value, $identifiers, $suffix, $options);
        }
    }

    /**
     * Encoding JSON for indentified field
     *
     * @param array &$data        Target data
     * @param mixed $identifiers  String or list of index in target data
     * @param suffix = null       Index suffix of newly created field, but null will overwrite.
     * @param int $options = 0    This is one of the arguments of json_encode()
     */
    public static function encodeJsonField(array &$data, $identifiers, $suffix = null, $options = 0)
    {
        if ($suffix !== null && strlen($suffix) <= 0) {
            $suffix = self::JSON_ENCODE_SUFFIX;
        }
        if (!is_array($identifiers)) {
            $identifiers = array($identifiers);
        }
        foreach ($data as $label => $value) {
            $new_label = $label . $suffix;
            if (in_array($label, $identifiers)) {
                $value = json_encode($value, $options);
                if ($suffix === null) {
                    $data[$label] = $value;
                } else {
                    $data[$new_label] = $value;
                }
            }
        }
    }
}
