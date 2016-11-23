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

use \Zepi\Turbo\Framework;
use \Zepi\Turbo\Backend\ObjectBackendAbstract;
use \Zepi\Core\Language\Exception;

/**
 * Manages the language files for the language module.
 * 
 * @author Matthias Zobrist <matthias.zobrist@zepi.net>
 * @copyright Copyright (c) 2015 zepi
 */
class LanguageFileManager
{
    /**
     * @access protected
     * @var \Zepi\Turbo\Framework
     */
    protected $framework;
    
    /**
     * @access protected
     * @var array
     */
    protected $languageFiles = array();
    
    /**
     * Constructs the object.
     * 
     * @access public
     * @param \Zepi\Turbo\Framework $framework
     */
    public function __construct(Framework $framework)
    {
        $this->framework = $framework;
    }
    
    /**
     * Returns the content of the translation file if a translation file was found.
     * Otherwise returns false.
     * 
     * @access public
     * @param string $namespace
     * @param string $specificLocale
     * @return string
     */
    public function loadTranslationFileContent($namespace, $specificLocale)
    {
        $path = $this->buildPathFromNamespace($namespace);
        if ($path === false) {
            return false;
        }
        
        $globalLocale = substr($specificLocale, 0, strpos($specificLocale, '_'));
        
        // If the extracted part of the specific locale is empty return false because
        // we didn't received a full locale.
        if ($globalLocale == '') {
            return false;
        }
        
        // Define the file paths to the translation files
        $specificFile = $path . '/languages/' . $specificLocale . '.zttf';
        $globalFile = $path . '/languages/' . $globalLocale . '.zttf';
        
        // Load the file content
        $content = $this->loadFileContent($globalFile, $specificFile);
        
        return $content;
    }

    /**
     * Loads the content of the translation file. If the specific translation 
     * file exists for the locale (in example de_DE.zttf) load the specific translation 
     * file. Otherwise try to load the the global translation file.
     * 
     * @param string $globalFile
     * @param string $specificFile
     * @return string
     */
    protected function loadFileContent($globalFile, $specificFile)
    {
        $content = '';
        
        if (file_exists($specificFile) && is_readable($specificFile)) {
            $content = file_get_contents($specificFile);
        } else if (file_exists($globalFile) && is_readable($globalFile)) {
            $content = file_get_contents($globalFile);
        }
        
        return $content;
    }
    
    /**
     * Returns the directory for the given module namespace
     * 
     * @access protected
     * @param string $namespace
     * @return false|string
     */
    protected function buildPathFromNamespace($namespace)
    {
        // Prepare the namespace
        $namespace = Framework::prepareNamespace($namespace);
        
        // Load the module
        $moduleManager = $this->framework->getModuleManager();
        $module = $moduleManager->getModule($namespace);
        if ($module === false) {
            return false;
        }
        
        return $module->getDirectory();
    }
}
