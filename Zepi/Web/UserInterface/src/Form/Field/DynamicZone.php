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
 * Form Element Dynamic Zone
 * 
 * @package Zepi\Web\UserInterface
 * @subpackage Form\Field
 * @author Matthias Zobrist <matthias.zobrist@zepi.net>
 * @copyright Copyright (c) 2015 zepi
 */

namespace Zepi\Web\UserInterface\Form\Field;

use \Zepi\Turbo\Request\RequestAbstract;

/**
 * Form Element Dynamic Zone
 * 
 * @author Matthias Zobrist <matthias.zobrist@zepi.net>
 * @copyright Copyright (c) 2015 zepi
 */
class DynamicZone extends FieldAbstract
{
    /**
     * @access protected
     * @var string
     */
    protected $_key = '';
    
    /**
     * @access protected
     * @var string
     */
    protected $_triggerKey;
    
    /**
     * @access protected
     * @var callable
     */
    protected $_callback;
    
    /**
     * Constructs the object
     * 
     * @access public
     * @param string $key
     * @param string $triggerKey
     * @param callable $callback
     */
    public function __construct($key, $triggerKey, $callback)
    {
        $this->_key = $key;
        $this->_triggerKey = $triggerKey;
        $this->_callback = $callback;
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
     * Returns the name of the template to render the field
     * 
     * @access public
     * @return string
     */
    public function getTemplateName()
    {
        return '\\Zepi\\Web\\UserInterface\\Templates\\Form\\Field\\DynamicZone';
    }
    
    /**
     * Returns the trigger key
     * 
     * @access public
     * @return string
     */
    public function getTriggerKey()
    {
        return $this->_triggerKey;
    }
    
    /**
     * Returns the html id of the trigger element
     * 
     * @access public
     * @return string
     */
    public function getTriggerHtmlId()
    {
        $form = $this->getParentOfType('\\Zepi\\Web\\UserInterface\\Form\Form');
        
        if (is_object($form)) {
            $part = $form->searchPartByKeyAndType($this->_triggerKey);
            return $part->getHtmlId();
        }
        
        return '';
    }
    
    /**
     * Returns the callback
     * 
     * @access public
     * @return callable
     */
    public function getCallback()
    {
        return $this->_callback;
    }
    
    /**
     * Returns the rendered dynamic zone
     * 
     * @access public
     * @return mixed
     */
    public function renderZone()
    {
        return call_user_func($this->_callback, $this);
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
        if ($value == true) {
            $form = $this->getParentOfType('\\Zepi\\Web\\UserInterface\\Form\\Form');
            
            if (is_object($form)) {
                $form->setIsSubmitted(false);
            }
        }
    }
}
