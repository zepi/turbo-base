<?php
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
class MetaInformationManager
{
    /**
     * @access protected
     * @var string
     */
    protected $_title;
    
    /**
     * @access protected
     * @var string
     */
    protected $_delimiter = ' - ';
    
    /**
     * @access protected
     * @var string
     */
    protected $_name;
    
    /**
     * Sets the title for the request
     * 
     * @access public
     * @param string $title
     */
    public function setTitle($title)
    {
        $this->_title = $title;
    }
    
    /**
     * Sets the delimitier
     * 
     * @access public
     * @param string $delimiter
     */
    public function setDelimiter($delimiter)
    {
        $this->_delimiter = $delimiter;
    }
    
    /**
     * Sets the name of the framework instance
     * 
     * @access public
     * @param string $name
     */
    public function setName($name)
    {
        $this->_name = $name;
    }
    
    /**
     * Returns the title for the request
     * 
     * @access public
     * @return string
     */
    public function getTitle()
    {
        $title = '';

        /**
         * If there is a title, add it to the returned title
         */
        if (trim($this->_title) != '') {
            $title = trim($this->_title);
        }
        
        /**
         * If there is a name of the framework instance, add it to 
         * the returned title
         */
        if (trim($this->_name) != '') {
            if (trim($title) != '') {
                $title .= $this->_delimiter;
            }
            
            $title .= trim($this->_name);
        }
        
        /**
         * If the title is empty, return a default title
         */
        if (trim($title) == '') {
            $title = 'zepi Turbo';
        }
        
        return $title;
    }
}
