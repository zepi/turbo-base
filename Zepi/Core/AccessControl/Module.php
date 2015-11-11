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
 * This module controls the access to the routes. Every 
 * object (i.e. user, group, api key) has a UUID and the 
 * access is granted to the UUID.
 * 
 * @package Zepi\Core\AccessControl
 * @author Matthias Zobrist <matthias.zobrist@zepi.net>
 * @copyright Copyright (c) 2015 zepi
 */

namespace Zepi\Core\AccessControl;

use \Zepi\Turbo\Module\ModuleAbstract;

/**
 * This module controls the access to the routes. Every 
 * object (i.e. user, group, api key) has a UUID and the 
 * access is granted to the UUID.
 * 
 * @author Matthias Zobrist <matthias.zobrist@zepi.net>
 * @copyright Copyright (c) 2015 zepi
 */
class Module extends ModuleAbstract
{
    /**
     * @access protected
     * @var \Zepi\Core\AccessControl\Manager\AccessControlManager
     */
    protected $_accessControlManager;
    
    /**
     * @access protected
     * @var \Zepi\Core\AccessControl\Manager\AccessLevelManager
     */
    protected $_accessLevelManager;
    
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
            case '\\Zepi\\Core\\AccessControl\\Manager\\AccessControlManager':
                if ($this->_accessControlManager === null) {
                    $accessEntitiesBackend = $this->getInstance('\\Zepi\\Core\\AccessControl\\Backend\\AccessEntitiesBackend');
                    $permissionsBackend = $this->getInstance('\\Zepi\\Core\\AccessControl\\Backend\\PermissionsBackend');
                    
                    $this->_accessControlManager = new $className($accessEntitiesBackend, $permissionsBackend);
                }
                
                return $this->_accessControlManager;
            break;
            
            case '\\Zepi\\Core\\AccessControl\\Manager\\AccessLevelManager':
                if ($this->_accessLevelManager === null) {
                    // Get the templates backend
                    $path = $this->_framework->getFrameworkDirectory() . '/data/access-levels.data';
                    $accessLevelsObjectBackend = new \Zepi\Turbo\Backend\FileObjectBackend($path);
            
                    $this->_accessLevelManager = new $className(
                            $this->_framework,
                            $accessLevelsObjectBackend,
                            $this->getInstance('\\Zepi\\Core\\AccessControl\\Manager\\AccessControlManager')
                    );
                    $this->_accessLevelManager->initializeAccessLevelManager();
                }
            
                return $this->_accessLevelManager;
                break;
            
            case '\\Zepi\\Core\\AccessControl\\Backend\\AccessEntitiesBackend':
                $databaseMysqlBackend = $this->_framework->getInstance('\\Zepi\\DataSources\\DatabaseMysql\\Backend\\DatabaseBackend');
                $permissionsBackend = $this->_framework->getInstance('\\Zepi\\Core\\AccessControl\\Backend\\PermissionsBackend');
                
                $accessEntitiesBackend = new $className($databaseMysqlBackend, $permissionsBackend);
                return $accessEntitiesBackend;
            break;
            
            case '\\Zepi\\Core\\AccessControl\\Backend\\PermissionsBackend':
                $databaseMysqlBackend = $this->_framework->getInstance('\\Zepi\\DataSources\\DatabaseMysql\\Backend\\DatabaseBackend');
                
                $permissionsBackend = new $className($databaseMysqlBackend, $this->_framework->getEventManager());
                return $permissionsBackend;
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
        $eventManager = $this->_framework->getEventManager();
        $eventManager->addEventHandler('\\Zepi\\Core\\AccessControl\\Event\\AccessDenied', '\\Zepi\\Core\\AccessControl\\EventHandler\\AccessDenied');
        
        $routeManager = $this->_framework->getRouteManager();
        $routeManager->addRoute('access|denied', '\\Zepi\\Core\\AccessControl\\Event\\AccessDenied');
        
        // This is a fresh activation of this module
        if ($oldVersionNumber === '') {
            $accessEntitiesBackend = $this->getInstance('\\Zepi\\Core\\AccessControl\\Backend\\AccessEntitiesBackend');
            $accessEntitiesBackend->setupDatabase();
            
            $permissionsBackend = $this->getInstance('\\Zepi\\Core\\AccessControl\\Backend\\PermissionsBackend');
            $permissionsBackend->setupDatabase();
        }
        
        // Access Levels
        $accessLevelsManager = $this->_framework->getInstance('\\Zepi\\Core\\AccessControl\\Manager\\AccessLevelManager');
        $accessLevelsManager->addAccessLevel(new \Zepi\Core\AccessControl\Entity\AccessLevel(
            '\\Global\\*',
            'Global Super User',
            'Super user privileges. Can do everything.',
            '\\Zepi\\Core\\AccessControl'
        ));
    }
    
    /**
     * This action will be executed on the deactiviation of the module
     * 
     * @access public
     */
    public function deactivate()
    {
        $eventManager = $this->_framework->getEventManager();
        $eventManager->removeEventHandler('\\Zepi\\Core\\AccessControl\\Event\\AccessDenied', '\\Zepi\\Core\\AccessControl\\EventHandler\\AccessDenied');
        
        $routeManager = $this->_framework->getRouteManager();
        $routeManager->removeRoute('access|denied', '\\Zepi\\Core\\AccessControl\\Event\\AccessDenied');
        
        // Access Levels
        $accessLevelsManager = $this->_framework->getInstance('\\Zepi\\Core\\AccessControl\\Manager\\AccessLevelManager');
        $accessLevelsManager->removeAccessLevel('\\Global\\*');
    }
}