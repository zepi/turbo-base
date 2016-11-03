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
 * Replaces all group access levels with the permissions of the group
 * 
 * @package Zepi\Web\AccessControl
 * @subpackage FilterHandler
 * @author Matthias Zobrist <matthias.zobrist@zepi.net>
 * @copyright Copyright (c) 2015 zepi
 */

namespace Zepi\Web\AccessControl\FilterHandler;

use \Zepi\Turbo\FrameworkInterface\FilterHandlerInterface;
use \Zepi\Turbo\Framework;
use \Zepi\Turbo\Request\RequestAbstract;
use \Zepi\Turbo\Response\Response;
use \Zepi\Core\Utils\Entity\DataRequest;
use \Zepi\Web\AccessControl\Entity\GroupAccessLevel;
use \Zepi\Core\AccessControl\Manager\AccessControlManager;

/**
 * Replaces all group access levels with the permissions of the group
 * 
 * @author Matthias Zobrist <matthias.zobrist@zepi.net>
 * @copyright Copyright (c) 2015 zepi
 */
class ResolveGroupPermissions implements FilterHandlerInterface
{
    /**
     * @access protected
     * @var \Zepi\Core\AccessControl\Manager\AccessControlManager
     */
    protected $accessControlManager;
    
    /**
     * Constructs the object
     * 
     * @access public
     * @param \Zepi\Core\AccessControl\Manager\AccessControlManager $accessControlManager
     */
    public function __construct(AccessControlManager $accessControlManager)
    {
        $this->accessControlManager = $accessControlManager;
    }
    
    /**
     * Replaces all group access levels with the permissions of the group
     * 
     * @access public
     * @param \Zepi\Turbo\Framework $framework
     * @param \Zepi\Turbo\Request\RequestAbstract $request
     * @param \Zepi\Turbo\Response\Response $response
     * @param mixed $value
     * @return mixed
     */
    public function execute(Framework $framework, RequestAbstract $request, Response $response, $value = null)
    {
        $permissions = array();
        foreach ($value as $accessLevel) {
            $parts = explode('\\', $accessLevel);

            if ($parts[1] === 'Group' && count($parts) === 3) {
                $uuid = $parts[2];
                
                $groupPermissions = $this->accessControlManager->getPermissionsForUuid($uuid);
                if ($groupPermissions !== false) {
                    foreach ($groupPermissions as $groupPermission) {
                        $permissions[] = $groupPermission;
                    }
                }
            } else {
                $permissions[] = $accessLevel;
            }
        }
        
        $permissions = array_unique($permissions);
        
        return $permissions;
    }
}
