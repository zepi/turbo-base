<?php
/**
 * Form Element Password
 * 
 * @package Zepi\Web\UserInterface
 * @subpackage Form\Field
 * @author Matthias Zobrist <matthias.zobrist@zepi.net>
 * @copyright Copyright (c) 2015 zepi
 */

namespace Zepi\Web\UserInterface\Form\Field;

/**
 * Form Element Password
 * 
 * @author Matthias Zobrist <matthias.zobrist@zepi.net>
 * @copyright Copyright (c) 2015 zepi
 */
class Password extends FieldAbstract
{
    /**
     * Returns the name of the template to render the field
     * 
     * @access public
     * @return string
     */
    public function getTemplateName()
    {
        return '\\Zepi\\Web\\UserInterface\\Templates\\Form\\Field\\Password';
    }
}
