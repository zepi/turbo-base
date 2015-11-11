<?php
/**
 * Registers the renderer
 * 
 * @package Zepi\Renderer\HtmlRenderer
 * @subpackage EventHandler
 * @author Matthias Zobrist <matthias.zobrist@zepi.net>
 * @copyright Copyright (c) 2015 zepi
 */

namespace Zepi\Renderer\HtmlRenderer\EventHandler;

use \Zepi\Turbo\FrameworkInterface\EventHandlerInterface;
use \Zepi\Turbo\Framework;
use \Zepi\Turbo\Request\RequestAbstract;
use \Zepi\Turbo\Response\Response;
use \Zepi\Renderer\HtmlRenderer\Exception;

/**
 * Registers the renderer
 * 
 * @author Matthias Zobrist <matthias.zobrist@zepi.net>
 * @copyright Copyright (c) 2015 zepi
 */
class RegisterRenderer implements EventHandlerInterface
{
    /**
     * Register the html renderer on the templates manager to render
     * the templates.
     * 
     * @access public
     * @param \Zepi\Turbo\Framework $framework
     * @param \Zepi\Turbo\Request\RequestAbstract $request
     * @param \Zepi\Turbo\Response\Response $response
     * @param mixed $value
     */
    public function executeEvent(Framework $framework, RequestAbstract $request, Response $response, $value = null)
    {
        $templatesManager = $framework->getInstance('\\Zepi\\Web\\General\\Manager\\TemplatesManager');
        $templatesManager->addRenderer($framework->getInstance('\\Zepi\\Renderer\\HtmlRenderer\\Renderer\\Renderer'));
    }
}
