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
 * The ProtectedMenuEntry representats a protected entry 
 * in the navigation.
 * 
 * @package Zepi\Web\AccessControl
 * @subpackage Entity
 * @author Matthias Zobrist <matthias.zobrist@zepi.net>
 * @copyright Copyright (c) 2015 zepi
 */

namespace Zepi\Web\AccessControl\Entity;

use \Zepi\Web\AccessControl\Entity\User;
use \Zepi\Turbo\FrameworkInterface\SessionInterface;

/**
 * The ProtectedMenuEntry representats a protected entry 
 * in the navigation.
 * 
 * @author Matthias Zobrist <matthias.zobrist@zepi.net>
 * @copyright Copyright (c) 2015 zepi
 */
class Session implements SessionInterface
{
    /**
     * @access protected
     * @var \Zepi\Web\AccessControl\Entity\User
     */
    protected $_user;
    
    /**
     * @access protected
     * @var string
     */
    protected $_sessionToken;
    
    /**
     * @access protected
     * @var integer
     */
    protected $_sessionTokenLifetime;
    
    /**
     * Constructs the object
     * 
     * @access public
     * @param \Zepi\Web\AccessControl\Entity\User $user
     * @param string $sessionToken
     * @param integer $sessionTokenLifetime
     */
    public function __construct(
        User $user,
        $sessionToken,
        $sessionTokenLifetime
    ) {
        $this->_user = $user;
        $this->_sessionToken = $sessionToken;
        $this->_sessionTokenLifetime = $sessionTokenLifetime;
    }
    
    /**
     * Returns the user of the session
     * 
     * @access public
     * @return \Zepi\Web\AccessControl\Entity\User
     */
    public function getUser()
    {
        return $this->_user;
    }
    
    /**
     * Returns true if the user of this session has acces
     * to the given access level, return false otherwise.
     * 
     * @access public
     * @param string $accessLevel
     * @return boolean
     */
    public function hasAccess($accessLevel)
    {
        return $this->_user->hasAccess($accessLevel);
    }
    
    /**
     * Returns the session token
     * 
     * @access public
     * @return string
     */
    public function getSessionToken()
    {
        return $this->_sessionToken;
    }
    
    /**
     * Returns the session token lifetime
     * 
     * @access public
     * @return integer
     */
    public function getSessionTokenLifetime()
    {
        return $this->_sessionTokenLifetime;
    }
}
