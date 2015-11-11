<?php
/**
 * This module delivers some utils for the framework core.
 * 
 * @package Zepi\Core\Utils
 * @author Matthias Zobrist <matthias.zobrist@zepi.net>
 * @copyright Copyright (c) 2015 zepi
 */

namespace Zepi\Core\Utils;

use \Zepi\Turbo\Module\ModuleAbstract;

/**
 * This module delivers some utils for the framework core.
 * 
 * @author Matthias Zobrist <matthias.zobrist@zepi.net>
 * @copyright Copyright (c) 2015 zepi
 */
class Module extends ModuleAbstract
{
    /**
     * @access protected
     * @var \Zepi\Core\Utils\Manager\ConfigurationManager
     */
    protected $_configurationManager;
    
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
            case '\\Zepi\\Core\\Utils\\Manager\\ConfigurationManager':
                if ($this->_configurationManager === null) {
                    $path = $this->_framework->getFrameworkDirectory() . '/config/framework.ini';
                    $configFileBackend = new \Zepi\Core\Utils\Backend\ConfigurationFileBackend($path);
                    
                    $this->_configurationManager = new $className($configFileBackend);
                    $this->_configurationManager->loadConfigurationFile();
                }
                
                return $this->_configurationManager;
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
