<?php
/**
 * Event handler to handle the registration process
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
use \Zepi\Web\UserInterface\Form\Field\Checkbox;
use \Zepi\Web\UserInterface\Form\Field\Submit;
use \Zepi\Web\UserInterface\Frontend\FrontendHelper;
use \Zepi\Web\AccessControl\Entity\User;
use \Zepi\Web\AccessControl\Manager\UserManager;
use \Zepi\Core\AccessControl\Manager\AccessControlManager;
use \Zepi\Web\Mail\Helper\MailHelper;

/**
 * Event handler to handle the registration process
 * 
 * @author Matthias Zobrist <matthias.zobrist@zepi.net>
 * @copyright Copyright (c) 2015 zepi
 */
class Registration extends FrontendEventHandler
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
     * @var \Zepi\Web\Mail\Helper\MailHelper
     */
    protected $mailHelper;
    
    /**
     * Constructs the object
     *
     * @access public
     * @param \Zepi\Web\UserInterface\Frontend\FrontendHelper $frontendHelper
     * @param \Zepi\Web\AccessControl\Manager\UserManager $userManager
     * @param \Zepi\Core\AccessControl\Manager\AccessControlManager $accessControlManager
     * @param \Zepi\Web\Mail\Helper\MailHelper $mailHelper
     */
    public function __construct(FrontendHelper $frontendHelper, UserManager $userManager, AccessControlManager $accessControlManager, MailHelper $mailHelper)
    {
        $this->frontendHelper = $frontendHelper;
        $this->userManager = $userManager;
        $this->accessControlManager = $accessControlManager;
        $this->mailHelper = $mailHelper;
    }
    
    /**
     * Handles the whole registration process
     * 
     * @access public
     * @param \Zepi\Turbo\Framework $framework
     * @param \Zepi\Turbo\Request\WebRequest $request
     * @param \Zepi\Turbo\Response\Response $response
     */
    public function execute(Framework $framework, WebRequest $request, Response $response)
    {
        // Set the title for the page
        $this->setTitle($this->translate('Registration', '\\Zepi\\Web\\AccessControl'));
        
        // Get the form object
        $registrationForm = $this->createForm($framework, $request, $response);
        
        // Process the submitted form data
        $registrationForm->processFormData($request);
        
        // Validate the form data and authorize the user
        $result = false;
        $errors = array();
        if ($registrationForm->isSubmitted()) {
            $errors = $registrationForm->validateFormData($framework);
            if (count($errors) === 0) {
                $result = $this->createUser($registrationForm, $framework, $request, $response);
            } 
        }
         
        // Fill the errors into the error box
        $errorBox = $registrationForm->getPart('register-errors');
        if (($registrationForm->isSubmitted() && !$result) || count($errors) > 0) {
            if (is_string($result)) {
                $errorBox->addError(new Error(
                    Error::GENERAL_ERROR,
                    $result
                ));
            } else if (count($errors) === 0) {
                $errorBox->addError(new Error(
                    Error::GENERAL_ERROR,
                    $this->translate('Your submitted data weren\'t correct. Please repeat the registration or contact the administrator.', '\\Zepi\\Web\\AccessControl')
                ));
            } else {
                foreach ($errors as $error) {
                    $errorBox->addError($error);
                }
            }
        }
        
        // If $result isn't true, display the login form
        if (!$registrationForm->isSubmitted() || $errorBox->hasErrors()) {
            $renderedOutput = $this->render('\\Zepi\\Web\\AccessControl\\Templates\\RegistrationForm', array(
                'result' => $result,
                'errors' => $errors,
                'form' => $registrationForm, 
                'layoutRenderer' => $this->getLayoutRenderer()
            ));
            
            $response->setOutput($renderedOutput);
        } else {
            // Display the successful saved message
            $response->setOutput($this->render('\\Zepi\\Web\\AccessControl\\Templates\\RegistrationFinished', array(
                    'title' => $this->getTitle()
            )));
        }
    }
    
    /**
     * Authorizes the user with his username and password. Initializes
     * the user session if the user data are valid.
     * 
     * @access protected
     * @param \Zepi\Web\UserInterface\Form\Form $registrationForm
     * @param \Zepi\Turbo\Framework $framework
     * @param \Zepi\Turbo\Request\RequestAbstract $request
     * @param \Zepi\Turbo\Response\Response $response
     * @return string|boolean
     */
    protected function createUser(Form $registrationForm, Framework $framework, RequestAbstract $request, Response $response)
    {
        $group = $registrationForm->searchPartByKeyAndType('user-data');
        $username = trim($group->getPart('username')->getValue());
        $email = trim($group->getPart('email')->getValue());
        $emailConfirmed = trim($group->getPart('email-confirmed')->getValue());
        $password = trim($group->getPart('password')->getValue());
        $passwordConfirmed = trim($group->getPart('password-confirmed')->getValue());
        $tos = $group->getPart('tos-accepted')->getValue();
        
        $result = $this->validateData($framework, $username, $email, $emailConfirmed, $password, $passwordConfirmed, $tos);
        
        // If the validate function returned a string there was an error in the validation.
        if ($result !== true) {
            return $result;
        }
        
        // Create the new user
        $user = new User('', '', $username, '', array('email' => $email));
        $user->setNewPassword($password);
        
        // Generate an activation code
        $activationToken = uniqid(md5($email), true);
        $user->setMetaData('activationToken', $activationToken);
        
        $user = $this->userManager->addUser($user);
        
        // Add the disabled access level
        $this->accessControlManager->grantPermission($user->getUuid(), '\\Global\\Disabled', 'Registration');
        
        // Send the registration mail
        $activationLink = $request->getFullRoute('/activate/' . $user->getUuid() . '/' . $activationToken . '/');
        $this->mailHelper->sendMail(
            $user->getMetaData('email'),
            $this->translate('Your registration', '\\Zepi\\Web\\AccessControl'),
            $this->render('\\Zepi\\Web\\AccessControl\\Mail\\Registration', array(
                'user' => $user,
                'activationLink' => $activationLink,
            ))
        );
        
        return true;
    }
    
    /**
     * Validates the input user data
     * 
     * @param \Zepi\Turbo\Framework $framework
     * @param string $username
     * @param string $email
     * @param string $emailConfirmed
     * @param string $password
     * @param string $passwordConfirmed
     * @param boolean $tos
     * @return boolean|string
     */
    protected function validateData(Framework $framework, $username, $email, $emailConfirmed, $password, $passwordConfirmed, $tos)
    {
        // If the given username already exists
        if ($this->userManager->hasUserForUsername($username)) {
            return $this->translate('The inserted username is already in use. Please select a new username.', '\\Zepi\\Web\\AccessControl');
        }
        
        // Email
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return $this->translate('Please insert a valid email address.', '\\Zepi\\Web\\AccessControl');
        }
        
        if ($email != $emailConfirmed) {
            return $this->translate('The inserted email adresses are not equal.', '\\Zepi\\Web\\AccessControl');
        }
        
        // Password
        if (strlen($password) < 8) {
            return $this->translate('The password needs at least 8 characters.', '\\Zepi\\Web\\AccessControl');
        }
        
        if ($password != $passwordConfirmed) {
            return $this->translate('The passwords are not equal.', '\\Zepi\\Web\\AccessControl');
        }
        
        // ToS
        if (!$tos) {
            return $this->translate('The passwords are not equal.', '\\Zepi\\Web\\AccessControl');
        }
        
        // Everything is okey
        return true;
    }

    /**
     * Returns the Form object for the login form
     * 
     * @access protected
     * @param \Zepi\Turbo\Framework $framework
     * @param \Zepi\Turbo\Request\RequestAbstract $request
     * @param \Zepi\Turbo\Response\Response $response
     * @return \Zepi\Web\UserInterface\Form\Form
     */
    protected function createForm(Framework $framework, RequestAbstract $request, Response $response)
    {
        // Create the form
        $form = new Form('register', $request->getFullRoute(), 'post');
        
        // Add the user data group
        $errorBox = new ErrorBox(
            'register-errors',
            1
        );
        $form->addPart($errorBox);
        
        // Add the user data group
        $group = new Group(
            'user-data',
            $this->translate('Please fill out the fields below and accept our terms and conditions.', '\\Zepi\\Web\\AccessControl'),
            array(
                new Text(
                    'username',
                    $this->translate('Username', '\\Zepi\\Web\\AccessControl'),
                    true
                ),
                new Text(
                    'email',
                    $this->translate('Email address', '\\Zepi\\Web\\AccessControl'),
                    true
                ),
                new Text(
                    'email-confirmed',
                    $this->translate('Confirm email address', '\\Zepi\\Web\\AccessControl'),
                    true,
                    '',
                    '',
                    array(),
                    '',
                    null,
                    false
                ),
                new Password(
                    'password',
                    $this->translate('Password', '\\Zepi\\Web\\AccessControl'),
                    true
                ),
                new Password(
                    'password-confirmed',
                    $this->translate('Confirm password', '\\Zepi\\Web\\AccessControl'),
                    true
                ),
                new Checkbox(
                    'tos-accepted',
                    $this->translate(
                        'Do you accept our <a href="%link%" target="_blank">terms of service</a>?', 
                        '\\Zepi\\Web\\AccessControl',
                        array('link' => $request->getFullRoute('tos'))
                    ),
                    true
                ),
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
                    $this->translate('Register', '\\Zepi\\Web\\AccessControl')
                )
            ),
            100
        );
        $form->addPart($buttonGroup);
        
        return $form;
    }
}
