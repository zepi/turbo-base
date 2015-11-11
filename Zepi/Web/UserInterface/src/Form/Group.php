<?php
/**
 * Form Group
 * 
 * @package Zepi\Web\UserInterface
 * @subpackage Form
 * @author Matthias Zobrist <matthias.zobrist@zepi.net>
 * @copyright Copyright (c) 2015 zepi
 */

namespace Zepi\Web\UserInterface\Form;

use \Zepi\Web\UserInterface\Layout\Part;

/**
 * Form Group
 * 
 * @author Matthias Zobrist <matthias.zobrist@zepi.net>
 * @copyright Copyright (c) 2015 zepi
 */
class Group extends Part
{
    /**
     * @access protected
     * @var string
     */
    protected $_key;
    
    /**
     * @access protected
     * @var string
     */
    protected $_label;
    
    /**
     * @access protected
     * @var string
     */
    protected $_templateKey = '\\Zepi\\Web\\UserInterface\\Templates\\Form\\Group';
    
    /**
     * Constructs the object
     * 
     * @access public
     * @param string $key
     * @param string $label
     * @param array $parts
     * @param integer $priority
     */
    public function __construct($key, $label, $parts = array(), $priority = 10)
    {
        $this->_key = $key;
        $this->_label = $label;
        $this->_priority = $priority;
        
        foreach ($parts as $part) {
            $this->addPart($part);
        }
    }
    
    /** 
     * Returns the html id of this group
     * 
     * @access public
     * @return string
     */
    public function getHtmlId()
    {
        $form = $this->getParentOfType('\\Zepi\\Web\\UserInterface\\Form\\Form');

        // If the group isn't assigned to a data form we can not generate
        // the id of the group.
        if (!$form) {
            return false;
        }
        
        return $form->getHtmlId() . '-' . $this->_key;
    }
    
    /**
     * Returns the key of this group
     * 
     * @access public
     * @return string
     */
    public function getKey()
    {
        return $this->_key;
    }
    
    /**
     * Returns the label for the group
     * 
     * @access public
     * @return string
     */
    public function getLabel()
    {
        return $this->_label;
    }
    
    /**
     * Returns true if the group has a label
     * 
     * @access public
     * @return boolean
     */
    public function hasLabel()
    {
        return (trim($this->_label) !== '');
    }
}
