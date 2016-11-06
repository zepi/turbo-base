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
use \Zepi\Turbo\Request\RequestAbstract;
use \Zepi\Turbo\Request\WebRequest;
use \Zepi\Turbo\Response\Response;
use \Zepi\Web\AccessControl\Entity\Group as EntityGroup;
use \Zepi\Web\AccessControl\Entity\GroupAccessLevel;
use \Zepi\Web\UserInterface\Form\Form;
use \Zepi\Web\UserInterface\Form\Group;
use \Zepi\Web\UserInterface\Form\ErrorBox;
use \Zepi\Web\UserInterface\Form\Error;
use \Zepi\Web\UserInterface\Form\ButtonGroup;
use \Zepi\Web\UserInterface\Form\Field\Text;
use \Zepi\Web\UserInterface\Form\Field\Textarea;
use \Zepi\Web\UserInterface\Form\Field\Password;
use \Zepi\Web\UserInterface\Form\Field\Button;
use \Zepi\Web\UserInterface\Form\Field\Submit;
use \Zepi\Web\UserInterface\Form\Field\Selector;
use \Zepi\Web\UserInterface\Layout\AbstractContainer;
use \Zepi\Web\UserInterface\Layout\Page;
use \Zepi\Web\UserInterface\Layout\Part;
use \Zepi\Web\UserInterface\Layout\Tabs;
use \Zepi\Web\UserInterface\Layout\Tab;
use \Zepi\Web\UserInterface\Layout\Row;
use \Zepi\Web\UserInterface\Layout\Column;
use \Zepi\Web\UserInterface\Frontend\FrontendHelper;
use \Zepi\Web\AccessControl\Manager\GroupManager;
use \Zepi\Core\AccessControl\Manager\AccessControlManager;
use \Zepi\Core\AccessControl\Manager\AccessLevelManager;
use \Zepi\Web\AccessControl\Helper\AccessLevelHelper;

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
     * @access protected
     * @var \Zepi\Web\AccessControl\Helper\AccessLevelHelper
     */
    protected $accessLevelHelper;
    
    /**
     * Constructs the object
     * 
     * @access public
     * @param \Zepi\Web\UserInterface\Frontend\FrontendHelper $frontendHelper
     * @param \Zepi\Web\AccessControl\Manager\GroupManager $groupManager
     * @param \Zepi\Core\AccessControl\Manager\AccessControlManager $accessControlManager
     * @param \Zepi\Core\AccessControl\Manager\AccessLevelManager $accessLevelManager
     * @param \Zepi\Web\AccessControl\Helper\AccessLevelHelper $accessLevelHelper
     */
    public function __construct(
        FrontendHelper $frontendHelper, 
        GroupManager $groupManager, 
        AccessControlManager $accessControlManager, 
        AccessLevelManager $accessLevelManager, 
        AccessLevelHelper $accessLevelHelper
    ) {
        $this->frontendHelper = $frontendHelper;
        $this->groupManager = $groupManager;
        $this->accessControlManager = $accessControlManager;
        $this->accessLevelManager = $accessLevelManager;
        $this->accessLevelHelper = $accessLevelHelper;
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
        
        // Prepare the page
        $this->activateMenuEntry('group-administration');
        $this->setTitle($title, $additionalTitle);
        
        // Get the form object
        $editGroupLayout = $this->getLayout($framework, $request, $response, $group);
        $editGroupForm = $editGroupLayout->searchPartByKeyAndType('edit-group', '\\Zepi\\Web\\UserInterface\\Form\\Form');
        
        // Process the data
        $errorBox = $this->processData($editGroupForm, $framework, $request, $response, $group);
        
        // If $result isn't true, display the edit user form
        if (!$editGroupForm->isSubmitted() || $errorBox->hasErrors()) {
            $response->setOutput($this->render('\\Zepi\\Web\\AccessControl\\Templates\\Administration\\EditGroupForm', array(
                'user' => $group,
                'title' => $this->getTitle(),
                'layout' => $editGroupLayout,
                'layoutRenderer' => $this->getLayoutRenderer()
            )));
        } else {
            // Display the successful saved message
            $response->setOutput($this->render('\\Zepi\\Web\\AccessControl\\Templates\\Administration\\EditGroupFinished', array(
                'title' => $this->getTitle()
            )));
        }
    }

    /**
     * Handle the save process
     *
     * @access protected
     * @param \Zepi\Web\UserInterface\Type\Form $editGroupForm
     * @param \Zepi\Turbo\Framework $framework
     * @param \Zepi\Turbo\Request\WebRequest $request
     * @param \Zepi\Turbo\Response\Response $response
     * @param \Zepi\Web\AccessControl\Entity\Group $group
     */
    protected function processData(Form $editGroupForm, Framework $framework, WebRequest $request, Response $response, EntityGroup $group)
    {
        // Process the submitted form data
        $editGroupForm->processFormData($request);
        
        // Validate the data
        $result = false;
        $errors = array();
        if ($editGroupForm->isSubmitted()) {
            $errors = $editGroupForm->validateFormData($framework);
            if (count($errors) === 0) {
                $result = $this->saveGroup($editGroupForm, $framework, $request, $response, $group);
            }
        }
        
        // Translate the result
        $errorBox = $editGroupForm->getPart('edit-group-errors');
        $errorBox->updateErrorBox($editGroupForm, $result, $errors);
        
        return $errorBox;
    }
    
    /**
     * Changes the password for the logged in user.
     * 
     * @access protected
     * @param \Zepi\Web\UserInterface\Type\Form $form
     * @param \Zepi\Turbo\Framework $framework
     * @param \Zepi\Turbo\Request\WebRequest $request
     * @param \Zepi\Turbo\Response\Response $response
     * @param \Zepi\Web\AccessControl\Entity\Group $group
     */
    protected function saveGroup(Form $form, Framework $framework, WebRequest $request, Response $response, EntityGroup $group)
    {
        // Get the password data
        $formGroup = $form->searchPartByKeyAndType('required-data');
        $groupname = trim($formGroup->getPart('groupname')->getValue());
        
        $result = $this->validateData($framework, $group, $groupname);
        
        // If the validate function returned a string there was an error in the validation.
        if ($result !== true) {
            return $result;
        }
        
        // Set the username
        $group->setName($groupname);
        
        // Set the optional data
        $optionalDataGroup = $form->searchPartByKeyAndType('optional-data');
        foreach ($optionalDataGroup->getChildrenByType('\\Zepi\\Web\\UserInterface\\Form\\Field\\FieldAbstract') as $field) {
            $group->setMetaData($field->getKey(), $field->getValue());
        }
        
        // Save the user
        if ($group->isNew()) {
            $group = $this->groupManager->addGroup($group);
        } else {
            $this->groupManager->updateGroup($group);
        }
        
        if ($group === false) {
            return false;
        }
        
        // Save the access levels
        $accessLevelsElement = $form->searchPartByKeyAndType('access-levels');
        $accessLevels = $this->cleanAccessLevels($group->getUuid(), $accessLevelsElement->getValue());

        $this->accessControlManager->updatePermissions($group, $accessLevels, $request->getSession()->getUser());
        
        return $result;
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
     * Validates the data for the change password function.
     * 
     * @access protected
     * @param \Zepi\Turbo\Framework $framework
     * @param \Zepi\Web\AccessControl\Entity\Group $group
     * @param string $groupname
     */
    protected function validateData(Framework $framework, EntityGroup $group, $groupname)
    {
        // Groupname
        if ($this->groupManager->hasGroupForName($groupname)) {
            $foundGroup = $this->groupManager->getGroupForName($groupname);
            
            if ($foundGroup->getUuid() != $group->getUuid()) {
                return $this->translate('The groupname is already in use.', '\\Zepi\\Web\\AccessControl');
            }
        }        
        
        return true;
    }

    /**
     * Returns the layout for the form.
     * 
     * @access public
     * @param \Zepi\Turbo\Framework $framework
     * @param \Zepi\Turbo\Request\WebRequest $request
     * @param \Zepi\Turbo\Response\Response $response
     * @param \Zepi\Web\AccessControl\Entity\Group $group
     * @return \Zepi\Web\UserInterface\Layout\Page
     */
    protected function getLayout(Framework $framework, WebRequest $request, Response $response, EntityGroup $group)
    {
        $accessLevelSelectorItems = $this->accessLevelHelper->transformAccessLevels(
            $this->accessLevelManager->getAccessLevels(), 
            $request->getSession()->getUser(), 
            $group
        );
        
        $rawPermissionsForUuid = $this->accessControlManager->getPermissionsRawForUuid($group->getUuid());
        if ($rawPermissionsForUuid === false) {
            $rawPermissionsForUuid = array();
        }
        
        $page = new Page(
            array(
                new Form('edit-group', $request->getFullRoute(), 'post', array(
                    new ErrorBox(
                        'edit-group-errors'
                    ),
                    new Tabs(
                        array(
                            new Tab(
                                array(
                                    new Row(
                                        array(
                                            new Column(array(
                                                new Group(
                                                    'required-data',
                                                    $this->translate('Required data', '\\Zepi\\Web\\AccessControl'),
                                                    array(
                                                        new Text(
                                                            'groupname',
                                                            $this->translate('Group name', '\\Zepi\\Web\\AccessControl'),
                                                            true,
                                                            $group->getName(),
                                                            $this->translate('The group name must be unique. Only one group can use a group name.', '\\Zepi\\Web\\AccessControl')
                                                        ),
                                                    ),
                                                    1
                                                )
                                            ), array('col-md-6')),
                                            new Column(array(
                                                new Group(
                                                    'optional-data',
                                                    $this->translate('Optional data', '\\Zepi\\Web\\AccessControl'),
                                                    array(
                                                        new Textarea(
                                                            'description',
                                                            $this->translate('Description', '\\Zepi\\Web\\AccessControl'),
                                                            false,
                                                            $group->getMetaData('description')
                                                        ),
                                                    ),
                                                    2
                                                )
                                            ), array('col-md-6')),
                                        )
                                    ),
                                ),
                                array(),
                                'group-tab',
                                $this->translate('Group informations', '\\Zepi\\Web\\AccessControl')
                            ),
                            new Tab(
                                array(
                                    new Selector(
                                        'access-levels',
                                        $this->translate('Access Level Selector', '\\Zepi\\Web\\AccessControl'),
                                        false,
                                        $rawPermissionsForUuid,
                                        $accessLevelSelectorItems,
                                        $this->translate('Available Access Levels', '\\Zepi\\Web\\AccessControl'),
                                        $this->translate('Granted Access Levels', '\\Zepi\\Web\\AccessControl'),
                                        '\\Zepi\\Web\\AccessControl\\Templates\\Form\\Snippet\\AccessLevel'
                                    ),
                                ),
                                array(),
                                'access-tab',
                                $this->translate('Permissions', '\\Zepi\\Web\\AccessControl')
                            )
                        )        
                    ),
                    new Row(
                        array(
                            new Column(array(
                                new ButtonGroup(
                                    'buttons-left',
                                    array(
                                        new Button(
                                            'back',
                                            $this->translate('Back', '\\Zepi\\Web\\AccessControl'),
                                            array('btn-default'),
                                            '',
                                            'a',
                                            $request->getFullRoute('/administration/groups/')
                                        )
                                    ),
                                    1000,
                                    array('text-left')
                                )
                            ), array('col-md-4')),
                            new Column(array(
                                new ButtonGroup(
                                    'buttons',
                                    array(
                                        new Submit(
                                            'submit',
                                            $this->translate('Save', '\\Zepi\\Web\\AccessControl'), 
                                            array('btn-large', 'btn-primary'),
                                            'mdi mdi-floppy'
                                        )
                                    ),
                                    1000
                                )
                            ), array('col-md-4'))
                        )
                    )
                ))
            )
        );
        
        return $page;
    }
}
