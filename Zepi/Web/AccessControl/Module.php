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
 * This module delivers the frontend functionality for the
 * access control module.
 * 
 * @package Zepi\Web\AccessControl
 * @author Matthias Zobrist <matthias.zobrist@zepi.net>
 * @copyright Copyright (c) 2015 zepi
 */

namespace Zepi\Web\AccessControl;

use \Zepi\Turbo\Module\ModuleAbstract;
use \Zepi\Web\AccessControl\Entity\ProtectedMenuEntry;
use \Zepi\Web\General\Manager\AssetsManager;

/**
 * This module delivers the frontend functionality for the
 * access control module.
 * 
 * @author Matthias Zobrist <matthias.zobrist@zepi.net>
 * @copyright Copyright (c) 2015 zepi
 */
class Module extends ModuleAbstract
{
    /**
     * @access protected
     * @var \Zepi\Web\AccessControl\Manager\UserManager
     */
    protected $_userManager;
    
    /**
     * @access protected
     * @var \Zepi\Web\AccessControl\Manager\GroupManager
     */
    protected $_groupManager;
    
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
            case '\\Zepi\\Web\\AccessControl\\Manager\\UserManager':
                if ($this->_userManager === null) {
                    $accessControlManager = $this->_framework->getInstance('\\Zepi\\Core\\AccessControl\\Manager\\AccessControlManager');
                    
                    $this->_userManager = new $className($accessControlManager);
                }
                
                return $this->_userManager;
            break;
            
            case '\\Zepi\\Web\\AccessControl\\Manager\\GroupManager':
                if ($this->_groupManager === null) {
                    $accessControlManager = $this->_framework->getInstance('\\Zepi\\Core\\AccessControl\\Manager\\AccessControlManager');
                    
                    $this->_groupManager = new $className($accessControlManager);
                }
                
                return $this->_groupManager;
            break;
            
            case '\\Zepi\\Web\\AccessControl\\Helper\\AccessLevelHelper':
                $translationManager = $this->_framework->getInstance('\\Zepi\\Core\\Language\\Manager\\TranslationManager');

                return new $className($translationManager);
                break;
            
            default: 
                return new $className();
            break;
        }
    }
    
    /**
     * Initializes the module
     * 
     * @access public
     */
    public function initialize()
    {
        $menuManager = $this->_framework->getInstance('\\Zepi\\Web\\General\\Manager\\MenuManager');
        $administrationMenuEntry = $menuManager->getMenuEntryForKey('administration');
        $translationManager = $this->_framework->getInstance('\\Zepi\\Core\\Language\\Manager\\TranslationManager');
        
        $accessMenuEntry = new \Zepi\Web\AccessControl\Entity\ProtectedMenuEntry(
            'access-administration',
            $translationManager->translate('Access management', '\\Zepi\\Web\\AccessControl'),
            '\\Zepi\\Web\\AccessControl\\AccessLevel\\EditUsersAndGroups',
            'administration'
        );
        $administrationMenuEntry->addChild($accessMenuEntry);
        
        $menuEntry = new \Zepi\Web\AccessControl\Entity\ProtectedMenuEntry(
            'user-administration',
            $translationManager->translate('User management', '\\Zepi\\Web\\AccessControl'),
            '\\Zepi\\Web\\AccessControl\\AccessLevel\\EditUsersAndGroups',
            'administration/users',
            'mdi-account'
        );
        $accessMenuEntry->addChild($menuEntry);
        
        $menuEntry = new \Zepi\Web\AccessControl\Entity\ProtectedMenuEntry(
            'group-administration',
            $translationManager->translate('Group management', '\\Zepi\\Web\\AccessControl'),
            '\\Zepi\\Web\\AccessControl\\AccessLevel\\EditUsersAndGroups',
            'administration/groups',
            'mdi-account-multiple'
        );
        $accessMenuEntry->addChild($menuEntry);
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
        $eventManager->addEventHandler('\\Zepi\\Web\\General\\Event\\MenuManager\\FilterMenuEntries', '\\Zepi\\Web\\AccessControl\\EventHandler\\FilterMenuEntriesForProtectedEntries');
        $eventManager->addEventHandler('\\Zepi\\Web\\AccessControl\\Event\\Login', '\\Zepi\\Web\\AccessControl\\EventHandler\\Login');
        $eventManager->addEventHandler('\\Zepi\\Web\\AccessControl\\Event\\Logout', '\\Zepi\\Web\\AccessControl\\EventHandler\\Logout');
        $eventManager->addEventHandler('\\Zepi\\Web\\AccessControl\\Event\\Profile', '\\Zepi\\Web\\AccessControl\\EventHandler\\Profile');
        $eventManager->addEventHandler('\\Zepi\\Web\\AccessControl\\Event\\ProfileChangePassword', '\\Zepi\\Web\\AccessControl\\EventHandler\\ProfileChangePassword');
        $eventManager->addEventHandler('\\Zepi\\Web\\AccessControl\\Event\\Management\\Users', '\\Zepi\\Web\\AccessControl\\EventHandler\\Management\\Users');
        $eventManager->addEventHandler('\\Zepi\\Turbo\\Event\\BeforeExecution', '\\Zepi\\Web\\AccessControl\\EventHandler\\StartSession');
        $eventManager->addEventHandler('\\Zepi\\Web\\General\\Event\\MenuManager\\PreSearchCorrectMenuEntry', '\\Zepi\\Web\\AccessControl\\EventHandler\\RegisterMenuEntries');
        $eventManager->addEventHandler('\\Zepi\\Core\\AccessControl\\Event\\AccessLevelManager\\RegisterAccessLevels', '\\Zepi\\Web\\AccessControl\\EventHandler\\RegisterGroupAccessLevels');
        $eventManager->addEventHandler('\\Zepi\\Core\\AccessControl\\Event\\PermissionsBackend\\ResolvePermissions', '\\Zepi\\Web\\AccessControl\\EventHandler\\ResolveGroupPermissions');
        
        // Administration
        $eventManager->addEventHandler('\\Zepi\\Web\\AccessControl\\Event\\Administration\\Users', '\\Zepi\\Web\\AccessControl\\EventHandler\\Administration\\Users');
        $eventManager->addEventHandler('\\Zepi\\Web\\AccessControl\\Event\\Administration\\EditUser', '\\Zepi\\Web\\AccessControl\\EventHandler\\Administration\\EditUser');
        $eventManager->addEventHandler('\\Zepi\\Web\\AccessControl\\Event\\Administration\\DeleteUser', '\\Zepi\\Web\\AccessControl\\EventHandler\\Administration\\DeleteUser');
        $eventManager->addEventHandler('\\Zepi\\Web\\AccessControl\\Event\\Administration\\Groups', '\\Zepi\\Web\\AccessControl\\EventHandler\\Administration\\Groups');
        $eventManager->addEventHandler('\\Zepi\\Web\\AccessControl\\Event\\Administration\\EditGroup', '\\Zepi\\Web\\AccessControl\\EventHandler\\Administration\\EditGroup');
        $eventManager->addEventHandler('\\Zepi\\Web\\AccessControl\\Event\\Administration\\DeleteGroup', '\\Zepi\\Web\\AccessControl\\EventHandler\\Administration\\DeleteGroup');
        
        $routeManager = $this->_framework->getRouteManager();
        $routeManager->addRoute('login', '\\Zepi\\Web\\AccessControl\\Event\\Login');
        $routeManager->addRoute('logout', '\\Zepi\\Web\\AccessControl\\Event\\Logout');
        $routeManager->addRoute('profile', '\\Zepi\\Web\\AccessControl\\Event\\Profile');
        $routeManager->addRoute('profile|change-password', '\\Zepi\\Web\\AccessControl\\Event\\ProfileChangePassword');
        
        // Administration
        $routeManager->addRoute('administration|users', '\\Zepi\\Web\\AccessControl\\Event\\Administration\\Users');
        $routeManager->addRoute('administration|users|page|[d]', '\\Zepi\\Web\\AccessControl\\Event\\Administration\\Users');
        $routeManager->addRoute('administration|users|add', '\\Zepi\\Web\\AccessControl\\Event\\Administration\\EditUser');
        $routeManager->addRoute('administration|users|modify|[s]', '\\Zepi\\Web\\AccessControl\\Event\\Administration\\EditUser');
        $routeManager->addRoute('administration|users|delete|[s]', '\\Zepi\\Web\\AccessControl\\Event\\Administration\\DeleteUser');
        $routeManager->addRoute('administration|users|delete|[s]|[s]', '\\Zepi\\Web\\AccessControl\\Event\\Administration\\DeleteUser');
        $routeManager->addRoute('administration|groups', '\\Zepi\\Web\\AccessControl\\Event\\Administration\\Groups');
        $routeManager->addRoute('administration|groups|page|[d]', '\\Zepi\\Web\\AccessControl\\Event\\Administration\\Groups');
        $routeManager->addRoute('administration|groups|add', '\\Zepi\\Web\\AccessControl\\Event\\Administration\\EditGroup');
        $routeManager->addRoute('administration|groups|modify|[s]', '\\Zepi\\Web\\AccessControl\\Event\\Administration\\EditGroup');
        $routeManager->addRoute('administration|groups|delete|[s]', '\\Zepi\\Web\\AccessControl\\Event\\Administration\\DeleteGroup');
        $routeManager->addRoute('administration|groups|delete|[s]|[s]', '\\Zepi\\Web\\AccessControl\\Event\\Administration\\DeleteGroup');
        
        $templatesManager = $this->_framework->getInstance('\\Zepi\\Web\\General\\Manager\\TemplatesManager');
        $templatesManager->addTemplate('\\Zepi\\Web\\AccessControl\\Templates\\LoginForm', $this->_directory . '/templates/Login.Form.phtml');
        $templatesManager->addTemplate('\\Zepi\\Web\\AccessControl\\Templates\\Logout', $this->_directory . '/templates/Logout.phtml');
        $templatesManager->addTemplate('\\Zepi\\Web\\AccessControl\\Templates\\Profile', $this->_directory . '/templates/Profile.phtml');
        $templatesManager->addTemplate('\\Zepi\\Web\\AccessControl\\Templates\\ProfileChangePasswordForm', $this->_directory . '/templates/ProfileChangePassword.Form.phtml');
        $templatesManager->addTemplate('\\Zepi\\Web\\AccessControl\\Templates\\ProfileChangePasswordFinished', $this->_directory . '/templates/ProfileChangePassword.Finished.phtml');
        
        // Administration
        $templatesManager->addTemplate('\\Zepi\\Web\\AccessControl\\Templates\\Administration\\Users', $this->_directory . '/templates/Administration/Users.phtml');
        $templatesManager->addTemplate('\\Zepi\\Web\\AccessControl\\Templates\\Administration\\EditUserForm', $this->_directory . '/templates/Administration/EditUser.Form.phtml');
        $templatesManager->addTemplate('\\Zepi\\Web\\AccessControl\\Templates\\Administration\\EditUserFinished', $this->_directory . '/templates/Administration/EditUser.Finished.phtml');
        $templatesManager->addTemplate('\\Zepi\\Web\\AccessControl\\Templates\\Administration\\DeleteUser', $this->_directory . '/templates/Administration/DeleteUser.phtml');
        $templatesManager->addTemplate('\\Zepi\\Web\\AccessControl\\Templates\\Administration\\DeleteUserFinished', $this->_directory . '/templates/Administration/DeleteUser.Finished.phtml');
        $templatesManager->addTemplate('\\Zepi\\Web\\AccessControl\\Templates\\Administration\\Groups', $this->_directory . '/templates/Administration/Groups.phtml');
        $templatesManager->addTemplate('\\Zepi\\Web\\AccessControl\\Templates\\Administration\\EditGroupForm', $this->_directory . '/templates/Administration/EditGroup.Form.phtml');
        $templatesManager->addTemplate('\\Zepi\\Web\\AccessControl\\Templates\\Administration\\EditGroupFinished', $this->_directory . '/templates/Administration/EditGroup.Finished.phtml');
        $templatesManager->addTemplate('\\Zepi\\Web\\AccessControl\\Templates\\Administration\\DeleteGroup', $this->_directory . '/templates/Administration/DeleteGroup.phtml');
        $templatesManager->addTemplate('\\Zepi\\Web\\AccessControl\\Templates\\Administration\\DeleteGroupFinished', $this->_directory . '/templates/Administration/DeleteGroup.Finished.phtml');
        
        // Form
        $templatesManager->addTemplate('\\Zepi\\Web\\AccessControl\\Templates\\Form\\Snippet\\AccessLevel', $this->_directory . '/templates/Form/Snippet/AccessLevel.phtml');
        
        // Access Levels
        $accessLevelsManager = $this->_framework->getInstance('\\Zepi\\Core\\AccessControl\\Manager\\AccessLevelManager');
        $accessLevelsManager->addAccessLevel(new \Zepi\Core\AccessControl\Entity\AccessLevel(
            '\\Zepi\\Web\\AccessControl\\AccessLevel\\EditUsersAndGroups', 
            'Manage users and groups', 
            'Can create, modify and delete users and groups.', 
            '\\Zepi\\Web\\AccessControl'
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
        $eventManager->removeEventHandler('\\Zepi\\Web\\General\\Event\\MenuManager\\FilterMenuEntries', '\\Zepi\\Web\\AccessControl\\EventHandler\\FilterMenuEntriesForProtectedEntries');
        $eventManager->removeEventHandler('\\Zepi\\Web\\AccessControl\\Event\\Login', '\\Zepi\\Web\\AccessControl\\EventHandler\\Login');
        $eventManager->removeEventHandler('\\Zepi\\Web\\AccessControl\\Event\\Logout', '\\Zepi\\Web\\AccessControl\\EventHandler\\Logout');
        $eventManager->removeEventHandler('\\Zepi\\Web\\AccessControl\\Event\\Profile', '\\Zepi\\Web\\AccessControl\\EventHandler\\Profile');
        $eventManager->removeEventHandler('\\Zepi\\Web\\AccessControl\\Event\\ProfileChangePassword', '\\Zepi\\Web\\AccessControl\\EventHandler\\ProfileChangePassword');
        $eventManager->removeEventHandler('\\Zepi\\Web\\AccessControl\\Event\\Management\\Users', '\\Zepi\\Web\\AccessControl\\EventHandler\\Management\\Users');
        $eventManager->removeEventHandler('\\Zepi\\Turbo\\Event\\BeforeExecution', '\\Zepi\\Web\\AccessControl\\EventHandler\\StartSession');
        $eventManager->removeEventHandler('\\Zepi\\Web\\General\\Event\\MenuManager\\PreSearchCorrectMenuEntry', '\\Zepi\\Web\\AccessControl\\EventHandler\\RegisterMenuEntries');
        $eventManager->removeEventHandler('\\Zepi\\Core\\AccessControl\\Event\\AccessLevelManager\\RegisterAccessLevels', '\\Zepi\\Web\\AccessControl\\EventHandler\\RegisterGroupAccessLevels');
        $eventManager->removeEventHandler('\\Zepi\\Core\\AccessControl\\Event\\PermissionsBackend\\ResolvePermissions', '\\Zepi\\Web\\AccessControl\\EventHandler\\ResolveGroupPermissions');
        
        // Administration
        $eventManager->removeEventHandler('\\Zepi\\Web\\AccessControl\\Event\\Administration\\Users', '\\Zepi\\Web\\AccessControl\\EventHandler\\Administration\\Users');
        $eventManager->removeEventHandler('\\Zepi\\Web\\AccessControl\\Event\\Administration\\EditUser', '\\Zepi\\Web\\AccessControl\\EventHandler\\Administration\\EditUser');
        $eventManager->removeEventHandler('\\Zepi\\Web\\AccessControl\\Event\\Administration\\DeleteUser', '\\Zepi\\Web\\AccessControl\\EventHandler\\Administration\\DeleteUser');
        $eventManager->removeEventHandler('\\Zepi\\Web\\AccessControl\\Event\\Administration\\Groups', '\\Zepi\\Web\\AccessControl\\EventHandler\\Administration\\Groups');
        $eventManager->removeEventHandler('\\Zepi\\Web\\AccessControl\\Event\\Administration\\EditGroup', '\\Zepi\\Web\\AccessControl\\EventHandler\\Administration\\EditGroup');
        $eventManager->removeEventHandler('\\Zepi\\Web\\AccessControl\\Event\\Administration\\DeleteGroup', '\\Zepi\\Web\\AccessControl\\EventHandler\\Administration\\DeleteGroup');
        
        
        $routeManager = $this->_framework->getRouteManager();
        $routeManager->removeRoute('login', '\\Zepi\\Web\\AccessControl\\Event\\Login');
        $routeManager->removeRoute('logout', '\\Zepi\\Web\\AccessControl\\Event\\Logout');
        $routeManager->removeRoute('profile', '\\Zepi\\Web\\AccessControl\\Event\\Profile');
        $routeManager->removeRoute('profile|change-password', '\\Zepi\\Web\\AccessControl\\Event\\ProfileChangePassword');
        
        // Administration
        $routeManager->removeRoute('administration|users', '\\Zepi\\Web\\AccessControl\\Event\\Administration\\Users');
        $routeManager->removeRoute('administration|users|page|[d]', '\\Zepi\\Web\\AccessControl\\Event\\Administration\\Users');
        $routeManager->removeRoute('administration|users|add', '\\Zepi\\Web\\AccessControl\\Event\\Administration\\EditUser');
        $routeManager->removeRoute('administration|users|modify|[s]', '\\Zepi\\Web\\AccessControl\\Event\\Administration\\EditUser');
        $routeManager->removeRoute('administration|users|delete|[s]', '\\Zepi\\Web\\AccessControl\\Event\\Administration\\DeleteUser');
        $routeManager->removeRoute('administration|users|delete|[s]|[s]', '\\Zepi\\Web\\AccessControl\\Event\\Administration\\DeleteUser');
        $routeManager->removeRoute('administration|groups', '\\Zepi\\Web\\AccessControl\\Event\\Administration\\Groups');
        $routeManager->removeRoute('administration|groups|page|[d]', '\\Zepi\\Web\\AccessControl\\Event\\Administration\\Groups');
        $routeManager->removeRoute('administration|groups|add', '\\Zepi\\Web\\AccessControl\\Event\\Administration\\EditGroup');
        $routeManager->removeRoute('administration|groups|modify|[s]', '\\Zepi\\Web\\AccessControl\\Event\\Administration\\EditGroup');
        $routeManager->removeRoute('administration|groups|delete|[s]', '\\Zepi\\Web\\AccessControl\\Event\\Administration\\DeleteGroup');
        $routeManager->removeRoute('administration|groups|delete|[s]|[s]', '\\Zepi\\Web\\AccessControl\\Event\\Administration\\DeleteGroup');
        
        
        $templatesManager = $this->_framework->getInstance('\\Zepi\\Web\\General\\Manager\\TemplatesManager');
        $templatesManager->removeTemplate('\\Zepi\\Web\\AccessControl\\Templates\\LoginForm', $this->_directory . '/templates/Login.Form.phtml');
        $templatesManager->removeTemplate('\\Zepi\\Web\\AccessControl\\Templates\\Logout', $this->_directory . '/templates/Logout.phtml');
        $templatesManager->removeTemplate('\\Zepi\\Web\\AccessControl\\Templates\\Profile', $this->_directory . '/templates/Profile.phtml');
        $templatesManager->removeTemplate('\\Zepi\\Web\\AccessControl\\Templates\\ProfileChangePasswordForm', $this->_directory . '/templates/ProfileChangePassword.Form.phtml');
        $templatesManager->removeTemplate('\\Zepi\\Web\\AccessControl\\Templates\\ProfileChangePasswordFinished', $this->_directory . '/templates/ProfileChangePassword.Finished.phtml');
        
        // Administration
        $templatesManager->removeTemplate('\\Zepi\\Web\\AccessControl\\Templates\\Administration\\Users', $this->_directory . '/templates/Administration/Users.phtml');
        $templatesManager->removeTemplate('\\Zepi\\Web\\AccessControl\\Templates\\Administration\\EditUserForm', $this->_directory . '/templates/Administration/EditUser.Form.phtml');
        $templatesManager->removeTemplate('\\Zepi\\Web\\AccessControl\\Templates\\Administration\\EditUserFinished', $this->_directory . '/templates/Administration/EditUser.Finished.phtml');
        $templatesManager->removeTemplate('\\Zepi\\Web\\AccessControl\\Templates\\Administration\\DeleteUser', $this->_directory . '/templates/Administration/DeleteUser.phtml');
        $templatesManager->removeTemplate('\\Zepi\\Web\\AccessControl\\Templates\\Administration\\DeleteUserFinished', $this->_directory . '/templates/Administration/DeleteUser.Finished.phtml');
        $templatesManager->removeTemplate('\\Zepi\\Web\\AccessControl\\Templates\\Administration\\Groups', $this->_directory . '/templates/Administration/Groups.phtml');
        $templatesManager->removeTemplate('\\Zepi\\Web\\AccessControl\\Templates\\Administration\\EditGroupForm', $this->_directory . '/templates/Administration/EditGroup.Form.phtml');
        $templatesManager->removeTemplate('\\Zepi\\Web\\AccessControl\\Templates\\Administration\\EditGroupFinished', $this->_directory . '/templates/Administration/EditGroup.Finished.phtml');
        $templatesManager->removeTemplate('\\Zepi\\Web\\AccessControl\\Templates\\Administration\\DeleteGroup', $this->_directory . '/templates/Administration/DeleteGroup.phtml');
        $templatesManager->removeTemplate('\\Zepi\\Web\\AccessControl\\Templates\\Administration\\DeleteGroupFinished', $this->_directory . '/templates/Administration/DeleteGroup.Finished.phtml');
        
        // Form
        $templatesManager->removeTemplate('\\Zepi\\Web\\AccessControl\\Templates\\Form\\Snippet\\AccessLevel', $this->_directory . '/templates/Form/Snippet/AccessLevel.phtml');
        
        // Access Levels
        $accessLevelsManager = $this->_framework->getInstance('\\Zepi\\Core\\AccessControl\\Manager\\AccessLevelManager');
        $accessLevelsManager->removeAccessLevel('\\Zepi\\Web\\AccessControl\\AccessLevel\\EditUsersAndGroups');
    }
}
