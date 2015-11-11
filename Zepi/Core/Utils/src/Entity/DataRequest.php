<?php
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
    protected $_filters = array();
     
    /**
     * @access protected
     * @var integer
     */
    protected $_page;
    
    /**
     * @access protected
     * @var integer
     */
    protected $_numberOfEntries;
    
    /**
     * @access protected
     * @var string
     */
    protected $_sortBy;
    
    /**
     * @access protected
     * @var string
     */
    protected $_sortByDirection;
    
    /**
     * @access protected
     * @var string
     */
    protected $_selectedFields = array();
    
    /**
     * Constructs the object
     * 
     * @access public
     * @param integer $page
     * @param integer $numberOfEntries
     * @param string $sortBy
     * @param string $sortByDirection
     * @param array $selectedFields
     */
    public function __construct($page, $numberOfEntries, $sortBy, $sortByDirection, $selectedFields = array('*'))
    {
        $this->_page = $page;
        $this->_numberOfEntries = $numberOfEntries;
        $this->_sortBy = $sortBy;
        $this->_sortByDirection = $sortByDirection;
        $this->_selectedFields = $selectedFields;
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
        $this->_filters[$key] = $filter;
    }
    
    /**
     * Returns all filters
     * 
     * @access public
     * @return array
     */
    public function getFilters()
    {
        return $this->_filters;
    }
    
    /**
     * Returns the Filter object for the given key
     * 
     * @access public
     * @param string $name
     * @return \Zepi\Core\Utils\Entity\Filter
     */
    public function getFilter($name)
    {
        foreach ($this->_filters as $filter) {
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
        $this->_filters = array();
    }
    
    /**
     * Returns the number of the loaded page
     * 
     * @access public
     * @return integer
     */
    public function getPage()
    {
        return $this->_page;
    }
    
    /**
     * Sets the number of the loaded page
     * 
     * @access public
     * @param integer $page
     */
    public function setPage($page)
    {
        $this->_page = $page;
    }
    
    /**
     * Returns the offset
     * 
     * @access public
     * @return integer
     */
    public function getOffset()
    {
        return ($this->_page - 1) * $this->_numberOfEntries;
    }
    
    /**
     * Returns the number of entries
     * 
     * @access public
     * @return integer
     */
    public function getNumberOfEntries()
    {
        return $this->_numberOfEntries;
    }
    
    /**
     * Sets the number of entries
     * 
     * @access public
     * @param integer $numberOfEntries
     */
    public function setNumberOfEntries($numberOfEntries)
    {
        $this->_numberOfEntries = $numberOfEntries;
    }
    
    /**
     * Returns the key with which the data should be sorted
     * 
     * @access public
     * @return string
     */
    public function getSortBy()
    {
        return $this->_sortBy;
    }
    
    /**
     * Sets the sort by field
     * 
     * @access public
     * @param string $sortBy
     */
    public function setSortBy($sortBy)
    {
        $this->_sortBy = $sortBy;
    }
    
    /**
     * Returns the direction by which the data should be sorted
     * 
     * @access public
     * @return string
     */
    public function getSortByDirection()
    {
        return $this->_sortByDirection;
    }
    
    /**
     * Sets the sort by direction
     * 
     * @access public
     * @param string $sortByDirection
     */
    public function setSortByDirection($sortByDirection)
    {
        $this->_sortByDirection = $sortByDirection;
    }
    
    /**
     * Returns the array with all selected fields
     * 
     * @access public
     * @return array
     */
    public function getSelectedFields()
    {
        return $this->_selectedFields;
    }
    
    /**
     * Sets the seleted fields
     * 
     * @access public
     * @param array $selectedFields
     */
    public function setSelectedFields($selectedFields)
    {
        $this->_selectedFields = $selectedFields;
    }
}
