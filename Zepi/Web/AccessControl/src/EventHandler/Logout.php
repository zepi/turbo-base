<?php
/**
 * Authorizes an user with the user credentials.
 * 
 * @package Zepi\Web\AccessControl
 * @subpackage EventHandler
 * @author Matthias Zobrist <matthias.zobrist@zepi.net>
 * @copyright Copyright (c) 2015 zepi
 */

namespace Zepi\Web\AccessControl\EventHandler;

use \Zepi\Turbo\FrameworkInterface\EventHandlerInterface;
use \Zepi\Turbo\Framework;
use \Zepi\Turbo\Request\RequestAbstract;
use \Zepi\Turbo\Response\Response;

/**
 * Authorizes an user with the user credentials.
 * 
 * @author Matthias Zobrist <matthias.zobrist@zepi.net>
 * @copyright Copyright (c) 2015 zepi
 */
class Logout implements EventHandlerInterface
{
    /**
     * Filters the given menu entries and removes all protected menu
     * entries for which the sender hasn't the correct permission.
     * 
     * @access public
     * @param \Zepi\Turbo\Framework $framework
     * @param \Zepi\Turbo\Request\RequestAbstract $request
     * @param \Zepi\Turbo\Response\Response $response
     * @param mixed $value
     */
    public function executeEvent(Framework $framework, RequestAbstract $request, Response $response, $value = null)
    {
        $translationManager = $framework->getInstance('\\Zepi\\Core\\Language\\Manager\\TranslationManager');
        
        // Redirect if the user hasn't a valid session
        if (!$request->hasSession('Zepi\\Web\\AccessControl\\Entity\\Session')) {
            $response->redirectTo('/');
            return;
        }
        
        // Get the session manager
        $sessionManager = $framework->getInstance('\\Zepi\\Web\\AccessControl\\Manager\\SessionManager');
        
        // Initializes the user session
        $sessionManager->logoutUser($request, $response);
        
        // Set the title for the page
        $metaInformationManager = $framework->getInstance('\\Zepi\\Web\\General\\Manager\\MetaInformationManager');
        $metaInformationManager->setTitle($translationManager->translate('Successfully logged out', '\\Zepi\\Web\\AccessControl'));
        
        // Display logout message
        $templatesManager = $framework->getInstance('\\Zepi\\Web\\General\\Manager\\TemplatesManager');
        $response->setOutput($templatesManager->renderTemplate('\\Zepi\\Web\\AccessControl\\Templates\\Logout', $framework, $request, $response));
    }
}
