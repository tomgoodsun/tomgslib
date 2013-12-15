<?php
/**
 * SqlField.php
 * 
 * This class for fields of result of query
 */
class DBTableField {
    private $data      = null;
    private $fieldName = null;
    private $length    = 0;
    private $type      = null;
    private $flags     = null;

    public function __construct($data, $fieldName, $length, $type, $flags) {
        $this->data      = $data;
        $this->fieldName = $fieldName;
        $this->length    = $length;
        $this->type      = $type;
        $this->flags     = $flags;
    }
    
    public function getLength() {
        return $this->length;
    }
    
    public function getType() {
        return $this->type;
    }

    public function getData() {
        return $this->data;
    }
    
    public function getFieldName() {
        return $this->fieldName;
    }

    public function getFlags() {
        return $this->flags;
    }
}
