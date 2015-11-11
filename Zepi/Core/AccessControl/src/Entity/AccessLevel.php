<?php

/**
 * Representats one access level
 *
 * @package Zepi\Core\AccessControl
 * @subpackage Entity
 * @author Matthias Zobrist <matthias.zobrist@zepi.net>
 * @copyright Copyright (c) 2015 zepi
 *           
 */
namespace Zepi\Core\AccessControl\Entity;

/**
 * Representats one access level
 *
 * @author Matthias Zobrist <matthias.zobrist@zepi.net>
 * @copyright Copyright (c) 2015 zepi
 */
class AccessLevel
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
    protected $_namespace;
    
    /**
     * Constructs the object
     * 
     * @access public
     * @param string $key
     * @param string $name
     * @param string $description
     * @param string $namespace
     */
    public function __construct($key, $name, $description, $namespace)
    {
        $this->_key = $key;
        $this->_name = $name;
        $this->_description = $description;
        $this->_namespace = $namespace;
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
        return md5($this->_namespace) . '-' . md5($this->_key);
    }
}
