<?php
/**
 * Form Element Textarea
 * 
 * @package Zepi\Web\UserInterface
 * @subpackage Form\Field
 * @author Matthias Zobrist <matthias.zobrist@zepi.net>
 * @copyright Copyright (c) 2015 zepi
 */

namespace Zepi\Web\UserInterface\Form\Field;

/**
 * Form Element Textarea
 * 
 * @author Matthias Zobrist <matthias.zobrist@zepi.net>
 * @copyright Copyright (c) 2015 zepi
 */
class Textarea extends FieldAbstract
{
    /**
     * @access protected
     * @var integer
     */
    protected $_rows;
    
    /**
     * Constructs the object
     * 
     * @access public
     * @param string $label
     * @param boolean $isMandatory
     * @param string $value
     * @param string $helpText
     * @param array $classes
     * @param string $placeholder
     */
    public function __construct($key, $label, $isMandatory = false, $value = '', $rows = 5, $helpText = '', $classes = array(), $placeholder = '')
    {
        $this->_rows = $rows;
        
        parent::__construct($key, $label, $isMandatory, $value, $helpText, $classes, $placeholder);
    }
    
    /**
     * Returns the name of the template to render the field
     * 
     * @access public
     * @return string
     */
    public function getTemplateName()
    {
        return '\\Zepi\\Web\\UserInterface\\Templates\\Form\\Field\\Textarea';
    }
    
    /**
     * Returns the number of rows
     * 
     * @access public
     * @return integer
     */
    public function getRows()
    {
        return $this->_rows;
    }
}
