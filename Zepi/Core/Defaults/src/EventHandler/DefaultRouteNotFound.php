<?php
/**
 * The DefaultRouteNotFound event handler will generate a 
 * route not found error message.
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
 * The DefaultRouteNotFound event handler will generate a 
 * route not found error message.
 * 
 * @author Matthias Zobrist <matthias.zobrist@zepi.net>
 * @copyright Copyright (c) 2015 zepi
 */
class DefaultRouteNotFound implements EventHandlerInterface
{
    /**
     * The DefaultRouteNotFound event handler will generate a 
     * route not found error message.
     * 
     * @access public
     * @param \Zepi\Turbo\Framework $framework
     * @param \Zepi\Turbo\Request\RequestAbstract $request
     * @param \Zepi\Turbo\Response\Response $response
     * @param mixed $value
     */
    public function executeEvent(Framework $framework, RequestAbstract $request, Response $response, $value = '')
    {
        $response->setOutputPart('404', 'The requested route is not available. We can\'t execute the request.');
    }
}
