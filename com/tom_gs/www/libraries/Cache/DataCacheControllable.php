<?php
/**
 * Interface DataCacheControllable
 * This interface guarantees these methods are available.
 *
 * @date 2012.03.26
 * @package tomgslib
 * @subpackage libraries
 * @subpackage Cache
 * @copyright tom-gs.com
 * @author tomgoodsun@gmail.com
 */

namespace com\tom_gs\www\libraries\Cache;

interface DataCacheControllable
{
    /**
     * Get value
     *
     * @param $key  Key specifying value
     * @return mixed
     */
    public function get($key);

    /**
     * Set value
     *
     * @param $key    Key specifying value
     * @param $value  Value
     * @param $life   Life that the value will have been kept in second
     */
    public function set($key, $value, $life = null);

    /**
     * Check existence
     *
     * @param $key  Key specifying value
     * @return boolean TRUE:exists / FALSE:doesn't exist
     */
    public function exists($key);

    /**
     * Delete value
     *
     * @param $key  Key specifying value
     */
    public function delete($label);
}
