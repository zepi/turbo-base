<?php
/**
 * Cell
 * 
 * @package Zepi\Web\UserInterface
 * @subpackage Table
 * @author Matthias Zobrist <matthias.zobrist@zepi.net>
 * @copyright Copyright (c) 2015 zepi
 */

namespace Zepi\Web\UserInterface\Table;

use \Zepi\Web\UserInterface\Table\Column;
use \Zepi\Web\UserInterface\Table\Row;

/**
 * Cell
 * 
 * @author Matthias Zobrist <matthias.zobrist@zepi.net>
 * @copyright Copyright (c) 2015 zepi
 */
class Cell
{
    /**
     * @access protected
     * @var \Zepi\Web\UserInterface\Table\Column
     */
    protected $_column;
    
    /**
     * @access protected
     * @var \Zepi\Web\UserInterface\Table\Row
     */
    protected $_row;
    
    /**
     * @access protected
     * @var mixed
     */
    protected $_value;
    
    /**
     * Construcst the object
     * 
     * @access public
     * @param \Zepi\Web\UserInterface\Table\Column $column
     * @param \Zepi\Web\UserInterface\Table\Row $row
     * @param mixed $value
     */
    public function __construct(Column $column, Row $row, $value = '')
    {
        $this->_column = $column;
        $this->_row = $row;
        $this->_value = $value;
    }
    
    /**
     * Returns the column of the cell
     * 
     * @access public
     * @return \Zepi\Web\UserInterface\Table\Column
     */
    public function getColumn()
    {
        return $this->_column;
    }
    
    /**
     * Returns the row of the cell
     * 
     * @access public
     * @return \Zepi\Web\UserInterface\Table\Row
     */
    public function getRow()
    {
        return $this->_row;
    }
    
    /**
     * Returns the value of the cell
     * 
     * @access public
     * @return mixed
     */
    public function getValue()
    {
        return $this->_value;
    }
}
