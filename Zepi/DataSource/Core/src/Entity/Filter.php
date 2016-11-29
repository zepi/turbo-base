<?php
/*
 * The MIT License (MIT)
 *
 * Copyright (c) 2016 zepi
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
 * Filter
 * 
 * @package Zepi\DataSource\Core
 * @subpackage Entity
 * @author Matthias Zobrist <matthias.zobrist@zepi.net>
 * @copyright Copyright (c) 2016 zepi
 */

namespace Zepi\DataSource\Core\Entity;

/**
 * Filter
 * 
 * @author Matthias Zobrist <matthias.zobrist@zepi.net>
 * @copyright Copyright (c) 2016 zepi
 */
class Filter
{
    /**
     * @access protected
     * @var string
     */
    protected $fieldName;
    
    /**
     * @access protected
     * @var mixed
     */
    protected $neededValue;
    
    /**
     * @access protected
     * @var string
     */
    protected $mode;
    
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
        $this->fieldName = $fieldName;
        $this->neededValue = $neededValue;
        $this->mode = $mode;
    }
    
    /**
     * Returns the field name
     * 
     * @access public
     * @return string
     */
    public function getFieldName()
    {
        return $this->fieldName;
    }
    
    /**
     * Returns the needed value
     * 
     * @access public
     * @return mixed
     */
    public function getNeededValue()
    {
        return $this->neededValue;
    }
    
    /**
     * Returns the mode of the filter
     * 
     * @access public
     * @return string
     */
    public function getMode()
    {
        return $this->mode;
    }
    
    /**
     * Returns the key for the filter
     * 
     * @access public
     * @return string
     */
    public function getKey()
    {
        return $this->fieldName . $this->mode . $this->neededValue;
    }
}
