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
     * @var array
     */
    protected $_languageFiles = array();
    
    /**
     * Constructs the object.
     * 
     * @access public
     * @param \Zepi\Turbo\Framework $framework
     */
    public function __construct(Framework $framework)
    {
        $this->_framework = $framework;
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
        $path = $this->_buildPathFromNamespace($namespace);
        if ($path === false) {
            return false;
        }
        
        $content = '';
        
        $globalLocale = substr($specificLocale, 0, strpos($specificLocale, '_'));
        
        // If the extracted part of the specific locale is empty return false because
        // we didn't received a full locale.
        if ($globalLocale == '') {
            return false;
        }
        
        // Define the file paths to the translation files
        $specificFile = $path . '/languages/' . $specificLocale . '.zttf';
        $globalFile = $path . '/languages/' . $globalLocale . '.zttf';
        
        // If the specific translation file exists for the locale (in example de_DE.zttf) load
        // the specific translation file. Otherwise try to load the the global translation file.
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
    protected function _buildPathFromNamespace($namespace)
    {
        // Prepare the namespace
        $namespace = Framework::prepareNamespace($namespace);
        
        // Load the module
        $moduleManager = $this->_framework->getModuleManager();
        $module = $moduleManager->getModuleByNamespace($namespace);
        if ($module === false) {
            return false;
        }
        
        return $module->getDirectory();
    }
}
