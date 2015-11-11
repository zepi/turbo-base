<?php
/**
 * This module delivers the language core for zepi Turbo.
 * 
 * @package Zepi\Core\Language
 * @author Matthias Zobrist <matthias.zobrist@zepi.net>
 * @copyright Copyright (c) 2015 zepi
 */

namespace Zepi\Core\Language;

use \Zepi\Turbo\Module\ModuleAbstract;

/**
 * This module delivers the language core for zepi Turbo.
 * 
 * @author Matthias Zobrist <matthias.zobrist@zepi.net>
 * @copyright Copyright (c) 2015 zepi
 */
class Module extends ModuleAbstract
{
    /**
     * @access protected
     * @var \Zepi\Core\Language\Manager\ConfigurationManager
     */
    protected $_languageFileManager;
    
    /**
     * @access protected
     * @var \Zepi\Core\Language\Manager\TranslationManager
     */
    protected $_translationManager;
    
    /**
     * Initializes and return an instance of the given class name.
     * 
     * @access public
     * @param string $className
     * @return mixed
     */
    public function getInstance($className)
    {
        switch ($className) {
            case '\\Zepi\\Core\\Language\\Manager\\LanguageFileManager':
                if ($this->_languageFileManager === null) {
                    $this->_languageFileManager = new $className($this->_framework);
                }
                
                return $this->_languageFileManager;
            break;
            
            case '\\Zepi\\Core\\Language\\Manager\\TranslationManager':
                if ($this->_translationManager === null) {
                    $languageFileManager = $this->getInstance('\\Zepi\\Core\\Language\\Manager\\LanguageFileManager');
                    
                    $this->_translationManager = new $className(
                        $languageFileManager,
                        $this->_framework->getRequest()
                    );
                }
                
                return $this->_translationManager;
            break;
            
            default: 
                return new $className();
            break;
        }
    }
    
    /**
     * This action will be executed on the activation of the module
     * 
     * @access public
     * @param string $versionNumber
     * @param string $oldVersionNumber
     */
    public function activate($versionNumber, $oldVersionNumber = '')
    {
        
    }
    
    /**
     * This action will be executed on the deactiviation of the module
     * 
     * @access public
     */
    public function deactivate()
    {
        
    }
}
