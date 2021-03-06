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
 * Representates a REST request
 * 
 * @package Zepi\Api\Rest
 * @subpackage Helper
 * @author Matthias Zobrist <matthias.zobrist@zepi.net>
 * @copyright Copyright (c) 2016 zepi
 */

namespace Zepi\Api\Rest\Entity;

/**
 * Representates a REST request
 * 
 * @author Matthias Zobrist <matthias.zobrist@zepi.net>
 * @copyright Copyright (c) 2016 zepi
 */
class Request
{
    const REQUEST_METHOD_GET = 'GET';
    const REQUEST_METHOD_POST = 'POST';
    const REQUEST_METHOD_PUT = 'PUT';
    const REQUEST_METHOD_DELETE = 'DELETE';
    
    /**
     * @access protected
     * @var string
     */
    protected $requestMethod = self::REQUEST_METHOD_GET;
    
    /**
     * @access protected
     * @var string
     */
    protected $host;
    
    /**
     * @access protected
     * @var string
     */
    protected $endpoint;
    
    /**
     * @access protected
     * @var array
     */
    protected $queryData = array();
    
    /**
     * @access protected
     * @var array
     */
    protected $postData = array();
    
    /**
     * Constructs the object
     * 
     * @access public
     * @param string $requestMethod
     * @param string $host
     * @param string $endpoint
     */
    public function __construct($requestMethod, $host, $endpoint)
    {
        $this->requestMethod = $requestMethod;
        $this->host = $host;
        $this->endpoint = $endpoint;
    }
    
    /**
     * Returns the request method
     * 
     * @access public
     * @return string
     */
    public function getRequestMethod()
    {
        return $this->requestMethod;
    }
    
    /**
     * Returns the host
     * 
     * @access public
     * @return string
     */
    public function getHost()
    {
        return $this->host;
    }
    
    /**
     * Returns the endpoint
     * 
     * @access public
     * @return string
     */
    public function getEndpoint()
    {
        return $this->endpoint;
    }
    
    /**
     * Add key value pair to the query data array
     * 
     * @access public
     * @param string $key
     * @param string $value
     */
    public function addQueryData($key, $value)
    {
        $this->queryData[$key] = $value;
    }
    
    /**
     * Returns the whole query data array
     * 
     * @access public
     * @return array
     */
    public function getQueryData()
    {
        return $this->queryData;
    }
    
    /**
     * Add key value pair to the query post array
     *
     * @access public
     * @param string $key
     * @param string $value
     */
    public function addPostData($key, $value)
    {
        $this->postData[$key] = $value;
    }
    
    /**
     * Returns the whole query post array
     *
     * @access public
     * @return array
     */
    public function getPostData()
    {
        return $this->postData;
    }
}
