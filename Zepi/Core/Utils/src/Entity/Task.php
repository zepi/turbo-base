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
 * Task
 * 
 * @package Zepi\Core\Utils
 * @subpackage Entity
 * @author Matthias Zobrist <matthias.zobrist@zepi.net>
 * @copyright Copyright (c) 2016 zepi
 */

namespace Zepi\Core\Utils\Entity;

/**
 * Task
 * 
 * @author Matthias Zobrist <matthias.zobrist@zepi.net>
 * @copyright Copyright (c) 2016 zepi
 */
class Task
{
    /**
     * @access protected
     * @var string
     */
    protected $_name;
    
    /**
     * @access protected
     * @var callable
     */
    protected $_callback;
    
    /**
     * @access protected
     * @var integer
     */
    protected $_resetTime;
    
    /**
     * @access protected
     * @var integer
     */
    protected $_instances;
    
    /**
     * Constructs the entity
     * 
     * @access public
     * @param string $name
     * @param callable $callback
     * @param integer $resetTime
     * @param integer $instances
     */
    public function __construct($name, $callback, $resetTime, $instances = 1)
    {
        $this->_name = $name;
        $this->_callback = $callback;
        $this->_resetTime = $resetTime;
        $this->_instances = $instances;
    }
    
    /**
     * Returns the name of the task
     * 
     * @access public
     * @return string
     */
    public function getName()
    {
        return $this->_name;
    }
    
    /**
     * Returns the callback
     * 
     * @access public
     * @return string
     */
    public function getCallback()
    {
        return $this->_callback;
    }
    
    /**
     * Returns the reset time of the task
     * 
     * @access public
     * @return integer
     */
    public function getResetTime()
    {
        return $this->_resetTime;
    }
    
    /**
     * Returns the number of instances of the task
     * 
     * @access public
     * @return integer
     */
    public function getInstances()
    {
        return $this->_instances;
    }
}
