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
