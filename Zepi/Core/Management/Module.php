<?php
/**
 * The Management module delivers some additional features for the framework.
 * These features are not system cirtical.
 * 
 * @package Zepi\Core\Management
 * @author Matthias Zobrist <matthias.zobrist@zepi.net>
 * @copyright Copyright (c) 2015 zepi
 */

namespace Zepi\Core\Management;

use \Zepi\Turbo\Module\ModuleAbstract;

/**
 * The Management module delivers some additional features for the framework.
 * These features are not system cirtical.
 * 
 * @author Matthias Zobrist <matthias.zobrist@zepi.net>
 * @copyright Copyright (c) 2015 zepi
 */
class Module extends ModuleAbstract
{
    /**
     * This action will be executed on the activation of the module
     * 
     * @access public
     * @param string $versionNumber
     * @param string $oldVersionNumber
     */
    public function activate($versionNumber, $oldVersionNumber = '')
    {
        $eventManager = $this->_framework->getEventManager();
        $eventManager->addEventHandler('\\Zepi\\Core\\Management\\Event\\RebuildFrameworkCache', '\\Zepi\\Core\\Management\\EventHandler\\RebuildFrameworkCache');
        $eventManager->addEventHandler('\\Zepi\\Core\\Management\\Event\\ListModules', '\\Zepi\\Core\\Management\\EventHandler\\ListModules');
        
        $routeManager = $this->_framework->getRouteManager();
        $routeManager->addRoute('core|rebuildFrameworkCache', '\\Zepi\\Core\\Management\\Event\\RebuildFrameworkCache');
        $routeManager->addRoute('core|listModules', '\\Zepi\\Core\\Management\\Event\\ListModules');
    }
    
    /**
     * This action will be executed on the deactiviation of the module
     * 
     * @access public
     */
    public function deactivate()
    {
        $eventManager = $this->_framework->getEventManager();
        $eventManager->removeEventHandler('\\Zepi\\Core\\Management\\Event\\RebuildFrameworkCache', '\\Zepi\\Core\\Management\\EventHandler\\RebuildFrameworkCache');
        $eventManager->removeEventHandler('\\Zepi\\Core\\Management\\Event\\ListModules', '\\Zepi\\Core\\Management\\EventHandler\\ListModules');
        
        $routeManager = $this->_framework->getRouteManager();
        $routeManager->removeRoute('core|rebuildFrameworkCache', '\\Zepi\\Core\\Management\\Event\\RebuildFrameworkCache');
        $routeManager->removeRoute('core|listModules', '\\Zepi\\Core\\Management\\Event\\ListModules');
    }
}
