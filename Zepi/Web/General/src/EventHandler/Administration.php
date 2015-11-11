<?php
/**
 * Displays the administration overview page
 * 
 * @package Zepi\Web\General
 * @subpackage EventHandler
 * @author Matthias Zobrist <matthias.zobrist@zepi.net>
 * @copyright Copyright (c) 2015 zepi
 */

namespace Zepi\Web\General\EventHandler;

use \Zepi\Turbo\FrameworkInterface\EventHandlerInterface;
use \Zepi\Turbo\Framework;
use \Zepi\Turbo\Request\RequestAbstract;
use \Zepi\Turbo\Response\Response;

/**
 * Displays the administration overview page
 * 
 * @author Matthias Zobrist <matthias.zobrist@zepi.net>
 * @copyright Copyright (c) 2015 zepi
 */
class Administration implements EventHandlerInterface
{
    /**
     * Displays the administration overview page
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
        if (!$request->hasSession('Zepi\\Web\\AccessControl\\Entity\\Session')) {
            $response->redirectTo('/');
            return;
        }
        
        $translationManager = $framework->getInstance('\\Zepi\\Core\\Language\\Manager\\TranslationManager');
        
        // Set the title for the page
        $metaInformationManager = $framework->getInstance('\\Zepi\\Web\\General\\Manager\\MetaInformationManager');
        $metaInformationManager->setTitle($translationManager->translate('Administration', '\\Zepi\\Web\\General'));

        // Initialize MenuManager
        $menuManager = $framework->getInstance('\\Zepi\\Web\\General\\Manager\\MenuManager'); 
        $menuManager->activateCorrectMenuEntry();
        
        $menuEntry = $menuManager->getActiveMenuEntry();
        $overviewPageRenderer = new \Zepi\Web\UserInterface\Renderer\OverviewPage();
        $overviewPage = $overviewPageRenderer->render($framework, $menuEntry);
        
        // Display logout message
        $templatesManager = $framework->getInstance('\\Zepi\\Web\\General\\Manager\\TemplatesManager');
        $response->setOutput($templatesManager->renderTemplate('\\Zepi\\Web\\General\\Templates\\Administration', array(
            'overviewPage' => $overviewPage
        )));
    }
}
