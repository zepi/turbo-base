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
 * PreparedTable
 * 
 * @package Zepi\Web\UserInterface
 * @subpackage Table
 * @author Matthias Zobrist <matthias.zobrist@zepi.net>
 * @copyright Copyright (c) 2015 zepi
 */

namespace Zepi\Web\UserInterface\Table;

use \Zepi\Web\UserInterface\Table\Head;
use \Zepi\Web\UserInterface\Table\Body;
use \Zepi\Web\UserInterface\Table\Foot;
use \Zepi\Web\UserInterface\Pagination\Pagination;

/**
 * PreparedTable
 * 
 * @author Matthias Zobrist <matthias.zobrist@zepi.net>
 * @copyright Copyright (c) 2015 zepi
 */
class PreparedTable
{
    /**
     * @access protected
     * @var \Zepi\Web\UserInterface\Table\TableAbstract
     */
    protected $table;
    
    /**
     * @access protected
     * @var array
     */
    protected $columns = array();
    
    /**
     * @access protected
     * @var \Zepi\Web\UserInterface\Table\Head
     */
    protected $head;
    
    /**
     * @access protected
     * @var \Zepi\Web\UserInterface\Table\Body
     */
    protected $body;
    
    /**
     * @access protected
     * @var \Zepi\Web\UserInterface\Table\Foot
     */
    protected $foot;
    
    /**
     * @access protected
     * @var \Zepi\Web\UserInterface\Pagination\Pagination
     */
    protected $pagination;
    
    /**
     * @access protected
     * @var string
     */
    protected $token;
    
    /**
     * @var array
     */
    protected $options = array();
    
    /**
     * Constructs the object
     * 
     * @access public
     * @param \Zepi\Web\UserInterface\Table\TableAbstract $table
     * @param array $columns
     */
    public function __construct(TableAbstract $table, $columns)
    {
        $this->table = $table;
        $this->columns = $columns;
    }
    
    /**
     * Returns the id for the prepared table
     * 
     * @return string
     */
    public function getId()
    {
        $className = get_class($this->table);
        
        return str_replace('\\', '_', $className);
    }
    
    /**
     * Returns the Table object
     * 
     * @access public
     * @return \Zepi\Web\UserInterface\Table\TableAbstract
     */
    public function getTable()
    {
        return $this->table;
    }
    
    /**
     * Returns the head
     * 
     * @access public
     * @return \Zepi\Web\UserInterface\Table\Head
     */
    public function getHead()
    {
        return $this->head;
    }
    
    /**
     * Sets the head
     * 
     * @access public
     * @param \Zepi\Web\UserInterface\Table\Head $head
     */
    public function setHead(Head $head)
    {
        $this->head = $head;
    }
    
    /**
     * Returns true if the table has a head
     * 
     * @access public
     * @return boolean
     */
    public function hasHead()
    {
        return ($this->head instanceof Head);
    }

    /**
     * Returns the body
     * 
     * @access public
     * @return \Zepi\Web\UserInterface\Table\Body
     */
    public function getBody()
    {
        return $this->body;
    }
    
    /**
     * Sets the body
     * 
     * @access public
     * @param \Zepi\Web\UserInterface\Table\Body $body
     */
    public function setBody(Body $body)
    {
        $this->body = $body;
    }
    
    /**
     * Returns true if the table has a body
     * 
     * @access public
     * @return boolean
     */
    public function hasBody()
    {
        return ($this->body != '');
    }
    
    /**
     * Returns the foot
     * 
     * @access public
     * @return \Zepi\Web\UserInterface\Table\Foot
     */
    public function getFoot()
    {
        return $this->foot;
    }
    
    /**
     * Sets the foot
     * 
     * @access public
     * @param \Zepi\Web\UserInterface\Table\Foot $foot
     */
    public function setFoot(Foot $foot)
    {
        $this->foot = $foot;
    }
    
    /**
     * Returns true if the table has a foot
     * 
     * @access public
     * @return boolean
     */
    public function hasFoot()
    {
        return ($this->foot != '');
    }
    
    /**
     * Returns the number of columns for this table
     * 
     * @access public
     * @return integer
     */
    public function getNumberOfColumns()
    {
        return count($this->columns);
    }
    
    /**
     * Returns the pagination object
     * 
     * @access public
     * @return \Zepi\Web\UserInterface\Pagination\Pagination
     */
    public function getPagination()
    {
        return $this->pagination;
    }

    /**
     * Adds the pagination object
     * 
     * @access public
     * @param \Zepi\Web\UserInterface\Pagination\Pagination $pagination
     */
    public function setPagination(Pagination $pagination)
    {
        $this->pagination = $pagination;
    }
    
    /**
     * Returns the token for the prepared table
     * 
     * @return string
     */
    public function getToken()
    {
        return $this->token;
    }
    
    /**
     * Sets the token for the prepared table
     * 
     * @param string $token
     */
    public function setToken($token)
    {
        $this->token = $token;
    }
    
    /**
     * Returns null if the key is not set or the 
     * value of the option for the given key
     * 
     * @param string $key
     * @return null|mixed
     */
    public function getOption($key)
    {
        if (!isset($this->options[$key])) {
            return null;
        }
        
        return $this->options[$key];
    }
    
    /**
     * Returns the whole options array
     * 
     * @return array
     */
    public function getOptions()
    {
        return $this->options;
    }
    
    /**
     * Sets a single option
     * 
     * @param string $key
     * @param mixed $value
     */
    public function setOption($key, $value)
    {
        $this->options[$key] = $value;
    }
    
    /**
     * Overrides all options
     * 
     * @param array $options
     */
    public function setOptions($options)
    {
        $this->options = $options;
    }
}
