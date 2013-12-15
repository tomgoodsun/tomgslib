<?php
/**
 * Separated Value Parser
 *
 * @package tomgslib
 * @subpackage libraries
 * @subpackage Data
 */

namespace com\tom_gs\www\libraries\Data;

abstract class SeparatedValuesParser
{
    /**
     * Content of data
     */
    private $contents;
    
    /**
     * Parsed data
     */
    private $parsed_data = array();
    
    /**
     * Cosutomized parsed data
     *
     * @param string $contents  Data
     */
    private $custom_parsed_data = array();

    public function __construct($contents)
    {
        $this->contents = $this->convertEncoding($contents);
        $this->init();
    }

    /**
     * Commence parsing process
     */
    private function init()
    {
        $this->parsed_data = $this->parse();
        $this->custom_parsed_data = $this->postParsingProcess($this->parsed_data);
    }

    /**
     * Commence parsing contents
     *
     * @return array Parsed data
     */
    private function parse()
    {
        $rows = $this->splitRows2Array($this->contents);
        $data = $this->splitRow2Array($rows);
        if (count($data) > 0) {
            return $data;
        }
        throw new \Exception('Parsing error.');
    }

    /**
     * Convert character encodings of contents
     * Nothing is done default
     *
     * @param string $contents  Data
     * @return string Encoding-converted contents
     */
    protected function convertEncoding($contents)
    {
        return $contents;
    }

    /**
     * Split to array by rows
     *
     * @param string $contents  Data
     * @return array  Splitted data
     */
    private function splitRows2Array($contents)
    {
        return explode($this->getLineFeedChar(), $contents);
    }

    /**
     * Split row to array
     *
     * @param array $contents  Array of rows
     * @return array  Splitted data
     */
    private function splitRow2Array(array $contents)
    {
        $result = array();
        $separating_char = $this->getSeparatingChar();
        foreach ($contents as $row) {
            $row = $this->cleanRow($row);
            $row_data = array();
            if (strlen($row) > 0) {
                $row_data = $this->cleanFields(explode($separating_char, $row));
                if (count($row_data) > 0) {
                    $result[] = $row_data;
                }
            }
        }
        return $result;
    }

    /**
     * Clean row string
     *
     * @param string $row  Row data
     * @return string  Cleaned data
     */
    protected function cleanRow($row)
    {
        return trim($row);
    }
    
    /**
     * Clean field string
     *
     * @param array $fields  Fields data
     * @return array  Cleaned fields
     */
    private function cleanFields(array $fields)
    {
        $is_effective = false;
        foreach ($fields as &$field) {
            $field = $this->cleanField($field);
            if (strlen($field) > 0) {
                $is_effective = true;
            }
        }
        if ($is_effective) {
            return $fields;
        }
        return array();
    }

    /**
     * Clean field string
     *
     * @param string $field  Fields data
     * @return string  Cleaned field
     */
    protected function cleanField($field)
    {
        return trim($field);
    }

    protected function postParsingProcess(array $data)
    {
        return $data;
    }

    /**
     * Getter - Contents
     *
     * @return string Row contents but encoded
     */
    public function getContents()
    {
        return $this->contents;
    }

    /**
     * Getter - Parsed contents
     *
     * @return array Simple separated data
     */
    public function getParsedData()
    {
        return $this->parsed_data;
    }

    /**
     * Getter - Custom parsed contents
     *
     * @return array Custom separated data
     */
    public function getCustomParsedData()
    {
        $this->custom_parsed_data;
    }

    abstract protected function getLineFeedChar();
    
    abstract protected function getSeparatingChar();
}
//class CommaSeparatedValuesParser extends SeparatedValuesParser
//{
//    protected function convertEncoding($contents)
//    {
//        return mb_convert_encoding('sjis-win', 'UTF-8', $contents);
//    }
//
//    protected function cleanRow($row)
//    {
//        $row = preg_replace('/^"/', '', $row);
//        $row = preg_replace('/"$/', '', $row);
//        $row = preg_replace('/","/', ',', $row);
//        return parent::cleanRow($row);
//    }
//
//    protected function getLineFeedChar()
//    {
//        return "\r";
//    }
//
//    protected function getSeparatingChar()
//    {
//        return ",";
//    }
//}
