<?php
/**
 * Form ButtonGroup
 * 
 * @package Zepi\Web\UserInterface
 * @subpackage Form
 * @author Matthias Zobrist <matthias.zobrist@zepi.net>
 * @copyright Copyright (c) 2015 zepi
 */

namespace Zepi\Web\UserInterface\Form;

/**
 * Form ButtonGroup
 * 
 * @author Matthias Zobrist <matthias.zobrist@zepi.net>
 * @copyright Copyright (c) 2015 zepi
 */
class ButtonGroup extends Group
{
    /**
     * @access protected
     * @var string
     */
    protected $_templateKey = '\\Zepi\\Web\\UserInterface\\Templates\\Form\\ButtonGroup';
    
    /**
     * @access protected
     * @var string
     */
    protected $_classes = '';
    
    /**
     * Constructs the object
     * 
     * @access public
     * @param string $key
     * @param array $parts
     * @param string $classes
     * @param integer $priority
     */
    public function __construct($key, $parts = array(), $priority = 10, $classes = 'text-center')
    {
        $this->_key = $key;
        $this->_priority = $priority;
        $this->_classes = $classes;

        foreach ($parts as $part) {
            $this->addPart($part);
        }
    }
    
    /**
     * Returns all classes of this button group
     * 
     * @access public
     * @return string
     */
    public function getClasses()
    {
        return $this->_classes;
    }
}
