<?php

class DbSqlJoinTable {
    /**
     * DbSqlTable object
     */
    private $table = null;

    /**
     * Join-mode
     */
    private $mode = null;

    /**
     * Join key column defs
     */
    private $keys = array();

    /**
     * Constructor
     *
     * @param DbSqlTable $table   DbSqlTable object
     * @param $mode               Join-mode
     * @param $key                Key column name
     * @param $target_key = NULL  Key column name of target table
     */
    public function __construct(DbSqlTable $table, $mode, $key, $target_key = NULL) {
        $this->table = $table;
        $this->mode = $mode;
        $this->key = $key;
        $this->target_key = $target_key;
        $this->addKey($key, $target_key);
    }

    /**
     * Add key column name
     *
     * @param $key                Key column name
     * @param $target_key = NULL  Key column name of target table
     */
    public function addKey($key, $target_key = NULL) {
        if (is_null($target_key)) {
            $target_key = $key;
        }
        $this->keys[] = array('key' => $key, 'target_key' => $target_key);
    }

    /**
     * Get DbSqlTable object
     *
     * @return DbSqlTable
     */
    public function getTable() {
        return $this->table;
    }

    /**
     * Get join-mode
     *
     * @return string  Join-mode
     */
    public function getMode() {
        return $this->mode;
    }

    public function createJoinConditionStatement($target_alias = NULL) {
        $key_list = array();
        for($i = 0; $i < count($this->keys); $i++) {

            $left = $this->table->getAlias() . DbSqlTable::COLUMN_ALIAS_OPERATER . $this->keys[$i]['key'];

            $right = '';
            if (isset($target_alias)) {
                $right .= $target_alias . DbSqlTable::COLUMN_ALIAS_OPERATER;
            }
            $right .= $this->keys[$i]['target_key'];
            $key_list[] = sprintf('%s = %s', $left, $right);
        }
        return join(' AND ', $key_list);
    }

//    public function getKey($with_alias = true) {
//        $alias = '';
//        if ($with_alias && !is_null($this->getTable()->getAlias())) {
//            $alias .= $this->table->getAlias() . DbSqlTable::COLUMN_ALIAS_OPERATER;
//        }
//        return $alias . $this->key;
//    }
//
//    public function getTargetKey($target_alias = null) {
//        $key = '';
//        if (isset($this->target_key)) {
//            $key = $this->target_key;
//        } else {
//            $key = $this->key;
//        }
//        $alias = '';
//        if (isset($target_alias)) {
//            $alias .= $target_alias . DbSqlTable::COLUMN_ALIAS_OPERATER;
//        }
//        return $alias . $key;
//    }
}
