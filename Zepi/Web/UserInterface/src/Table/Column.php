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
