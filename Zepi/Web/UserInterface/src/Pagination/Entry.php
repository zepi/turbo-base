<?php
/**
 * Entry
 * 
 * @package Zepi\Web\UserInterface
 * @subpackage Pagination
 * @author Matthias Zobrist <matthias.zobrist@zepi.net>
 * @copyright Copyright (c) 2015 zepi
 */

namespace Zepi\Web\UserInterface\Pagination;

/**
 * Entry
 * 
 * @author Matthias Zobrist <matthias.zobrist@zepi.net>
 * @copyright Copyright (c) 2015 zepi
 */
class Entry
{
    /**
     * @access protected
     * @var string
     */
    protected $_label;
    
    /**
     * Constructs the object
     * 
     * @param string $label
     */
    public function __construct($label)
    {
        $this->_label = $label;
    }
    
    /**
     * Returns the label of the entry
     * 
     * @access public
     * @return string
     */
    public function getLabel()
    {
        return $this->_label;
    }
}
