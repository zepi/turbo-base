<?php
/**
 * Renders HTML files and returns the content.
 * 
 * @package Zepi\Renderer\HtmlRenderer
 * @subpackage Renderer
 * @author Matthias Zobrist <matthias.zobrist@zepi.net>
 * @copyright Copyright (c) 2015 zepi
 */

namespace Zepi\Renderer\HtmlRenderer\Renderer;

use \Zepi\Turbo\Framework;
use \Zepi\Turbo\Request\RequestAbstract;
use \Zepi\Turbo\Response\Response;
use \Zepi\Web\General\Template\RendererAbstract;

/**
 * Renders HTML files and returns the content.
 * 
 * @author Matthias Zobrist <matthias.zobrist@zepi.net>
 * @copyright Copyright (c) 2015 zepi
 */
class Renderer extends RendererAbstract
{
    /**
     * @access protected
     * @var string
     */
    protected $_extension = 'phtml';
    
    /**
     * Renders a template file. This function renders the given 
     * template file and returns the output to the template manager.
     * 
     * @access public
     * @param string $templateFile
     * @param \Zepi\Turbo\Framework $framework
     * @param \Zepi\Turbo\Request\RequestAbstract $request
     * @param \Zepi\Turbo\Response\Response $response
     * @param array $additionalData
     * @return string
     */
    public function renderTemplateFile($templateFile, Framework $framework, RequestAbstract $request, Response $response, $additionalData = array())
    {
        if (!file_exists($templateFile)) {
            return;
        }
        
        // Start the output buffer
        ob_start();
        
        // Render the template
        include($templateFile);
        
        // Get the content and deactivate the output buffer
        $content = ob_get_contents();
        ob_end_clean();
        
        return $content;
    }
}
    