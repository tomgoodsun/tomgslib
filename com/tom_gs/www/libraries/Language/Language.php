<?php
/**
 * Langunage
 * 
 * @package libraries
 * @subpackage Language
 * @author Tom Higuchi
 * @copyright tom-gs.com(http://www.tom-gs.com)
 */

namespace com\tom_gs\www\libraries\Language;

class Language
{
    private $default_locale = 'en-US';
    private $locale;
    private $language_root_dir;
    private $content_name;
    private $extension;
    private $messages = array();

    /**
     * Language class constructor
     * 
     * @param string $locale             The location string of region
     * @param string $language_root_dir  The path to root directory of language files
     * @param string $content_name       The name of current showing content
     * @param string $extension          The extension of file to use for language file without '.(dot)'
     * @return Language  Instance of Language class
     */
    private function __construct($locale, $language_root_dir, $content_name, $extension)
    {
        $this->locale = $locale;
        $this->language_root_dir = $language_root_dir;
        $this->content_name = $content_name;
        $this->extension = '.' . $extension;
        $this->initializeLanguage();
    }

    /**
     * The instance of this class must be used in singleton
     * 
     * @param string $locale             The location string of region
     * @param string $language_root_dir  The path to root directory of language files
     * @param string $content_name       The name of current showing content
     * @param string $extension          The extension of file to use for language file without '.(dot)'
     */
    public function getInstance(
        $locale = 'en-US',
        $language_root_dir = 'languages',
        $content_name = 'index',
        $extension = 'ini'
    ) {
        static $instance;
        if ($instance === null) {
            $class_name = __CLASS__;
            $instance = new $class_name($locale, $language_root_dir . DS, $content_name, $extension);
        }
        return $instance;
    }

    /**
     * Initialize language from language file
     */
    private function initializeLanguage()
    {
        $language_root_dir = $this->language_root_dir . $this->locale . DS;
        if (!file_exists($language_root_dir)) {
            $language_root_dir = $this->language_root_dir . $this->default_locale . DS;
            if (!file_exists($language_root_dir)) {
                $err_msg = $this->sprintf(
                    'Language locale directory \'%s\' does not exist.',
                    $language_root_dir
                );
                throw new LanguageException($err_msg);
            }
        }
        $this->addLanguageFile($this->language_root_dir, $this->content_name);
    }

    /**
     * Get language string value from private array
     * 
     * @param string $label  The key of private array or the language message name
     * @return String  Text
     */
    private function _($label)
    {
        if (array_key_exists($label, $this->messages)) {
            return $this->messages[$label];
        }
        return $label;
    }

    /**
     * Initialize arguments native PHP functions like printf or sprintf
     * 
     * @param array $args  This must be array returning from native PHP function func_get_args()
     * @return array  Arguments
     */
    private function initPrintfArgs(array $args)
    {
        if (count($args) > 0) {
            $args[0] = $this->_($args[0]);
        }
        return $args;
    }

    /**
     * Add language file
     * 
     * @param string $language_root_dir  The path to root directory of language files
     * @param string $content_name       The name of current showing content
     */
    public function addLanguageFile($language_dir, $content_name)
    {
        $adding_language_dir = $language_dir . $this->locale . DS;
        if (!file_exists($adding_language_dir)) {
            $adding_language_dir = $language_dir . $this->default_locale . DS;
            if (!file_exists($adding_language_dir)) {
                //$err_msg = $this->sprintf(
                //    'Language locale directory \'%s\' does not exist.',
                //    $language_dir
                //);
                //throw new LanguageException($err_msg);
                return;
            }
        }
        $language_file = $adding_language_dir . $content_name . $this->extension;
        $this->addMessages($language_file);
    }

    /**
     * Add language file
     * 
     * @param string $language_file  The path to root directory of language files
     */
    public function addMessages($language_file)
    {
        if (!file_exists($language_file)) {
            return;
        }
        $this->messages += parse_ini_file($language_file);
    }

    /**
     * Optionally adding language messages to private array
     * 
     * @param string $label    The index key of associated private array
     * @param string $message  The string value to set into $label key of private array
     * @param string $mode     Available adding mode is below:
     *     [a, append]
     *         Joins $message after the existed string of $label key
     *     [i, insert]
     *         Joins $message before the existed string of $label key
     *     [o, overwrite]
     *         Change the existed string of $label key to $message
     *     [d, default]
     *         Default do nothing or create new key named $label and set $message into it
     */
    public function addMessage($label, $message, $mode = 'default')
    {
        if (!array_key_exists($label, $this->messages)) {
            $this->messages[$label] = $message;
            return;
        }
        $this->addMessageByMode($label, $message, $mode);
        return;
    }

    /**
     * Optionally adding language messages to private array by mode
     * 
     * @param string $label    The index key of associated private array
     * @param string $message  The string value to set into $label key of private array
     * @param string $mode     Available adding mode is below:
     *     [a, append]
     *         Joins $message after the existed string of $label key
     *     [i, insert]
     *         Joins $message before the existed string of $label key
     *     [o, overwrite]
     *         Change the existed string of $label key to $message
     *     [d, default]
     *         Default do nothing or create new key named $label and set $message into it
     */
    public function addMessageByMode($label, $message, $mode)
    {
        switch ($mode) {
            case 'a':
            case 'append':
                $this->messages[$label] = $this->messages[$label] . $message;
                break;
            case 'i':
            case 'insert':
                $this->messages[$label] = $message . $this->messages[$label];
                break;
            case 'o':
            case 'overwrite':
                $this->messages[$label] = $message;
                break;
            case 'd':
            case 'default':
            default:
                break;
        }
        return;
    }

    /**
     * Returns private array
     * 
     * @return array  Text list
     */
    public function getAllTexts()
    {
        return $this->messages;
    }

    /**
     * Get string from private array
     * 
     * @param string $label  The key of private array or the language message name
     * @return String  Text
     */
    public function getText($label)
    {
        return $this->_($label);
    }

    /**
     * Private implementation of native PHP function sprintf()
     * 
     * @return String  Text
     */
    public function sprintf()
    {
        $args = func_get_args();
        return call_user_func_array('sprintf', $this->initPrintfArgs($args));
    }

    /**
     * Private implementation of native PHP function printf()
     * 
     * @return String  Text
     */
    public function printf()
    {
        $args = func_get_args();
        return call_user_func_array('printf', $this->initPrintfArgs($args));
    }
}
