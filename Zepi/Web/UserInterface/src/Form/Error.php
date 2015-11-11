<?php
/**
 * Form Error
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
 * Form Error
 * 
 * @author Matthias Zobrist <matthias.zobrist@zepi.net>
 * @copyright Copyright (c) 2015 zepi
 */
class Error extends Part
{
    const GENERAL_ERROR = 1;
    const MANDATORY_FIELD = 2;
    const WRONG_INPUT = 3;
    const WRONG_FORMAT = 4;
    
    /**
     * @access protected
     * @var integer
     */
    protected $_errorCode;
    
    /**
     * @access protected
     * @var string
     */
    protected $_errorMessage;

    /**
     * @access protected
     * @var \Zepi\Web\UserInterface\Form\Field\FieldAbstract
     */    
    protected $_field;
    
    /**
     * Constructs the object
     * 
     * @access public
     * @param integer $errorCode
     * @param string $errorMessage
     * @param \Zepi\Web\UserInterface\Form\Field\FieldAbstract $field
     */
    public function __construct($errorCode, $errorMessage, FieldAbstract $field = null)
    {
        $this->_errorCode = $errorCode;
        $this->_errorMessage = $errorMessage;
        $this->_field = $field;
    }
    
    /**
     * Returns the error code of the error
     * 
     * @access public
     * @return integer
     */
    public function getErrorCode()
    {
        return $this->_errorCode;
    }
    
    /**
     * Returns the error message of the error
     * 
     * @access public
     * @return string
     */
    public function getErrorMessage()
    {
        return $this->_errorMessage;
    }
    
    /**
     * Returns the field of the error
     * 
     * @access public
     * @return \Zepi\Web\UserInterface\Form\Field\FieldAbstract
     */
    public function getField()
    {
        return $this->_field;
    }
    
    /**
     * Returns true if the error has a field assigned
     * 
     * @access public
     * @return boolean
     */
    public function hasField()
    {
        return ($this->_field !== null);
    }
}
