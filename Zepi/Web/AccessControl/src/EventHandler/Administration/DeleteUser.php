<?php
/**
 * Event handler for the delete user function
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
use \Zepi\Web\AccessControl\Entity\User;
use \Zepi\Web\UserInterface\Form\Form;
use \Zepi\Web\UserInterface\Form\Group;
use \Zepi\Web\UserInterface\Form\ErrorBox;
use \Zepi\Web\UserInterface\Form\Error;
use \Zepi\Web\UserInterface\Form\ButtonGroup;
use \Zepi\Web\UserInterface\Form\Field\Text;
use \Zepi\Web\UserInterface\Form\Field\Textarea;
use \Zepi\Web\UserInterface\Form\Field\Password;
use \Zepi\Web\UserInterface\Form\Field\Submit;
use \Zepi\Web\UserInterface\Layout\AbstractContainer;
use \Zepi\Web\UserInterface\Layout\Page;
use \Zepi\Web\UserInterface\Layout\Part;
use \Zepi\Web\UserInterface\Layout\Tab;
use \Zepi\Web\UserInterface\Layout\Row;
use \Zepi\Web\UserInterface\Layout\Column;

/**
 * Event handler for the delete user function
 * 
 * @author Matthias Zobrist <matthias.zobrist@zepi.net>
 * @copyright Copyright (c) 2015 zepi
 */
class DeleteUser implements EventHandlerInterface
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
        
        $additionalTitle = $translationManager->translate('Delete user', '\\Zepi\\Web\\AccessControl');
        $title = $translationManager->translate('User management', '\\Zepi\\Web\\AccessControl') . ' - ' . $additionalTitle;
        
        // Activate the correct menu entry and add the breadcrumb function entry
        $menuManager = $framework->getInstance('\\Zepi\\Web\\General\\Manager\\MenuManager');
        $menuManager->setActiveMenuEntry($menuManager->getMenuEntryForKey('user-administration'));
        $menuManager->setBreadcrumbFunction($additionalTitle);
        
        // Set the title for the page
        $metaInformationManager = $framework->getInstance('\\Zepi\\Web\\General\\Manager\\MetaInformationManager');
        $metaInformationManager->setTitle($title);
        
        // Get the user
        $userManager = $framework->getInstance('\\Zepi\\Web\\AccessControl\\Manager\\UserManager');
        $uuid = $request->getRouteParam(0);
        
        // If the UUID does not exists redirect to the overview page
        if (!$userManager->hasUserForUuid($uuid)) {
            $response->redirectTo($request->getFullRoute('/administration/users/'));
            return;
        }
        
        $user = $userManager->getUserForUuid($request->getRouteParam(0));
        
        // If $result isn't true, display the edit user form
        if ($request->getRouteParam(1) === 'confirmed') {
            $userManager->deleteUser($user->getUuid());
            
            $response->setOutput($templatesManager->renderTemplate('\\Zepi\\Web\\AccessControl\\Templates\\Administration\\DeleteUserFinished', array(
                'user' => $user
            )));
        } else {
            // Display the delete user confirmation
            $response->setOutput($templatesManager->renderTemplate('\\Zepi\\Web\\AccessControl\\Templates\\Administration\\DeleteUser', array(
                'user' => $user
            )));
        }
    }
}
