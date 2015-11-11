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
 * Form Error Box
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
 * Form Error Box
 * 
 * @author Matthias Zobrist <matthias.zobrist@zepi.net>
 * @copyright Copyright (c) 2015 zepi
 */
class ErrorBox extends Part
{
    /**
     * @access protected
     * @var string
     */
    protected $_templateKey = '\\Zepi\\Web\\UserInterface\\Templates\\Form\\ErrorBox';
    
    /**
     * @access protected
     * @var array
     */
    protected $_errors = array();
    
    /**
     * Constructs the object
     * 
     * @access public
     * @param string $key
     * @param array $errors
     */
    public function __construct($key, $priority = 10, $errors = array())
    {
        $this->_key = $key;
        $this->_priority = 10;
        $this->_errors = $errors;
    }
    
    /**
     * Adds an error to the error box
     * 
     * @access public
     * @param \Zepi\Web\UserInterface\Form\Error $error
     */
    public function addError(Error $error)
    {
        $this->_errors[] = $error;
    }
    
    /**
     * Returns all errors of the error box
     * 
     * @access public
     * @return array
     */
    public function getErrors()
    {
        return $this->_errors;
    }
    
    /**
     * Returns true if the error box has any errors
     * 
     * @access public
     * @return boolean
     */
    public function hasErrors()
    {
        return (count($this->_errors) > 0);
    }
}
