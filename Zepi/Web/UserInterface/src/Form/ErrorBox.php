<?php
/**
 * Form Error Box
 * 
 * @package Zepi\Web\UserInterface
 * @subpackage Form
 * @author Matthias Zobrist <matthias.zobrist@zepi.net>
 * @copyright Copyright (c) 2015 zepi
 */

namespace Zepi\Web\UserInterface\Form;

use \Zepi\Web\UserInterface\Form\Field\FieldAbstract;
use \Zepi\Web\UserInterface\Layout\Part;

/**
 * Form Error Box
 * 
 * @author Matthias Zobrist <matthias.zobrist@zepi.net>
 * @copyright Copyright (c) 2015 zepi
 */
class ErrorBox extends Part
{
    /**
     * @access protected
     * @var string
     */
    protected $_templateKey = '\\Zepi\\Web\\UserInterface\\Templates\\Form\\ErrorBox';
    
    /**
     * @access protected
     * @var array
     */
    protected $_errors = array();
    
    /**
     * Constructs the object
     * 
     * @access public
     * @param string $key
     * @param array $errors
     */
    public function __construct($key, $priority = 10, $errors = array())
    {
        $this->_key = $key;
        $this->_priority = 10;
        $this->_errors = $errors;
    }
    
    /**
     * Adds an error to the error box
     * 
     * @access public
     * @param \Zepi\Web\UserInterface\Form\Error $error
     */
    public function addError(Error $error)
    {
        $this->_errors[] = $error;
    }
    
    /**
     * Returns all errors of the error box
     * 
     * @access public
     * @return array
     */
    public function getErrors()
    {
        return $this->_errors;
    }
    
    /**
     * Returns true if the error box has any errors
     * 
     * @access public
     * @return boolean
     */
    public function hasErrors()
    {
        return (count($this->_errors) > 0);
    }
}
