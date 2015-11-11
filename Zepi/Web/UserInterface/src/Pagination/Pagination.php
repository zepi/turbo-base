<?php
/**
 * Pagination
 * 
 * @package Zepi\Web\UserInterface
 * @subpackage Pagination
 * @author Matthias Zobrist <matthias.zobrist@zepi.net>
 * @copyright Copyright (c) 2015 zepi
 */

namespace Zepi\Web\UserInterface\Pagination;

/**
 * Pagination
 * 
 * @author Matthias Zobrist <matthias.zobrist@zepi.net>
 * @copyright Copyright (c) 2015 zepi
 */
class Pagination
{
    /**
     * @access protected
     * @var array
     */
    protected $_entries = array();
    
    /**
     * Adds an entry to the pagination
     * 
     * @access public
     * @param Entry $entry
     */
    public function addEntry(Entry $entry)
    {
        $this->_entries[] = $entry;
    }
    
    /**
     * Returns all entries for the pagination
     * 
     * @access public
     * @return array
     */
    public function getEntries()
    {
        return $this->_entries;
    }
    
    public function getLatestEntry()
    {
        return end($this->_entries);
    }
}
