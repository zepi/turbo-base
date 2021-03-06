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

/**
 * Form Element Select
 * 
 * @author Matthias Zobrist <matthias.zobrist@zepi.net>
 * @copyright Copyright (c) 2015 zepi
 */
class ExtendedSelect extends Select
{
    /**
     * @var array
     */
    protected $extendedOptions;
    
    /**
     * Constructs the object
     *
     * @access public
     * @param string $label
     * @param boolean $isMandatory
     * @param array $value
     * @param array $availableValues
     * @param integer $maxNumberOfSelection
     * @param array $extendedOptions
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
        $extendedOptions = array(),
        $helpText = '',
        $classes = array(),
        $placeholder = '',
        $tabIndex = null
    ) {
        parent::__construct($key, $label, $isMandatory, $value, $availableValues, $maxNumberOfSelection, $helpText, $classes, $placeholder, $tabIndex);

        $this->extendedOptions = $extendedOptions;
        $this->classes[] = 'extended-select';
    }
    
    /**
     * Returns the extended options
     *
     * @return array
     */
    public function getExtendedOptions()
    {
        return $this->extendedOptions;
    }
}