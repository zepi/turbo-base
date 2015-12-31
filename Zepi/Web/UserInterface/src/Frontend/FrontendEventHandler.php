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
 * Frontend event handler for normal web pages
 * 
 * @package Zepi\Web\UserInterface
 * @subpackage Frontend
 * @author Matthias Zobrist <matthias.zobrist@zepi.net>
 * @copyright Copyright (c) 2015 zepi
 */

namespace Zepi\Web\UserInterface\Frontend;

use \Zepi\Turbo\FrameworkInterface\WebEventHandlerInterface;
use \Zepi\Turbo\Framework;
use \Zepi\Turbo\Request\RequestAbstract;
use \Zepi\Turbo\Request\WebRequest;
use \Zepi\Turbo\Response\Response;
/**
 * Frontend event handler for normal web pages
 * 
 * @author Matthias Zobrist <matthias.zobrist@zepi.net>
 * @copyright Copyright (c) 2015 zepi
 */
abstract class FrontendEventHandler implements WebEventHandlerInterface
{
    /**
     * @access protected
     * @var \Zepi\Web\UserInterface\Frontend\FrontendHelper
     */
    protected $_frontendHelper;
    
    /**
     * Constructs the object
     * 
     * @access public
     * @param \Zepi\Web\UserInterface\Frontend\FrontendHelper $frontendHelper
     */
    public function __construct(FrontendHelper $frontendHelper)
    {
        $this->_frontendHelper = $frontendHelper;
    }
    
    /**
     * Executes the handler
     * 
     * @abstract
     * @access public
     * @param \Zepi\Turbo\Framework $framework
     * @param \Zepi\Turbo\Request\WebRequest $request
     * @param \Zepi\Turbo\Response\Response $response
     */
    abstract public function execute(Framework $framework, WebRequest $request, Response $response);

    /**
     * Translates a string
     *
     * @access public
     * @param string $string
     * @param string $namespace
     * @param array $arguments
     * @return string
     */
    public function translate($string, $namespace = null, $arguments = array())
    {
        return $this->_frontendHelper->translate($string, $namespace, $arguments);
    }
    
    /**
     * Renders a template
     *
     * @access public
     * @param string $key
     * @param array $additionalData
     * @return string
     */
    public function render($key, $additionalData = array())
    {
        return $this->_frontendHelper->render($key, $additionalData);
    }
    
    /**
     * Sets the title of the page
     *
     * @access public
     * @param string $title
     * @param string $function
     */
    public function setTitle($title, $function = '')
    {
        $this->_frontendHelper->setTitle($title, $function);
    }
    
    /**
     * Returns the the title of the page
     * 
     * @access public
     * @return string
     */
    public function getTitle()
    {
        return $this->_frontendHelper->getTitle();
    }
    
    /**
     * Activates the correct menu entry and returns the activated
     * menu entry
     *
     * @access public
     * @param string $key
     * @return \Zepi\Web\General\Entity\MenuEntry
     */
    public function activateMenuEntry($key = '')
    {
        return $this->_frontendHelper->activateMenuEntry($key);
    }
    
    /**
     * Returns the menu manager
     * 
     * @access public
     * @return \Zepi\Web\General\Manager\MenuManager
     */
    public function getMenuManager()
    {
        return $this->_frontendHelper->getMenuManager();
    }
    
    /**
     * Returns the layout renderer
     *
     * @access public
     * @return \Zepi\Web\UserInterface\Renderer\Layout
     */
    public function getLayoutRenderer()
    {
        return $this->_frontendHelper->getLayoutRenderer();
    }
    
    /**
     * Returns the overview page renderer
     *
     * @access public
     * @return \Zepi\Web\UserInterface\Renderer\OverviewPage
     */
    public function getOverviewPageRenderer()
    {
        return $this->_frontendHelper->getOverviewPageRenderer();
    }
    
    /**
     * Returns the table renderer
     *
     * @access public
     * @return \Zepi\Web\UserInterface\Renderer\Table
     */
    public function getTableRenderer()
    {
        return $this->_frontendHelper->getTableRenderer();
    }
}
