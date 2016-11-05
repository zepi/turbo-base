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
 * DataRequest
 * 
 * @package Zepi\Core\Utils
 * @subpackage Entity
 * @author Matthias Zobrist <matthias.zobrist@zepi.net>
 * @copyright Copyright (c) 2015 zepi
 */

namespace Zepi\Core\Utils\Entity;

/**
 * DataRequest
 * 
 * @author Matthias Zobrist <matthias.zobrist@zepi.net>
 * @copyright Copyright (c) 2015 zepi
 */
class DataRequest
{
    /**
     * @access protected
     * @var array
     */
    protected $filters = array();
     
    /**
     * @access protected
     * @var integer
     */
    protected $page;
    
    /**
     * @access protected
     * @var false|integer
     */
    protected $numberOfEntries;
    
    /**
     * @access protected
     * @var string
     */
    protected $sortBy;
    
    /**
     * @access protected
     * @var string
     */
    protected $sortByDirection;
    
    /**
     * @access protected
     * @var array
     */
    protected $selectedFields = array();
    
    /**
     * Constructs the object
     * 
     * @access public
     * @param integer $page
     * @param false|integer $numberOfEntries
     * @param string $sortBy
     * @param string $sortByDirection
     * @param array $selectedFields
     */
    public function __construct($page, $numberOfEntries, $sortBy, $sortByDirection, $selectedFields = array('*'))
    {
        $this->page = $page;
        $this->numberOfEntries = $numberOfEntries;
        $this->sortBy = $sortBy;
        $this->sortByDirection = $sortByDirection;
        $this->selectedFields = $selectedFields;
    }
    
    /**
     * Adds a filter
     * 
     * @access public
     * @param \Zepi\Core\Utils\Entity\Filter $filter
     */
    public function addFilter(Filter $filter)
    {
        $key = $filter->getKey();
        $this->filters[$key] = $filter;
    }
    
    /**
     * Returns all filters
     * 
     * @access public
     * @return array
     */
    public function getFilters()
    {
        return $this->filters;
    }
    
    /**
     * Returns true if the request has a filter for the given name
     *
     * @access public
     * @param string $name
     * @return boolean
     */
    public function hasFilter($name)
    {
        foreach ($this->filters as $filter) {
            if ($filter->getFieldName() === $name) {
                return true;
            }
        }
    
        return false;
    }
    
    /**
     * Returns the Filter object for the given key
     * 
     * @access public
     * @param string $name
     * @return false|\Zepi\Core\Utils\Entity\Filter
     */
    public function getFilter($name)
    {
        foreach ($this->filters as $filter) {
            if ($filter->getFieldName() === $name) {
                return $filter;
            }
        }
        
        return false;
    }
    
    /**
     * Removes all filters
     * 
     * @access public
     */
    public function clearFilters()
    {
        $this->filters = array();
    }
    
    /**
     * Returns the number of the loaded page
     * 
     * @access public
     * @return integer
     */
    public function getPage()
    {
        return $this->page;
    }
    
    /**
     * Sets the number of the loaded page
     * 
     * @access public
     * @param integer $page
     */
    public function setPage($page)
    {
        $this->page = intval($page);
        
        if ($this->page == 0) {
            $this->page = 1;
        }
    }
    
    /**
     * Returns the offset
     * 
     * @access public
     * @return false|integer
     */
    public function getOffset()
    {
        if ($this->numberOfEntries === false) {
            return false;
        }
        
        return ($this->page - 1) * $this->numberOfEntries;
    }
    
    /**
     * Returns the number of entries
     * 
     * @access public
     * @return false|integer
     */
    public function getNumberOfEntries()
    {
        return $this->numberOfEntries;
    }
    
    /**
     * Sets the number of entries
     * 
     * @access public
     * @param false|integer $numberOfEntries
     */
    public function setNumberOfEntries($numberOfEntries)
    {
        $this->numberOfEntries = $numberOfEntries;
    }
    
    /**
     * Returns true if only a range of the result is requested
     * 
     * @return boolean
     */
    public function hasRange()
    {
        return ($this->getOffset() !== false && $this->getOffset() >= 0 && $this->getNumberOfEntries() !== false && $this->getNumberOfEntries() > 0);
    }
    
    /**
     * Returns the key with which the data should be sorted
     * 
     * @access public
     * @return string
     */
    public function getSortBy()
    {
        return $this->sortBy;
    }
    
    /**
     * Sets the sort by field
     * 
     * @access public
     * @param string $sortBy
     */
    public function setSortBy($sortBy)
    {
        $this->sortBy = $sortBy;
    }
    
    /**
     * Returns true if the data request has sorting
     * 
     * @return boolean
     */
    public function hasSorting()
    {
        return ($this->sortBy !== '');
    }
    
    /**
     * Returns the direction by which the data should be sorted
     * 
     * @access public
     * @return string
     */
    public function getSortByDirection()
    {
        return $this->sortByDirection;
    }
    
    /**
     * Sets the sort by direction
     * 
     * @access public
     * @param string $sortByDirection
     */
    public function setSortByDirection($sortByDirection)
    {
        $this->sortByDirection = $sortByDirection;
    }
    
    /**
     * Returns the array with all selected fields
     * 
     * @access public
     * @return array
     */
    public function getSelectedFields()
    {
        return $this->selectedFields;
    }
    
    /**
     * Sets the seleted fields
     * 
     * @access public
     * @param array $selectedFields
     */
    public function setSelectedFields($selectedFields)
    {
        $this->selectedFields = $selectedFields;
    }
}
