<?php
/**
 * Request Utility
 * 
 * @package tomgslib
 * @subpackage libraries
 * @subpackage Utility
 */

namespace com\tom_gs\www\libraries\Utility;

final class RequestUtility
{
    /**
     * Add query parameters to URL
     *
     * @param string $url        URL to be added query parameters
     * @param array $parameters  Data for query parameter
     * @return string Query parameter string
     */
    public static function addQueryParameters($url, array $parameters)
    {
        $url .= (!strpos($url, '?') ? '?' : '&');
        return $url . self::createQueryParameters($parameters);
    }

    /**
     * Create query parameters from array
     *
     * @param array $parameters  Data for query parameter
     * @return string Query parameter string
     */
    public static function createQueryParameters(array $parameters)
    {
        $param_set = array();
        foreach ($parameters as $index => $value) {
            $index = urlencode($index);
            if (is_array($value)) {
                self::createParameters($index, $value, $param_set);
            } else {
                $param_set[] = sprintf('%s=%s', $index, urlencode($value));
            }
        }
        return implode($param_set, '&');
    }

    /**
     * Create sub query parameters
     *
     * @param string $parent_key_name  Key name of parent
     * @param array $parameters        Data for query parameter
     * @param array &$param_set        Parameter result set
     */
    private static function createParameters($parent_key_name, array $parameters, array &$param_set)
    {
        foreach ($parameters as $index => $value) {
            if (is_array($value)) {
                $name = sprintf('%s[%s]', $parent_key_name, urlencode($index));
                self::createParameters($name, $value, $param_set);
            } else {
                // To make the clean format, rename the array index
                $label = preg_match('/^[0-9]{1,}$/', $index) ? '' : urlencode($index);
                $name = sprintf('%s[%s]', $parent_key_name, $label);
                $param_set[] = sprintf('%s=%s', $name, urlencode($value));
            }
        }
    }
}
