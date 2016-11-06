<?php
/**
 * Event handler to generate a new password
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
use \Zepi\Web\UserInterface\Frontend\FrontendHelper;
use \Zepi\Web\AccessControl\Entity\User;
use \Zepi\Web\AccessControl\Manager\UserManager;
use \Zepi\Web\Mail\Helper\MailHelper;

/**
 * Event handler to generate a new password
 * 
 * @author Matthias Zobrist <matthias.zobrist@zepi.net>
 * @copyright Copyright (c) 2015 zepi
 */
class GenerateNewPassword extends FrontendEventHandler
{
    /**
     * @access protected
     * @var \Zepi\Web\AccessControl\Manager\UserManager
     */
    protected $userManager;
    
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
     * @param \Zepi\Web\Mail\Helper\MailHelper $mailHelper
     */
    public function __construct(FrontendHelper $frontendHelper, UserManager $userManager, MailHelper $mailHelper)
    {
        $this->frontendHelper = $frontendHelper;
        $this->userManager = $userManager;
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
        $this->setTitle($this->translate('Generate a new password', '\\Zepi\\Web\\AccessControl'));
        
        // Generate a new password
        $result = $this->generateNewPassword($framework, $request, $response);
        
        // Display the successful saved message
        $response->setOutput($this->render('\\Zepi\\Web\\AccessControl\\Templates\\GenerateNewPasswordFinished', array(
            'title' => $this->getTitle(),
            'result' => $result['result'],
            'message' => $result['message']
        )));
    }
    
    /**
     * Authorizes the user with his username and password. Initializes
     * the user session if the user data are valid.
     * 
     * @access protected
     * @param \Zepi\Turbo\Framework $framework
     * @param \Zepi\Turbo\Request\RequestAbstract $request
     * @param \Zepi\Turbo\Response\Response $response
     * @return string|boolean
     */
    protected function generateNewPassword(Framework $framework, RequestAbstract $request, Response $response)
    {
        $uuid = $request->getRouteParam('uuid');
        $token = $request->getRouteParam('token');
        
        if ($uuid === false || !$this->userManager->hasUserForUuid($uuid) || $token === false) {
            $response->redirectTo('/');
            return;
        }
        
        // Load the user
        $user = $this->userManager->getUserForUuid($uuid);
        
        if ($user->getMetaData('passwordRequestToken') == '') {
            return array(
                'result' => false,
                'message' => $this->translate('You haven\'t requested a new password.', '\\Zepi\\Web\\AccessControl'
            ));
        }
        
        // If the validate function returned a string there was an error in the validation.
        if ($user->getMetaData('passwordRequestToken') !== $token || $user->getMetaData('passwordRequestTokenLifetime') < time()) {
            return array(
                'result' => false,
                'message' => $this->translate('The given token is invalid or expired. Please request a new password.', '\\Zepi\\Web\\AccessControl'
            ));
        }
        
        // Generate a new password
        $password = $this->generateRandomPassword();
        
        // Save the new password
        $user->setNewPassword($password);
        
        // Reset the token
        $user->setMetaData('passwordRequestToken', '');
        $user->setMetaData('passwordRequestTokenLifetime', 0);
        
        // Update the user
        $this->userManager->updateUser($user);
        
        // Send the request mail
        $this->mailHelper->sendMail(
            $user->getMetaData('email'),
            $this->translate('New password generated', '\\Zepi\\Web\\AccessControl'),
            $this->render('\\Zepi\\Web\\AccessControl\\Mail\\GenerateNewPassword', array(
                'user' => $user,
                'password' => $password,
            ))
        );
        
        return array(
            'result' => true,
            'message' => $this->translate('Your new password is generated and saved. You will receive an email with the new password.', '\\Zepi\\Web\\AccessControl'
        ));
    }
    
    /**
     * Generates a new random password
     * 
     * @access protected
     * @return string
     */
    protected function generateRandomPassword()
    {
        $alphabet = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890+*=-_/()!?[]{}';
        $password = array();
        $alphabetLength = strlen($alphabet) - 1;
        
        for ($i = 0; $i < 10; $i++) {
            $charIndex = mt_rand(0, $alphabetLength);
            $password[] = $alphabet[$charIndex];
        }
        
        $password = implode('', $password);
        
        if (!preg_match('/\d/', $password)) {
            $password = mt_rand(0, 9) . $password . mt_rand(0, 9);
        }
        
        return $password;
    }
}
