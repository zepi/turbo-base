<?php
/**
 * A hidden menu entry will not be visible in the navigation or in the 
 * subnavigation but the parent node will be active if available.
 * 
 * @package Zepi\Web\General
 * @subpackage Entity
 * @author Matthias Zobrist <matthias.zobrist@zepi.net>
 * @copyright Copyright (c) 2015 zepi
 */

namespace Zepi\Web\General\Entity;

/**
 * A hidden menu entry will not be visible in the navigation or in the 
 * subnavigation but the parent node will be active if available.
 * 
 * @author Matthias Zobrist <matthias.zobrist@zepi.net>
 * @copyright Copyright (c) 2015 zepi
 */
class HiddenMenuEntry extends MenuEntry
{
    /**
     * Constructs the object
     * 
     * @access public
     * @param string $name
     * @param string $target
     * @param string $iconClass
     */
    public function __construct($name, $target = '#', $iconClass = '')
    {
        $this->_name = $name;
        $this->_target = $target;
        $this->_iconClass = $iconClass;
    }
}
