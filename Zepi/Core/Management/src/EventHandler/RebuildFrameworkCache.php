<?php
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

use \Zepi\Turbo\FrameworkInterface\EventHandlerInterface;
use \Zepi\Turbo\Framework;
use \Zepi\Turbo\Request\RequestAbstract;
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
class RebuildFrameworkCache implements EventHandlerInterface
{
    /**
     * The RebuildFrameworkCache event handler deletes the events and routes
     * cache and executes the activation method on all modules.
     * 
     * @access public
     * @param \Zepi\Turbo\Framework $framework
     * @param \Zepi\Turbo\Request\RequestAbstract $request
     * @param \Zepi\Turbo\Response\Response $response
     * @param mixed $value
     * 
     * @throws Zepi\Core\Management\Exception The cache can only rebuilt by command line interface!
     */
    public function executeEvent(Framework $framework, RequestAbstract $request, Response $response, $value = '')
    {
        // If the event is executed by an other request than an cli request we throw a new exception
        if (!($request instanceof CliRequest)) {
            throw new Exception('The cache can only rebuilt by command line interface!');
            return;
        }

        $framework->getEventManager()->clearCache(false);
        $framework->getRouteManager()->clearCache(false);
        
        $framework->getModuleManager()->reactivateModules();
        
        $response->setOutputPart('cacheCleared', 'The cache was successfully cleared and rebuilt!');
    }
}
