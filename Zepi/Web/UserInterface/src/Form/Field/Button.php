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
 * Form Element Button button
 * 
 * @package Zepi\Web\UserInterface
 * @subpackage Form\Field
 * @author Matthias Zobrist <matthias.zobrist@zepi.net>
 * @copyright Copyright (c) 2015 zepi
 */

namespace Zepi\Web\UserInterface\Form\Field;

/**
 * Form Element Button button
 * 
 * @author Matthias Zobrist <matthias.zobrist@zepi.net>
 * @copyright Copyright (c) 2015 zepi
 */
class Button extends FieldAbstract
{
    /**
     * @access protected
     * @var string
     */
    protected $type = 'button';
    
    /**
     * @access protected
     * @var array
     */
    protected $classes = array();
    
    /**
     * @access protected
     * @var string
     */
    protected $templateKey = '\\Zepi\\Web\\UserInterface\\Templates\\Form\\Field\\Blank';
    
    /**
     * @access protected
     * @var string
     */
    protected $iconClass = '';
    
    /**
     * @access protected
     * @var string
     */
    protected $htmlType;
    
    /**
     * @access protected
     * @var string
     */
    protected $href;
    
    /**
     * Constructs the object
     * 
     * @access public
     * @param string $key
     * @param string $label
     * @param array $classes
     * @param string $iconClass
     * @param string $htmlType
     * @param string $href
     * @param integer $tabIndex
     */
    public function __construct($key, $label, $classes = array(), $iconClass = '', $htmlType = 'button', $href = false, $tabIndex = null)
    {
        $this->key = $key;
        $this->label = $label;
        $this->iconClass = $iconClass;
        $this->htmlType = $htmlType;
        $this->tabIndex = $tabIndex;
        
        if ($href !== false) {
            $this->href = $href;
        }
        
        if (count($classes) > 0) {
            $this->classes = array_merge($this->classes, $classes);
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
        return '\\Zepi\\Web\\UserInterface\\Templates\\Form\\Field\\Button';
    }
    
    /**
     * Returns the type of the button
     * 
     * @access public
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }
    
    /**
     * Returns the icon class
     * 
     * @access public
     * @return string
     */
    public function getIconClass()
    {
        return $this->iconClass;
    }
    
    /**
     * Returns true if the icon class isn't empty
     * 
     * @access public
     * @return boolean
     */
    public function hasIconClass()
    {
        return ($this->iconClass !== '');
    }
    
    /**
     * Returns the html type of this button
     * 
     * @access public
     * @return string
     */
    public function getHtmlType()
    {
        return $this->htmlType;
    }
    
    /**
     * Returns the href of this button
     * 
     * @access public
     * @return string
     */
    public function getHref()
    {
        return $this->href;
    }
}
