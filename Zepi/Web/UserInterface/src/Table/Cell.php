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
    protected $column;
    
    /**
     * @access protected
     * @var \Zepi\Web\UserInterface\Table\Row
     */
    protected $row;
    
    /**
     * @access protected
     * @var mixed
     */
    protected $value;
    
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
        $this->column = $column;
        $this->row = $row;
        $this->value = $value;
    }
    
    /**
     * Returns the column of the cell
     * 
     * @access public
     * @return \Zepi\Web\UserInterface\Table\Column
     */
    public function getColumn()
    {
        return $this->column;
    }
    
    /**
     * Returns the row of the cell
     * 
     * @access public
     * @return \Zepi\Web\UserInterface\Table\Row
     */
    public function getRow()
    {
        return $this->row;
    }
    
    /**
     * Returns the value of the cell
     * 
     * @access public
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }
}
