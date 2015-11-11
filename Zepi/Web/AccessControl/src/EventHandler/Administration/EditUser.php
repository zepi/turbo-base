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

use \Zepi\Turbo\FrameworkInterface\EventHandlerInterface;
use \Zepi\Turbo\Framework;
use \Zepi\Turbo\Request\RequestAbstract;
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

/**
 * Displays the edit user form and saves the data to the database.
 * 
 * @author Matthias Zobrist <matthias.zobrist@zepi.net>
 * @copyright Copyright (c) 2015 zepi
 */
class EditUser implements EventHandlerInterface
{
    /**
     * Displays the edit user form and saves the data to the database.
     * 
     * @access public
     * @param \Zepi\Turbo\Framework $framework
     * @param \Zepi\Turbo\Request\RequestAbstract $request
     * @param \Zepi\Turbo\Response\Response $response
     * @param mixed $value
     */
    public function executeEvent(Framework $framework, RequestAbstract $request, Response $response, $value = null)
    {
        // Redirect if the user hasn't a valid session
        if (!$request->hasSession() || !$request->getSession()->hasAccess('\\Zepi\\Web\\AccessControl\\AccessLevel\\EditUsersAndGroups')) {
            $response->redirectTo('/');
            return;
        }
        
        // Get the translation manager
        $translationManager = $framework->getInstance('\\Zepi\\Core\\Language\\Manager\\TranslationManager');
        $templatesManager = $framework->getInstance('\\Zepi\\Web\\General\\Manager\\TemplatesManager');
        
        // If there is a request parameter we need to edit a user. Otherwise we create a new one.
        if ($request->getRouteParam(0) !== false) {
            $additionalTitle = $translationManager->translate('Modify user', '\\Zepi\\Web\\AccessControl');
            
            $userManager = $framework->getInstance('\\Zepi\\Web\\AccessControl\\Manager\\UserManager');
            $user = $userManager->getUserForUuid($request->getRouteParam(0));
        } else {
            $additionalTitle = $translationManager->translate('Add user', '\\Zepi\\Web\\AccessControl');
            
            $user = new User('', '', '', '', array());
        }
        $title = $translationManager->translate('User management', '\\Zepi\\Web\\AccessControl') . ' - ' . $additionalTitle;
        
        // Activate the correct menu entry and add the breadcrumb function entry
        $menuManager = $framework->getInstance('\\Zepi\\Web\\General\\Manager\\MenuManager');
        $menuManager->setActiveMenuEntry($menuManager->getMenuEntryForKey('user-administration'));
        $menuManager->setBreadcrumbFunction($additionalTitle);
        
        // Set the title for the page
        $metaInformationManager = $framework->getInstance('\\Zepi\\Web\\General\\Manager\\MetaInformationManager');
        $metaInformationManager->setTitle($title);
        
        // Get the Form Renderer
        $layoutRenderer = $framework->getInstance('\\Zepi\\Web\\UserInterface\\Renderer\\Layout');
        
        // Get the form object
        $editUserLayout = $this->_getLayout($framework, $request, $response, $user);
        $editUserForm = $editUserLayout->searchPartByKeyAndType('edit-user', '\\Zepi\\Web\\UserInterface\\Form\\Form');
        
        // Process the data
        $errorBox = $this->_processData($editUserForm, $framework, $request, $response, $user);
        
        // If $result isn't true, display the edit user form
        if (!$editUserForm->isSubmitted() || $errorBox->hasErrors()) {
            $response->setOutput($templatesManager->renderTemplate('\\Zepi\\Web\\AccessControl\\Templates\\Administration\\EditUserForm', array(
                'user' => $user,
                'title' => $title,
                'layout' => $editUserLayout,
                'layoutRenderer' => $layoutRenderer
            )));
        } else {
            // Display the successful saved message
            $response->setOutput($templatesManager->renderTemplate('\\Zepi\\Web\\AccessControl\\Templates\\Administration\\EditUserFinished', array(
                'title' => $title
            )));
        }
    }
    
    /**
     * Handle the save process
     *
     * @access protected
     * @param \Zepi\Web\UserInterface\Type\Form $editUserForm
     * @param \Zepi\Turbo\Framework $framework
     * @param \Zepi\Turbo\Request\RequestAbstract $request
     * @param \Zepi\Turbo\Response\Response $response
     * @param \Zepi\Web\AccessControl\Entity\User $user
     */
    protected function _processData(Form $editUserForm, Framework $framework, RequestAbstract $request, Response $response, User $user)
    {
        // Process the submitted form data
        $editUserForm->processFormData($request);
        
        // Validate the data
        $result = false;
        $errors = array();
        if ($editUserForm->isSubmitted()) {
            $errors = $editUserForm->validateFormData($framework);
            if (count($errors) === 0) {
                $result = $this->_saveUser($editUserForm, $framework, $request, $response, $user);
            } 
        }
        
        // Translate the result
        $errorBox = $editUserForm->getPart('edit-user-errors');
        if (($editUserForm->isSubmitted() && $result !== true) || count($errors) > 0) {
            if (is_string($result)) {
                $errorBox->addError(new Error(
                    Error::GENERAL_ERROR,
                    $result
                ));
            } else if (count($errors) === 0) {
                $errorBox->addError(new Error(
                    Error::GENERAL_ERROR,
                    $translationManager->translate('Your submitted data weren\'t correct. Please repeat the login with your correct user data or contact the administrator.', '\\Zepi\\Web\\AccessControl')
                ));
            } else {
                foreach ($errors as $error) {
                    $errorBox->addError($error);
                }
            }
        }
    
        return $errorBox;
    }

    /**
     * Changes the password for the logged in user.
     * 
     * @access protected
     * @param \Zepi\Web\UserInterface\Type\Form $form
     * @param \Zepi\Turbo\Framework $framework
     * @param \Zepi\Turbo\Request\RequestAbstract $request
     * @param \Zepi\Turbo\Response\Response $response
     * @param \Zepi\Web\AccessControl\Entity\User $user
     */
    protected function _saveUser(Form $form, Framework $framework, RequestAbstract $request, Response $response, User $user)
    {
        // Get the password data
        $group = $form->searchPartByKeyAndType('required-data');
        $username = trim($group->getPart('username')->getValue());
        $password = trim($group->getPart('password')->getValue());
        $passwordConfirmed = trim($group->getPart('password-confirmed')->getValue());
        
        $result = $this->_validateData($framework, $user, $username, $password, $passwordConfirmed);
        
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
        $userManager = $framework->getInstance('\\Zepi\\Web\\AccessControl\\Manager\\UserManager');
        
        if ($user->isNew()) {
            $user = $userManager->addUser($user);
        } else {
            $userManager->updateUser($user);
        }
        
        // Save the access levels
        $accessLevelsElement = $form->searchPartByKeyAndType('access-levels');
        $accessLevels = $accessLevelsElement->getValue();
        
        $accessControlManager = $framework->getInstance('\\Zepi\\Core\\AccessControl\\Manager\\AccessControlManager');
        $accessControlManager->updatePermissions($user->getUuid(), $accessLevels, $request->getSession()->getUser());
        
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
    protected function _validateData(Framework $framework, User $user, $username, $password, $passwordConfirmed)
    {
        $translationManager = $framework->getInstance('\\Zepi\\Core\\Language\\Manager\\TranslationManager');
        $userManager = $framework->getInstance('\\Zepi\\Web\\AccessControl\\Manager\\UserManager');
        
        // Username
        if ($userManager->hasUserForUsername($username)) {
            $foundUser = $userManager->getUserForUsername($username);
            
            if ($foundUser->getUuid() != $user->getUuid()) {
                return $translationManager->translate('The username is already in use.', '\\Zepi\\Web\\AccessControl');
            }
        }        
        
        // If the user not is new and the password is empty, we return true because we 
        // don't need a password validation.
        if (!$user->isNew() && $password == '') {
            return true;
        }
        
        // Password
        if (strlen($password) < 8) {
            return $translationManager->translate('The password needs at least 8 characters.', '\\Zepi\\Web\\AccessControl');
        }
        
        if ($password != $passwordConfirmed) {
            return $translationManager->translate('The passwords are not equal.', '\\Zepi\\Web\\AccessControl');
        }

        return true;
    }

    /**
     * Returns the layout for the form.
     * 
     * @access public
     * @param \Zepi\Turbo\Framework $framework
     * @param \Zepi\Turbo\Request\RequestAbstract $request
     * @param \Zepi\Turbo\Response\Response $response
     * @param \Zepi\Web\AccessControl\Entity\User $user
     * @return \Zepi\Web\UserInterface\Layout\Page
     */
    public function _getLayout(Framework $framework, RequestAbstract $request, Response $response, User $user)
    {
        $accessControlManager = $framework->getInstance('\\Zepi\\Core\\AccessControl\\Manager\\AccessControlManager');
        $accessLevelManager = $framework->getInstance('\\Zepi\\Core\\AccessControl\\Manager\\AccessLevelManager');
        $translationManager = $framework->getInstance('\\Zepi\\Core\\Language\\Manager\\TranslationManager');
        $accessLevelHelper = $framework->getInstance('\\Zepi\\Web\\AccessControl\\Helper\\AccessLevelHelper');
        
        $accessLevelSelectorItems = $accessLevelHelper->transformAccessLevels(
                $accessLevelManager->getAccessLevels(),
                $request->getSession()->getUser()
        );
        
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
                                                    $translationManager->translate('Required data', '\\Zepi\\Web\\AccessControl'),
                                                    array(
                                                        new Text(
                                                            'username',
                                                            $translationManager->translate('Username', '\\Zepi\\Web\\AccessControl'),
                                                            true,
                                                            $user->getName(),
                                                            $translationManager->translate('The username must be unique. Only one user can use an username.', '\\Zepi\\Web\\AccessControl')
                                                        ),
                                                        new Password(
                                                            'password',
                                                            $translationManager->translate('Password', '\\Zepi\\Web\\AccessControl'),
                                                            $user->isNew()
                                                        ),
                                                        new Password(
                                                            'password-confirmed',
                                                            $translationManager->translate('Confirm password', '\\Zepi\\Web\\AccessControl'),
                                                            $user->isNew()
                                                        ),
                                                    ),
                                                    1
                                                )
                                            ), array('col-md-12')),
                                            new Column(array(
                                                new Group(
                                                    'optional-data',
                                                    $translationManager->translate('Optional data', '\\Zepi\\Web\\AccessControl'),
                                                    array(
                                                        new Text(
                                                            'email',
                                                            $translationManager->translate('Email address', '\\Zepi\\Web\\AccessControl'),
                                                            false,
                                                            $user->getMetaData('email')
                                                        ),
                                                        new Text(
                                                            'location',
                                                            $translationManager->translate('Location', '\\Zepi\\Web\\AccessControl'),
                                                            false,
                                                            $user->getMetaData('location')
                                                        ),
                                                        new Text(
                                                            'website',
                                                            $translationManager->translate('Website', '\\Zepi\\Web\\AccessControl'),
                                                            false,
                                                            $user->getMetaData('website')
                                                        ),
                                                        new Text(
                                                            'twitter',
                                                            $translationManager->translate('Twitter', '\\Zepi\\Web\\AccessControl'),
                                                            false,
                                                            $user->getMetaData('twitter')
                                                        ),
                                                        new Textarea(
                                                            'biography',
                                                            $translationManager->translate('Biography', '\\Zepi\\Web\\AccessControl'),
                                                            false,
                                                            $user->getMetaData('biography')
                                                        )
                                                    ),
                                                    2
                                                )
                                            ), array('col-md-12')),
                                        )
                                    ),
                                ),
                                array(),
                                'group-tab',
                                $translationManager->translate('Gruppeninformationen', '\\Zepi\\Web\\AccessControl')
                            ),
                            new Tab(
                                array(
                                    new Selector(
                                        'access-levels',
                                        $translationManager->translate('Access Level Selector', '\\Zepi\\Web\\AccessControl'),
                                        false,
                                        $accessControlManager->getPermissionsRaw($user->getUuid()),
                                        $accessLevelSelectorItems,
                                        $translationManager->translate('Available Access Levels', '\\Zepi\\Web\\AccessControl'),
                                        $translationManager->translate('Granted Access Levels', '\\Zepi\\Web\\AccessControl'),
                                        '\\Zepi\\Web\\AccessControl\\Templates\\Form\\Snippet\\AccessLevel'
                                    ),
                                ),
                                array(),
                                'access-tab',
                                $translationManager->translate('Berechtigungen', '\\Zepi\\Web\\AccessControl')
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
                                            $translationManager->translate('Back', '\\Zepi\\Web\\AccessControl'),
                                            array('btn-default'),
                                            '',
                                            'a',
                                            $request->getFullRoute('/administration/users/')
                                        )
                                    ),
                                    1000,
                                    'text-left'
                                )
                            ), array('col-md-6')),
                            new Column(array(
                                new ButtonGroup(
                                    'buttons',
                                    array(
                                        new Submit(
                                            'submit',
                                            $translationManager->translate('Save', '\\Zepi\\Web\\AccessControl'), 
                                            array('btn-large', 'btn-primary'),
                                            'mdi mdi-floppy'
                                        )
                                    ),
                                    1000
                                )
                            ), array('col-md-12'))
                        )
                    )
                ))
            )
        );
        
        return $page;
    }
}
