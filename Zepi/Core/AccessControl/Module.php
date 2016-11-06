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
    protected $accessControlManager;
    
    /**
     * @access protected
     * @var \Zepi\Core\AccessControl\Manager\AccessLevelManager
     */
    protected $accessLevelManager;
    
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
                if ($this->accessControlManager === null) {
                    $dataSourceManager = $this->framework->getDataSourceManager();
                    
                    $this->accessControlManager = $this->framework->initiateObject($className, array(
                        'accessEntitiesDataSource' => $dataSourceManager->getDataSource('\\Zepi\\Core\\AccessControl\\DataSource\\AccessEntitiesDataSourceInterface'),
                        'permissionsDataSource' => $dataSourceManager->getDataSource('\\Zepi\\Core\\AccessControl\\DataSource\\PermissionsDataSourceInterface')
                    ));
                }
                
                return $this->accessControlManager;
            break;
            
            case '\\Zepi\\Core\\AccessControl\\Manager\\AccessLevelManager':
                if ($this->accessLevelManager === null) {
                    $accessLevelsObjectBackend = $this->framework->initiateObject('\Zepi\Turbo\Backend\FileObjectBackend', array(
                        'path' => $this->framework->getRootDirectory() . '/data/access-levels.data'
                    ));
                    
                    $this->accessLevelManager = $this->framework->initiateObject($className, array(
                        'accessLevelObjectBackend' => $accessLevelsObjectBackend
                    ));
                    $this->accessLevelManager->initializeAccessLevelManager();
                }
            
                return $this->accessLevelManager;
            break;
            
            case '\\Zepi\\Core\\AccessControl\\DataSource\\AccessEntitiesDataSourceDoctrine':
                $dataSourceManager = $this->framework->getDataSourceManager();
                
                return $this->framework->initiateObject($className, array(
                    'permissionsDataSource' => $dataSourceManager->getDataSource('\\Zepi\\Core\\AccessControl\\DataSource\\PermissionsDataSourceInterface')
                ));
            break;
            
            default: 
                return $this->framework->initiateObject($className);
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
        $runtimeManager = $this->framework->getRuntimeManager();
        $runtimeManager->addFilterHandler('\\Zepi\\Core\\AccessControl\\Filter\\AccessLevelManager\\RemoveAccessLevel', '\\Zepi\\Core\\AccessControl\\FilterHandler\\RevokePermissionsForRemovedAccessLevel');
        
        // Data Sources
        $dataSourceManager = $this->framework->getDataSourceManager();
        $dataSourceManager->addDataSource(
            '\\Zepi\\DataSourceDriver\\Doctrine',
            '\\Zepi\\Core\\AccessControl\\DataSource\\AccessEntitiesDataSourceDoctrine'
        );
        
        $dataSourceManager->addDataSource(
            '\\Zepi\\DataSourceDriver\\Doctrine',
            '\\Zepi\\Core\\AccessControl\\DataSource\\PermissionsDataSourceDoctrine'
        );
        
        // Access Levels
        $accessLevelsManager = $this->framework->getInstance('\\Zepi\\Core\\AccessControl\\Manager\\AccessLevelManager');
        $accessLevelsManager->addAccessLevel(new \Zepi\Core\AccessControl\Entity\AccessLevel(
            '\\Global\\*',
            'Global Super User',
            'Super user privileges. Can do everything.',
            '\\Zepi\\Core\\AccessControl'
        ));
        
        $accessLevelsManager->addAccessLevel(new \Zepi\Core\AccessControl\Entity\AccessLevel(
            '\\Global\\Disabled',
            'Disabled User',
            'A disabled user. Can do nothing, not even login.',
            '\\Zepi\\Core\\AccessControl'
        ));
    }
}
