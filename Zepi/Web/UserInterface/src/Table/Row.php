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
    protected $parentPart;
    
    /**
     * @access protected
     * @var array
     */
    protected $cells = array();
    
    /**
     * Constructs the object
     * 
     * @access public
     * @param \Zepi\Web\UserInterface\Table\Part $part
     */
    public function __construct(Part $part)
    {
        $this->parentPart = $part;
    }
    
    /**
     * Returns the parent part of the row
     * 
     * @access public
     * @return \Zepi\Web\UserInterface\Table\Part
     */
    public function getParentPart()
    {
        return $this->parentPart;
    }
    
    /**
     * Adds a cell
     * 
     * @access public
     * @param \Zepi\Web\UserInterface\Table\Cell $cell
     */
    public function addCell(Cell $cell)
    {
        $this->cells[] = $cell;
    }
    
    /**
     * Returns all cells
     * 
     * @access public
     * @return array
     */
    public function getCells()
    {
        return $this->cells;
    }
}
