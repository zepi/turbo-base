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
 * The DefaultOutputRenderer is the default output renderer.
 * The output will be text based and contains all output parts
 * from the response.
 * 
 * @package Zepi\Core\Defaults
 * @subpackage EventHandler
 * @author Matthias Zobrist <matthias.zobrist@zepi.net>
 * @copyright Copyright (c) 2015 zepi
 */

namespace Zepi\Core\Defaults\EventHandler;

use \Zepi\Turbo\FrameworkInterface\EventHandlerInterface;
use \Zepi\Turbo\Framework;
use \Zepi\Turbo\Request\RequestAbstract;
use \Zepi\Turbo\Response\Response;

/**
 * The DefaultOutputRenderer is the default output renderer.
 * The output will be text based and contains all output parts
 * from the response.
 * 
 * @author Matthias Zobrist <matthias.zobrist@zepi.net>
 * @copyright Copyright (c) 2015 zepi
 */
class DefaultOutputRenderer implements EventHandlerInterface
{
    /**
     * Executes the event. This function must handle all exceptions. 
     * If the function doesn't catch an exception, the exception 
     * will terminate the whole process.
     * 
     * @access public
     * @param \Zepi\Turbo\Framework $framework
     * @param \Zepi\Turbo\Request\RequestAbstract $request
     * @param \Zepi\Turbo\Response\Response $response
     */
    public function execute(Framework $framework, RequestAbstract $request, Response $response)
    {
        if ($response->hasOutput()) {
            return;
        }
        
        $output = '                                                         ' . PHP_EOL
                . '                  _     ________  ______  ____  ____     ' . PHP_EOL
                . '    _______ _ __ (_)   /_  __/ / / / __ \/ __ )/ __ \    ' . PHP_EOL
                . '   |_  / _ \ \'_ \| |    / / / / / / /_/ / __  / / / /    ' . PHP_EOL
                . '    / /  __/ |_) | |   / / / /_/ / _, _/ /_/ / /_/ /     ' . PHP_EOL
                . '   /___\___| .__/|_|  /_/  \____/_/ |_/_____/\____/      ' . PHP_EOL
                . '           |_|                                           ' . PHP_EOL . PHP_EOL
                . '             (C) Copyright ' . date('Y') . ' by zepi' . PHP_EOL
                . '               https://turbo.zepi.net                   ' . PHP_EOL . PHP_EOL
                . '________________________________________________________' . PHP_EOL . PHP_EOL;
        
        foreach ($response->getOutputParts() as $part) {
            $output .= $part . PHP_EOL;
        }
        
        $response->setOutput($output);
    }
}
