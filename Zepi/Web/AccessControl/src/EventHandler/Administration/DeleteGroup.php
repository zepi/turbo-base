<?php
/**
 * Event handler for the delete group function
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
use \Zepi\Web\AccessControl\Entity\Group;

/**
 * Event handler for the delete group function
 * 
 * @author Matthias Zobrist <matthias.zobrist@zepi.net>
 * @copyright Copyright (c) 2015 zepi
 */
class DeleteGroup implements EventHandlerInterface
{
    /**
     * Displays the edit user form and saves the data to the database.
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
        
        // Get the translation manager
        $translationManager = $framework->getInstance('\\Zepi\\Core\\Language\\Manager\\TranslationManager');
        $templatesManager = $framework->getInstance('\\Zepi\\Web\\General\\Manager\\TemplatesManager');
        
        $additionalTitle = $translationManager->translate('Delete group', '\\Zepi\\Web\\AccessControl');
        $title = $translationManager->translate('Group management', '\\Zepi\\Web\\AccessControl') . ' - ' . $additionalTitle;
        
        // Activate the correct menu entry and add the breadcrumb function entry
        $menuManager = $framework->getInstance('\\Zepi\\Web\\General\\Manager\\MenuManager');
        $menuManager->setActiveMenuEntry($menuManager->getMenuEntryForKey('group-administration'));
        $menuManager->setBreadcrumbFunction($additionalTitle);
        
        // Set the title for the page
        $metaInformationManager = $framework->getInstance('\\Zepi\\Web\\General\\Manager\\MetaInformationManager');
        $metaInformationManager->setTitle($title);
        
        // Get the user
        $groupManager = $framework->getInstance('\\Zepi\\Web\\AccessControl\\Manager\\GroupManager');
        $uuid = $request->getRouteParam(0);
        
        // If the UUID does not exists redirect to the overview page
        if (!$groupManager->hasGroupForUuid($uuid)) {
            $response->redirectTo($request->getFullRoute('/administration/groups/'));
            return;
        }
        
        $group = $groupManager->getGroupForUuid($uuid);
        
        // If $result isn't true, display the edit user form
        if ($request->getRouteParam(1) === 'confirmed') {
            $groupManager->deleteGroup($group->getUuid());
            
            $response->setOutput($templatesManager->renderTemplate('\\Zepi\\Web\\AccessControl\\Templates\\Administration\\DeleteGroupFinished', array(
                'group' => $group
            )));
        } else {
            // Display the delete user confirmation
            $response->setOutput($templatesManager->renderTemplate('\\Zepi\\Web\\AccessControl\\Templates\\Administration\\DeleteGroup', array(
                'group' => $group
            )));
        }
    }
}
