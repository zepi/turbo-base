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
 * Execute the installation the access control module
 * 
 * @package Zepi\Web\AccessControl
 * @subpackage EventHandler
 * @author Matthias Zobrist <matthias.zobrist@zepi.net>
 * @copyright Copyright (c) 2015 zepi
 */

namespace Zepi\Web\AccessControl\EventHandler;

use \Zepi\Turbo\FrameworkInterface\CliEventHandlerInterface;
use \Zepi\Turbo\Framework;
use \Zepi\Turbo\Request\RequestAbstract;
use \Zepi\Turbo\Request\CliRequest;
use \Zepi\Turbo\Response\Response;
use \Zepi\Web\Test\Exception;
use \Zepi\Web\AccessControl\Manager\UserManager;
use \Zepi\Core\AccessControl\Manager\AccessControlManager;
use \Zepi\Core\Utils\Helper\CliHelper;

/**
 * Execute the installation the access control module
 * 
 * @author Matthias Zobrist <matthias.zobrist@zepi.net>
 * @copyright Copyright (c) 2015 zepi
 */
class ExecuteInstallation implements CliEventHandlerInterface
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
     * @access protected
     * @var \Zepi\Core\Utils\Helper\CliHelper
     */
    protected $cliHelper;
    
    /**
     * Constructs the object
     * 
     * @access public
     * @param \Zepi\Web\AccessControl\Manager\UserManager $userManager
     * @param \Zepi\Core\AccessControl\Manager\AccessControlManager $accessControlManager
     * @param \Zepi\Core\Utils\Helper\CliHelper $cliHelper
     */
    public function __construct(UserManager $userManager, AccessControlManager $accessControlManager, CliHelper $cliHelper)
    {
        $this->userManager = $userManager;
        $this->accessControlManager = $accessControlManager;
        $this->cliHelper = $cliHelper;
    }
    
    /**
     * Execute the installation the access control module
     * 
     * @access public
     * @param \Zepi\Turbo\Framework $framework
     * @param \Zepi\Turbo\Request\CliRequest $request
     * @param \Zepi\Turbo\Response\Response $response
     */
    public function execute(Framework $framework, CliRequest $request, Response $response)
    {
        // Execute the installer only if there are no users
        $dataRequest = new \Zepi\DataSource\Core\Entity\DataRequest(1, 0, 'name', 'ASC');
        if ($this->userManager->count($dataRequest) > 0) {
            return;
        }
        
        $username = '';
        while ($username === '') {
            $username = trim($this->cliHelper->inputText('Please enter the username for the super-admin user:'));
        }
        
        $password = '';
        while ($password === '') {
            $password = trim($this->cliHelper->inputText('Please enter the password for the super-admin user:'));
        }
        
        // Create the super-admin user
        $user = new \Zepi\Web\AccessControl\Entity\User('', '', $username, '', array());
        $user->setNewPassword($password);
        
        // Save the super-admin user
        $user = $this->userManager->add($user);
        
        // Add the super-admin access level
        $this->accessControlManager->grantPermission(
            $user->getUuid(),
            '\\Zepi\\Web\\AccessControl\\Entity\\User',
            '\\Global\\*',
            'CLI'
        );
    }
}
