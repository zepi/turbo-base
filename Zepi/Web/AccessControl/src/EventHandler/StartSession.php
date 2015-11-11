<?php
/**
 * Starts the session after the initialization of the framework
 * core and is one of the first events which will be executed.
 * 
 * @package Zepi\Web\AccessControl
 * @subpackage EventHandler
 * @author Matthias Zobrist <matthias.zobrist@zepi.net>
 * @copyright Copyright (c) 2015 zepi
 */

namespace Zepi\Web\AccessControl\EventHandler;

use \Zepi\Turbo\FrameworkInterface\EventHandlerInterface;
use \Zepi\Turbo\Framework;
use \Zepi\Turbo\Request\RequestAbstract;
use \Zepi\Turbo\Response\Response;

/**
 * Starts the session after the initialization of the framework
 * core and is one of the first events which will be executed.
 * 
 * @author Matthias Zobrist <matthias.zobrist@zepi.net>
 * @copyright Copyright (c) 2015 zepi
 */
class StartSession implements EventHandlerInterface
{
    /**
     * Starts the session after the initialization of the framework
     * core and is one of the first events which will be executed.
     * 
     * @access public
     * @param \Zepi\Turbo\Framework $framework
     * @param \Zepi\Turbo\Request\RequestAbstract $request
     * @param \Zepi\Turbo\Response\Response $response
     * @param mixed $value
     */
    public function executeEvent(Framework $framework, RequestAbstract $request, Response $response, $value = null)
    {
        if (!$request instanceof \Zepi\Turbo\Request\WebRequest) {
            return;
        }
        
        // Get the session manager
        $sessionManager = $framework->getInstance('\\Zepi\\Web\\AccessControl\\Manager\\SessionManager');
        
        // Reinitialize the session
        $sessionManager->reinitializeSession($framework, $request, $response);
    }
}
