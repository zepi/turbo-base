<?php
/**
 * Event handler to activate an account
 * 
 * @package Zepi\Web\AccessControl
 * @subpackage EventHandler
 * @author Matthias Zobrist <matthias.zobrist@zepi.net>
 * @copyright Copyright (c) 2015 zepi
 */

namespace Zepi\Web\AccessControl\EventHandler;

use \Zepi\Web\UserInterface\Frontend\FrontendEventHandler;
use \Zepi\Turbo\Framework;
use \Zepi\Turbo\Request\WebRequest;
use \Zepi\Turbo\Response\Response;
use \Zepi\Web\UserInterface\Frontend\FrontendHelper;
use \Zepi\Web\AccessControl\Entity\User;
use \Zepi\Web\AccessControl\Manager\UserManager;
use \Zepi\Core\AccessControl\Manager\AccessControlManager;

/**
 * Event handler to activate an account
 * 
 * @author Matthias Zobrist <matthias.zobrist@zepi.net>
 * @copyright Copyright (c) 2015 zepi
 */
class Activation extends FrontendEventHandler
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
     * Constructs the object
     *
     * @access public
     * @param \Zepi\Web\UserInterface\Frontend\FrontendHelper $frontendHelper
     * @param \Zepi\Web\AccessControl\Manager\UserManager $userManager
     * @param \Zepi\Core\AccessControl\Manager\AccessControlManager $accessControlManager
     */
    public function __construct(FrontendHelper $frontendHelper, UserManager $userManager, AccessControlManager $accessControlManager)
    {
        $this->frontendHelper = $frontendHelper;
        $this->userManager = $userManager;
        $this->accessControlManager = $accessControlManager;
    }
    
    /**
     * Deletes a cluster in the database
     * 
     * @access public
     * @param \Zepi\Turbo\Framework $framework
     * @param \Zepi\Turbo\Request\WebRequest $request
     * @param \Zepi\Turbo\Response\Response $response
     */
    public function execute(Framework $framework, WebRequest $request, Response $response)
    {
        $title = $this->translate('Activate account', '\\Zepi\\Web\\AccessControl');
        
        // Prepare the page
        $this->setTitle($title);
        
        // Get the cluster
        $uuid = $request->getRouteParam('uuid');
        $activationToken = $request->getRouteParam('token');
        
        // Activate the user
        $result = array('result' => false, 'message' => $this->translate('Wrong request parameters.', '\\Zepi\\Web\\AccessControl'));
        if ($uuid != false && $activationToken != false) {
            $result = $this->activateUser($uuid, $activationToken);
        }
        
        // Display the result
        $response->setOutput($this->render('\\Zepi\\Web\\AccessControl\\Templates\\Activation', array(
            'result' => $result
        )));
    }
    
    /**
     * Activates the user or returns an error message
     * 
     * @access protected
     * @param string $uuid
     * @param string $activationToken
     * @return array
     */
    protected function activateUser($uuid, $activationToken)
    {
        // Check the uuid
        if (!$this->userManager->hasUserForUuid($uuid)) {
            return array('result' => false, 'message' => $this->translate('Account with the given UUID does not exist.', '\\Zepi\\Web\\AccessControl'));
        }
        
        // Compare the activation token
        $user = $this->userManager->getUserForUuid($uuid);
        if ($user->getMetaData('activationToken') !== $activationToken) {
            return array('result' => false, 'message' => $this->translate('The given activation token is not valid.', '\\Zepi\\Web\\AccessControl'));
        }
        
        // Remove the disabled access level
        $this->accessControlManager->revokePermission($uuid, get_class($user), '\\Global\\Disabled');
        $this->accessControlManager->grantPermission($uuid, get_class($user), '\\Global\\Active', 'Activation');
        
        return array('result' => true, 'message' => $this->translate('Your account was activated successfully.', '\\Zepi\\Web\\AccessControl'));
    }
}
