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
 * Form Group
 * 
 * @package Zepi\Web\UserInterface
 * @subpackage Form
 * @author Matthias Zobrist <matthias.zobrist@zepi.net>
 * @copyright Copyright (c) 2015 zepi
 */

namespace Zepi\Web\UserInterface\Form;

use \Zepi\Web\UserInterface\Layout\Part;

/**
 * Form Group
 * 
 * @author Matthias Zobrist <matthias.zobrist@zepi.net>
 * @copyright Copyright (c) 2015 zepi
 */
class Group extends Part
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
    protected $_label;
    
    /**
     * @access protected
     * @var string
     */
    protected $_templateKey = '\\Zepi\\Web\\UserInterface\\Templates\\Form\\Group';
    
    /**
     * Constructs the object
     * 
     * @access public
     * @param string $key
     * @param string $label
     * @param array $parts
     * @param integer $priority
     */
    public function __construct($key, $label, $parts = array(), $priority = 10)
    {
        $this->_key = $key;
        $this->_label = $label;
        $this->_priority = $priority;
        
        foreach ($parts as $part) {
            $this->addPart($part);
        }
    }
    
    /** 
     * Returns the html id of this group
     * 
     * @access public
     * @return string
     */
    public function getHtmlId()
    {
        $form = $this->getParentOfType('\\Zepi\\Web\\UserInterface\\Form\\Form');

        // If the group isn't assigned to a data form we can not generate
        // the id of the group.
        if (!$form) {
            return false;
        }
        
        return $form->getHtmlId() . '-' . $this->_key;
    }
    
    /**
     * Returns the key of this group
     * 
     * @access public
     * @return string
     */
    public function getKey()
    {
        return $this->_key;
    }
    
    /**
     * Returns the label for the group
     * 
     * @access public
     * @return string
     */
    public function getLabel()
    {
        return $this->_label;
    }
    
    /**
     * Returns true if the group has a label
     * 
     * @access public
     * @return boolean
     */
    public function hasLabel()
    {
        return (trim($this->_label) !== '');
    }
}
