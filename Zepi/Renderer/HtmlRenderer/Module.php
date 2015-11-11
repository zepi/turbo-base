<?php
/**
 * This module delivers an easy to use template system.
 * 
 * @package Zepi\Renderer\HtmlRenderer
 * @author Matthias Zobrist <matthias.zobrist@zepi.net>
 * @copyright Copyright (c) 2015 zepi
 */

namespace Zepi\Renderer\HtmlRenderer;

use \Zepi\Turbo\Module\ModuleAbstract;

/**
 * This module delivers an easy to use template system.
 * 
 * @author Matthias Zobrist <matthias.zobrist@zepi.net>
 * @copyright Copyright (c) 2015 zepi
 */
class Module extends ModuleAbstract
{
    /**
     * @access protected
     * @var \Zepi\Renderer\HtmlRenderer\Renderer\Renderer
     */
    protected $_htmlRenderer;
    
    /**
     * Initializes and return an instance of the given class name.
     * 
     * @access public
     * @param string $className
     * @return mixed
     */
    public function getInstance($className)
    {
        switch ($className) {
            case '\\Zepi\\Renderer\\HtmlRenderer\\Renderer\\Renderer':
                if ($this->_htmlRenderer === null) {
                    $this->_htmlRenderer = new $className(
                        $this->_framework->getInstance('\\Zepi\\Web\\General\\Manager\\TemplatesManager'),
                        $this->_framework->getInstance('\\Zepi\\Core\\Language\\Manager\\TranslationManager')
                    );
                }
                
                return $this->_htmlRenderer;
            break;
            
            default: 
                return new $className();
            break;
        }
    }
    
    /**
     * Initializes the module
     * 
     * @access public
     */
    public function initialize()
    {
        
    }
    
    /**
     * This action will be executed on the activation of the module
     * 
     * @access public
     * @param string $versionNumber
     * @param string $oldVersionNumber
     */
    public function activate($versionNumber, $oldVersionNumber = '')
    {
        $eventManager = $this->_framework->getEventManager();
        $eventManager->addEventHandler('\\Zepi\\Web\\General\\Event\\RegisterRenderers', '\\Zepi\\Renderer\\HtmlRenderer\\EventHandler\\RegisterRenderer');
    }
    
    /**
     * This action will be executed on the deactiviation of the module
     * 
     * @access public
     */
    public function deactivate()
    {
        
    }
}
