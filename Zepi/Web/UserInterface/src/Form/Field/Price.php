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
 * Form Element Price
 * 
 * @package Zepi\Web\UserInterface
 * @subpackage Form\Field
 * @author Matthias Zobrist <matthias.zobrist@zepi.net>
 * @copyright Copyright (c) 2015 zepi
 */

namespace Zepi\Web\UserInterface\Form\Field;

use \Zepi\Turbo\Request\RequestAbstract;

/**
 * Form Element Price
 * 
 * @author Matthias Zobrist <matthias.zobrist@zepi.net>
 * @copyright Copyright (c) 2015 zepi
 */
class Price extends FieldAbstract
{
    /**
     * @access protected
     * @var string
     */
    protected $_currency;
    
    /**
     * @access protected
     * @var string
     */
    protected $_separatorThousands;
    
    /**
     * @access protected
     * @var string
     */
    protected $_separatorDecimal;
    
    /**
     * Constructs the object
     *
     * @access public
     * @param string $label
     * @param boolean $isMandatory
     * @param mixed $value
     * @param stirng $currency
     * @param string $separatorThousands
     * @param string $separatorDecimal
     * @param string $helpText
     * @param array $classes
     * @param string $placeholder
     */
    public function __construct($key, $label, $isMandatory = false, $value = '', $currency = 'USD', $separatorThousands = '.', $separatorDecimal = ',', $helpText = '', $classes = array(), $placeholder = '')
    {
        $this->_currency = $currency;
        $this->_separatorThousands = $separatorThousands;
        $this->_separatorDecimal = $separatorDecimal;
        
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
        return '\\Zepi\\Web\\UserInterface\\Templates\\Form\\Field\\Price';
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
        $this->_value = preg_replace('/([^0-9\\.])/i', '', $value);
    }
    
    /**
     * Returns the currency of the price field
     * 
     * @access public
     * @return string
     */
    public function getCurrency()
    {
        return $this->_currency;
    }
    
    /**
     * Returns the thousands separator
     * 
     * @access public
     * @return string
     */
    public function getSeparatorThousands()
    {
        return $this->_separatorThousands;
    }
    
    /**
     * Returns the decimal separator
     * 
     * @access public
     * @return string
     */
    public function getSeparatorDecimal()
    {
        return $this->_separatorDecimal;
    }
}
