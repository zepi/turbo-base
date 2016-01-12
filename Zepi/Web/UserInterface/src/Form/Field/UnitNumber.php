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
use \Zepi\Web\UserInterface\Form\Error;
use \Zepi\Turbo\Framework;

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
     * @access protected
     * @var integer
     */
    protected $_minValue;
    
    /**
     * @access protected
     * @var integer
     */
    protected $_maxValue;
    
    /**
     * Constructs the object
     *
     * @access public
     * @param string $label
     * @param boolean $isMandatory
     * @param mixed $value
     * @param string $prefix
     * @param string $suffix
     * @param integer $minValue
     * @param integer $maxValue
     * @param string $helpText
     * @param array $classes
     * @param string $placeholder
     * @param integer $tabIndex
     */
    public function __construct($key, $label, $isMandatory = false, $value = '', $prefix = '', $suffix = '', $minValue = null, $maxValue = null, $helpText = '', $classes = array(), $placeholder = '', $tabIndex = null)
    {
        $this->_prefix = $prefix;
        $this->_suffix = $suffix;
        $this->_minValue = $minValue;
        $this->_maxValue = $maxValue;
    
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
    
    /**
     * Returns the min value
     *
     * @access public
     * @return integer
     */
    public function getMinValue()
    {
        return $this->_minValue;
    }
    
    /**
     * Returns the max value
     * 
     * @access public
     * @return integer
     */
    public function getMaxValue()
    {
        return $this->_maxValue;
    }
    
    /**
     * Validates the value. Returns true if everything is okey or an Error
     * object if there was an error.
     *
     * @access public
     * @param \Zepi\Turbo\Framework $framework
     * @return true|\Zepi\Web\UserInterface\Form\Error
     */
    public function validate(Framework $framework)
    {
        $translationManager = $framework->getInstance('\\Zepi\\Core\\Language\\Manager\\TranslationManager');
    
        if ($this->_minValue !== null && $this->_value < $this->_minValue) {
            return new Error(
                Error::INVALID_VALUE,
                $translationManager->translate(
                    'The value for the field %field% is lower than the allowed minimum (%min%).',
                    '\\Zepi\\Web\\UserInterface',
                    array(
                        'field' => $this->_label,
                        'min' => $this->_minValue
                    )
                )
            );
        }
    
        if ($this->_maxValue !== null && $this->_value > $this->_maxValue) {
            return new Error(
                Error::INVALID_VALUE,
                $translationManager->translate(
                    'The value for the field %field% is higher than the allowed maximum (%max%).',
                    '\\Zepi\\Web\\UserInterface',
                    array(
                        'field' => $this->_label,
                        'max' => $this->_maxValue
                    )
                )
            );
        }
    
        return true;
    }
}
