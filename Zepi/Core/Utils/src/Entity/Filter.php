<?php
/**
 * Filter
 * 
 * @package Zepi\Core\Utils
 * @subpackage Entity
 * @author Matthias Zobrist <matthias.zobrist@zepi.net>
 * @copyright Copyright (c) 2015 zepi
 */

namespace Zepi\Core\Utils\Entity;

/**
 * Filter
 * 
 * @author Matthias Zobrist <matthias.zobrist@zepi.net>
 * @copyright Copyright (c) 2015 zepi
 */
class Filter
{
    /**
     * @access protected
     * @var string
     */
    protected $_fieldName;
    
    /**
     * @access protected
     * @var mixed
     */
    protected $_neededValue;
    
    /**
     * @access protected
     * @var string
     */
    protected $_mode;
    
    /**
     * Constructs the object
     * 
     * @access public
     * @param string $fieldName
     * @param mixed $neededValue
     * @param string $mode
     */
    public function __construct($fieldName, $neededValue, $mode = '=')
    {
        $this->_fieldName = $fieldName;
        $this->_neededValue = $neededValue;
        $this->_mode = $mode;
    }
    
    /**
     * Returns the field name
     * 
     * @access public
     * @return string
     */
    public function getFieldName()
    {
        return $this->_fieldName;
    }
    
    /**
     * Returns the needed value
     * 
     * @access public
     * @return mixed
     */
    public function getNeededValue()
    {
        return $this->_neededValue;
    }
    
    /**
     * Returns the mode of the filter
     * 
     * @access public
     * @return string
     */
    public function getMode()
    {
        return $this->_mode;
    }
    
    /**
     * Returns the key for the filter
     * 
     * @access public
     * @return string
     */
    public function getKey()
    {
        return $this->_fieldName . $this->_mode . $this->_neededValue;
    }
}
