<?php
/**
 * Displays the profile page for an logged in user.
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
 * Displays the profile page for an logged in user.
 * 
 * @author Matthias Zobrist <matthias.zobrist@zepi.net>
 * @copyright Copyright (c) 2015 zepi
 */
class Profile implements EventHandlerInterface
{
    /**
     * Displays the profile page for an logged in user.
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
        if (!$request->hasSession()) {
            $response->redirectTo('/');
            return;
        }
        
        $translationManager = $framework->getInstance('\\Zepi\\Core\\Language\\Manager\\TranslationManager');
        
        // Set the title for the page
        $metaInformationManager = $framework->getInstance('\\Zepi\\Web\\General\\Manager\\MetaInformationManager');
        $metaInformationManager->setTitle($translationManager->translate('Profile', '\\Zepi\\Web\\AccessControl'));


        // Initialize MenuManager
        $menuManager = $framework->getInstance('\\Zepi\\Web\\General\\Manager\\MenuManager'); 
        $menuManager->activateCorrectMenuEntry();
        
        $menuEntry = $menuManager->getActiveMenuEntry();
        $overviewPageRenderer = new \Zepi\Web\UserInterface\Renderer\OverviewPage();
        $overviewPage = $overviewPageRenderer->render($framework, $menuEntry);
        
        // Display logout message
        $templatesManager = $framework->getInstance('\\Zepi\\Web\\General\\Manager\\TemplatesManager');
        $response->setOutput($templatesManager->renderTemplate('\\Zepi\\Web\\AccessControl\\Templates\\Profile', array(
            'overviewPage' => $overviewPage
        )));
    }
}
