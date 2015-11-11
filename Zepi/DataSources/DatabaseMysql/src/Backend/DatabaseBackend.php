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
 * Database Backend to connect to a database
 * 
 * @package Zepi\DataSources\DatabaseMysql
 * @subpackage Backend
 * @author Matthias Zobrist <matthias.zobrist@zepi.net>
 * @copyright Copyright (c) 2015 zepi
 */

namespace Zepi\DataSources\DatabaseMysql\Backend;

use \Zepi\DataSources\DatabaseMysql\Wrapper\Pdo;
use \Zepi\DataSources\DatabaseMysql\Exception;
use \Zepi\Core\Utils\Entity\DataRequest;

/**
 * Database Backend to connect to a database
 * 
 * @author Matthias Zobrist <matthias.zobrist@zepi.net>
 * @copyright Copyright (c) 2015 zepi
 */
class DatabaseBackend
{
	/**
     * @var bool
     */
    protected $_isConnected = false;
    
    /**
     * @var \Zepi\DataSources\DatabaseMysql\Wrapper\Pdo
     */
    protected $_pdo;
    
    /**
     * Constructs the object.
     *
     * @param \Zepi\DataSources\DatabaseMysql\Wrapper\Pdo $pdo
     */
    public function __construct(Pdo $pdo)
    {
        $this->_pdo = $pdo;
    }
    
    /**
     * Returns the SQL statement for the given DataRequest object
     * 
     * @access public
     * @param \Zepi\Core\Utils\Entity\DataRequest $dataRequest
     * @param string $table
     * @param string $columnPrefix
     * @return string
     */
    public function buildDataRequestQuery(DataRequest $dataRequest, $table, $columnPrefix)
    {
        $sql = 'SELECT ' . implode(', ', $dataRequest->getSelectedFields()) . ' ';
        
        $sql .= 'FROM ' . $table . ' ';
        
        foreach ($dataRequest->getFilters() as $filter) {
            if (strpos($sql, 'WHERE') === false) {
                $sql .= 'WHERE ';
            } else {
                $sql .= 'AND ';
            }
            
            $sql .= $this->_getFieldName($columnPrefix, $filter->getFieldName()) . ' ' . $filter->getMode() . ' ' . $this->_pdo->quote($this->_replaceWildcard($filter->getNeededValue())) . ' ';
        }
        
        // Sorting
        if ($dataRequest->getSortBy() != '') {
            $mode = 'ASC';
            if (in_array($dataRequest->getSortByDirection(), array('ASC', 'DESC'))) {
                $mode = $dataRequest->getSortByDirection();
            }
            
            $sql .= 'ORDER BY ' . $this->_getFieldName($columnPrefix, $dataRequest->getSortBy()) . ' ' . $mode . ' ';
        }
        
        // Offset
        if ($dataRequest->getOffset() > 0 || $dataRequest->getNumberOfEntries() > 0) {
            $sql .= 'LIMIT ' . $dataRequest->getOffset() . ', ' . $dataRequest->getNumberOfEntries() . ' ';
        }
        
        return $sql;
    }
    
    /**
     * Returns the complete field name
     * 
     * @access protected
     * @param string $columnPrefix
     * @param string $columnName
     * @return string
     */
    protected function _getFieldName($columnPrefix, $columnName)
    {
        return $columnPrefix . '_' . $columnName;
    }
    
    /**
     * Replaces any wildcards from the filter value
     * 
     * @access protected
     * @param string $value
     * @return string
     */
    protected function _replaceWildcard($value)
    {
        return str_replace('*', '%', $value);
    }
    
    /**
     * Executes the SQL Query
     *
     * @param string $sql
     * @return integer
     * 
     * @throws \Zepi\DataSources\DatabaseMysql\Exception Failure in preparing the sql query
     */
    public function execute($sql)
    {
        try {
             $this->_connect();
             $statement = $this->_pdo->query($sql);
             
             return $statement->rowCount();
        }
        catch (\PDOException $e) {
             throw new Exception('Failure in preparing the sql query: "' . $e . '"');
        }
    }
    
    /**
     * Executes the SQL Query
     *
     * @param string $sql
     * @return PDOStatement|false
     * 
     * @throws \Zepi\DataSources\DatabaseMysql\Exception Failure in executing the sql query
     */
    public function query($sql)
    {
       try {
            $this->_connect();
            $statement = $this->_pdo->query($sql);
 
            $statement->setFetchMode(\PDO::FETCH_ASSOC);           
            return $statement;
       }
       catch (\PDOException $e) {
            throw new Exception('Failure in executing the sql query: "' . $e .'"');
       }
    }
    
    /**
     * Connects to the database using lazy initialization
     */
    protected function _connect()
    {
        if ($this->_isConnected) {
            return;
        }
        
        $this->_pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        $this->_isConnected = true;
    }

    /**
     * Escapes a string
     * 
     * @param string $value
     * @return string $value
     */
    public function escape($value) 
    {
        return $this->_pdo->quote($value);
    }

    /**
     * Begins a new transaction
     */
    public function beginTransaction()
    {
        $this->_pdo->beginTransaction();
    }
    
    /**
     * Commits a transaction
     */
    public function commit()
    {
        $this->_pdo->commit();
    }
    
    /**
     * Rolls back an transaction
     */
    public function rollBack()
    {
        $this->_pdo->rollBack();
    }
    
    /**
     * Returns the last insert id
     * 
     * @access public
     * @return integer
     */
    public function getLastId()
    {
        return $this->_pdo->lastInsertId();
    }
}