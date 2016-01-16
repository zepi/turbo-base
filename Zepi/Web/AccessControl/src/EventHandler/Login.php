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
 * Authorizes an user with the user credentials.
 * 
 * @package Zepi\Web\AccessControl
 * @subpackage EventHandler
 * @author Matthias Zobrist <matthias.zobrist@zepi.net>
 * @copyright Copyright (c) 2015 zepi
 */

namespace Zepi\Web\AccessControl\EventHandler;

use \Zepi\Web\UserInterface\Frontend\FrontendEventHandler;
use \Zepi\Turbo\Framework;
use \Zepi\Turbo\Request\RequestAbstract;
use \Zepi\Turbo\Request\WebRequest;
use \Zepi\Turbo\Response\Response;
use \Zepi\Web\UserInterface\Form\Form;
use \Zepi\Web\UserInterface\Form\Group;
use \Zepi\Web\UserInterface\Form\ErrorBox;
use \Zepi\Web\UserInterface\Form\Error;
use \Zepi\Web\UserInterface\Form\ButtonGroup;
use \Zepi\Web\UserInterface\Form\Field\Text;
use \Zepi\Web\UserInterface\Form\Field\Password;
use \Zepi\Web\UserInterface\Form\Field\Hidden;
use \Zepi\Web\UserInterface\Form\Field\Submit;
use \Zepi\Web\UserInterface\Frontend\FrontendHelper;
use \Zepi\Web\AccessControl\Manager\SessionManager;
use \Zepi\Web\AccessControl\Manager\UserManager;

/**
 * Authorizes an user with the user credentials.
 * 
 * @author Matthias Zobrist <matthias.zobrist@zepi.net>
 * @copyright Copyright (c) 2015 zepi
 */
class Login extends FrontendEventHandler
{
    /**
     * @access protected
     * @var \Zepi\Web\AccessControl\Manager\SessionManager
     */
    protected $_sessionManager;
    
    /**
     * @access protected
     * @var \Zepi\Web\AccessControl\Manager\UserManager
     */
    protected $_userManager;
    
    /**
     * Constructs the object
     *
     * @access public
     * @param \Zepi\Web\UserInterface\Frontend\FrontendHelper $frontendHelper
     * @param \Zepi\Web\AccessControl\Manager\SessionManager $sessionManager
     * @param \Zepi\Web\AccessControl\Manager\UserManager $userManager
     */
    public function __construct(FrontendHelper $frontendHelper, SessionManager $sessionManager, UserManager $userManager)
    {
        $this->_frontendHelper = $frontendHelper;
        $this->_sessionManager = $sessionManager;
        $this->_userManager = $userManager;
    }
    
    /**
     * Filters the given menu entries and removes all protected menu
     * entries for which the sender hasn't the correct permission.
     * 
     * @access public
     * @param \Zepi\Turbo\Framework $framework
     * @param \Zepi\Turbo\Request\WebRequest $request
     * @param \Zepi\Turbo\Response\Response $response
     */
    public function execute(Framework $framework, WebRequest $request, Response $response)
    {
        // Redirect if the user already has a valid session
        if ($request->hasSession()) {
            $response->redirectTo('/', 307);
            return;
        }
        
        // Set the title for the page
        $this->setTitle($this->translate('Login', '\\Zepi\\Web\\AccessControl'));
        
        // Get the form object
        $loginForm = $this->_createForm($framework, $request, $response);
        
        // Process the submitted form data
        $loginForm->processFormData($request);
        
        // Validate the form data and authorize the user
        $result = false;
        $errors = array();
        if ($loginForm->isSubmitted()) {
            $errors = $loginForm->validateFormData($framework);
            if (count($errors) === 0) {
                $result = $this->_authorizeUser($loginForm, $framework, $request, $response);
            } 
        }
         
        // Fill the errors into the error box
        $errorBox = $loginForm->getPart('login-errors');
        if (($loginForm->isSubmitted() && $result !== true) || count($errors) > 0) {
            if (is_string($result)) {
                $errorBox->addError(new Error(
                    Error::GENERAL_ERROR,
                    $result
                ));
            } else if (count($errors) === 0) {
                $errorBox->addError(new Error(
                    Error::GENERAL_ERROR,
                    $this->translate('Your submitted data weren\'t correct. Please repeat the login with your correct user data or contact the administrator.', '\\Zepi\\Web\\AccessControl')
                ));
            } else {
                foreach ($errors as $error) {
                    $errorBox->addError($error);
                }
            }
        }
        
        // If $result isn't true, display the login form
        if (!$loginForm->isSubmitted() || $errorBox->hasErrors()) {
            $renderedOutput = $this->render('\\Zepi\\Web\\AccessControl\\Templates\\LoginForm', array(
                'result' => $result,
                'errors' => $errors,
                'form' => $loginForm, 
                'layoutRenderer' => $this->getLayoutRenderer(),
                'allowRegistration' => $this->getSetting('accesscontrol', 'allowRegistration'),
                'allowRenewPassword' => $this->getSetting('accesscontrol', 'allowRenewPassword'),
            ));
            
            $response->setOutput($renderedOutput);
        }
    }
    
    /**
     * Authorizes the user with his username and password. Initializes
     * the user session if the user data are valid.
     * 
     * @access protected
     * @param \Zepi\Web\UserInterface\Form\Form $loginForm
     * @param \Zepi\Turbo\Framework $framework
     * @param \Zepi\Turbo\Request\WebRequest $request
     * @param \Zepi\Turbo\Response\Response $response
     * @return string|boolean
     */
    protected function _authorizeUser(Form $loginForm, Framework $framework, WebRequest $request, Response $response)
    {
        $user = $this->_validateUserData($framework, $loginForm->getField('user-data', 'username')->getValue(), $loginForm->getField('user-data', 'password')->getValue());
        if ($user === false) {
            return false;
        }
        
        // If the user is disabled we cannot create a session
        if (!$user->hasAccess('\\Global\\*') && $user->hasAccess('\\Global\\Disabled')) {
            return $this->translate('Your user is disabled. Please contact the administrator.', '\\Zepi\\Web\\AccessControl');
        }
        
        // Initializes the user session
        $this->_sessionManager->initializeUserSession($request, $response, $user);
        
        // Redirect to the target or to the start page
        $target = '/';
        $origin = $loginForm->getField('user-data', 'origin')->getValue();
        if ($origin !== '') {
            $target = base64_decode($origin);
        }
        $response->redirectTo($target);
        
        return true;
    }
    
    /**
     * Validates the input user data
     * 
     * @param \Zepi\Turbo\Framework $framework
     * @param string $username
     * @param string $password
     * @return boolean|\Zepi\Web\AccessControl\Entity\User
     */
    protected function _validateUserData(Framework $framework, $username, $password)
    {
        // If the password isn't at least 8 characters long
        if (strlen($password) < 8) {
            return false;
        }
        
        // If the given username doesn't exists
        if (!$this->_userManager->hasUserForUsername($username)) {
            return false;
        }
        
        $user = $this->_userManager->getUserForUsername($username);
        
        // If the user not is usable
        if ($user === false) {
            return false;
        }
        
        // If the inserted password not is correct
        if (!$user->comparePasswords($password)) {
            return false;
        }
        
        // Everything is okey
        return $user;
    }

    /**
     * Returns the Form object for the login form
     * 
     * @access protected
     * @param \Zepi\Turbo\Framework $framework
     * @param \Zepi\Turbo\Request\WebRequest $request
     * @param \Zepi\Turbo\Response\Response $response
     * @return \Zepi\Web\UserInterface\Form\Form
     */
    protected function _createForm(Framework $framework, WebRequest $request, Response $response)
    {
        // Create the form
        $form = new Form('login', $request->getFullRoute('login'), 'post');
        
        // Add the user data group
        $errorBox = new ErrorBox(
            'login-errors',
            1
        );
        $form->addPart($errorBox);
        
        $origin = '';
        if ($request->hasParam('_origin')) {
            $origin = $request->getParam('_origin');
        }
        
        $helpText = '';
        if ($this->getSetting('accesscontrol', 'allowRenewPassword')) {
            $helpText = $this->translate('Lost your password? <a href="%link%">Renew it here.</a>', '\\Zepi\\Web\\AccessControl', array(
                'link' => $request->getFullRoute('request-new-password')
            ));
        }
        
        // Add the user data group
        $group = new Group(
            'user-data',
            $this->translate('User data', '\\Zepi\\Web\\AccessControl'),
            array(
                new Text(
                    'username',
                    $this->translate('Username', '\\Zepi\\Web\\AccessControl'),
                    true
                ),
                new Password(
                    'password',
                    $this->translate('Password', '\\Zepi\\Web\\AccessControl'),
                    true,
                    '',
                    $helpText
                ),
                new Hidden(
                    'origin',
                    $origin
                )
            ),
            10
        );
        $form->addPart($group);
        
        // Add the submit button
        $buttonGroup = new ButtonGroup(
            'buttons',
            array(
                new Submit(
                    'submit',
                    $this->translate('Login', '\\Zepi\\Web\\AccessControl')
                )
            ),
            100
        );
        $form->addPart($buttonGroup);
        
        return $form;
    }
}
