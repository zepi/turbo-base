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
 * The RebuildFrameworkCache event handler deletes the events and routes
 * cache and executes the activation method on all modules.
 * 
 * @package Zepi\Core\Management
 * @subpackage EventHandler
 * @author Matthias Zobrist <matthias.zobrist@zepi.net>
 * @copyright Copyright (c) 2015 zepi
 */

namespace Zepi\Core\Management\EventHandler;

use \Zepi\Turbo\FrameworkInterface\CliEventHandlerInterface;
use \Zepi\Turbo\Framework;
use \Zepi\Turbo\Response\Response;
use \Zepi\Turbo\Request\CliRequest;
use \Zepi\Core\Management\Exception;

/**
 * The RebuildFrameworkCache event handler deletes the events and routes
 * cache and executes the activation method on all modules.
 * 
 * @author Matthias Zobrist <matthias.zobrist@zepi.net>
 * @copyright Copyright (c) 2015 zepi
 */
class RebuildFrameworkCache implements CliEventHandlerInterface
{
    /**
     * The RebuildFrameworkCache event handler deletes the events and routes
     * cache and executes the activation method on all modules.
     * 
     * @access public
     * @param \Zepi\Turbo\Framework $framework
     * @param \Zepi\Turbo\Request\CliRequest $request
     * @param \Zepi\Turbo\Response\Response $response
     */
    public function execute(Framework $framework, CliRequest $request, Response $response)
    {
        $framework->getRuntimeManager()->clearCache(false);
        $framework->getRouteManager()->clearCache(false);
        
        $framework->getModuleManager()->reactivateModules();
        
        $response->setOutputPart('cacheCleared', 'The cache was successfully cleared and rebuilt!');
    }
}
