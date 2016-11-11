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
 * This event handler lists all activated modules with the description
 * of each module.
 * 
 * @package Zepi\Core\Management
 * @subpackage EventHandler
 * @author Matthias Zobrist <matthias.zobrist@zepi.net>
 * @copyright Copyright (c) 2015 zepi
 */

namespace Zepi\Core\Management\EventHandler;

use \Zepi\Turbo\FrameworkInterface\CliEventHandlerInterface;
use \Zepi\Turbo\Framework;
use \Zepi\Turbo\Request\CliRequest;
use \Zepi\Turbo\Response\Response;
use \Zepi\Core\Management\Exception;

/**
 * This event handler lists all activated modules with the description
 * of each module.
 * 
 * @author Matthias Zobrist <matthias.zobrist@zepi.net>
 * @copyright Copyright (c) 2015 zepi
 */
class ListModules implements CliEventHandlerInterface
{
    /**
     * This event handler lists all activated modules with the description
     * of each module.
     * 
     * @access public
     * @param \Zepi\Turbo\Framework $framework
     * @param \Zepi\Turbo\Request\CliRequest $request
     * @param \Zepi\Turbo\Response\Response $response
     */
    public function execute(Framework $framework, CliRequest $request, Response $response)
    {
        $output = 'Activated modules:' . PHP_EOL;
        $output .= '==================' . PHP_EOL . PHP_EOL;
        $moduleManager = $framework->getModuleManager();
        foreach ($moduleManager->getModules() as $namespace => $module) {
            $properties = $moduleManager->getModuleProperties($module->getDirectory());
            $info = $properties['module'];
            
            $output .= '- ' . $info['name'] . ' ' . $info['version'] . ' (' . $namespace . '):' . PHP_EOL;
            $output .= '  ' . $info['description'] . PHP_EOL . PHP_EOL;
        }
        
        $response->setOutputPart('modules', $output);
    }
}
