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
 * Form FieldAbstract
 * 
 * @package Zepi\Web\UserInterface
 * @subpackage Form\Field
 * @author Matthias Zobrist <matthias.zobrist@zepi.net>
 * @copyright Copyright (c) 2015 zepi
 */

namespace Zepi\Web\UserInterface\Form\Field;

use \Zepi\Web\UserInterface\Form\Group;
use \Zepi\Web\UserInterface\Layout\Part;

/**
 * Form FieldAbstract
 * 
 * @author Matthias Zobrist <matthias.zobrist@zepi.net>
 * @copyright Copyright (c) 2015 zepi
 */
abstract class FieldAbstract extends Part
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
     * @var boolean
     */
    protected $_isMandatory;
    
    /**
     * @access protected
     * @var string
     */
    protected $_helpText;
    
    /**
     * @access protected
     * @var array
     */
    protected $_classes = array();
    
    /**
     * @access protected
     * @var string
     */
    protected $_placeholder;
    
    /**
     * @access protected
     * @var \Zepi\Web\UserInterface\Form\Group
     */
    protected $_formGroup;
    
    /**
     * @access protected
     * @var mixed
     */
    protected $_value;
    
    /**
     * @access protected
     * @var string
     */
    protected $_templateKey = '\\Zepi\\Web\\UserInterface\\Templates\\Form\\Field\\Base';
    
    /**
     * Constructs the object
     * 
     * @access public
     * @param string $label
     * @param boolean $isMandatory
     * @param string $value
     * @param string $helpText
     * @param array $classes
     * @param string $placeholder
     */
    public function __construct($key, $label, $isMandatory = false, $value = '', $helpText = '', $classes = array(), $placeholder = '')
    {
        $this->_key = $key;
        $this->_label = $label;
        $this->_isMandatory = $isMandatory;
        $this->_value = $value;
        $this->_helpText = $helpText;
        $this->_placeholder = $placeholder;
        
        if (count($classes) > 0) {
            $this->_classes = $classes;
        }
    }
    
    /**
     * Returns true if the label of the field should be displayed
     * 
     * @access public
     * @return boolean
     */
    public function displayLabel()
    {
        return true;
    }
    
    /**
     * Returns true if the field should be displayed full width
     * 
     * @return boolean
     */
    public function fullWidth()
    {
        return false;
    }
    
    /**
     * Returns the name of the template to render the field
     * 
     * @access public
     * @return string
     */
    public function getTemplateName()
    {
        return '\\Zepi\\Web\\UserInterface\\Templates\\Field\\Abstract';
    }
    
    /**
     * Returns the html id of this field
     * 
     * @access public
     * @return string
     */
    public function getHtmlId()
    {
        $group = $this->getParentOfType('\\Zepi\\Web\\UserInterface\\Form\\Group');

        /**
         * If the field isn't assigned to a form group return false
         */
        if (!$group) {
            return false;
        }
        
        return $group->getHtmlId() . '-' . $this->_key;
    }
    
    /**
     * Returns the html name of this field
     * 
     * @access public
     * @return string
     */
    public function getHtmlName()
    {
        $group = $this->getParent('\\Zepi\\Web\\UserInterface\\Form\\Group');
        
        /**
         * If the field isn't assigned to a form group return false
         */
        if (!$group) {
            return false;
        }
        
        return $group->getKey() . '-' . $this->_key;
    }
    
    /**
     * Returns the key of the field
     * 
     * @access public
     * @return string
     */
    public function getKey()
    {
        return $this->_key;
    }
    
    /**
     * Returns the label of the field
     * 
     * @access public
     * @return string
     */
    public function getLabel()
    {
        return $this->_label;
    }
    
    /**
     * Returns true if the field is mandatory.
     * 
     * @access public
     * @return boolean
     */
    public function isMandatory()
    {
        return ($this->_isMandatory);
    }
    
    /**
     * Returns true if the field has a label
     * 
     * @access public
     * @return boolean
     */
    public function hasLabel()
    {
        return (trim($this->_label) != '');
    }

    /**
     * Returns the help text for the field
     * 
     * @access public
     * @return string
     */
    public function getHelpText()
    {
        return $this->_helpText;
    }
    
    /**
     * Returns the placeholder for the field
     * 
     * @access public
     * @return string
     */
    public function getPlaceholder()
    {
        return $this->_placeholder;
    }
      
    /** 
     * Returns the array with all classes for the field
     * 
     * @access public
     * @return array
     */
    public function getClasses()
    {
        return $this->_classes;
    }
    
    /**
     * Returns a space separated string with all classes.
     * 
     * @access public
     * @return string
     */
    public function getHtmlClasses()
    {
        return implode(' ', $this->_classes);
    }
    
    /**
     * Returns the value of the field
     * 
     * @access public
     * @return mixed
     */
    public function getValue()
    {
        return $this->_value;
    }
    
    /**
     * Sets the html form value of the field
     * 
     * @access public
     * @param string $value
     */
    public function setValue($value)
    {
        $this->_value = $value;
    }
    
    /**
     * Returns true if the field has a value, otherwise false.
     * 
     * @access public
     * @return boolean
     */
    public function hasValue()
    {
        return ($this->_value != '');
    }
    
    /**
     * Validates the value. Returns true if everything is okey or an Error
     * object if there was an error.
     * 
     * @access public
     * @return true|\Zepi\Web\UserInterface\Form\Error
     */
    public function validate()
    {
        return true;
    }
}