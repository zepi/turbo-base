<?php
/**
 * Abstract Renderer to define a Renderer
 * 
 * @package Zepi\Web\General
 * @subpackage Template
 * @author Matthias Zobrist <matthias.zobrist@zepi.net>
 * @copyright Copyright (c) 2015 zepi
 */

namespace Zepi\Web\General\Template;

use \Zepi\Turbo\Framework;
use \Zepi\Turbo\Request\RequestAbstract;
use \Zepi\Turbo\Response\Response;
use \Zepi\Web\General\Manager\TemplatesManager;
use \Zepi\Core\Language\Manager\TranslationManager;

/**
 * Abstract Renderer to define a Renderer
 * 
 * @author Matthias Zobrist <matthias.zobrist@zepi.net>
 * @copyright Copyright (c) 2015 zepi
 */
abstract class RendererAbstract
{
    /**
     * @access protected
     * @var string
     */
    protected $_extension = '';
    
    /**
     * @access protected
     * @var \Zepi\Web\General\Manager\TemplatesManager
     */
    protected $_templatesManager;
    
    /**
     * @access protected
     * @var \Zepi\Core\Language\Manager\TranslationManager
     */
    protected $_translationManager;
    
    /**
     * Constructs the object
     * 
     * @access public
     * @param \Zepi\Web\General\Manager\TemplatesManager $templatesManager
     * @param \Zepi\Core\Language\Manager\TranslationManager $translationManager
     */
    public function __construct(TemplatesManager $templatesManager, TranslationManager $translationManager)
    {
        $this->_templatesManager = $templatesManager;
        $this->_translationManager = $translationManager;
    }
    
    /**
     * Returns the file extension for the template
     * files. The renderer should be able to render all
     * template files with the given extension.
     * 
     * @access public
     * @return string
     */
    public function getExtension()
    {
        return $this->_extension;
    }
    
    /**
     * Renders a template file. This function renders the given 
     * template file and returns the output to the template manager.
     * 
     * @abstract
     * @access public
     * @param string $templateFile
     * @param \Zepi\Turbo\Framework $framework
     * @param \Zepi\Turbo\Request\RequestAbstract $request
     * @param \Zepi\Turbo\Response\Response $response
     * @param array $additionalData
     * @return string
     */
    abstract public function renderTemplateFile($templateFile, Framework $framework, RequestAbstract $request, Response $response, $additionalData = array());
    
    /**
     * Renders the template and returns the content.
     * 
     * @access public
     * @param string $key
     * @param array $additionalData
     * @return string
     */
    public function renderTemplate($key, $additionalData = array())
    {
        return $this->_templatesManager->renderTemplate($key, $additionalData);
    }
    
    /**
     * Renders and prints the content of a  template.
     * 
     * @access public
     * @param string $key
     * @param array $additionalData
     */
    public function printTemplate($key, $additionalData = array())
    {
        echo $this->renderTemplate($key, $additionalData);
    }
    
    /**
     * Gives the string to translate and the domain to the translation manager
     * and returns the value
     * 
     * @access public
     * @param string $string
     * @param string $namespace
     * @return string
     */
    public function translate($string, $namespace)
    {
        return $this->_translationManager->translate($string, $namespace);
    }
}
