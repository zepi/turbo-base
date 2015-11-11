<?php
/**
 * Form Element Submit button
 * 
 * @package Zepi\Web\UserInterface
 * @subpackage Form\Field
 * @author Matthias Zobrist <matthias.zobrist@zepi.net>
 * @copyright Copyright (c) 2015 zepi
 */

namespace Zepi\Web\UserInterface\Form\Field;

/**
 * Form Element Submit button
 * 
 * @author Matthias Zobrist <matthias.zobrist@zepi.net>
 * @copyright Copyright (c) 2015 zepi
 */
class Submit extends Button
{
    /**
     * @access protected
     * @var string
     */
    protected $_type = 'submit';
    
    /**
     * @access protected
     * @var array
     */
    protected $_classes = array(
        'btn-primary',
        'submit-btn',
    );
    
    /**
     * Sets the html form value of the field
     * 
     * @access public
     * @param string $value
     */
    public function setValue($value)
    {
        if ($value == $this->_label) {
            $form = $this->getParentOfType('\\Zepi\\Web\\UserInterface\\Form\\Form');
            
            if (is_object($form)) {
                $form->setIsSubmitted(true);
            }
        }
        
        parent::setValue($value);
    }
}
