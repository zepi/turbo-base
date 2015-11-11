<?php
/**
 * Column
 * 
 * @package Zepi\Web\UserInterface
 * @subpackage Table
 * @author Matthias Zobrist <matthias.zobrist@zepi.net>
 * @copyright Copyright (c) 2015 zepi
 */

namespace Zepi\Web\UserInterface\Table;

/**
 * Column
 * 
 * @author Matthias Zobrist <matthias.zobrist@zepi.net>
 * @copyright Copyright (c) 2015 zepi
 */
class Column
{
    const WIDTH_AUTO = 'auto';
    
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
     * @var mixed
     */
    protected $_width;
    
    /**
     * @access protected
     * @var boolean
     */
    protected $_filterable;
    
    /**
     * @access protected
     * @var string
     */
    protected $_fieldType;
    
    /**
     * @access protected
     * @var string
     */
    protected $_classes;
    
    /**
     * Constructs the object
     * 
     * @access public
     * @param string $key
     * @param string $name
     * @param mixed $width
     * @param boolean $filterable
     * @param string $fieldType
     * @param string $classes
     */
    public function __construct($key, $name, $width, $filterable = false, $fieldType = 'text', $classes = '')
    {
        $this->_key = $key;
        $this->_name = $name;
        $this->_width = $width;
        $this->_filterable = $filterable;
        $this->_fieldType = $fieldType;
        $this->_classes = $classes;
    }
    
    /**
     * Returns the key of the column
     * 
     * @access public
     * @return string
     */
    public function getKey()
    {
        return $this->_key;
    }
    
    /**
     * Returns the name of the column
     * 
     * @access public
     * @return string
     */
    public function getName()
    {
        return $this->_name;
    }
    
    /**
     * Returns the width of the column
     * 
     * @access public
     * @return mixed
     */
    public function getWidth()
    {
        return $this->_width;
    }
    
    /**
     * Returns the correct with for the html attribute
     * 
     * @access public
     * @return string
     */
    public function getHtmlWidth()
    {
        if (is_string($this->_width) && $this->_width === self::WIDTH_AUTO) {
            return '';
        } else if (intval($this->_width) > 0) {
            return $this->_width . '%';
        }
        
        return $this->_width;
    }
    
    /**
     * Returns true if the column is filterable
     * 
     * @access public
     * @return boolean
     */
    public function isFilterable()
    {
        return $this->_filterable;
    }
    
    /**
     * Returns the field type of the column
     * 
     * @access public
     * @return string
     */
    public function getFieldType()
    {
        return $this->_fieldType;
    }
    
    /**
     * Returns all classes of the column
     * 
     * @access public
     * @return string
     */
    public function getClasses()
    {
        return $this->_classes;
    }
}
