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
 * Form Element Select
 * 
 * @package Zepi\Web\UserInterface
 * @subpackage Form\Field
 * @author Matthias Zobrist <matthias.zobrist@zepi.net>
 * @copyright Copyright (c) 2015 zepi
 */

namespace Zepi\Web\UserInterface\Form\Field;

use \Zepi\Web\UserInterface\Form\Error;
use \Zepi\Turbo\Framework;
use \Zepi\Turbo\Request\RequestAbstract;

/**
 * Form Element Select
 * 
 * @author Matthias Zobrist <matthias.zobrist@zepi.net>
 * @copyright Copyright (c) 2015 zepi
 */
class Select extends FieldAbstract
{
    /**
     * @access protected
     * @var array
     */
    protected $availableValues = array();
    
    /**
     * @var integer
     */
    protected $maxNumberOfSelection;
    
    /**
     * Constructs the object
     *
     * @access public
     * @param string $label
     * @param boolean $isMandatory
     * @param array $value
     * @param array $availableValues
     * @param integer $maxNumberOfSelection
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
        $availableValues = array(),
        $maxNumberOfSelection = 1,
        $helpText = '',
        $classes = array(),
        $placeholder = '',
        $tabIndex = null
    ) {
        $this->availableValues = $availableValues;
        $this->maxNumberOfSelection = $maxNumberOfSelection;
    
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
        return '\\Zepi\\Web\\UserInterface\\Templates\\Form\\Field\\Select';
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
        if ($this->maxNumberOfSelection > 1) {
            $this->value = array();
            foreach ($value as $id) {
                $this->value[] = $id;
            }
        } else {
            $this->value = $value;
        }
    }
    
    /**
     * Returns the available values
     * 
     * @access public
     * @return array
     */
    public function getAvailableValues()
    {
        return $this->availableValues;
    }
    
    /**
     * Returns the maximum number of selected entities
     *
     * @return integer
     */
    public function getMaxNumberOfSelection()
    {
        return $this->maxNumberOfSelection;
    }
    
    /**
     * Returns true if the field contains the given entity as value
     *
     * @param mixed $value
     * @return boolean
     */
    public function isSelected($value)
    {
        if (is_array($this->value) && in_array($value, $this->value)) {
            return true;
        } else if ($this->value == $value) {
            return true;
        }
    
        return false;
    }
    
    /**
     * Validates the value. Returns true if everything is okey or an Error
     * object if there was an error.
     *
     * @access public
     * @param \Zepi\Turbo\Framework $framework
     * @return boolean|\Zepi\Web\UserInterface\Form\Error
     */
    public function validate(Framework $framework)
    {
        $translationManager = $framework->getInstance('\\Zepi\\Core\\Language\\Manager\\TranslationManager');
        
        if ($this->maxNumberOfSelection > 1 && count($this->value) > $this->maxNumberOfSelection) {
            return new Error(
                Error::INVALID_VALUE,
                $translationManager->translate(
                    'You have selected too many items in the field %field%. Please select only %max% items.',
                    '\\Zepi\\Web\\UserInterface',
                    array(
                        'field' => $this->label,
                        'max' => $this->maxNumberOfSelection
                    )
                )
            );
        }
        
        return true;
    }
}
