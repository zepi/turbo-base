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
 * Form Error
 * 
 * @package Zepi\Web\UserInterface
 * @subpackage Form
 * @author Matthias Zobrist <matthias.zobrist@zepi.net>
 * @copyright Copyright (c) 2015 zepi
 */

namespace Zepi\Web\UserInterface\Form;

use \Zepi\Web\UserInterface\Form\Field\FieldAbstract;
use \Zepi\Web\UserInterface\Layout\Part;

/**
 * Form Error
 * 
 * @author Matthias Zobrist <matthias.zobrist@zepi.net>
 * @copyright Copyright (c) 2015 zepi
 */
class Error extends Part
{
    const GENERAL_ERROR = 1;
    const MANDATORY_FIELD = 2;
    const WRONG_INPUT = 3;
    const WRONG_FORMAT = 4;
    const INVALID_VALUE = 5;
    
    /**
     * @access protected
     * @var integer
     */
    protected $errorCode;
    
    /**
     * @access protected
     * @var string
     */
    protected $errorMessage;

    /**
     * @access protected
     * @var \Zepi\Web\UserInterface\Form\Field\FieldAbstract
     */    
    protected $field;
    
    /**
     * Constructs the object
     * 
     * @access public
     * @param integer $errorCode
     * @param string $errorMessage
     * @param \Zepi\Web\UserInterface\Form\Field\FieldAbstract $field
     */
    public function __construct($errorCode, $errorMessage, FieldAbstract $field = null)
    {
        $this->errorCode = $errorCode;
        $this->errorMessage = $errorMessage;
        $this->field = $field;
    }
    
    /**
     * Returns the error code of the error
     * 
     * @access public
     * @return integer
     */
    public function getErrorCode()
    {
        return $this->errorCode;
    }
    
    /**
     * Returns the error message of the error
     * 
     * @access public
     * @return string
     */
    public function getErrorMessage()
    {
        return $this->errorMessage;
    }
    
    /**
     * Returns the field of the error
     * 
     * @access public
     * @return \Zepi\Web\UserInterface\Form\Field\FieldAbstract
     */
    public function getField()
    {
        return $this->field;
    }
    
    /**
     * Returns true if the error has a field assigned
     * 
     * @access public
     * @return boolean
     */
    public function hasField()
    {
        return ($this->field !== null);
    }
}
