<?php
/**
 * The User object representates the access entitiy "user"
 * 
 * @package Zepi\Web\AccessControl
 * @subpackage Entity
 * @author Matthias Zobrist <matthias.zobrist@zepi.net>
 * @copyright Copyright (c) 2015 zepi
 */

namespace Zepi\Web\AccessControl\Entity;

use \Zepi\Core\AccessControl\Entity\AccessEntity;

/**
 * The User object representates the access entitiy "user"
 * 
 * @author Matthias Zobrist <matthias.zobrist@zepi.net>
 * @copyright Copyright (c) 2015 zepi
 */
class User extends AccessEntity
{
    /**
     * Constructs the object
     * 
     * @param integer $id
     * @param string $uuid
     * @param string $name
     * @param string $key
     * @param array $metaData
     */
    public function __construct($id, $uuid, $name, $key, array $metaData)
    {
        parent::__construct(
            $id,
            $uuid,
            get_class($this),
            $name,
            $key,
            $metaData
        );
    }
    
    /**
     * Compares the given password from the login form with 
     * the saved one.
     * 
     * @access public
     * @param string $password
     * @return boolean
     */
    public function comparePasswords($password)
    {
        $passwordSalted = $this->_saltPassword($password);
        if (password_verify($passwordSalted, $this->_key)) {
            return true;
        }
        
        return false;
    }
    
    /**
     * Saves a new password
     * 
     * @access public
     * @param string $password
     */
    public function setNewPassword($password)
    {
        $this->_generateSalt();
        
        $this->_key = $this->_encodePassword($password);
    }
    
    /**
     * Encodes the given plain password
     * 
     * @access protected
     * @param string $password
     * @return string
     */
    protected function _encodePassword($password)
    {
        $passwordSalted = $this->_saltPassword($password);
        $passwordEncoded = password_hash($passwordSalted, PASSWORD_DEFAULT);
        
        return $passwordEncoded;
    }
    
    /**
     * Returns the salted password
     * 
     * @access protected
     * @param string $password
     * @return string
     */
    protected function _saltPassword($password)
    {
        $salt = '';
        if (isset($this->_metaData['salt'])) {
            $salt = $this->_metaData['salt'];
        } else {
            $salt = $this->_generateSalt();
        }
        
        return $salt . $password;
    }
    
    /**
     * Generates and sets a new salt
     * 
     * @access protected
     * @return string
     */
    protected function _generateSalt()
    {
        $this->_metaData['salt'] = uniqid('', true);
        
        return $this->_metaData['salt'];
    }
}
