<?php
/**
 * Manages the access levels.
 * 
 * @package Zepi\Core\AccessControl
 * @subpackage Manager
 * @author Matthias Zobrist <matthias.zobrist@zepi.net>
 * @copyright Copyright (c) 2015 zepi
 */

namespace Zepi\Core\AccessControl\Manager;

use \Zepi\Turbo\Framework;
use \Zepi\Turbo\Request\RequestAbstract;
use \Zepi\Turbo\Response\Response;
use \Zepi\Turbo\Backend\ObjectBackendAbstract;
use \Zepi\Core\AccessControl\Entity\AccessLevel;

/**
 * Manages the access levels.
 * 
 * @author Matthias Zobrist <matthias.zobrist@zepi.net>
 * @copyright Copyright (c) 2015 zepi
 */
class AccessLevelManager
{
    /**
     * @access protected
     * @var array
     */
    protected $_accessLevels = array();
    
    /**
     * @access protected
     * @var Zepi\Turbo\Framework
     */
    protected $_framework;
    
    /**
     * @access protected
     * @var \Zepi\Turbo\Backend\ObjectBackendAbstract
     */
    protected $_accessLevelsObjectBackend;
    
    /**
     * @access protected
     * @var \Zepi\Core\AccessControl\Manager\AccessControlManager
     */
    protected $_accessControlManager;
    
    /**
     * Constructs the object
     * 
     * @access public
     * @param \Zepi\Turbo\Framework $framework
     * @param \Zepi\Turbo\Backend\ObjectBackendAbstract $accessLevelObjectBackend
     * @param \Zepi\Core\AccessControl\Manager\AccessControlManager $accessControlManager
     */
    public function __construct(
        Framework $framework,
        ObjectBackendAbstract $accessLevelObjectBackend,
        AccessControlManager $accessControlManager
    ) {
        $this->_framework = $framework;
        $this->_accessLevelsObjectBackend = $accessLevelObjectBackend;
        $this->_accessControlManager = $accessControlManager;
    }
    
    /**
     * Initializes the access levels manager.
     * 
     * @access public
     */
    public function initializeAccessLevelManager()
    {
        $this->_loadAccessLevels();
    }
    
    /**
     * Loads the access levels from the object backend
     * 
     * @access public
     */
    protected function _loadAccessLevels()
    {
        $accessLevels = $this->_accessLevelsObjectBackend->loadObject();
        if (!is_array($accessLevels)) {
            $accessLevels = array();
        }
        
        $this->_accessLevels = $accessLevels;
    }
    
    /**
     * Saves the access levels to the object backend.
     * 
     * @access public
     */
    protected function _saveAccessLevels()
    {
        $this->_accessLevelsObjectBackend->saveObject($this->_accessLevels);
    }
    
    /**
     * Adds a new access level.
     * 
     * @access public
     * @param AccessLevel $accessLevel
     * @return boolean
     */
    public function addAccessLevel(AccessLevel $accessLevel)
    {
        // Add the access level
        $this->_accessLevels[$accessLevel->getKey()] = $accessLevel;
        
        // Save the access levels
        $this->_saveAccessLevels();
    }
    
    /**
     * Removes the access level.
     * 
     * @access public
     * @param string $key
     */
    public function removeAccessLevel($key)
    {
        if (!isset($this->_accessLevels[$key])) {
            return false;
        }
        
        // Remove the access level
        unset($this->_accessLevels[$key]);
        
        // Save the access levels
        $this->_saveAccessLevels();
        
        // Revoke all permissions for the given access level key
        $this->_accessControlManager->revokePermissions($key);
    }
    
    /**
     * Returns all access levels
     * 
     * @access public
     * @return array
     */
    public function getAccessLevels()
    {
        // Give the modules the opportunity to add additional access levels
        $eventManager = $this->_framework->getEventManager();
        $eventManager->executeEvent('\\Zepi\\Core\\AccessControl\\Event\\AccessLevelManager\\RegisterAccessLevels');
        
        return $this->_accessLevels;
    }
}
