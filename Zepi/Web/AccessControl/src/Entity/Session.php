<?php
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
