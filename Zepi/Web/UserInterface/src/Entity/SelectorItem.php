<?php

/**
 * Interface for the 
 *
 * @package Zepi\Web\UserInterface
 * @subpackage Entity
 * @author Matthias Zobrist <matthias.zobrist@zepi.net>
 * @copyright Copyright (c) 2015 zepi
 *           
 */
namespace Zepi\Web\UserInterface\Entity;

/**
 * Representats one access level
 *
 * @author Matthias Zobrist <matthias.zobrist@zepi.net>
 * @copyright Copyright (c) 2015 zepi
 */
class SelectorItem
{
    /**
     * @access protected
     * @var string
     */
    protected $_key;
    
    /**
     * @access protected
     * @var string
     */
    protected $_name;
    
    /**
     * @access protected
     * @var string
     */
    protected $_description;
    
    /**
     * @access protected
     * @var string
     */
    protected $_icon;
    
    /**
     * @access protected
     * @var boolean
     */
    protected $_disabled = false;
    
    /**
     * Constructs the object
     *
     * @access public
     * @param string $key
     * @param string $name
     * @param string $description
     * @param string $icon
     * @param boolean $disabled
     */
    public function __construct($key, $name, $description, $icon, $disabled)
    {
        $this->_key = $key;
        $this->_name = $name;
        $this->_description = $description;
        $this->_icon = $icon;
        $this->_disabled = $disabled;
    }
    
    /**
     * Returns the key of the access level
     *
     * @access public
     * @return string
     */
    public function getKey()
    {
        return $this->_key;
    }
    
    /**
     * Returns the name of the access level
     *
     * @access public
     * @return string
     */
    public function getName()
    {
        return $this->_name;
    }
    
    /**
     * Returns the description of the access level
     *
     * @access public
     * @return string
     */
    public function getDescription()
    {
        return $this->_description;
    }
    
    /**
     * Returns the module namespace of the access level
     *
     * @access public
     * @return string
     */
    public function getNamespace()
    {
        return $this->_namespace;
    }
    
    /**
     * Returns the hash of the access level
     *
     * @access public
     * @return string
     */
    public function getHash()
    {
        return md5($this->_name) . '-' . md5($this->_key);
    }
    
    /**
     * Returns the name of the selector item
     * 
     * @access public
     * @return string
     */
    public function getIcon()
    {
        return $this->_icon;
    }
    
    /**
     * Returns true if the selector item is disabled
     * 
     * @access public
     * @return boolean
     */
    public function isDisabled()
    {
        return ($this->_disabled);
    }
}
