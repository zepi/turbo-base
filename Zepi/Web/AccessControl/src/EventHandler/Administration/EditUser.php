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
use \Zepi\Web\AccessControl\Entity\User;
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
use \Zepi\Web\AccessControl\Manager\UserManager;
use \Zepi\Core\AccessControl\Manager\AccessControlManager;
use \Zepi\Core\AccessControl\Manager\AccessLevelManager;
use \Zepi\Web\AccessControl\Helper\AccessLevelHelper;

/**
 * Displays the edit user form and saves the data to the database.
 * 
 * @author Matthias Zobrist <matthias.zobrist@zepi.net>
 * @copyright Copyright (c) 2015 zepi
 */
class EditUser extends FrontendEventHandler
{
    /**
     * @access protected
     * @var \Zepi\Web\AccessControl\Manager\UserManager
     */
    protected $userManager;
    
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
     * @param \Zepi\Web\AccessControl\Manager\UserManager $userManager
     * @param \Zepi\Core\AccessControl\Manager\AccessControlManager $accessControlManager
     * @param \Zepi\Core\AccessControl\Manager\AccessLevelManager $accessLevelManager
     * @param \Zepi\Web\AccessControl\Helper\AccessLevelHelper $accessLevelHelper
     */
    public function __construct(
        FrontendHelper $frontendHelper,
        UserManager $userManager,
        AccessControlManager $accessControlManager,
        AccessLevelManager $accessLevelManager,
        AccessLevelHelper $accessLevelHelper
    ) {
        $this->frontendHelper = $frontendHelper;
        $this->userManager = $userManager;
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
        
        $uuid = $request->getRouteParam(0);
        
        // If there is a request parameter we need to edit a user. Otherwise we create a new one.
        if (is_string($uuid)) {
            $additionalTitle = $this->translate('Modify user', '\\Zepi\\Web\\AccessControl');
            
            $user = $this->userManager->getUserForUuid($uuid);
        } else {
            $additionalTitle = $this->translate('Add user', '\\Zepi\\Web\\AccessControl');
            
            $user = new User('', '', '', '', array());
        }
        $title = $this->translate('User management', '\\Zepi\\Web\\AccessControl');
        
        // Prepare the page
        $this->activateMenuEntry('user-administration');
        $this->setTitle($title, $additionalTitle);
        
        // Get the form object
        $editUserLayout = $this->getLayout($framework, $request, $response, $user);
        $editUserForm = $editUserLayout->searchPartByKeyAndType('edit-user', '\\Zepi\\Web\\UserInterface\\Form\\Form');
        
        // Process the data
        $errorBox = $this->processData($editUserForm, $framework, $request, $response, $user);
        
        // If $result isn't true, display the edit user form
        if (!$editUserForm->isSubmitted() || $errorBox->hasErrors()) {
            $response->setOutput($this->render('\\Zepi\\Web\\AccessControl\\Templates\\Administration\\EditUserForm', array(
                'user' => $user,
                'title' => $this->getTitle(),
                'layout' => $editUserLayout,
                'layoutRenderer' => $this->getLayoutRenderer()
            )));
        } else {
            // Display the successful saved message
            $response->setOutput($this->render('\\Zepi\\Web\\AccessControl\\Templates\\Administration\\EditUserFinished', array(
                'title' => $this->getTitle()
            )));
        }
    }
    
    /**
     * Handle the save process
     *
     * @access protected
     * @param \Zepi\Web\UserInterface\Type\Form $editUserForm
     * @param \Zepi\Turbo\Framework $framework
     * @param \Zepi\Turbo\Request\WebRequest $request
     * @param \Zepi\Turbo\Response\Response $response
     * @param \Zepi\Web\AccessControl\Entity\User $user
     */
    protected function processData(Form $editUserForm, Framework $framework, WebRequest $request, Response $response, User $user)
    {
        // Process the submitted form data
        $editUserForm->processFormData($request);
        
        // Validate the data
        $result = false;
        $errors = array();
        if ($editUserForm->isSubmitted()) {
            $errors = $editUserForm->validateFormData($framework);
            if (count($errors) === 0) {
                $result = $this->saveUser($editUserForm, $framework, $request, $response, $user);
            } 
        }
        
        // Translate the result
        $errorBox = $editUserForm->getPart('edit-user-errors');
        $errorBox->updateErrorBox($editUserForm, $result, $errors);
    
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
     * @param \Zepi\Web\AccessControl\Entity\User $user
     */
    protected function saveUser(Form $form, Framework $framework, WebRequest $request, Response $response, User $user)
    {
        // Get the password data
        $group = $form->searchPartByKeyAndType('required-data');
        $username = trim($group->getPart('username')->getValue());
        $password = trim($group->getPart('password')->getValue());
        $passwordConfirmed = trim($group->getPart('password-confirmed')->getValue());
        
        $result = $this->validateData($framework, $user, $username, $password, $passwordConfirmed);
        
        // If the validate function returned a string there was an error in the validation.
        if ($result !== true) {
            return $result;
        }
        
        // Set the username
        $user->setName($username);
        
        // Set the password to a new user or if the user has changed the password
        if ($user->isNew() || $password != '') {
            $user->setNewPassword($password);
        }
        
        // Set the optional data
        $optionalDataGroup = $form->searchPartByKeyAndType('optional-data');
        foreach ($optionalDataGroup->getChildrenByType('\\Zepi\\Web\\UserInterface\\Form\\Field\\FieldAbstract') as $field) {
            $user->setMetaData($field->getKey(), $field->getValue());
        }

        // Save the user
        if ($user->isNew()) {
            $user = $this->userManager->addUser($user);
        } else {
            $this->userManager->updateUser($user);
        }
        
        if ($user === false) {
            return false;
        }
        
        // Save the access levels
        $accessLevelsElement = $form->searchPartByKeyAndType('access-levels');
        $accessLevels = $accessLevelsElement->getValue();
        
        $this->accessControlManager->updatePermissions($user, $accessLevels, $request->getSession()->getUser());
        
        return $result;
    }

    /**
     * Validates the data for the change password function.
     * 
     * @access protected
     * @param \Zepi\Turbo\Framework $framework
     * @param \Zepi\Web\AccessControl\Entity\User $user
     * @param string $username
     * @param string $password
     * @param string $passwordConfirmed
     */
    protected function validateData(Framework $framework, User $user, $username, $password, $passwordConfirmed)
    {
        // Username
        if ($this->userManager->hasUserForUsername($username)) {
            $foundUser = $this->userManager->getUserForUsername($username);
            
            if ($foundUser->getUuid() != $user->getUuid()) {
                return $this->translate('The username is already in use.', '\\Zepi\\Web\\AccessControl');
            }
        }        
        
        // If the user not is new and the password is empty, we return true because we 
        // don't need a password validation.
        if (!$user->isNew() && $password == '') {
            return true;
        }
        
        // Password
        if (strlen($password) < 8) {
            return $this->translate('The password needs at least 8 characters.', '\\Zepi\\Web\\AccessControl');
        }
        
        if ($password != $passwordConfirmed) {
            return $this->translate('The passwords are not equal.', '\\Zepi\\Web\\AccessControl');
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
     * @param \Zepi\Web\AccessControl\Entity\User $user
     * @return \Zepi\Web\UserInterface\Layout\Page
     */
    public function getLayout(Framework $framework, WebRequest $request, Response $response, User $user)
    {
        $accessLevelSelectorItems = $this->accessLevelHelper->transformAccessLevels(
            $this->accessLevelManager->getAccessLevels(),
            $request->getSession()->getUser()
        );
        
        $rawPermissionsForUuid = $this->accessControlManager->getPermissionsRawForUuid($user->getUuid());
        if ($rawPermissionsForUuid === false) {
            $rawPermissionsForUuid = array();
        }
        
        $page = new Page(
            array(
                new Form('edit-user', $request->getFullRoute(), 'post', array(
                    new ErrorBox(
                        'edit-user-errors'
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
                                                            'username',
                                                            $this->translate('Username', '\\Zepi\\Web\\AccessControl'),
                                                            true,
                                                            $user->getName(),
                                                            $this->translate('The username must be unique. Only one user can use an username.', '\\Zepi\\Web\\AccessControl')
                                                        ),
                                                        new Password(
                                                            'password',
                                                            $this->translate('Password', '\\Zepi\\Web\\AccessControl'),
                                                            $user->isNew()
                                                        ),
                                                        new Password(
                                                            'password-confirmed',
                                                            $this->translate('Confirm password', '\\Zepi\\Web\\AccessControl'),
                                                            $user->isNew()
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
                                                        new Text(
                                                            'email',
                                                            $this->translate('Email address', '\\Zepi\\Web\\AccessControl'),
                                                            false,
                                                            $user->getMetaData('email')
                                                        ),
                                                        new Text(
                                                            'location',
                                                            $this->translate('Location', '\\Zepi\\Web\\AccessControl'),
                                                            false,
                                                            $user->getMetaData('location')
                                                        ),
                                                        new Text(
                                                            'website',
                                                            $this->translate('Website', '\\Zepi\\Web\\AccessControl'),
                                                            false,
                                                            $user->getMetaData('website')
                                                        ),
                                                        new Text(
                                                            'twitter',
                                                            $this->translate('Twitter', '\\Zepi\\Web\\AccessControl'),
                                                            false,
                                                            $user->getMetaData('twitter')
                                                        ),
                                                        new Textarea(
                                                            'biography',
                                                            $this->translate('Biography', '\\Zepi\\Web\\AccessControl'),
                                                            false,
                                                            $user->getMetaData('biography')
                                                        )
                                                    ),
                                                    2
                                                )
                                            ), array('col-md-6')),
                                        )
                                    ),
                                ),
                                array(),
                                'group-tab',
                                $this->translate('User informations', '\\Zepi\\Web\\AccessControl')
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
                                            $request->getFullRoute('/administration/users/')
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
