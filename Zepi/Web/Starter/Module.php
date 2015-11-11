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
 * zepi Turbo Starter
 * 
 * @package Zepi\Web\Test
 * @author Matthias Zobrist <matthias.zobrist@zepi.net>
 * @copyright Copyright (c) 2015 zepi
 */

namespace Zepi\Web\Starter;

use \Zepi\Turbo\Module\ModuleAbstract;

/**
 * zepi Turbo Starter
 * 
 * @author Matthias Zobrist <matthias.zobrist@zepi.net>
 * @copyright Copyright (c) 2015 zepi
 */
class Module extends ModuleAbstract
{
    /**
     * Initializes and return an instance of the given class name.
     * 
     * @access public
     * @param string $className
     * @return mixed
     */
    public function getInstance($className)
    {
        
    }
    
    /**
     * Initializes the module
     * 
     * @access public
     */
    public function initialize()
    {
        $menuManager = $this->_framework->getInstance('\\Zepi\\Web\\General\\Manager\\MenuManager');
        
        $menuEntry = new \Zepi\Web\General\Entity\MenuEntry(
            'home',
            '',
            '/',
            'mdi-home'
        );
        $menuManager->addMenuEntry('menu-left', $menuEntry);
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
        // Add the event handler
        $eventManager = $this->_framework->getEventManager();
        $eventManager->addEventHandler('\\Zepi\\Web\\Starter\\Event\\Homepage', '\\Zepi\\Web\\Starter\\EventHandler\\Homepage');
        
        // Add the route
        $routeManager = $this->_framework->getRouteManager();
        $routeManager->addRoute('', '\\Zepi\\Web\\Starter\\Event\\Homepage');
        
        // Add the template
        $templatesManager = $this->_framework->getInstance('\\Zepi\\Web\\General\\Manager\\TemplatesManager');
        $templatesManager->addTemplate('\\Zepi\\Web\\Starter\\Templates\\Homepage', $this->_directory . '/templates/home.phtml');
    }
    
    /**
     * This action will be executed on the deactiviation of the module
     * 
     * @access public
     */
    public function deactivate()
    {
        // Add the event handler
        $eventManager = $this->_framework->getEventManager();
        $eventManager->removeEventHandler('\\Zepi\\Web\\Starter\\Event\\Homepage', '\\Zepi\\Web\\Starter\\EventHandler\\Homepage');
        
        // Add the route
        $routeManager = $this->_framework->getRouteManager();
        $routeManager->removeRoute('', '\\Zepi\\Web\\Starter\\Event\\Homepage');
        
        // Add the template
        $templatesManager = $this->_framework->getInstance('\\Zepi\\Web\\General\\Manager\\TemplatesManager');
        $templatesManager->removeTemplate('\\Zepi\\Web\\Starter\\Templates\\Homepage', $this->_directory . '/templates/home.phtml');
    }
}
