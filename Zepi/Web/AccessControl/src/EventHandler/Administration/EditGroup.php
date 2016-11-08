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
 * Displays the edit user form and saves the data to the database.
 * 
 * @package Zepi\Web\AccessControl
 * @subpackage EventHandler\Administration
 * @author Matthias Zobrist <matthias.zobrist@zepi.net>
 * @copyright Copyright (c) 2015 zepi
 */

namespace Zepi\Web\AccessControl\EventHandler\Administration;

use \Zepi\Web\UserInterface\Frontend\FrontendEventHandler;
use \Zepi\Turbo\Framework;
use \Zepi\Turbo\Request\WebRequest;
use \Zepi\Turbo\Response\Response;
use \Zepi\Web\AccessControl\Entity\Group as EntityGroup;
use \Zepi\Web\UserInterface\Form\Form;
use \Zepi\Web\UserInterface\Form\Error;
use \Zepi\Web\UserInterface\Frontend\FrontendHelper;
use \Zepi\Web\AccessControl\Manager\GroupManager;
use \Zepi\Core\AccessControl\Manager\AccessControlManager;
use \Zepi\Core\AccessControl\Manager\AccessLevelManager;
use \Zepi\Web\AccessControl\Helper\AccessLevelHelper;
use \Zepi\Web\AccessControl\Layout\EditGroupLayout;

/**
 * Displays the edit user form and saves the data to the database.
 * 
 * @author Matthias Zobrist <matthias.zobrist@zepi.net>
 * @copyright Copyright (c) 2015 zepi
 */
class EditGroup extends FrontendEventHandler
{
    /**
     * @access protected
     * @var \Zepi\Web\AccessControl\Manager\GroupManager
     */
    protected $groupManager;
    
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
     * @var \Zepi\Web\AccessControl\Layout\EditGroupLayout
     */
    protected $layout;
    
    /**
     * Constructs the object
     * 
     * @access public
     * @param \Zepi\Web\UserInterface\Frontend\FrontendHelper $frontendHelper
     * @param \Zepi\Web\AccessControl\Manager\GroupManager $groupManager
     * @param \Zepi\Core\AccessControl\Manager\AccessControlManager $accessControlManager
     * @param \Zepi\Core\AccessControl\Manager\AccessLevelManager $accessLevelManager
     * @param \Zepi\Web\AccessControl\Layout\EditGroupLayout $layout
     */
    public function __construct(
        FrontendHelper $frontendHelper, 
        GroupManager $groupManager, 
        AccessControlManager $accessControlManager, 
        AccessLevelManager $accessLevelManager, 
        AccessLevelHelper $accessLevelHelper,
        EditGroupLayout $layout
    ) {
        $this->frontendHelper = $frontendHelper;
        $this->groupManager = $groupManager;
        $this->accessControlManager = $accessControlManager;
        $this->accessLevelManager = $accessLevelManager;
        $this->accessLevelHelper = $accessLevelHelper;
        $this->layout = $layout;
    }
    
    /**
     * Displays the edit user form and saves the data to the database.
     * 
     * @access public
     * @param \Zepi\Turbo\Framework $framework
     * @param \Zepi\Turbo\Request\WebRequest $request
     * @param \Zepi\Turbo\Response\Response $response
     */
    public function execute(Framework $framework, WebRequest $request, Response $response)
    {
        // Redirect if the user hasn't a valid session
        if (!$request->hasSession() || !$request->getSession()->hasAccess('\\Zepi\\Web\\AccessControl\\AccessLevel\\EditUsersAndGroups')) {
            $response->redirectTo('/');
            return;
        }

        $uuid = $request->getRouteParam('uuid');
        
        // If there is a request parameter we need to edit a user. Otherwise we create a new one.
        if (is_string($uuid)) {
            $additionalTitle = $this->translate('Modify group', '\\Zepi\\Web\\AccessControl');
            
            $group = $this->groupManager->getGroupForUuid($uuid);
        } else {
            $additionalTitle = $this->translate('Add group', '\\Zepi\\Web\\AccessControl');
            
            $group = new EntityGroup('', '', '', '', array());
        }
        $title = $this->translate('Group management', '\\Zepi\\Web\\AccessControl');
        $this->layout->setGroup($group);
        
        // Prepare the page
        $this->activateMenuEntry('group-administration');
        $this->setTitle($title, $additionalTitle);
        
        // Process the data
        $result = $this->processFormData($request, $group);
        if ($result === true) {
            // Display the successful saved message
            $response->setOutput($this->render('\\Zepi\\Web\\AccessControl\\Templates\\Administration\\EditGroupFinished', array(
                'title' => $this->getTitle()
            )));
        } else {
            $response->setOutput($this->render('\\Zepi\\Web\\AccessControl\\Templates\\Administration\\EditGroupForm', array(
                'user' => $group,
                'title' => $this->getTitle(),
                'layout' => $this->layout->getLayout(),
                'layoutRenderer' => $this->getLayoutRenderer()
            )));
        }
    }
    
    /**
     * Validates the form data, updates the group and saves the
     * group into the database.
     *
     * @param \Zepi\Turbo\Request\WebRequest $request
     * @param \Zepi\Web\AccessControl\Entity\Group $group
     * @return boolean|string
     */
    protected function processFormData(WebRequest $request, EntityGroup $group)
    {
        $result = $this->layout->validateFormData($request, function ($formValues) use ($group) {
            return $this->validateData(
                $group,
                $formValues['required-data.groupname']
            );
        });
    
        if ($result == Form::DATA_VALID) {
            $result = $this->saveGroup($request, $group);
        }

        return $result;
    }
    
    /**
     * Saves the group
     *
     * @access protected
     * @param \Zepi\Turbo\Request\WebRequest $request
     * @param \Zepi\Web\AccessControl\Entity\EntityGroup $group
     */
    protected function saveGroup(WebRequest $request, EntityGroup $group)
    {
        $formValues = $this->layout->getFormValues();
    
        // Set the groupname
        $group->setName($formValues['required-data.groupname']);
    
        // Set the optional data
        $group->setMetaData('description', $formValues['optional-data.description']);
    
        // Save the group
        if ($group->isNew()) {
            $group = $this->groupManager->addGroup($group);
        } else {
            $this->groupManager->updateGroup($group);
        }
    
        if ($group === false) {
            return false;
        }
    
        // Save the access levels
        $this->accessControlManager->updatePermissions($group, $formValues['access-levels'], $request->getSession()->getUser());
        $accessLevels = $this->cleanAccessLevels($group->getUuid(), $formValues['access-levels']);
        
        $this->accessControlManager->updatePermissions($group, $accessLevels, $request->getSession()->getUser());
    
        return true;
    }
    
    /**
     * Removes the the access level for this group if
     * it is added to the access levels.
     * 
     * @access public
     * @param string $uuid
     * @param array $accessLevels
     * @return array
     */
    protected function cleanAccessLevels($uuid, $accessLevels)
    {
        foreach ($accessLevels as $key => $accessLevel) {
            $parts = explode('\\', $accessLevel);
            
            if ($parts[1] === 'Group' && count($parts) === 3 && $parts[2] === $uuid) {
                unset($accessLevels[$key]);
            }
        }

        return $accessLevels;
    }

    /**
     * Validates the group data.
     * 
     * @access protected
     * @param \Zepi\Web\AccessControl\Entity\Group $group
     * @param string $groupname
     */
    protected function validateData(EntityGroup $group, $groupname)
    {
        $errors = array();
        
        // Groupname
        if ($this->groupManager->hasGroupForName($groupname) && $this->groupManager->getGroupForName($groupname)->getUuid() != $group->getUuid()) {
            $errors[] = new Error(Error::GENERAL_ERROR, $this->translate('The groupname is already in use.', '\\Zepi\\Web\\AccessControl'));
        }
        
        if (count($errors) > 0) {
            return $errors;
        }
        
        return true;
    }
}
