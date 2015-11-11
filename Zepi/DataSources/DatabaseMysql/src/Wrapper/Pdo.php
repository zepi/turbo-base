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
 * Database handler class.
 * Wraps PDO to work around connecting to the database in PDO constructor.
 * Will lazy initialize DB connection on first request.
 * 
 * @package Zepi\DataSources\DatabaseMysql
 * @subpackage Wrapper
 * @author Matthias Zobrist <matthias.zobrist@zepi.net>
 * @copyright Copyright (c) 2015 zepi
 */

namespace Zepi\DataSources\DatabaseMysql\Wrapper;

/**
 * Database handler class.
 * Wraps PDO to work around connecting to the database in PDO constructor.
 * Will lazy initialize DB connection on first request.
 * 
 * @author Matthias Zobrist <matthias.zobrist@zepi.net>
 * @copyright Copyright (c) 2015 zepi
 */
class Pdo
{
    /**
     * @var \PDO
     */
    protected $_pdo;
    
    /**
     * @var string
     */
    protected $_dsn;
    
    /**
     * @var string
     */
    protected $_username;
    
    /**
     * @var string
     */
    protected $_password;
    
    /**
     * @var integer
     */
    protected $_connectionTime;
    
    /**
     * @var array
     */
    protected $_options = array();
    
    /**
    * Constructs the object.
	* Parameters are the same as in the original PDO class.
	*
	* @param string $_dsn
	* @param string $_username
	* @param string $_password
	* @param array $_options
	* @return null
	*/
    public function __construct($dsn, $username = '', $password = '', $options = array())
    {
        $this->_dsn = $dsn;
        $this->_username = $username;
        $this->_password = $password;
        $this->_options = $options;
    }
    
    /**
	* Delegates all method calls to the PDO object, lazy initializing it on demand.
	*
	* @param string $method
	* @param array $parameters
	* @return mixed
	*/
    public function __call($method, $parameters)
    {
        if ($this->_connectionTime < (time() - 300)) {
            $this->_pdo = null;
        }
        
        if ($this->_pdo === null) {
            $this->_pdo = new \PDO($this->_dsn, $this->_username, $this->_password, $this->_options);
            $this->_connectionTime = time();
        }
        
        return call_user_func_array(array($this->_pdo, $method), $parameters);
    }
}
