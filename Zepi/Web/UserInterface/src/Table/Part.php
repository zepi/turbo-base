<?php
/**
 * Part
 * 
 * @package Zepi\Web\UserInterface
 * @subpackage Table
 * @author Matthias Zobrist <matthias.zobrist@zepi.net>
 * @copyright Copyright (c) 2015 zepi
 */

namespace Zepi\Web\UserInterface\Table;

use \Zepi\Web\UserInterface\Table\Row;

/**
 * Part
 * 
 * @author Matthias Zobrist <matthias.zobrist@zepi.net>
 * @copyright Copyright (c) 2015 zepi
 */
class Part
{
    /**
     * @access protected
     * @var array
     */
    protected $_rows = array();
    
    /**
     * Adds a row
     * 
     * @access public
     * @param \Zepi\Web\UserInterface\Table\Row $row
     */
    public function addRow(Row $row)
    {
        $this->_rows[] = $row;
    }
    
    /**
     * Returns all rows
     * 
     * @access public
     * @return array
     */
    public function getRows()
    {
        return $this->_rows;
    }
    
    /**
     * Returns true if the part has any rows
     * 
     * @access public
     * @return boolean
     */
    public function hasRows()
    {
        return (count($this->_rows) > 0);
    }
}
