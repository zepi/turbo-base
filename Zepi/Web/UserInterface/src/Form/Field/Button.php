<?php
/**
 * Form Element Button button
 * 
 * @package Zepi\Web\UserInterface
 * @subpackage Form\Field
 * @author Matthias Zobrist <matthias.zobrist@zepi.net>
 * @copyright Copyright (c) 2015 zepi
 */

namespace Zepi\Web\UserInterface\Form\Field;

/**
 * Form Element Button button
 * 
 * @author Matthias Zobrist <matthias.zobrist@zepi.net>
 * @copyright Copyright (c) 2015 zepi
 */
class Button extends FieldAbstract
{
    /**
     * @access protected
     * @var string
     */
    protected $_type = 'button';
    
    /**
     * @access protected
     * @var array
     */
    protected $_classes = array();
    
    /**
     * @access protected
     * @var string
     */
    protected $_templateKey = '\\Zepi\\Web\\UserInterface\\Templates\\Form\\Field\\Blank';
    
    /**
     * @access protected
     * @var string
     */
    protected $_iconClass = '';
    
    /**
     * @access protected
     * @var string
     */
    protected $_htmlType;
    
    /**
     * @access protected
     * @var string
     */
    protected $_href;
    
    /**
     * Constructs the object
     * 
     * @access public
     * @param string $key
     * @param string $label
     * @param array $classes
     * @param string $iconClass
     * @param string $htmlType
     * @param string $href
     */
    public function __construct($key, $label, $classes = array(), $iconClass = '', $htmlType = 'button', $href = false)
    {
        $this->_key = $key;
        $this->_label = $label;
        $this->_iconClass = $iconClass;
        $this->_htmlType = $htmlType;
        $this->_href = $href;
        
        if (count($classes) > 0) {
            $this->_classes = array_merge($this->_classes, $classes);
        }
    }
    
    /**
     * Returns true if the label of the field should be displayed
     * 
     * @access public
     * @return boolean
     */
    public function displayLabel()
    {
        return false;
    }
    
    /**
     * Returns the name of the template to render the field
     * 
     * @access public
     * @return string
     */
    public function getTemplateName()
    {
        return '\\Zepi\\Web\\UserInterface\\Templates\\Form\\Field\\Button';
    }
    
    /**
     * Returns the type of the button
     * 
     * @access public
     * @return string
     */
    public function getType()
    {
        return $this->_type;
    }
    
    /**
     * Returns the icon class
     * 
     * @access public
     * @return string
     */
    public function getIconClass()
    {
        return $this->_iconClass;
    }
    
    /**
     * Returns true if the icon class isn't empty
     * 
     * @access public
     * @return boolean
     */
    public function hasIconClass()
    {
        return ($this->_iconClass !== '');
    }
    
    /**
     * Returns the html type of this button
     * 
     * @access public
     * @return string
     */
    public function getHtmlType()
    {
        return $this->_htmlType;
    }
    
    /**
     * Returns the href of this button
     * 
     * @access public
     * @return string
     */
    public function getHref()
    {
        return $this->_href;
    }
}
