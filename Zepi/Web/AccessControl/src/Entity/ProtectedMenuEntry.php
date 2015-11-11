<?php
/**
 * The ProtectedMenuEntry representats a protected entry 
 * in the navigation.
 * 
 * @package Zepi\Web\AccessControl
 * @subpackage Entity
 * @author Matthias Zobrist <matthias.zobrist@zepi.net>
 * @copyright Copyright (c) 2015 zepi
 */

namespace Zepi\Web\AccessControl\Entity;

use \Zepi\Web\General\Entity\MenuEntry;

/**
 * The ProtectedMenuEntry representats a protected entry 
 * in the navigation.
 * 
 * @author Matthias Zobrist <matthias.zobrist@zepi.net>
 * @copyright Copyright (c) 2015 zepi
 */
class ProtectedMenuEntry extends MenuEntry
{
    /**
     * @access protected
     * @var string
     */
    protected $_accessLevelKey;
    
    /**
     * Constructs the object
     * 
     * @access public
     * @param string $key
     * @param string $name
     * @param string $accessLevelKey
     * @param string $target
     * @param string $iconClass
     * @param string $class
     * @param string $window
     */
    public function __construct(
        $key, 
        $name,
        $accessLevelKey,
        $target = '#', 
        $iconClass = '',
        $class = '', 
        $window = 'self'
    ) {
       parent::__construct($key, $name, $target, $iconClass, $class, $window);
       
       $this->_accessLevelKey = $accessLevelKey;
    }
    
    /**
     * Returns the access level key for the protected
     * menu entry.
     * 
     * @access public
     * @return string
     */
    public function getAccessLevelKey()
    {
        return $this->_accessLevelKey;
    }
}
