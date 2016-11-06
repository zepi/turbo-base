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
    protected $userManager;
    
    /**
     * @access protected
     * @var \Zepi\Web\AccessControl\Manager\GroupManager
     */
    protected $groupManager;
    
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
                if ($this->userManager === null) {
                    $this->userManager = $this->framework->initiateObject($className);
                }
                
                return $this->userManager;
            break;
            
            case '\\Zepi\\Web\\AccessControl\\Manager\\GroupManager':
                if ($this->groupManager === null) {
                    $this->groupManager = $this->framework->initiateObject($className);
                }
                
                return $this->groupManager;
            break;
            
            default: 
                return $this->framework->initiateObject($className);
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
        $menuManager = $this->framework->getInstance('\\Zepi\\Web\\General\\Manager\\MenuManager');
        $administrationMenuEntry = $menuManager->getMenuEntryForKey('administration');
        $translationManager = $this->framework->getInstance('\\Zepi\\Core\\Language\\Manager\\TranslationManager');
        
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
        $runtimeManager = $this->framework->getRuntimeManager();
        $runtimeManager->addEventHandler('\\Zepi\\Installation\\ExecuteInstallation', '\\Zepi\\Web\\AccessControl\\EventHandler\\ExecuteInstallation', 100);
        $runtimeManager->addEventHandler('\\Zepi\\Web\\AccessControl\\Event\\Registration', '\\Zepi\\Web\\AccessControl\\EventHandler\\Registration');
        $runtimeManager->addEventHandler('\\Zepi\\Web\\AccessControl\\Event\\Activation', '\\Zepi\\Web\\AccessControl\\EventHandler\\Activation');
        $runtimeManager->addEventHandler('\\Zepi\\Web\\AccessControl\\Event\\RequestNewPassword', '\\Zepi\\Web\\AccessControl\\EventHandler\\RequestNewPassword');
        $runtimeManager->addEventHandler('\\Zepi\\Web\\AccessControl\\Event\\GenerateNewPassword', '\\Zepi\\Web\\AccessControl\\EventHandler\\GenerateNewPassword');
        $runtimeManager->addEventHandler('\\Zepi\\Web\\AccessControl\\Event\\Login', '\\Zepi\\Web\\AccessControl\\EventHandler\\Login');
        $runtimeManager->addEventHandler('\\Zepi\\Web\\AccessControl\\Event\\Logout', '\\Zepi\\Web\\AccessControl\\EventHandler\\Logout');
        $runtimeManager->addEventHandler('\\Zepi\\Web\\AccessControl\\Event\\Profile', '\\Zepi\\Web\\AccessControl\\EventHandler\\Profile');
        $runtimeManager->addEventHandler('\\Zepi\\Web\\AccessControl\\Event\\ProfileChangePassword', '\\Zepi\\Web\\AccessControl\\EventHandler\\ProfileChangePassword');
        $runtimeManager->addEventHandler('\\Zepi\\Web\\AccessControl\\Event\\Management\\Users', '\\Zepi\\Web\\AccessControl\\EventHandler\\Management\\Users');
        $runtimeManager->addEventHandler('\\Zepi\\Turbo\\Event\\BeforeExecution', '\\Zepi\\Web\\AccessControl\\EventHandler\\StartSession', 1);
        $runtimeManager->addEventHandler('\\Zepi\\Turbo\\Event\\BeforeExecution', '\\Zepi\\Web\\AccessControl\\EventHandler\\RegisterMenuEntries');
        $runtimeManager->addEventHandler('\\Zepi\\Core\\AccessControl\\Event\\AccessLevelManager\\RegisterAccessLevels', '\\Zepi\\Web\\AccessControl\\EventHandler\\RegisterGroupAccessLevels');
        
        // Administration
        $runtimeManager->addEventHandler('\\Zepi\\Web\\AccessControl\\Event\\Administration\\Users', '\\Zepi\\Web\\AccessControl\\EventHandler\\Administration\\Users');
        $runtimeManager->addEventHandler('\\Zepi\\Web\\AccessControl\\Event\\Administration\\EditUser', '\\Zepi\\Web\\AccessControl\\EventHandler\\Administration\\EditUser');
        $runtimeManager->addEventHandler('\\Zepi\\Web\\AccessControl\\Event\\Administration\\DeleteUser', '\\Zepi\\Web\\AccessControl\\EventHandler\\Administration\\DeleteUser');
        $runtimeManager->addEventHandler('\\Zepi\\Web\\AccessControl\\Event\\Administration\\Groups', '\\Zepi\\Web\\AccessControl\\EventHandler\\Administration\\Groups');
        $runtimeManager->addEventHandler('\\Zepi\\Web\\AccessControl\\Event\\Administration\\EditGroup', '\\Zepi\\Web\\AccessControl\\EventHandler\\Administration\\EditGroup');
        $runtimeManager->addEventHandler('\\Zepi\\Web\\AccessControl\\Event\\Administration\\DeleteGroup', '\\Zepi\\Web\\AccessControl\\EventHandler\\Administration\\DeleteGroup');
        
        
        
        $runtimeManager->addFilterHandler('\\Zepi\\Web\\General\\Filter\\MenuManager\\FilterMenuEntries', '\\Zepi\\Web\\AccessControl\\FilterHandler\\FilterMenuEntriesForProtectedEntries');
        $runtimeManager->addFilterHandler('\\Zepi\\Core\\AccessControl\\Filter\\PermissionsBackend\\ResolvePermissions', '\\Zepi\\Web\\AccessControl\\FilterHandler\\ResolveGroupPermissions');
        
        
        
        $routeManager = $this->framework->getRouteManager();
        $routeManager->addRoute('register', '\\Zepi\\Web\\AccessControl\\Event\\Registration');
        $routeManager->addRoute('activate|[s:uuid]|[s:token]', '\\Zepi\\Web\\AccessControl\\Event\\Activation');
        $routeManager->addRoute('request-new-password', '\\Zepi\\Web\\AccessControl\\Event\\RequestNewPassword');
        $routeManager->addRoute('generate-new-password|[s:uuid]|[s:token]', '\\Zepi\\Web\\AccessControl\\Event\\GenerateNewPassword');
        $routeManager->addRoute('login', '\\Zepi\\Web\\AccessControl\\Event\\Login');
        $routeManager->addRoute('logout', '\\Zepi\\Web\\AccessControl\\Event\\Logout');
        $routeManager->addRoute('profile', '\\Zepi\\Web\\AccessControl\\Event\\Profile');
        $routeManager->addRoute('profile|change-password', '\\Zepi\\Web\\AccessControl\\Event\\ProfileChangePassword');
        
        // Administration
        $routeManager->addRoute('administration|users', '\\Zepi\\Web\\AccessControl\\Event\\Administration\\Users');
        $routeManager->addRoute('administration|users|add', '\\Zepi\\Web\\AccessControl\\Event\\Administration\\EditUser');
        $routeManager->addRoute('administration|users|modify|[s:uuid]', '\\Zepi\\Web\\AccessControl\\Event\\Administration\\EditUser');
        $routeManager->addRoute('administration|users|delete|[s:uuid]', '\\Zepi\\Web\\AccessControl\\Event\\Administration\\DeleteUser');
        $routeManager->addRoute('administration|users|delete|[s:uuid]|[s:confirmation]', '\\Zepi\\Web\\AccessControl\\Event\\Administration\\DeleteUser');
        $routeManager->addRoute('administration|groups', '\\Zepi\\Web\\AccessControl\\Event\\Administration\\Groups');
        $routeManager->addRoute('administration|groups|add', '\\Zepi\\Web\\AccessControl\\Event\\Administration\\EditGroup');
        $routeManager->addRoute('administration|groups|modify|[s:uuid]', '\\Zepi\\Web\\AccessControl\\Event\\Administration\\EditGroup');
        $routeManager->addRoute('administration|groups|delete|[s:uuid]', '\\Zepi\\Web\\AccessControl\\Event\\Administration\\DeleteGroup');
        $routeManager->addRoute('administration|groups|delete|[s:uuid]|[s:confirmation]', '\\Zepi\\Web\\AccessControl\\Event\\Administration\\DeleteGroup');
        
        
        
        $templatesManager = $this->framework->getInstance('\\Zepi\\Web\\General\\Manager\\TemplatesManager');
        $templatesManager->addTemplate('\\Zepi\\Web\\AccessControl\\Templates\\RegistrationForm', $this->directory . '/templates/Registration.Form.phtml');
        $templatesManager->addTemplate('\\Zepi\\Web\\AccessControl\\Templates\\RegistrationFinished', $this->directory . '/templates/Registration.Finished.phtml');
        $templatesManager->addTemplate('\\Zepi\\Web\\AccessControl\\Templates\\Activation', $this->directory . '/templates/Activation.phtml');
        $templatesManager->addTemplate('\\Zepi\\Web\\AccessControl\\Templates\\RequestNewPasswordForm', $this->directory . '/templates/RequestNewPassword.Form.phtml');
        $templatesManager->addTemplate('\\Zepi\\Web\\AccessControl\\Templates\\RequestNewPasswordFinished', $this->directory . '/templates/RequestNewPassword.Finished.phtml');
        $templatesManager->addTemplate('\\Zepi\\Web\\AccessControl\\Templates\\GenerateNewPasswordFinished', $this->directory . '/templates/GenerateNewPassword.Finished.phtml');
        $templatesManager->addTemplate('\\Zepi\\Web\\AccessControl\\Templates\\LoginForm', $this->directory . '/templates/Login.Form.phtml');
        $templatesManager->addTemplate('\\Zepi\\Web\\AccessControl\\Templates\\Logout', $this->directory . '/templates/Logout.phtml');
        $templatesManager->addTemplate('\\Zepi\\Web\\AccessControl\\Templates\\Profile', $this->directory . '/templates/Profile.phtml');
        $templatesManager->addTemplate('\\Zepi\\Web\\AccessControl\\Templates\\ProfileChangePasswordForm', $this->directory . '/templates/ProfileChangePassword.Form.phtml');
        $templatesManager->addTemplate('\\Zepi\\Web\\AccessControl\\Templates\\ProfileChangePasswordFinished', $this->directory . '/templates/ProfileChangePassword.Finished.phtml');
        
        $templatesManager->addTemplate('\\Zepi\\Web\\AccessControl\\Mail\\Registration', $this->directory . '/templates/Mail/Registration.phtml');
        $templatesManager->addTemplate('\\Zepi\\Web\\AccessControl\\Mail\\RequestNewPassword', $this->directory . '/templates/Mail/RequestNewPassword.phtml');
        $templatesManager->addTemplate('\\Zepi\\Web\\AccessControl\\Mail\\GenerateNewPassword', $this->directory . '/templates/Mail/GenerateNewPassword.phtml');
        
        // Administration
        $templatesManager->addTemplate('\\Zepi\\Web\\AccessControl\\Templates\\Administration\\Users', $this->directory . '/templates/Administration/Users.phtml');
        $templatesManager->addTemplate('\\Zepi\\Web\\AccessControl\\Templates\\Administration\\EditUserForm', $this->directory . '/templates/Administration/EditUser.Form.phtml');
        $templatesManager->addTemplate('\\Zepi\\Web\\AccessControl\\Templates\\Administration\\EditUserFinished', $this->directory . '/templates/Administration/EditUser.Finished.phtml');
        $templatesManager->addTemplate('\\Zepi\\Web\\AccessControl\\Templates\\Administration\\DeleteUser', $this->directory . '/templates/Administration/DeleteUser.phtml');
        $templatesManager->addTemplate('\\Zepi\\Web\\AccessControl\\Templates\\Administration\\DeleteUserFinished', $this->directory . '/templates/Administration/DeleteUser.Finished.phtml');
        $templatesManager->addTemplate('\\Zepi\\Web\\AccessControl\\Templates\\Administration\\Groups', $this->directory . '/templates/Administration/Groups.phtml');
        $templatesManager->addTemplate('\\Zepi\\Web\\AccessControl\\Templates\\Administration\\EditGroupForm', $this->directory . '/templates/Administration/EditGroup.Form.phtml');
        $templatesManager->addTemplate('\\Zepi\\Web\\AccessControl\\Templates\\Administration\\EditGroupFinished', $this->directory . '/templates/Administration/EditGroup.Finished.phtml');
        $templatesManager->addTemplate('\\Zepi\\Web\\AccessControl\\Templates\\Administration\\DeleteGroup', $this->directory . '/templates/Administration/DeleteGroup.phtml');
        $templatesManager->addTemplate('\\Zepi\\Web\\AccessControl\\Templates\\Administration\\DeleteGroupFinished', $this->directory . '/templates/Administration/DeleteGroup.Finished.phtml');
        
        // Form
        $templatesManager->addTemplate('\\Zepi\\Web\\AccessControl\\Templates\\Form\\Snippet\\AccessLevel', $this->directory . '/templates/Form/Snippet/AccessLevel.phtml');
        
        // Access Levels
        $accessLevelsManager = $this->framework->getInstance('\\Zepi\\Core\\AccessControl\\Manager\\AccessLevelManager');
        $accessLevelsManager->addAccessLevel(new \Zepi\Core\AccessControl\Entity\AccessLevel(
            '\\Zepi\\Web\\AccessControl\\AccessLevel\\EditUsersAndGroups', 
            'Manage users and groups', 
            'Can create, modify and delete users and groups.', 
            '\\Zepi\\Web\\AccessControl'
        ));
        
        // Configuration
        $configurationManager = $this->framework->getInstance('\\Zepi\\Core\\Utils\\Manager\\ConfigurationManager');
        $configurationManager->addSettingIfNotSet('accesscontrol.allowRegistration', true);
        $configurationManager->addSettingIfNotSet('accesscontrol.allowRenewPassword', true);
        $configurationManager->saveConfigurationFile();
    }
}
