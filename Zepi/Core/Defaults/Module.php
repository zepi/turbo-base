<?php
/**
 * The defaults module delivers the defaults for zepi Turbo.
 * 
 * @package Zepi\Core\Defaults
 * @author Matthias Zobrist <matthias.zobrist@zepi.net>
 * @copyright Copyright (c) 2015 zepi
 */

namespace Zepi\Core\Defaults;

use \Zepi\Turbo\Module\ModuleAbstract;

/**
 * The defaults module delivers the defaults for zepi Turbo.
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
        $eventManager->addEventHandler('\\Zepi\\Turbo\\Event\\FinalizeOutput', '\\Zepi\\Core\\Defaults\\EventHandler\\DefaultOutputRenderer', 9999);
        $eventManager->addEventHandler('\\Zepi\\Turbo\\Event\\RouteNotFound', '\\Zepi\\Core\\Defaults\\EventHandler\\DefaultRouteNotFound', 9999);
    }
    
    /**
     * This action will be executed on the deactiviation of the module
     * 
     * @access public
     */
    public function deactivate()
    {
        $eventManager = $this->_framework->getEventManager();
        $eventManager->removeEventHandler('\\Zepi\\Turbo\\Event\\FinalizeOutput', '\\Zepi\\Core\\Defaults\\EventHandler\\DefaultOutputRenderer', 9999);
        $eventManager->removeEventHandler('\\Zepi\\Turbo\\Event\\RouteNotFound', '\\Zepi\\Core\\Defaults\\EventHandler\\DefaultRouteNotFound', 9999);
    }
}
