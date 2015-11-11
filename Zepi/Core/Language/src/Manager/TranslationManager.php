<?php
/**
 * Manages the language files for the language module.
 * 
 * @package Zepi\Core\Language
 * @subpackage Manager
 * @author Matthias Zobrist <matthias.zobrist@zepi.net>
 * @copyright Copyright (c) 2015 zepi
 */

namespace Zepi\Core\Language\Manager;

use \Zepi\Turbo\Request\RequestAbstract;

/**
 * Manages the language files for the language module.
 * 
 * @author Matthias Zobrist <matthias.zobrist@zepi.net>
 * @copyright Copyright (c) 2015 zepi
 */
class TranslationManager
{
    /**
     * @access protected
     * @var \Zepi\Core\Language\Manager\LanguageFileManager
     */
    protected $_languageFileManager;
    
    /**
     * @access protected
     * @var \Zepi\Turbo\Request\RequestAbstract
     */
    protected $_request;
    
    /**
     * @access protected
     * @var array
     */
    protected $_translatedStrings = array();
    
    /**
     * @access protected
     * @var array
     */
    protected $_loadableFiles = array();
    
    /**
     * Constructs the object.
     * 
     * @access public
     * @param \Zepi\Core\Language\Manager\LanguageFileManager $languageFileManager
     * @param \Zepi\Turbo\Request\RequestAbstract $request
     */
    public function __construct(LanguageFileManager $languageFileManager, RequestAbstract $request)
    {
        $this->_languageFileManager = $languageFileManager;
        $this->_request = $request;
    }
    
    /**
     * Returns the translated version of the given string. If no
     * translation is available, the function will return the given 
     * string back.
     * 
     * @access public
     * @param string $string
     * @param string $namespace
     * @return string
     */
    public function translate($string, $namespace)
    {
        // If the domain not is loaded already load the translation file for the given namespace
        if (!isset($this->_translatedStrings[$namespace]) || !is_array($this->_translatedStrings[$namespace])) {
            $this->_loadLanguageFileForNamespace($namespace);
        }
        
        // If no translation for the original string is available or the string is not translated 
        // return the original string back to the caller.
        if (!isset($this->_translatedStrings[$namespace][$string]) || $this->_translatedStrings[$namespace][$string] == '') {
            return $string;
        }
        
        return $this->_translatedStrings[$namespace][$string];
    }
    
    /**
     * Searches all language files which should be loaded for the 
     * requested locale.
     * 
     * @access protected
     * @param string $namespace
     */
    protected function _loadLanguageFileForNamespace($namespace)
    {
        $loadedLocale = $this->_request->getLocale();
        $content = $this->_languageFileManager->loadTranslationFileContent($namespace, $loadedLocale);
        
        // If the received content is empty return false
        if ($content === false) {
            return false;
        }
        
        $lines = explode(PHP_EOL, $content);
        
        foreach ($lines as $line) {
            $delimiter = strpos($line, ' = ');
            if ($delimiter === false) {
                continue;
            }
            
            $pattern = substr($line, 0, $delimiter);
            $replacement = substr($line, $delimiter + 3);
            
            if (!isset($this->_translatedStrings[$namespace]) || !is_array($this->_translatedStrings[$namespace])) {
                $this->_translatedStrings[$namespace] = array();
            }
            
            $this->_translatedStrings[$namespace][$pattern] = $replacement;
        }
    }
}
