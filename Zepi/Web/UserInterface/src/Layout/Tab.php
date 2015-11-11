<?php
/**
 * Tab
 * 
 * @package Zepi\Web\UserInterface
 * @subpackage Layout
 * @author Matthias Zobrist <matthias.zobrist@zepi.net>
 * @copyright Copyright (c) 2015 zepi
 */

namespace Zepi\Web\UserInterface\Layout;

/**
 * Tab
 * 
 * @author Matthias Zobrist <matthias.zobrist@zepi.net>
 * @copyright Copyright (c) 2015 zepi
 */
class Tab extends Part
{
    /**
     * @access protected
     * @var string
     */
    protected $_name;
    
    /**
     * @access protected
     * @var boolean
     */
    protected $_active = false;
    
    /**
     * @access protected
     * @var string
     */
    protected $_templateKey = '\\Zepi\\Web\\UserInterface\\Templates\\Layout\\Tab';
    
    /**
     * Construct the object
     *
     * @access public
     * @param array $parts
     * @param array $classes
     * @param string $key
     * @param string $name
     * @param integer $priority
     */
    public function __construct($parts = array(), $classes = array(), $key = '', $name = '', $priority = 10)
    {
        parent::__construct($parts, $classes, $key, $priority);
        
        $this->_name = $name;
    }
    
    /**
     * Returns the name of the tab
     * 
     * @access public
     * @return string
     */
    public function getName()
    {
        return $this->_name;
    }
    
    /**
     * Returns true if the tab is active
     * 
     * @access public
     * @return boolean
     */
    public function isActive()
    {
        return ($this->_active);
    }
    
    /**
     * Activates or deactivates the tab
     *
     * @access public
     * @param boolean $active
     */
    public function setActive($active)
    {
        $this->_active = $active;
    }
}
