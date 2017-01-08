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
use \Zepi\Web\AccessControl\Entity\User;
use \Zepi\Web\UserInterface\Frontend\FrontendHelper;
use \Zepi\Web\UserInterface\Form\Form;
use \Zepi\Web\UserInterface\Form\Error;
use \Zepi\Web\AccessControl\Manager\UserManager;
use \Zepi\Core\AccessControl\Manager\AccessControlManager;
use \Zepi\Core\AccessControl\Manager\AccessLevelManager;
use \Zepi\Web\AccessControl\Layout\EditUserLayout;

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
     * @var \Zepi\Web\AccessControl\Layout\EditUserLayout
     */
    protected $layout;
    
    /**
     * Constructs the object
     *
     * @access public
     * @param \Zepi\Web\UserInterface\Frontend\FrontendHelper $frontendHelper
     * @param \Zepi\Web\AccessControl\Manager\UserManager $userManager
     * @param \Zepi\Core\AccessControl\Manager\AccessControlManager $accessControlManager
     * @param \Zepi\Core\AccessControl\Manager\AccessLevelManager $accessLevelManager
     * @param \Zepi\Web\AccessControl\Layout\EditUserLayout $layout
     */
    public function __construct(
        FrontendHelper $frontendHelper,
        UserManager $userManager,
        AccessControlManager $accessControlManager,
        AccessLevelManager $accessLevelManager,
        EditUserLayout $layout
    ) {
        $this->frontendHelper = $frontendHelper;
        $this->userManager = $userManager;
        $this->accessControlManager = $accessControlManager;
        $this->accessLevelManager = $accessLevelManager;
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
        $uuid = $request->getRouteParam('uuid');
        
        // If there is a request parameter we need to edit a user. Otherwise we create a new one.
        if (is_string($uuid)) {
            $additionalTitle = $this->translate('Modify user', '\\Zepi\\Web\\AccessControl');
            
            $user = $this->userManager->getUserForUuid($uuid);
        } else {
            $additionalTitle = $this->translate('Add user', '\\Zepi\\Web\\AccessControl');
            
            $user = new User('', '', '', '', array());
        }
        $title = $this->translate('User management', '\\Zepi\\Web\\AccessControl');
        $this->layout->setUser($user);
        
        // Prepare the page
        $this->activateMenuEntry('user-administration');
        $this->setTitle($title, $additionalTitle);
        
        // Process the data
        $result = $this->processFormData($request, $response, $user);
        if ($result === true) {
            // Display the successful saved message
            $response->setOutput($this->render('\\Zepi\\Web\\AccessControl\\Templates\\Administration\\EditUserFinished', array(
                'title' => $this->getTitle()
            )));
        } else {
            // Display the form
            $response->setOutput($this->render('\\Zepi\\Web\\AccessControl\\Templates\\Administration\\EditUserForm', array(
                'user' => $user,
                'title' => $this->getTitle(),
                'layout' => $this->layout->getLayout(),
                'layoutRenderer' => $this->getLayoutRenderer()
            )));
        }
    }
    
    /**
     * Validates the form data, updates the user and saves the
     * user into the database.
     * 
     * @param \Zepi\Turbo\Request\WebRequest $request
     * @param \Zepi\Turbo\Response\Response $response
     * @param \Zepi\Web\AccessControl\Entity\User $user
     * @return boolean|string
     */
    protected function processFormData(WebRequest $request, Response $response, User $user)
    {
        $result = $this->layout->validateFormData($request, $response, function ($formValues) use ($user) {
            return $this->validateData(
                $user, 
                $formValues['required-data.username'],
                $formValues['required-data.password'],
                $formValues['required-data.password-confirmed']
            );
        });
        
        if ($result == Form::DATA_VALID) {
            $result = $this->saveUser($request, $user);
        }
        
        return $result;
    }
    
    /**
     * Changes the password for the logged in user.
     * 
     * @access protected
     * @param \Zepi\Turbo\Request\WebRequest $request
     * @param \Zepi\Web\AccessControl\Entity\User $user
     */
    protected function saveUser(WebRequest $request, User $user)
    {
        $formValues = $this->layout->getFormValues();
        
        // Set the username
        $user->setName($formValues['required-data.username']);
        
        // Set the password to a new user or if the user has changed the password
        if ($user->isNew() || $formValues['required-data.password'] != '') {
            $user->setNewPassword($formValues['required-data.password']);
        }
        
        // Set the optional data
        $user->setMetaData('email', $formValues['optional-data.email']);
        $user->setMetaData('location', $formValues['optional-data.location']);
        $user->setMetaData('website', $formValues['optional-data.website']);
        $user->setMetaData('twitter', $formValues['optional-data.twitter']);
        $user->setMetaData('biography', $formValues['optional-data.biography']);

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
        $this->accessControlManager->updatePermissions($user, $formValues['access-levels'], $request->getSession()->getUser());
        
        return true;
    }

    /**
     * Validates the data for the change password function.
     * 
     * @access protected
     * @param \Zepi\Web\AccessControl\Entity\User $user
     * @param string $username
     * @param string $password
     * @param string $passwordConfirmed
     */
    protected function validateData(User $user, $username, $password, $passwordConfirmed)
    {
        $errors = array();
        
        // Username
        if ($this->isUsernameInUse($username, $user)) {
            $errors[] = new Error(Error::GENERAL_ERROR, $this->translate('The username is already in use.', '\\Zepi\\Web\\AccessControl'));
        }        
        
        // If the user is new or the password is not empty we need to validate
        // the password.
        if ($this->shouldValidatePassword($user, $password)) {
            // Password
            if (strlen($password) < 8) {
                $errors[] = new Error(Error::WRONG_INPUT, $this->translate('The password needs at least 8 characters.', '\\Zepi\\Web\\AccessControl'));
            }
            
            if ($password != $passwordConfirmed) {
                $errors[] = new Error(Error::WRONG_INPUT, $this->translate('The passwords are not equal.', '\\Zepi\\Web\\AccessControl'));
            }
        }
        
        if (count($errors) > 0) {
            return $errors;
        }
        
        return true;
    }
    
    /**
     * Returns true if the username is in use and not is the edited user.
     * 
     * @param string $username
     * @param \Zepi\Web\AccessControl\Entity\User $user
     * @return boolean
     */
    protected function isUsernameInUse($username, User $user)
    {
        return ($this->userManager->hasUserForUsername($username) && $this->userManager->getUserForUsername($username)->getUuid() != $user->getUuid());
    }
    
    /**
     * Returns true if the password should be validated.
     * 
     * @param \Zepi\Web\AccessControl\Entity\User $user
     * @param string $password
     * @return boolean
     */
    protected function shouldValidatePassword(User $user, $password)
    {
        return ($user->isNew() || $password != '');
    }
}
