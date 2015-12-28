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
 * Form Element UnitNumber
 * 
 * @package Zepi\Web\UserInterface
 * @subpackage Form\Field
 * @author Matthias Zobrist <matthias.zobrist@zepi.net>
 * @copyright Copyright (c) 2015 zepi
 */

namespace Zepi\Web\UserInterface\Form\Field;

use \Zepi\Turbo\Request\RequestAbstract;

/**
 * Form Element UnitNumber
 * 
 * @author Matthias Zobrist <matthias.zobrist@zepi.net>
 * @copyright Copyright (c) 2015 zepi
 */
class UnitNumber extends FieldAbstract
{
    /**
     * @access protected
     * @var string
     */
    protected $_prefix;
    
    /**
     * @access protected
     * @var string
     */
    protected $_suffix;
    
    /**
     * Constructs the object
     *
     * @access public
     * @param string $label
     * @param boolean $isMandatory
     * @param mixed $value
     * @param stirng $prefix
     * @param string $suffix
     * @param string $helpText
     * @param array $classes
     * @param string $placeholder
     * @param integer $tabIndex
     */
    public function __construct($key, $label, $isMandatory = false, $value = '', $prefix = '', $suffix = '', $helpText = '', $classes = array(), $placeholder = '', $tabIndex = null)
    {
        $this->_prefix = $prefix;
        $this->_suffix = $suffix;
    
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
        return '\\Zepi\\Web\\UserInterface\\Templates\\Form\\Field\\UnitNumber';
    }
    
    /**
     * Sets the html form value of the field
     *
     * @access public
     * @param mixed $value
     * @param \Zepi\Turbo\Request\RequestAbstract $request
     */
    public function setValue($value, RequestAbstract $request)
    {
        $this->_value = intval($value);
    }
    
    /**
     * Returns the prefix
     *
     * @access public
     * @return string
     */
    public function getPrefix()
    {
        return $this->_prefix;
    }
    
    /**
     * Returns the suffix
     *
     * @access public
     * @return string
     */
    public function getSuffix()
    {
        return $this->_suffix;
    }
}
