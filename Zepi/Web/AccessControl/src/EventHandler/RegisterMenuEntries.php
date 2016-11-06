<?php
/*
 * The MIT License (MIT)
 *
 * Copyright (c) 2015 zepi
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 */

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

use \Zepi\Web\UserInterface\Frontend\FrontendEventHandler;
use \Zepi\Turbo\Framework;
use \Zepi\Turbo\Request\WebRequest;
use \Zepi\Turbo\Response\Response;

/**
 * Registers the menu entries which are only accessable if the user is logged in
 * or not logged in, in example login or logout menu entry.
 * 
 * @author Matthias Zobrist <matthias.zobrist@zepi.net>
 * @copyright Copyright (c) 2015 zepi
 */
class RegisterMenuEntries extends FrontendEventHandler
{
    /**
     * Registers the menu entries which are only accessable if the user is logged in
     * or not logged in, in example login or logout menu entry.
     * 
     * @access public
     * @param \Zepi\Turbo\Framework $framework
     * @param \Zepi\Turbo\Request\WebRequest $request
     * @param \Zepi\Turbo\Response\Response $response
     */
    public function execute(Framework $framework, WebRequest $request, Response $response)
    {
        if ($request->hasSession()) {
            $profileMenuEntry = new \Zepi\Web\General\Entity\MenuEntry(
                'profile',
                $this->translate('Profile', '\\Zepi\\Web\\AccessControl'),
                'profile',
                'mdi-account'
            );
            $this->getMenuManager()->addMenuEntry('menu-right', $profileMenuEntry, 90);
            
            // Add the hidden user settings menu entry
            $userSettingsSubMenuEntry = new \Zepi\Web\General\Entity\HiddenMenuEntry(
                $this->translate('User settings', '\\Zepi\\Web\\AccessControl')
            );
            $profileMenuEntry->addChild($userSettingsSubMenuEntry);
            
            // Add the hidden change password menu entry 
            $changePasswordSubMenuEntry = new \Zepi\Web\General\Entity\HiddenMenuEntry(
                $this->translate('Change password', '\\Zepi\\Web\\AccessControl'),
                'profile/change-password',
                'mdi-key-variant'
            );
            $userSettingsSubMenuEntry->addChild($changePasswordSubMenuEntry);
            
            // Add the logout menu entry
            $menuEntry = new \Zepi\Web\General\Entity\MenuEntry(
                'logout',
                $this->translate('Logout', '\\Zepi\\Web\\AccessControl'),
                'logout',
                'mdi-logout'
            );
            $this->getMenuManager()->addMenuEntry('menu-right', $menuEntry, 100);
        } else {
            if ($this->getSetting('accesscontrol.allowRegistration')) {
                $menuEntry = new \Zepi\Web\General\Entity\MenuEntry(
                    'registration',
                    $this->translate('Registration', '\\Pmx\\Autopilot\\AccessControl'),
                    '/register/',
                    'mdi-account-circle'
                );
                $this->getMenuManager()->addMenuEntry('menu-right', $menuEntry);
            }
            
            $menuEntry = new \Zepi\Web\General\Entity\MenuEntry(
                'login',
                $this->translate('Login', '\\Zepi\\Web\\AccessControl'),
                'login',
                'mdi-login'
            );
            $this->getMenuManager()->addMenuEntry('menu-right', $menuEntry, 100);
        }
    }
}
