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
 * Replaces the event name with a redirect event if the url 
 * hasn't a slash at the end of the url.
 * 
 * @package Zepi\Web\General
 * @subpackage FilterHandler
 * @author Matthias Zobrist <matthias.zobrist@zepi.net>
 * @copyright Copyright (c) 2016 zepi
 */

namespace Zepi\Web\General\FilterHandler;

use \Zepi\Turbo\FrameworkInterface\FilterHandlerInterface;
use \Zepi\Turbo\Framework;
use \Zepi\Turbo\Request\RequestAbstract;
use \Zepi\Turbo\Request\WebRequest;
use \Zepi\Turbo\Response\Response;

/**
 * Replaces the event name with a redirect event if the url 
 * hasn't a slash at the end of the url.
 * 
 * @author Matthias Zobrist <matthias.zobrist@zepi.net>
 * @copyright Copyright (c) 2016 zepi
 */
class VerifyEventName implements FilterHandlerInterface
{
    /**
     * Replaces the event name with a redirect event if the url 
     * hasn't a slash at the end of the url.
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
        if (!($request instanceof WebRequest)) {
            return;
        }
        
        $fullUrl = $request->getRequestedUrl();
        
        $urlParts = parse_url($fullUrl);
        
        if (isset($urlParts['path'])) {
            $path = $urlParts['path'];
        
            if (substr($path, -1) !== '/' && strrpos($path, '.') < strrpos($path, '/')) {
                $path .= '/';
            }
        
            $urlParts['path'] = $path;
        }
        
        $completeUrl = $response->buildUrl($urlParts);
        
        if ($completeUrl !== $request->getRequestedUrl()) {
            $response->redirectTo($completeUrl);
            return null;
        }
        
        return $value;
    }
}
