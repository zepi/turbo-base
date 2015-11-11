<?php
/**
 * Row
 * 
 * @package Zepi\Web\UserInterface
 * @subpackage Table
 * @author Matthias Zobrist <matthias.zobrist@zepi.net>
 * @copyright Copyright (c) 2015 zepi
 */

namespace Zepi\Web\UserInterface\Table;

use \Zepi\Web\UserInterface\Table\Cell;
use \Zepi\Web\UserInterface\Table\Part;

/**
 * Row
 * 
 * @author Matthias Zobrist <matthias.zobrist@zepi.net>
 * @copyright Copyright (c) 2015 zepi
 */
class Row
{
    /**
     * @access protected
     * @var \Zepi\Web\UserInterface\Table\Part
     */
    protected $_parentPart;
    
    /**
     * @access protected
     * @var array
     */
    protected $_cells = array();
    
    /**
     * Constructs the object
     * 
     * @access public
     * @param \Zepi\Web\UserInterface\Table\Part $part
     */
    public function __construct(Part $part)
    {
        $this->_parentPart = $part;
    }
    
    /**
     * Returns the parent part of the row
     * 
     * @access public
     * @return \Zepi\Web\UserInterface\Table\Part
     */
    public function getParentPart()
    {
        return $this->_parentPart;
    }
    
    /**
     * Adds a cell
     * 
     * @access public
     * @param \Zepi\Web\UserInterface\Table\Cell $cell
     */
    public function addCell(Cell $cell)
    {
        $this->_cells[] = $cell;
    }
    
    /**
     * Returns all cells
     * 
     * @access public
     * @return array
     */
    public function getCells()
    {
        return $this->_cells;
    }
}
