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
 * Manages template files and manages the renderer.
 * 
 * @package Zepi\Web\General
 * @subpackage Manager
 * @author Matthias Zobrist <matthias.zobrist@zepi.net>
 * @copyright Copyright (c) 2015 zepi
 */

namespace Zepi\Web\General\Manager;

use \Zepi\Turbo\Framework;
use \Zepi\Turbo\Request\RequestAbstract;
use \Zepi\Turbo\Response\Response;
use \Zepi\Turbo\Backend\ObjectBackendAbstract;
use \Zepi\Web\General\Template\RendererAbstract;

/**
 * Manages template files and manages the renderer.
 * 
 * @author Matthias Zobrist <matthias.zobrist@zepi.net>
 * @copyright Copyright (c) 2015 zepi
 */
class TemplatesManager
{
    /**
     * @access protected
     * @var array
     */
    protected $templates = array();
    
    /**
     * @access protected
     * @var \Zepi\Turbo\Framework
     */
    protected $framework;
    
    /**
     * @access protected
     * @var \Zepi\Turbo\Backend\ObjectBackendAbstract
     */
    protected $templatesObjectBackend;
    
    /**
     * @access protected
     * @var array
     */
    protected $renderer = array();
    
    /**
     * Constructs the object
     * 
     * @access public
     * @param \Zepi\Turbo\Framework $framework
     * @param \Zepi\Turbo\Backend\ObjectBackendAbstract $templatesObjectBackend
     */
    public function __construct(
        Framework $framework,
        ObjectBackendAbstract $templatesObjectBackend
    ) {
        $this->framework = $framework;
        $this->templatesObjectBackend = $templatesObjectBackend;
    }
    
    /**
     * Initializes the templates manager.
     * 
     * @access public
     */
    public function initializeTemplatesManager()
    {
        $this->loadTemplates();
    }
    
    /**
     * Loads the templates from the object backend
     * 
     * @access public
     */
    protected function loadTemplates()
    {
        $templates = $this->templatesObjectBackend->loadObject();
        if (!is_array($templates)) {
            $templates = array();
        }
        
        $this->templates = $templates;
    }
    
    /**
     * Saves the templates to the object backend.
     * 
     * @access public
     */
    protected function saveTemplates()
    {
        $this->templatesObjectBackend->saveObject($this->templates);
    }
    
    /**
     * Adds a new template file to the given key with the given priority.
     * 
     * @access public
     * @param string $key
     * @param string $file
     * @param integer $priority
     * @return boolean
     */
    public function addTemplate($key, $file, $priority = 10)
    {
        // If the file does not exists we can't add the file
        // to the templates...
        if (!file_exists($file) || !is_readable($file)) {
            return false;
        }
        
        // Create the arrays, if they are not existing
        if (!isset($this->templates[$key]) || !is_array($this->templates[$key])) {
            $this->templates[$key] = array();
        }
        
        $this->templates[$key][$priority] = $file;
        
        ksort($this->templates[$key]);
        
        $this->saveTemplates();
        
        return true;
    }
    
    /**
     * Removes the template file for the given key and priority.
     * 
     * @access public
     * @param string $key
     * @param string $file
     * @param integer $priority
     */
    public function removeTemplate($key, $file, $priority = 10)
    {
        // If we can't find the template file we do not have to remove anything.
        if (!isset($this->templates[$key][$priority])) {
            return false;
        }
        
        // Remove the template file.
        unset($this->templates[$key][$priority]);
    }
    
    /**
     * Add a renderer.
     * 
     * @access public
     * @param \Zepi\Web\General\Template\RendererAbstract $renderer
     * @return boolean
     */
    public function addRenderer(RendererAbstract $renderer)
    {
        $extension = $renderer->getExtension();

        if (isset($this->renderer[$extension])) {
            return false;
        }
        
        $this->renderer[$extension] = $renderer;
        return true;
    }
    
    /**
     * Renders all template files for the given template key.
     * 
     * @access public
     * @param string $key
     * @param array $additionalData
     * @return string
     */
    public function renderTemplate($key, $additionalData = array())
    {
        if (!isset($this->templates[$key])) {
            return '';
        }
        
        $output = '';
        
        // Render the template files
        foreach ($this->templates[$key] as $priority => $templateFile) {
            $output .= $this->searchRendererAndRenderTemplate($templateFile, $additionalData);
        }
        
        return $output;
    }
    
    /**
     * Searches a renderer for the given template file and renders the 
     * file.
     * 
     * @access protected
     * @param string $templateFile
     * @param array $additionalData
     * @return string
     */
    protected function searchRendererAndRenderTemplate($templateFile, $additionalData = array())
    {
        // Get the file information for the template file
        $fileInfo = pathinfo($templateFile);
        $extension = $fileInfo['extension'];
        
        // If we haven't a renderer for this extension we return an
        // empty string to prevent everything from failing.
        if (!isset($this->renderer[$extension])) {
            return '';
        }
        
        // Take the renderer...
        $renderer = $this->renderer[$extension];
        
        // ...and render the template file.
        return $renderer->renderTemplateFile(
            $templateFile, 
            $this->framework, 
            $this->framework->getRequest(), 
            $this->framework->getResponse(),
            $additionalData
        );
    }
}
