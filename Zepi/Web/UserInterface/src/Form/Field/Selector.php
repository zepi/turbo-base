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
 * Form Element Selector
 * 
 * @package Zepi\Web\UserInterface
 * @subpackage Form\Field
 * @author Matthias Zobrist <matthias.zobrist@zepi.net>
 * @copyright Copyright (c) 2015 zepi
 */

namespace Zepi\Web\UserInterface\Form\Field;

use \Zepi\Web\UserInterface\Form\Field\FieldAbstract;

/**
 * Form Element Selector
 * 
 * @author Matthias Zobrist <matthias.zobrist@zepi.net>
 * @copyright Copyright (c) 2015 zepi
 */
class Selector extends FieldAbstract
{
    /**
     * @access protected
     * @var array
     */
    protected $_items = array();
    
    /**
     * @access protected
     * @var string
     */
    protected $_leftTitle;
    
    /**
     * @access protected
     * @var string
     */
    protected $_rightTitle;
    
    /**
     * @access protected
     * @var string
     */
    protected $_itemTemplate;
    
    /**
     * @access protected
     * @var array
     */
    protected $_leftItems = array();
    
    /**
     * @access protected
     * @var array
     */
    protected $_rightItems = array();
    
    /**
     * Constructs the object
     *
     * @access public
     * @param string $label
     * @param boolean $isMandatory
     * @param array $value
     * @param array $items
     * @param string $leftTitle
     * @param string $rightTitle
     * @param string $itemTemplate
     * @param string $helpText
     * @param array $classes
     * @param string $placeholder
     */
    public function __construct(
        $key, 
        $label, 
        $isMandatory = false, 
        $value = array(), 
        $items = array(), 
        $leftTitle = 'Available', 
        $rightTitle = 'Selected', 
        $itemTemplate = '\\Zepi\\Web\\UserInterface\\Templates\\Form\\Snippet\\SelectorItem', 
        $helpText = '', 
        $classes = array(), 
        $placeholder = ''
    ) {
        $this->_items = $items;
        $this->_leftTitle = $leftTitle;
        $this->_rightTitle = $rightTitle;
        $this->_itemTemplate = $itemTemplate;
        
        parent::__construct($key, $label, $isMandatory, $value, $helpText, $classes, $placeholder);
    }
    
    /**
     * Returns the name of the template to render the field
     * 
     * @access public
     * @return string
     */
    public function getTemplateName()
    {
        return '\\Zepi\\Web\\UserInterface\\Templates\\Form\\Field\\Selector';
    }
    
    /**
     * Returns true if the label of the field should be displayed
     *
     * @access public
     * @return boolean
     */
    public function displayLabel()
    {
        return false;
    }
    
    /**
     * Returns true if the field should be displayed full width
     *
     * @access public
     * @return boolean
     */
    public function fullWidth()
    {
        return true;
    }
    
    /**
     * Sets the html form value of the field
     *
     * @access public
     * @param string $value
     */
    public function setValue($value)
    {
        $hashs = json_decode($value);
        
        if ($hashs === false) {
            $this->_value = array();
            return;
        }

        $values = array();        
        foreach ($hashs as $hash) {
            $item = $this->_getItemByHash($hash);
            if ($item === false) {
                continue;
            }
            
            $values[] = $item->getKey();
        }
        
        $this->_value = $values;
        
        $this->_transformItems();
    }
    
    /**
     * Returns the item for the given hash or false
     * if the hash is not available.
     * 
     * @access public
     * @param string $hash
     * @return \Zepi\Web\UserInterface\Entity\SelectorItem|boolean
     */
    protected function _getItemByHash($hash)
    {
        foreach ($this->_items as $item) {
            if ($item->getHash() === $hash) {
                return $item;
            }
        }
        
        return false;
    }
    
    /**
     * Returns all items
     *  
     * @access public
     * @return array
     */
    public function getItems()
    {
        return $this->_items;
    }
    
    /**
     * Returns the title of the left side
     * 
     * @access public
     * @return string
     */
    public function getLeftTitle()
    {
        return $this->_leftTitle;
    }
    
    /**
     * Returns the title of the right side
     * 
     * @access public
     * @return string
     */
    public function getRightTitle()
    {
        return $this->_rightTitle;
    }
    
    /**
     * Returns the item template
     * 
     * @access public
     * @return string
     */
    public function getItemTemplate()
    {
        return $this->_itemTemplate;
    }
    
    /**
     * Returns the left items
     * 
     * @access public
     * @return array
     */
    public function getLeftItems()
    {
        // Transform the items into the two sides
        if (empty($this->_leftItems) && empty($this->_rightItems)) {
            $this->_transformItems();
        }
        
        return $this->_leftItems;
    }
    
    /**
     * Returns the right items
     * 
     * @access public
     * @return array
     */
    public function getRightItems()
    {
        // Transform the items into the two sides
        if (empty($this->_leftItems) && empty($this->_rightItems)) {
            $this->_transformItems();
        }
        
        return $this->_rightItems;
    }
    
    /**
     * Assigns each item to one of the two lists.
     * 
     * @access protected
     */
    protected function _transformItems()
    {
        $this->_leftItems = array();
        $this->_rightItems = array();
        
        foreach ($this->_items as $hash => $value) {
            if (array_search($value->getKey(), $this->_value) !== false) {
                $this->_rightItems[] = $value;
            } else {
                $this->_leftItems[] = $value;
            }
        }
    }
}
