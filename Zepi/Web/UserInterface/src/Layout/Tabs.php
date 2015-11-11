<?php
/**
 * Tabs
 * 
 * @package Zepi\Web\UserInterface
 * @subpackage Layout
 * @author Matthias Zobrist <matthias.zobrist@zepi.net>
 * @copyright Copyright (c) 2015 zepi
 */

namespace Zepi\Web\UserInterface\Layout;

use \Zepi\Web\UserInterface\Exception;

/**
 * Tabs
 * 
 * @author Matthias Zobrist <matthias.zobrist@zepi.net>
 * @copyright Copyright (c) 2015 zepi
 */
class Tabs extends Part
{
    /**
     * @access protected
     * @var string
     */
    protected $_templateKey = '\\Zepi\\Web\\UserInterface\\Templates\\Layout\\Tabs';
    
    /**
     * Construct the object
     *
     * @access public
     * @param array $parts
     * @param array $classes
     * @param string $key
     * @param integer $priority
     * 
     * @throws \Zepi\Web\UserInterface\Exception Only Tab objects are allowd to add to a Tabs object.
     */
    public function __construct($parts = array(), $classes = array(), $key = '', $priority = 10)
    {
        parent::__construct(array(), $classes, $key, $priority);
        
        if (is_array($parts)) {
            foreach ($parts as $part) {
                if ($part instanceof \Zepi\Web\UserInterface\Layout\Tab) {
                    // Activate the first tab
                    if (count($this->_parts) === 0) {
                        $part->setActive(true);
                    }
                    
                    $this->addPart($part);
                } else {
                    throw new Exception('Only Tab objects are allowed to add to a Tabs object.');
                }
            }
        }
    }
}
