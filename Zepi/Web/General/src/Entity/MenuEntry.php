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
 * The MenuEntry representats an entry in the navigation.
 * 
 * @package Zepi\Web\General
 * @subpackage Entity
 * @author Matthias Zobrist <matthias.zobrist@zepi.net>
 * @copyright Copyright (c) 2015 zepi
 */

namespace Zepi\Web\General\Entity;

/**
 * The MenuEntry representats an entry in the navigation.
 * 
 * @author Matthias Zobrist <matthias.zobrist@zepi.net>
 * @copyright Copyright (c) 2015 zepi
 */
class MenuEntry
{
    /**
     * @access protected
     * @var string
     */
    protected $key;
    
    /**
     * @access protected
     * @var string
     */
    protected $name;
    
    /**
     * @access protected
     * @var string
     */
    protected $target;
    
    /**
     * @access protected
     * @var string
     */
    protected $window;
    
    /**
     * @access protected
     * @var string
     */
    protected $class;
    
    /**
     * @access protected
     * @var string
     */
    protected $iconClass;
    
    /**
     * @access protected
     * @var boolean
     */
    protected $hideWhenEmpty;
    
    /**
     * @access protected
     * @var MenuEntry
     */
    protected $parent = null;
    
    /**
     * @access protected
     * @var array
     */
    protected $children = array();
    
    /**
     * @access protected
     * @var boolean
     */
    protected $isActive = false;
    
    /**
     * Constructs the object
     * 
     * @access public
     * @param string $key
     * @param string $name
     * @param string $target
     * @param string $iconClass
     * @param string $class
     * @param string $window
     * @param boolean $hideWhenEmpty
     */
    public function __construct(
        $key, 
        $name,
        $target = '#', 
        $iconClass = '',
        $class = '',
        $window = '_self',
        $hideWhenEmpty = false
    ) {
        $this->key = $key;
        $this->name = $name;
        $this->target = $target;
        $this->window = $window;
        $this->class = $class;
        $this->iconClass = $iconClass;
        $this->hideWhenEmpty = $hideWhenEmpty;
    }
    
    /**
     * Returns the key of the MenuEntry
     * 
     * @access public
     * @return string
     */
    public function getKey()
    {
        return $this->key;
    }
    
    /**
     * Returns the name of the MenuEntry
     * 
     * @access public
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }
    
    /**
     * Returns the target (e.g. url) of the MenuEntry
     * 
     * @access public
     * @return string
     */
    public function getTarget()
    {
        return $this->target;
    }
    
    /**
     * Returns the window of the MenuEntry, e.g. blank
     * or self.
     * 
     * @access public
     * @return string
     */
    public function getWindow()
    {
        return $this->window;
    }
    
    /**
     * Returns the class of the MenuEntry
     * 
     * @access public
     * @return string
     */
    public function getClass()
    {
        return $this->class;
    }
    
    /**
     * Returns the icon class of the MenuEntry
     * 
     * @access public
     * @return string
     */
    public function getIconClass()
    {
        return $this->iconClass;
    }
    
    /**
     * Returns true if the MenuEntry should be hidden when there are no children
     * 
     * @access public
     * @return boolean
     */
    public function hideWhenEmpty()
    {
        return ($this->hideWhenEmpty);
    }
    
    /**
     * Returns the children of the MenuEntry
     * 
     * @access public
     * @return array
     */
    public function getChildren()
    {
        return $this->children;
    }
    
    /**
     * Returns true if the menu entry has one or more children.
     * 
     * @access public
     * @return boolean
     */
    public function hasChildren()
    {
        return (count($this->children) > 0);
    }
    
    /**
     * Returns true if the menu entry has one or more children which are
     * visible. Otherwise returns false.
     * 
     * @access public
     * @return boolean
     */
    public function hasVisibleChildren()
    {
        foreach ($this->children as $child) {
            if (!($child instanceof \Zepi\Web\General\Entity\HiddenMenuEntry)) {
                return true;
            }
        }
        
        return false;
    }
    
    /**
     * Returns true if the menu entry should be hidden
     * 
     * @access public
     * @return boolean
     */
    public function shouldHide()
    {
        if (!$this->hideWhenEmpty) {
            return false;
        }
        
        foreach ($this->children as $child) {
            if (!$child->shouldHide()) {
                return false;
            }
        }
        
        return true;
    }
    
    /**
     * Sets the children of the menu entry.
     * 
     * @access public
     * @param array $children
     * @return boolean
     */
    public function setChildren($children)
    {
        if (!is_array($children)) {
            return false;
        }
         
        $this->children = $children;
        
        return true;
    }
    
    /**
     * Adds a MenuEntry as child and add this MenuEntry as 
     * parent of the child.
     * 
     * @access public
     * @param \Zepi\Web\General\Entity\MenuEntry $child
     * @return boolean
     */
    public function addChild(MenuEntry $child)
    {
        if (isset($this->children[$child->getKey()])) {
            return false;
        }
        
        $this->children[$child->getKey()] = $child;
        
        // Sets this MenuEntry as parent of the child
        $child->setParent($this);
        
        return true;
    }
    
    /**
     * Returns the parent menu entry
     * 
     * @access public
     * @return MenuEntry
     */
    public function getParent()
    {
        return $this->parent;
    }
    
    /**
     * Sets the parent menu entry.
     * 
     * @access public
     * @param \Zepi\Web\General\Entity\MenuEntry $parent
     */
    public function setParent(MenuEntry $parent)
    {
        $this->parent = $parent;
    }
    
    /**
     * Returns true if the menu entry has a parent menu entry
     * 
     * @access public
     * @return boolean
     */
    public function hasParent()
    {
        return ($this->parent != null);
    }
    
    /**
     * Returns the best available target, if the menu entry has no target the 
     * target of the parent object will be returned.
     * 
     * @return string
     */
    public function getBestTarget()
    {
        if ($this->hasParent() && ($this->target == '' || $this->target == '#')) {
            return $this->parent->getBestTarget();
        }
        
        return $this->target;
    }
    
    /**
     * Sets the menu entry as active
     * 
     * @access public
     * @param boolean $isActive
     */
    public function setActive($isActive)
    {
        $this->isActive = (bool) $isActive;
        
        if ($this->parent !== null) {
            $this->parent->setActive($isActive);
        }
    }
    
    /**
     * Returns true if the menu entry is active.
     * 
     * @access public
     * @return boolean
     */
    public function isActive()
    {
        return ($this->isActive);
    }
}
