<?php
/**
 * Displays the management page for groups.
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

/**
 * Displays the management page for groups.
 * 
 * @author Matthias Zobrist <matthias.zobrist@zepi.net>
 * @copyright Copyright (c) 2015 zepi
 */
class Groups implements EventHandlerInterface
{
    /**
     * Displays the management page for groups.
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
        
        $translationManager = $framework->getInstance('\\Zepi\\Core\\Language\\Manager\\TranslationManager');
        
        // Set the title for the page
        $metaInformationManager = $framework->getInstance('\\Zepi\\Web\\General\\Manager\\MetaInformationManager');
        $metaInformationManager->setTitle($translationManager->translate('Group management', '\\Zepi\\Web\\AccessControl'));
        
        // Activate the correct menu entry and add the breadcrumb function entry
        $menuManager = $framework->getInstance('\\Zepi\\Web\\General\\Manager\\MenuManager');
        $menuManager->setActiveMenuEntry($menuManager->getMenuEntryForKey('group-administration'));
        
        // Get the Table Renderer
        $tableRenderer = $framework->getInstance('\\Zepi\\Web\\UserInterface\\Renderer\\Table');
        
        // Generate the Table
        $groupTable = new \Zepi\Web\AccessControl\Table\GroupTable(
            $framework, 
            true,
            true
        );
        
        // Displays the group table
        $templatesManager = $framework->getInstance('\\Zepi\\Web\\General\\Manager\\TemplatesManager');
        $response->setOutput($templatesManager->renderTemplate('\\Zepi\\Web\\AccessControl\\Templates\\Administration\\Groups', array(
            'groupTable' => $groupTable,
            'tableRenderer' => $tableRenderer
        )));
    }
}
