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
    protected $extension = 'phtml';
    
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
    