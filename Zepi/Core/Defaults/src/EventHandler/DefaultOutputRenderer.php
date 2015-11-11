<?php
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
     * The DefaultOutputRenderer is the default output renderer.
     * The output will be text based and contains all output parts
     * from the response.
     * 
     * @access public
     * @param \Zepi\Turbo\Framework $framework
     * @param \Zepi\Turbo\Request\RequestAbstract $request
     * @param \Zepi\Turbo\Response\Response $response
     * @param mixed $value
     */
    public function executeEvent(Framework $framework, RequestAbstract $request, Response $response, $value = null)
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
