<?php
/**
 * CustomException.php
 * 
 * This all exception using this liblaries must extends this exception class
 * as base class when create other exception classes
 */

namespace com\tom_gs\www\libraries\Exception;

abstract class CustomException extends \Exception
{
    /**
     * Original Message Formats
     */
    private $promptFormat   = '[%s: %s on line %d] ';
    private $stringFormat   = '%s';
    private $styledFormat   = '<span style="%s">%s</span> <span style="%s">%s</span><br />';
    private $viewAreaFormat = '<div style="%s">%s</div>';

    /**
     * CSS for Original Message
     */
    private $msg_PromptStyle = 'color: #666; font-weight: bold;';
    private $msg_StringStyle = 'color: #C33;';
    private $styledMessage = 'Unknown Exception';

    /**
     * CSS for result string of getTraceAsString()
     */
    private $tas_NumberStyle = 'color: #666; font-weight: bold;';
    private $tas_PathStyle   = 'color: #C33;';
    private $tas_TraceStyle  = 'color: #369;';
    private $tas_OtherStyle  = 'color: #666;';

    /**
     * Sub class must be gotten that name
     */
    abstract protected function getClassName();

    /**
     * Create and set message to member variables
     * 
     * @param $message  The exception message
     * @return String 
     */
    protected function createMessage($message)
    {
        $prompt = sprintf($this->promptFormat, $this->getClassName(), $this->getFile(), $this->getLine());
        $string = sprintf($this->stringFormat, $message);
        $message = $prompt . $string;
        $this->styledMessage =
            sprintf($this->styledFormat, $this->msg_PromptStyle, $prompt, $this->msg_StringStyle, $string);
        return $message;
    }

    /**
     * Get styled string of getTraceAsString()
     * 
     * @return String 
     */
    private function getStyledTraceAsString()
    {
        $string = '';
        $lines = split("\n", $this->getTraceAsString());
        foreach ($lines as $value) {
            $str = $value;
            $str = preg_replace('/:\s{1,}(.*)$/', ': <span style="' . $this->tas_TraceStyle . ';">$1</span>', $str);
            $str = preg_replace('/\s{1,}(\/.*)$/', ' <span style="' . $this->tas_PathStyle . ';">$1</span>', $str);
            $str = preg_replace('/^(#\d{1,})/', '<span style="' . $this->tas_NumberStyle . ';">$1</span>', $str);
            $str = preg_replace('/(\{.*\})/', '<span style="' . $this->tas_OtherStyle . ';">$1</span>', $str);
            $string .= $str . '<br />' . "\n";
        }
        return $string;
    }

    /**
     * Get styled string of message
     * 
     * @access    private
     */
    private function getStyledMessage()
    {
        return $this->styledMessage;
    }

    /**
     * Write all strings privately kept in Exception class
     * 
     * @access    private
     */
    final public function printRawAll()
    {
        $message = $this->getTraceAsString() . $this->message;
        echo $message;
    }

    /**
     * Write all strings with CSS
     * 
     * @access    private
     */
    final public function printAll()
    {
        $viewAreaStyle = 'background: #FFF;';
        $viewAreaStyle .= 'border: 1px solid #666;';
        $viewAreaStyle .= 'font-size: 12px;';
        $viewAreaStyle .= 'font-family: Courier New;';
        $viewAreaStyle .= 'margin-bottom: 1em;';
        $message = $this->getStyledTraceAsString() . $this->getStyledMessage();
        echo sprintf($this->viewAreaFormat, $viewAreaStyle, $message) . "\n";
    }
}
