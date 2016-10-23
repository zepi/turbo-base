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
use \Zepi\Turbo\Request\RequestAbstract;

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
    protected $items = array();
    
    /**
     * @access protected
     * @var string
     */
    protected $leftTitle;
    
    /**
     * @access protected
     * @var string
     */
    protected $rightTitle;
    
    /**
     * @access protected
     * @var string
     */
    protected $itemTemplate;
    
    /**
     * @access protected
     * @var array
     */
    protected $leftItems = array();
    
    /**
     * @access protected
     * @var array
     */
    protected $rightItems = array();
    
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
     * @param integer $tabIndex
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
        $placeholder = '',
        $tabIndex = null
    ) {
        $this->items = $items;
        $this->leftTitle = $leftTitle;
        $this->rightTitle = $rightTitle;
        $this->itemTemplate = $itemTemplate;
        
        parent::__construct($key, $label, $isMandatory, $value, $helpText, $classes, $placeholder, $tabIndex);
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
     * @param \Zepi\Turbo\Request\RequestAbstract $request
     */
    public function setValue($value, RequestAbstract $request)
    {
        $hashs = json_decode($value);
        
        if ($hashs === false) {
            $this->value = array();
            return;
        }

        $values = array();        
        foreach ($hashs as $hash) {
            $item = $this->getItemByHash($hash);
            if ($item === false) {
                continue;
            }
            
            $values[] = $item->getKey();
        }

        $this->value = $values;
        
        $this->transformItems();
    }
    
    /**
     * Returns the item for the given hash or false
     * if the hash is not available.
     * 
     * @access public
     * @param string $hash
     * @return \Zepi\Web\UserInterface\Entity\SelectorItem|boolean
     */
    protected function getItemByHash($hash)
    {
        foreach ($this->items as $item) {
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
        return $this->items;
    }
    
    /**
     * Returns the title of the left side
     * 
     * @access public
     * @return string
     */
    public function getLeftTitle()
    {
        return $this->leftTitle;
    }
    
    /**
     * Returns the title of the right side
     * 
     * @access public
     * @return string
     */
    public function getRightTitle()
    {
        return $this->rightTitle;
    }
    
    /**
     * Returns the item template
     * 
     * @access public
     * @return string
     */
    public function getItemTemplate()
    {
        return $this->itemTemplate;
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
        if (empty($this->leftItems) && empty($this->rightItems)) {
            $this->transformItems();
        }
        
        return $this->leftItems;
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
        if (empty($this->leftItems) && empty($this->rightItems)) {
            $this->transformItems();
        }
        
        return $this->rightItems;
    }
    
    /**
     * Assigns each item to one of the two lists.
     * 
     * @access protected
     */
    protected function transformItems()
    {
        $this->leftItems = array();
        $this->rightItems = array();
        
        foreach ($this->items as $hash => $value) {
            if (array_search($value->getKey(), $this->value) !== false) {
                $this->rightItems[] = $value;
            } else {
                $this->leftItems[] = $value;
            }
        }
    }
}
