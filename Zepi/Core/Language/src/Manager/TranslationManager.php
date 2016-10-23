<?php
/*
 * The MIT License (MIT)
 *
 * Copyright (c) 2015 zepi
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 */

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
    protected $languageFileManager;
    
    /**
     * @access protected
     * @var \Zepi\Turbo\Request\RequestAbstract
     */
    protected $request;
    
    /**
     * @access protected
     * @var array
     */
    protected $translatedStrings = array();
    
    /**
     * @access protected
     * @var array
     */
    protected $loadableFiles = array();
    
    /**
     * Constructs the object.
     * 
     * @access public
     * @param \Zepi\Core\Language\Manager\LanguageFileManager $languageFileManager
     * @param \Zepi\Turbo\Request\RequestAbstract $request
     */
    public function __construct(LanguageFileManager $languageFileManager, RequestAbstract $request)
    {
        $this->languageFileManager = $languageFileManager;
        $this->request = $request;
    }
    
    /**
     * Returns the translated version of the given string. If no
     * translation is available, the function will return the given 
     * string back.
     * 
     * @access public
     * @param string $string
     * @param string $namespace
     * @param array $arguments
     * @return string
     */
    public function translate($string, $namespace, $arguments = array())
    {
        // If the domain not is loaded already load the translation file for the given namespace
        if (!isset($this->translatedStrings[$namespace]) || !is_array($this->translatedStrings[$namespace])) {
            $this->loadLanguageFileForNamespace($namespace);
        }
        
        // If no translation for the original string is available or the string is not translated 
        // return the original string back to the caller.
        if (!isset($this->translatedStrings[$namespace][$string]) || $this->translatedStrings[$namespace][$string] == '') {
            if (count($arguments) > 0) {
                $string = $this->replacePlaceholders($string, $arguments);
            }
            
            return $string;
        }
        
        $translatedString = $this->replacePlaceholders($this->translatedStrings[$namespace][$string], $arguments);
        
        if (count($arguments) > 0) {
            $translatedString = $this->replacePlaceholders($translatedString, $arguments);
        }
        
        return $translatedString;
    }
    
    /**
     * Replaces the placeholders in the string with the correct values
     * 
     * @access protected
     * @param string $string
     * @param array $arguments
     * @return string
     */
    protected function replacePlaceholders($string, $arguments)
    {
        foreach ($arguments as $key => $value) {
            $string = str_replace('%' . $key . '%', $value, $string);
        }
        
        return $string;
    }
    
    /**
     * Searches all language files which should be loaded for the 
     * requested locale.
     * 
     * @access protected
     * @param string $namespace
     */
    protected function loadLanguageFileForNamespace($namespace)
    {
        $loadedLocale = $this->request->getLocale();
        $content = $this->languageFileManager->loadTranslationFileContent($namespace, $loadedLocale);
        
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
            
            if (!isset($this->translatedStrings[$namespace]) || !is_array($this->translatedStrings[$namespace])) {
                $this->translatedStrings[$namespace] = array();
            }
            
            $this->translatedStrings[$namespace][$pattern] = $replacement;
        }
    }
}
