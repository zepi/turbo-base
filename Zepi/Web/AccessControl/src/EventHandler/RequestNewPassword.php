<?php
/**
 * Event handler to handle the registration process
 * 
 * @package Zepi\Web\AccessControl
 * @subpackage EventHandler
 * @author Matthias Zobrist <m.zobrist@promatrix.ch>
 * @copyright Copyright (c) 2015 promatrix ag
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
use \Zepi\Web\UserInterface\Form\Field\Submit;
use \Zepi\Web\UserInterface\Frontend\FrontendHelper;
use \Zepi\Web\AccessControl\Entity\User;
use \Zepi\Web\AccessControl\Manager\UserManager;
use \Zepi\Web\Mail\Helper\MailHelper;

/**
 * Event handler to handle the registration process
 * 
 * @author Matthias Zobrist <m.zobrist@promatrix.ch>
 * @copyright Copyright (c) 2015 promatrix ag
 */
class RequestNewPassword extends FrontendEventHandler
{
    /**
     * @access protected
     * @var \Zepi\Web\AccessControl\Manager\UserManager
     */
    protected $_userManager;
    
    /**
     * @access protected
     * @var \Zepi\Web\Mail\Helper\MailHelper
     */
    protected $_mailHelper;
    
    /**
     * Constructs the object
     *
     * @access public
     * @param \Zepi\Web\UserInterface\Frontend\FrontendHelper $frontendHelper
     * @param \Zepi\Web\AccessControl\Manager\UserManager $userManager
     * @param \Zepi\Web\Mail\Helper\MailHelper $mailHelper
     */
    public function __construct(FrontendHelper $frontendHelper, UserManager $userManager, MailHelper $mailHelper)
    {
        $this->_frontendHelper = $frontendHelper;
        $this->_userManager = $userManager;
        $this->_mailHelper = $mailHelper;
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
        $this->setTitle($this->translate('Request a new password', '\\Zepi\\Web\\AccessControl'));
        
        // Get the form object
        $requestForm = $this->_createForm($framework, $request, $response);
        
        // Process the submitted form data
        $requestForm->processFormData($request);
        
        // Validate the form data and authorize the user
        $result = false;
        $errors = array();
        if ($requestForm->isSubmitted()) {
            $errors = $requestForm->validateFormData($framework);
            if (count($errors) === 0) {
                $result = $this->_sendRequest($requestForm, $framework, $request, $response);
            } 
        }
         
        // Fill the errors into the error box
        $errorBox = $requestForm->getPart('request-errors');
        if (($requestForm->isSubmitted() && $result !== true) || count($errors) > 0) {
            if (is_string($result)) {
                $errorBox->addError(new Error(
                    Error::GENERAL_ERROR,
                    $result
                ));
            } else if (count($errors) === 0) {
                $errorBox->addError(new Error(
                    Error::GENERAL_ERROR,
                    $this->translate('Your submitted data weren\'t correct. Please repeat the request or contact the administrator.', '\\Zepi\\Web\\AccessControl')
                ));
            } else {
                foreach ($errors as $error) {
                    $errorBox->addError($error);
                }
            }
        }
        
        // If $result isn't true, display the login form
        if (!$requestForm->isSubmitted() || $errorBox->hasErrors()) {
            $renderedOutput = $this->render('\\Zepi\\Web\\AccessControl\\Templates\\RequestNewPasswordForm', array(
                'result' => $result,
                'errors' => $errors,
                'form' => $requestForm, 
                'layoutRenderer' => $this->getLayoutRenderer()
            ));
            
            $response->setOutput($renderedOutput);
        } else {
            // Display the successful saved message
            $response->setOutput($this->render('\\Zepi\\Web\\AccessControl\\Templates\\RequestNewPasswordFinished', array(
                'title' => $this->getTitle()
            )));
        }
    }
    
    /**
     * Authorizes the user with his username and password. Initializes
     * the user session if the user data are valid.
     * 
     * @access protected
     * @param \Zepi\Web\UserInterface\Form\Form $form
     * @param \Zepi\Turbo\Framework $framework
     * @param \Zepi\Turbo\Request\RequestAbstract $request
     * @param \Zepi\Turbo\Response\Response $response
     * @return string|boolean
     */
    protected function _sendRequest(Form $form, Framework $framework, RequestAbstract $request, Response $response)
    {
        $group = $form->searchPartByKeyAndType('user-data');
        $username = trim($group->getPart('username')->getValue());
        
        $result = $this->_validateData($framework, $username);
        
        // If the validate function returned a string there was an error in the validation.
        if ($result !== true) {
            return $result;
        }
        
        // Load the user
        $user = $this->_userManager->getUserForUsername($username);
        
        // Generate an request token
        $token = uniqid(md5($user->getMetaData('email')), true);
        $user->setMetaData('passwordRequestToken', $token);
        $user->setMetaData('passwordRequestTokenLifetime', time() + 3600);
        
        $this->_userManager->updateUser($user);
        
        // Send the request mail
        $requestLink = $request->getFullRoute('/generate-new-password/' . $user->getUuid() . '/' . $token . '/');
        $this->_mailHelper->sendMail(
            $user->getMetaData('email'),
            $this->translate('New password requested', '\\Zepi\\Web\\AccessControl'),
            $this->render('\\Zepi\\Web\\AccessControl\\Mail\\RequestNewPassword', array(
                'user' => $user,
                'requestLink' => $requestLink,
            ))
        );
        
        return true;
    }
    
    /**
     * Validates the input user data
     * 
     * @param \Zepi\Turbo\Framework $framework
     * @param string $username
     * @return boolean|string
     */
    protected function _validateData(Framework $framework, $username)
    {
        // If the given username doesn't exists
        if (!$this->_userManager->hasUserForUsername($username)) {
            return $this->translate('The inserted username does not exist.', '\\Zepi\\Web\\AccessControl');
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
    protected function _createForm(Framework $framework, RequestAbstract $request, Response $response)
    {
        // Create the form
        $form = new Form('request-new-password', $request->getFullRoute(), 'post');
        
        // Add the user data group
        $errorBox = new ErrorBox(
            'request-errors',
            1
        );
        $form->addPart($errorBox);
        
        // Add the user data group
        $group = new Group(
            'user-data',
            $this->translate('Please insert your username and submit the form.', '\\Zepi\\Web\\AccessControl'),
            array(
                new Text(
                    'username',
                    $this->translate('Username', '\\Zepi\\Web\\AccessControl'),
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
                    $this->translate('Request new password', '\\Zepi\\Web\\AccessControl')
                )
            ),
            100
        );
        $form->addPart($buttonGroup);
        
        return $form;
    }
}
