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
 * Frontend helper to deliver a global api to the main
 * managers
 * 
 * @package Zepi\Web\UserInterface
 * @subpackage Frontend
 * @author Matthias Zobrist <matthias.zobrist@zepi.net>
 * @copyright Copyright (c) 2015 zepi
 */

namespace Zepi\Web\UserInterface\Frontend;

use \Zepi\Core\Utils\Manager\ConfigurationManager;
use \Zepi\Core\Language\Manager\TranslationManager;
use \Zepi\Web\General\Manager\TemplatesManager;
use \Zepi\Web\General\Manager\MetaInformationManager;
use \Zepi\Web\General\Manager\MenuManager;
use \Zepi\Web\UserInterface\Renderer\Layout;
use \Zepi\Web\UserInterface\Renderer\Table;
use \Zepi\Web\UserInterface\Renderer\OverviewPage;

/**
 * Frontend helper to deliver a global api to the main
 * managers
 * 
 * @author Matthias Zobrist <matthias.zobrist@zepi.net>
 * @copyright Copyright (c) 2015 zepi
 */
class FrontendHelper
{
    /**
     * @access protected
     * @var \Zepi\Core\Utils\Manager\ConfigurationManager
     */
    protected $configurationManager;
    
    /**
     * @access protected
     * @var \Zepi\Core\Language\Manager\TranslationManager
     */
    protected $translationManager;
    
    /**
     * @access protected
     * @var \Zepi\Web\General\Manager\TemplatesManager
     */
    protected $templatesManager;
    
    /**
     * @access protected
     * @var \Zepi\Web\General\Manager\MetaInformationManager
     */
    protected $metaInformationManager;
    
    /**
     * @access protected
     * @var \Zepi\Web\General\Manager\MenuManager
     */
    protected $menuManager;
    
    /**
     * @access protected
     * @var \Zepi\Web\UserInterface\Renderer\Layout
     */
    protected $layoutRenderer;
    
    /**
     * @access protected
     * @var \Zepi\Web\UserInterface\Renderer\OverviewPage
     */
    protected $overviewPageRenderer;
    
    /**
     * @access protected
     * @var \Zepi\Web\UserInterface\Renderer\Table
     */
    protected $tableRenderer;
    
    /**
     * @access protected
     * @var string
     */
    protected $completeTitle;
    
    /**
     * Constructs the object
     * 
     * @access public
     * @param \Zepi\Core\Utils\Manager\ConfigurationManager $configurationManager
     * @param \Zepi\Core\Language\Manager\TranslationManager $translationManager
     * @param \Zepi\Web\General\Manager\TemplatesManager $templatesManager
     * @param \Zepi\Web\General\Manager\MetaInformationManager $metaInformationManager
     * @param \Zepi\Web\General\Manager\MenuManager $menuManager
     * @param \Zepi\Web\UserInterface\Renderer\Layout $layoutRenderer
     * @param \Zepi\Web\UserInterface\Renderer\OverviewPage $overviewPageRenderer
     * @param \Zepi\Web\UserInterface\Renderer\Table $tableRenderer
     */
    public function __construct(
        ConfigurationManager $configurationManager,
        TranslationManager $translationManager,
        TemplatesManager $templatesManager,
        MetaInformationManager $metaInformationManager,
        MenuManager $menuManager,
        Layout $layoutRenderer,
        OverviewPage $overviewPageRenderer,
        Table $tableRenderer
    ) {
        $this->configurationManager = $configurationManager;
        $this->translationManager = $translationManager;
        $this->templatesManager = $templatesManager;
        $this->metaInformationManager = $metaInformationManager;
        $this->menuManager = $menuManager;
        $this->layoutRenderer = $layoutRenderer;
        $this->overviewPageRenderer = $overviewPageRenderer;
        $this->tableRenderer = $tableRenderer;
    }
    
    /**
     * Returns the setting for the given group and key
     * 
     * @access public
     * @param string $path
     * @return mixed
     */
    public function getSetting($path)
    {
        return $this->configurationManager->getSetting($path);
    }
    
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
        return $this->translationManager->translate($string, $namespace, $arguments);
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
        return $this->templatesManager->renderTemplate($key, $additionalData);
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
        $this->completeTitle = $title;
        
        if ($function != '') {
            $this->completeTitle .= ' - ' . $function;
            $this->menuManager->setBreadcrumbFunction($function);
        }
        
        $this->metaInformationManager->setTitle($this->completeTitle);
    }
    
    /**
     * Returns the the title of the page
     *
     * @access public
     * @return string
     */
    public function getTitle()
    {
        return $this->completeTitle;
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
        if ($key != '') {
            $menuEntry = $this->menuManager->getMenuEntryForKey($key);
            
            if ($menuEntry !== false) {
                $this->menuManager->setActiveMenuEntry($menuEntry);
            }
        } else {
            $this->menuManager->activateCorrectMenuEntry();
        }
        
        return $this->menuManager->getActiveMenuEntry();
    }
    
    /**
     * Returns the menu manager
     *
     * @access public
     * @return \Zepi\Web\General\Manager\MenuManager
     */
    public function getMenuManager()
    {
        return $this->menuManager;
    }
    
    /**
     * Returns the layout renderer
     * 
     * @access public
     * @return \Zepi\Web\UserInterface\Renderer\Layout
     */
    public function getLayoutRenderer()
    {
        return $this->layoutRenderer;
    }
    
    /**
     * Returns the overview page renderer
     * 
     * @access public
     * @return \Zepi\Web\UserInterface\Renderer\OverviewPage
     */
    public function getOverviewPageRenderer()
    {
        return $this->overviewPageRenderer;
    }
    
    /**
     * Returns the table renderer
     * 
     * @access public
     * @return \Zepi\Web\UserInterface\Renderer\Table
     */
    public function getTableRenderer()
    {
        return $this->tableRenderer;
    }
}
