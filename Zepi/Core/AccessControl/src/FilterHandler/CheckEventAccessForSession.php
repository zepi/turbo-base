<?php
/*
 * The MIT License (MIT)
 *
 * Copyright (c) 2016 zepi
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
 * Checks if the logged in user has access to the given event name.
 * 
 * @package Zepi\Core\AccessControl
 * @subpackage FilterHandler
 * @author Matthias Zobrist <matthias.zobrist@zepi.net>
 * @copyright Copyright (c) 2016 zepi
 */

namespace Zepi\Core\AccessControl\FilterHandler;

use \Zepi\Turbo\FrameworkInterface\FilterHandlerInterface;
use \Zepi\Turbo\Framework;
use \Zepi\Turbo\Request\RequestAbstract;
use \Zepi\Turbo\Response\Response;
use \Zepi\Core\AccessControl\Manager\EventAccessManager;

/**
 * Checks if the logged in user has access to the given event name.
 * 
 * @author Matthias Zobrist <matthias.zobrist@zepi.net>
 * @copyright Copyright (c) 2016 zepi
 */
class CheckEventAccessForSession implements FilterHandlerInterface
{
    /**
     * @var \Zepi\Core\AccessControl\Manager\EventAccessManager
     */
    protected $eventAccessManager;
    
    /**
     * Constructs the object
     * 
     * @param \Zepi\Core\AccessControl\Manager\EventAccessManager $eventAccessManager
     */
    public function __construct(EventAccessManager $eventAccessManager)
    {
        $this->eventAccessManager = $eventAccessManager;
    }
    
    /**
     * Revokes all permissions for the given access level key
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
        $items = $this->eventAccessManager->getAccessLevelsForEvent($value);
        
        // If there are no access levels for the given event name the access
        // to the event is not restricted.
        if ($items === false) {
            return $value;
        }
        
        
        if (!$request->hasSession()) {
            return '\\Zepi\\Core\\AccessControl\\Event\\RedirectRequestWithoutSession';
        }
            
        foreach ($items as $accessLevel) {
            if ($request->getSession()->hasAccess($accessLevel)) {
                return $value;
            }
        }
        
        return '\\Zepi\\Core\\AccessControl\\Event\\DisplayNoAccessMessage';
    }
}
