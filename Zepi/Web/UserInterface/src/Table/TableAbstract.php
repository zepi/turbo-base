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
 * A Table displays a data table in the framework. This function must
 * be extended because the getData function is table specific.
 * 
 * @package Zepi\Web\UserInterface
 * @subpackage Table
 * @author Matthias Zobrist <matthias.zobrist@zepi.net>
 * @copyright Copyright (c) 2015 zepi
 */

namespace Zepi\Web\UserInterface\Table;

use \Zepi\Turbo\Framework;
use \Zepi\Core\Utils\Entity\DataRequest;

/**
 * A Table displays a data table in the framework. This function must
 * be extended because the getData function is table specific.
 * 
 * @author Matthias Zobrist <matthias.zobrist@zepi.net>
 * @copyright Copyright (c) 2015 zepi
 */
abstract class TableAbstract
{
    /**
     * @access protected
     * @var \Zepi\Turbo\Framework
     */
    protected $framework;
    
    /**
     * @access protected
     * @var boolean
     */
    protected $hasPagination;
    
    /**
     * @access protected
     * @var boolean
     */
    protected $hasFilters;
    
    /**
     * @access protected
     * @var boolean
     */
    protected $saveDataRequest;
    
    /**
     * Constructs the object
     * 
     * @access public
     * @param \Zepi\Turbo\Framework $framework
     * @param boolean $hasPagination
     * @param boolean $hasFilters
     * @param boolean $saveDataRequest
     */
    public function __construct(Framework $framework, $hasPagination = true, $hasFilters = false, $saveDataRequest = true)
    {
        $this->framework = $framework;
        $this->hasPagination = $hasPagination;
        $this->hasFilters = $hasFilters;
        $this->saveDataRequest = $saveDataRequest;
    }
    
    /**
     * Returns true if the table has a pagination
     * 
     * @access public
     * @return boolean
     */
    public function hasPagination()
    {
        return ($this->hasPagination);
    }
    
    /**
     * Returns true if the table has filters
     * 
     * @access public
     * @return boolean
     */
    public function hasFilters()
    {
        return ($this->hasFilters);
    }
    
    /**
     * Returns true if the table should save the data request in
     * the session of the user
     * 
     * @access public
     * @return boolean
     */
    public function shouldSaveDataRequest()
    {
        return ($this->saveDataRequest);
    }
    
    /**
     * Returns an array with all data which should be displayed on this page
     * 
     * @abstract
     * @access public
     * @param \Zepi\Core\Utils\Entity\DataRequest $request
     * @return array
     */
    abstract public function getData(DataRequest $request);
    
    /**
     * Returns the total number of entries are available for the given
     * data request object
     * 
     * @abstract
     * @access public
     * @param \Zepi\Core\Utils\Entity\DataRequest $request
     * @return integer
     */
    abstract public function countData(DataRequest $request);
    
    /**
     * Returns an array with all columns
     * 
     * @access public
     * @return array
     */
    abstract public function getColumns();
    
    /**
     * Returns the data for the given key and row (object)
     * 
     * @access public
     * @param string $key
     * @param mixed $object
     * @return mixed
     */
    abstract public function getDataForRow($key, $object);
    
    /**
     * Returns an array with all filter values for the column
     *
     * @access public
     * @param string $key
     * @return array
     */
    public function getFilterValues($key)
    {
        return array();
    }
    
    /**
     * Prepares the filter value for the column
     * 
     * @access public
     * @param string $key
     * @param mixed $value
     * @return mixed
     */
    public function prepareFilterValue($key, $value)
    {
        return $value;
    }
}