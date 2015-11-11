<?php
/**
 * Registers the menu entries which are only accessable if the user is logged in
 * or not logged in, in example login or logout menu entry.
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
 * Registers the menu entries which are only accessable if the user is logged in
 * or not logged in, in example login or logout menu entry.
 * 
 * @author Matthias Zobrist <matthias.zobrist@zepi.net>
 * @copyright Copyright (c) 2015 zepi
 */
class RegisterMenuEntries implements EventHandlerInterface
{
    /**
     * Registers the menu entries which are only accessable if the user is logged in
     * or not logged in, in example login or logout menu entry.
     * 
     * @access public
     * @param \Zepi\Turbo\Framework $framework
     * @param \Zepi\Turbo\Request\RequestAbstract $request
     * @param \Zepi\Turbo\Response\Response $response
     * @param mixed $value
     */
    public function executeEvent(Framework $framework, RequestAbstract $request, Response $response, $value = null)
    {
        $menuManager = $framework->getInstance('\\Zepi\\Web\\General\\Manager\\MenuManager');
        $translationManager = $framework->getInstance('\\Zepi\\Core\\Language\\Manager\\TranslationManager');

        if ($request->hasSession('Zepi\\Web\\AccessControl\\Entity\\Session')) {
            $profileMenuEntry = new \Zepi\Web\General\Entity\MenuEntry(
                'profile',
                $translationManager->translate('Profile', '\\Zepi\\Web\\AccessControl'),
                'profile',
                'mdi-account'
            );
            $menuManager->addMenuEntry('menu-right', $profileMenuEntry);
            
            // Add the hidden user settings menu entry
            $userSettingsSubMenuEntry = new \Zepi\Web\General\Entity\HiddenMenuEntry(
                $translationManager->translate('User settings', '\\Zepi\\Web\\AccessControl')
            );
            $profileMenuEntry->addChild($userSettingsSubMenuEntry);
            
            // Add the hidden change password menu entry 
            $changePasswordSubMenuEntry = new \Zepi\Web\General\Entity\HiddenMenuEntry(
                $translationManager->translate('Change password', '\\Zepi\\Web\\AccessControl'),
                'profile/change-password',
                'mdi-key-variant'
            );
            $userSettingsSubMenuEntry->addChild($changePasswordSubMenuEntry);
            
            // Add the logout menu entry
            $menuEntry = new \Zepi\Web\General\Entity\MenuEntry(
                'logout',
                $translationManager->translate('Logout', '\\Zepi\\Web\\AccessControl'),
                'logout',
                'mdi-logout'
            );
            $menuManager->addMenuEntry('menu-right', $menuEntry);
        } else {
            $menuEntry = new \Zepi\Web\General\Entity\MenuEntry(
                'login',
                $translationManager->translate('Login', '\\Zepi\\Web\\AccessControl'),
                'login',
                'mdi-login'
            );
            $menuManager->addMenuEntry('menu-right', $menuEntry);
        }
    }
}
