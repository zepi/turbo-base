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
