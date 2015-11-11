<?php
/**
 * Page
 * 
 * @package Zepi\Web\UserInterface
 * @subpackage Pagination
 * @author Matthias Zobrist <matthias.zobrist@zepi.net>
 * @copyright Copyright (c) 2015 zepi
 */

namespace Zepi\Web\UserInterface\Pagination;

/**
 * Page
 * 
 * @author Matthias Zobrist <matthias.zobrist@zepi.net>
 * @copyright Copyright (c) 2015 zepi
 */
class Page extends Entry
{
    /**
     * @access protected
     * @var string
     */
    protected $_url;
    
    /**
     * Constructs the object
     * 
     * @param string $label
     */
    public function __construct($label, $url)
    {
        parent::__construct($label);
        
        $this->_url = $url;
    }
    
    /**
     * Returns the url of the page
     * 
     * @access public
     * @return string
     */
    public function getUrl()
    {
        return $this->_url;
    }
}
