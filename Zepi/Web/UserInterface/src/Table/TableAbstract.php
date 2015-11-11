<?php
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
    protected $_framework;
    
    /**
     * @access protected
     * @var boolean
     */
    protected $_hasPagination;
    
    /**
     * @access protected
     * @var boolean
     */
    protected $_hasFilters;
    
    /**
     * @access protected
     * @var boolean
     */
    protected $_saveDataRequest;
    
    /**
     * Constructs the object
     * 
     * @access public
     * @param \Zepi\Turbo\Framework $framework
     * @param array $columns
     * @param boolean $hasPagination
     * @param boolean $hasFilters
     * @param boolean $saveDataRequest
     */
    public function __construct(Framework $framework, $hasPagination = true, $hasFilters = false, $saveDataRequest = true)
    {
        $this->_framework = $framework;
        $this->_hasPagination = $hasPagination;
        $this->_hasFilters = $hasFilters;
        $this->_saveDataRequest = $saveDataRequest;
    }
    
    /**
     * Returns true if the table has a pagination
     * 
     * @access public
     * @return boolean
     */
    public function hasPagination()
    {
        return ($this->_hasPagination);
    }
    
    /**
     * Returns true if the table has filters
     * 
     * @access public
     * @return boolean
     */
    public function hasFilters()
    {
        return ($this->_hasFilters);
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
        return ($this->_saveDataRequest);
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
}
