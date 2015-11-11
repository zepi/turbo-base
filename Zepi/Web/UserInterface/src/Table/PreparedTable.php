<?php
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
    protected $_table;
    
    /**
     * @access protected
     * @var array
     */
    protected $_columns = array();
    
    /**
     * @access protected
     * @var \Zepi\Web\UserInterface\Table\Head
     */
    protected $_head;
    
    /**
     * @access protected
     * @var \Zepi\Web\UserInterface\Table\Body
     */
    protected $_body;
    
    /**
     * @access protected
     * @var \Zepi\Web\UserInterface\Table\Foot
     */
    protected $_foot;
    
    /**
     * @access protected
     * @var \Zepi\Web\UserInterface\Pagination\Pagination
     */
    protected $_pagination;
    
    /**
     * Constructs the object
     * 
     * @access public
     * @param \Zepi\Web\UserInterface\Table\TableAbstract $table
     * @param array $columns
     */
    public function __construct(TableAbstract $table, $columns)
    {
        $this->_table = $table;
        $this->_columns = $columns;
    }
    
    /**
     * Returns the Table object
     * 
     * @access public
     * @return \Zepi\Web\UserInterface\Table\TableAbstract
     */
    public function getTable()
    {
        return $this->_table;
    }
    
    /**
     * Returns the head
     * 
     * @access public
     * @return \Zepi\Web\UserInterface\Table\Head
     */
    public function getHead()
    {
        return $this->_head;
    }
    
    /**
     * Sets the head
     * 
     * @access public
     * @param \Zepi\Web\UserInterface\Table\Head $head
     */
    public function setHead(Head $head)
    {
        $this->_head = $head;
    }
    
    /**
     * Returns true if the table has a head
     * 
     * @access public
     * @return boolean
     */
    public function hasHead()
    {
        return ($this->_head instanceof Head);
    }

    /**
     * Returns the body
     * 
     * @access public
     * @return \Zepi\Web\UserInterface\Table\Body
     */
    public function getBody()
    {
        return $this->_body;
    }
    
    /**
     * Sets the body
     * 
     * @access public
     * @param \Zepi\Web\UserInterface\Table\Body $body
     */
    public function setBody(Body $body)
    {
        $this->_body = $body;
    }
    
    /**
     * Returns true if the table has a body
     * 
     * @access public
     * @return boolean
     */
    public function hasBody()
    {
        return ($this->_body != '');
    }
    
    /**
     * Returns the foot
     * 
     * @access public
     * @return \Zepi\Web\UserInterface\Table\Foot
     */
    public function getFoot()
    {
        return $this->_foot;
    }
    
    /**
     * Sets the foot
     * 
     * @access public
     * @param \Zepi\Web\UserInterface\Table\Foot $foot
     */
    public function setFoot(Foot $foot)
    {
        $this->_foot = $foot;
    }
    
    /**
     * Returns true if the table has a foot
     * 
     * @access public
     * @return boolean
     */
    public function hasFoot()
    {
        return ($this->_foot != '');
    }
    
    /**
     * Returns the number of columns for this table
     * 
     * @access public
     * @return integer
     */
    public function getNumberOfColumns()
    {
        return count($this->_columns);
    }
    
    /**
     * Returns the pagination object
     * 
     * @access public
     * @return \Zepi\Web\UserInterface\Pagination\Pagination
     */
    public function getPagination()
    {
        return $this->_pagination;
    }

    /**
     * Adds the pagination object
     * 
     * @access public
     * @param \Zepi\Web\UserInterface\Pagination\Pagination $pagination
     */
    public function setPagination(Pagination $pagination)
    {
        $this->_pagination = $pagination;
    }
}
