<?php
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

use \Zepi\Turbo\FrameworkInterface\EventHandlerInterface;
use \Zepi\Turbo\Framework;
use \Zepi\Turbo\Request\RequestAbstract;
use \Zepi\Turbo\Response\Response;
use \Zepi\Turbo\Request\CliRequest;
use \Zepi\Core\Management\Exception;

/**
 * This event handler lists all activated modules with the description
 * of each module.
 * 
 * @author Matthias Zobrist <matthias.zobrist@zepi.net>
 * @copyright Copyright (c) 2015 zepi
 */
class ListModules implements EventHandlerInterface
{
    /**
     * This event handler lists all activated modules with the description
     * of each module.
     * 
     * @access public
     * @param \Zepi\Turbo\Framework $framework
     * @param \Zepi\Turbo\Request\RequestAbstract $request
     * @param \Zepi\Turbo\Response\Response $response
     * @param mixed $value
     * 
     * @throws Zepi\Core\Management\Exception The list with the activated modules can only be viewed from command line!
     */
    public function executeEvent(Framework $framework, RequestAbstract $request, Response $response, $value = null)
    {
        // If the event is not executed by a command line we throw a new exception
        if (!($request instanceof CliRequest)) {
            throw new Exception('The list with the activated modules can only be viewed from command line!');
            return;
        }
        
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
