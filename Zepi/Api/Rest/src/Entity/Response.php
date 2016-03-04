<?php
/*
 * The MIT License (MIT)
 *
 * Copyright (c) 2016 zepi
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
 * Representates a REST response
 * 
 * @package Zepi\Api\Rest
 * @subpackage Helper
 * @author Matthias Zobrist <matthias.zobrist@zepi.net>
 * @copyright Copyright (c) 2016 zepi
 */

namespace Zepi\Api\Rest\Entity;

/**
 * Representates a REST response
 * 
 * @author Matthias Zobrist <matthias.zobrist@zepi.net>
 * @copyright Copyright (c) 2016 zepi
 */
class Response
{
    /**
     * @access protected
     * @var integer
     */
    protected $_code;
    
    /**
     * @access protected
     * @var array
     */
    protected $_result = array();
    
    /**
     * @access protected
     * @var array
     */
    protected $_data;
    
    /**
     * @access protected
     * @var \Zepi\Api\Rest\Entity\Request
     */
    protected $_request;
    
    /**
     * Constructs the object
     * 
     * @access public
     * @param integer $code
     * @param string $result
     * @param \stdClass $data
     * @param \Zepi\Api\Rest\Entity\Request $request
     */
    public function __construct($code, $result, \stdClass $data, Request $request)
    {
        $this->_code = $code;
        $this->_result = $result;
        $this->_data = $data;
        $this->_request = $request;
    }
    
    /**
     * Returns the response code
     * 
     * @access public
     * @return integer
     */
    public function getCode()
    {
        return $this->_code;
    }
    
    /**
     * Returns true if the response is okey
     * 
     * @access public
     * @return boolean
     */
    public function isOk()
    {
        return ($this->_code === 200);
    }
    
    /**
     * Returns the raw result
     * 
     * @access public
     * @return string
     */
    public function getResult()
    {
        return $this->_result;
    }
    
    /**
     * Returns the data object
     * 
     * @access public
     * @return \stdClass
     */
    public function getData()
    {
        return $this->_data;
    }
    
    /**
     * Returns the request object
     * 
     * @access public
     * @return \Zepi\Api\Rest\Entity\Request
     */
    public function getRequest()
    {
        return $this->_request;
    }
}
