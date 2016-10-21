<?php
/*
 * The MIT License (MIT)
 *
 * Copyright (c) 2015 zepi
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 */

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
 * 
 * @Entity 
 * @Table(name="users")
 */
class User extends AccessEntity
{
    /**
     * Constructs the object
     * 
     * @param integer $id
     * @param string $uuid
     * @param string $name
     * @param string $privateKey
     * @param array $metaData
     */
    public function __construct($id, $uuid, $name, $privateKey, array $metaData)
    {
        parent::__construct(
            $id,
            $uuid,
            $name,
            $privateKey,
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
        $passwordSalted = $this->saltPassword($password);
        if (password_verify($passwordSalted, $this->privateKey)) {
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
        $this->generateSalt();
        
        $this->privateKey = $this->encodePassword($password);
    }
    
    /**
     * Encodes the given plain password
     * 
     * @access protected
     * @param string $password
     * @return string
     */
    protected function encodePassword($password)
    {
        $passwordSalted = $this->saltPassword($password);
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
    protected function saltPassword($password)
    {
        if (isset($this->metaData['salt'])) {
            $salt = $this->metaData['salt'];
        } else {
            $salt = $this->generateSalt();
        }
        
        return $salt . $password;
    }
    
    /**
     * Generates and sets a new salt
     * 
     * @access protected
     * @return string
     */
    protected function generateSalt()
    {
        $this->metaData['salt'] = uniqid('', true);
        
        return $this->metaData['salt'];
    }
}
