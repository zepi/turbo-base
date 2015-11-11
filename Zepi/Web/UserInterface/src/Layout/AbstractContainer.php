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
 * AbstractContainer
 * 
 * @package Zepi\Web\UserInterface
 * @subpackage Layout
 * @author Matthias Zobrist <matthias.zobrist@zepi.net>
 * @copyright Copyright (c) 2015 zepi
 */

namespace Zepi\Web\UserInterface\Layout;

/**
 * AbstractContainer
 * 
 * @author Matthias Zobrist <matthias.zobrist@zepi.net>
 * @copyright Copyright (c) 2015 zepi
 */
abstract class AbstractContainer
{
    /**
     * @access protected
     * @var string
     */
    protected $_key = '';
    
    /**
     * @access protected
     * @var array
     */
    protected $_parts = array();
    
    /**
     * @access protected
     * @var array
     */
    protected $_classes = array();
    
    /**
     * @access protected
     * @var string
     */
    protected $_templateKey = '';
    
    /**
     * @access protected
     * @var integer
     */
    protected $_priority = 10;
    
    /**
     * @access protected
     * @var \Zepi\Web\UserInterface\Layout\AbstractContainer
     */
    protected $_parent;
    
    /**
     * Construct the object
     * 
     * @access public
     * @param array $parts
     * @param array $classes
     * @param string $key
     * @param integer $priority
     */
    public function __construct($parts = array(), $classes = array(), $key = '', $priority = 10)
    {
        if (is_array($parts)) {
            foreach ($parts as $part) {
                $this->addPart($part);
            }
        }
        
        if (is_array($classes)) {
            $this->_classes = $classes;
        }
        
        $this->_key = uniqid();
        if ($key != '') {
            $this->_key = $key;
        }
        
        $this->_priority = $priority;
    }
    
    /**
     * Returns the key for the container
     * 
     * @access public
     * @return string
     */
    public function getKey()
    {
        return $this->_key;
    }
    
    /**
     * Returns the html id for the container. The id is built by the keys
     * of this element and all parents.
     * 
     * @return string
     */
    public function getHtmlId()
    {
        $id = '';
        if (is_object($this->_parent)) {
            $id = $this->_parent->getHtmlId();
        }
        
        if ($this->_key !== '') {
            $id .= '-' . $this->_key;
        }
        
        return $id;
    }
    
    /**
     * Adds a Part to the Page
     * 
     * @access public
     * @param \Zepi\Web\UserInterface\Layout\Part $part
     */
    public function addPart(Part $part)
    {
        $this->_parts[] = $part;
        $part->setParent($this);
    }
    
    /**
     * Returns the part for the given key, if there is no part
     * with this key registred return false.
     * 
     * @access public
     * @return \Zepi\Web\UserInterface\Layout\Part
     */    
    public function getPart($key)
    {
        foreach ($this->_parts as $part) {
            if ($part->getKey() === $key) {
                return $part;
            }
        }
        
        return false;
    }
    
    /**
     * Returns an array with all parts
     * 
     * @access public
     * @return array
     */
    public function getParts()
    {
        return $this->_parts;
    }
    
    /**
     * Returns an array with all classes
     * 
     * @access public
     * @return array
     */
    public function getClasses()
    {
        return $this->_classes;
    }
    
    /**
     * Returns the template key of the container
     * 
     * @access public
     * @return string
     */
    public function getTemplateKey()
    {
        return $this->_templateKey;
    }
    
    /**
     * Returns the priority of the container
     * 
     * @access public
     * @return integer
     */
    public function getPriority()
    {
        return $this->_priority;
    }
    
    /**
     * Returns the container of this container
     * 
     * @access public
     * @return \Zepi\Web\UserInterface\Layout\AbstractContainer
     */
    public function getParent()
    {
        return $this->_parent;
    }
    
    /**
     * Sets the parent of this container
     * 
     * @access public
     * @param \Zepi\Web\UserInterface\Layout\AbstractContainer $parent
     */
    public function setParent(AbstractContainer $parent)
    {
        $this->_parent = $parent;
    }
    
    /**
     * Returns the object for the given key and type or false 
     * if the object doesn't exists.
     * 
     * @access public
     * @param string $key
     * @param string $type
     * @return false|mixed
     */
    public function searchPartByKeyAndType($key, $type = '\\Zepi\\Web\\UserInterface\\Layout\\AbstractContainer')
    {
        foreach ($this->_parts as $part) {
            if ((is_a($part, $type) || is_subclass_of($part, $type)) && $part->getKey() === $key) {
                return $part;
            }
            
            $result = $part->searchPartByKeyAndType($key, $type);
            if ($result !== false) {
                return $result;
            }
        }
        
        return false;
    }
    
    /**
     * Returns the next parent of this container for the given type
     * If there is no type like that the function will return false
     * 
     * @access public
     * @param string $type
     * @return false|mixed
     */
    public function getParentOfType($type)
    {
        if (is_a($this, $type)) {
            return $this;
        }
        
        if ($this->_parent !== null) {
            return $this->_parent->getParentOfType($type);
        }
        
        return false;
    }
    
    /**
     * Returns all children with the given type.
     * 
     * @access public
     * @param string $type
     * @param boolean $recursive
     * @return array
     */
    public function getChildrenByType($type, $recursive = true)
    {
        $results = array();

        foreach ($this->_parts as $part) {
            if (is_a($part, $type) || is_subclass_of($part, $type)) {
                $results[] = $part;
            }
            
            if ($recursive) {
                $results = array_merge($results, $part->getChildrenByType($type, $recursive));
            }
        }

        return $results;
    }
}
