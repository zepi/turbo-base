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
 * Manages the access entity type "User"
 * 
 * @package Zepi\Web\AccessControl
 * @subpackage Manager
 * @author Matthias Zobrist <matthias.zobrist@zepi.net>
 * @copyright Copyright (c) 2015 zepi
 */

namespace Zepi\Web\AccessControl\Manager;

use \Zepi\Core\AccessControl\Exception;
use \Zepi\Core\AccessControl\Backend\AccessEntitiesBackend;
use \Zepi\Core\AccessControl\Backend\PermissionsBackend;
use \Zepi\Core\AccessControl\Manager\AccessControlManager;
use \Zepi\Web\AccessControl\Entity\User;
use \Zepi\Core\Utils\Entity\DataRequest;
use \Zepi\Core\Utils\Entity\Filter;

/**
 * Manages the access entity type "user"
 * 
 * @author Matthias Zobrist <matthias.zobrist@zepi.net>
 * @copyright Copyright (c) 2015 zepi
 */
class UserManager
{
    /**
     * @var string
     */
    const ACCESS_ENTITY_TYPE = '\\Zepi\\Web\\AccessControl\\Entity\\User';
    
    /**
     * @access protected
     * @var \Zepi\Core\AccessControl\Manager\AccessControlManager
     */
    protected $_accessControlManager;
    
    /**
     * Constructs the object
     * 
     * @access public
     * @param \Zepi\Core\AccessControl\Manager\AccessControlManager $accessControlManager
     */
    public function __construct(
        AccessControlManager $accessControlManager
    ) {
        $this->_accessControlManager = $accessControlManager;
    }
    
    /**
     * Adds the given user to the access entities
     * 
     * @param \Zepi\Web\AccessControl\Entity\User $user
     * @return boolean
     * 
     * @throws \Zepi\Core\AccessControl\Exception Cannot add the user. Username is already in use.
     */
    public function addUser(User $user)
    {
        // If the username already is used we cannot add a new user
        if ($this->_accessControlManager->hasAccessEntityForName(self::ACCESS_ENTITY_TYPE, $user->getName())) {
            throw new Exception('Cannot add the user. Username is already in use.');
        }
        
        // Add the access entity
        $uuid = $this->_accessControlManager->addAccessEntity(
            self::ACCESS_ENTITY_TYPE, 
            $user->getName(),
            $user->getKey(),
            $user->getMetaDataArray()
        );
        
        if ($uuid === false) {
            throw new Exception('Cannot add the user. Internal softeware error.');
        }
        
        return $this->_accessControlManager->getAccessEntityForUuid($uuid);
    }
    
    /**
     * Updates the given user
     * 
     * @param \Zepi\Web\AccessControl\Entity\User $user
     * @return boolean
     * 
     * @throws \Zepi\Core\AccessControl\Exception Cannot update the user. User does not exist.
     */
    public function updateUser(User $user)
    {
        // If the uuid does not exists we cannot update the user
        if (!$this->_accessControlManager->hasAccessEntityForUuid($user->getUuid())) {
            throw new Exception('Cannot update the user. User does not exist.');
        }
        
        // Add the access entity
        return $this->_accessControlManager->updateAccessEntity(
            $user->getUuid(), 
            $user->getName(),
            $user->getKey(),
            $user->getMetaDataArray()
        );
    }
    
    /**
     * Deletes the user with the given uuid
     * 
     * @param string $uuid
     * @return boolean
     * 
     * @throws \Zepi\Core\AccessControl\Exception Cannot delete the user. User does not exist.
     */
    public function deleteUser($uuid)
    {
        // If the uuid does not exists we cannot delete the user
        if (!$this->_accessControlManager->hasAccessEntityForUuid($uuid)) {
            throw new Exception('Cannot update the user. User does not exist.');
        }
        
        // Delete the access entity
        return $this->_accessControlManager->deleteAccessEntity($uuid);
    }
    
    /**
     * Returns true if the given uuid exists as access entity
     * 
     * @access public
     * @param string $uuid
     * @return boolean
     */
    public function hasUserForUuid($uuid)
    {
        if ($this->_accessControlManager->hasAccessEntityForUuid($uuid)) {
            return true;
        }
        
        return false;
    }
    
    /**
     * Returns true if the given username exists as access entity
     * 
     * @access public
     * @param string $username
     * @return boolean
     */
    public function hasUserForUsername($username)
    {
        if ($this->_accessControlManager->hasAccessEntityForName(self::ACCESS_ENTITY_TYPE, $username)) {
            return true;
        }
        
        return false;
    }
    
    /**
     * Returns the user object for the given uuid
     * 
     * @access public
     * @param string $uuid
     * @return boolean
     */
    public function getUserForUuid($uuid)
    {
        if (!$this->_accessControlManager->hasAccessEntityForUuid($uuid)) {
            return false;
        }
        
        // Get the access entity
        $accessEntity = $this->_accessControlManager->getAccessEntityForUuid($uuid);

        if ($accessEntity === false) {
            return false;
        }
        
        // Create the user object
        $user = new User(
            $accessEntity->getId(),
            $accessEntity->getUuid(),
            $accessEntity->getName(),
            $accessEntity->getKey(),
            $accessEntity->getMetaDataArray()
        );
        
        $user->setPermissions($accessEntity->getPermissions());
        
        return $user;
    }
    
    /**
     * Returns the user object for the given username
     * 
     * @access public
     * @param string $username
     * @return boolean
     */
    public function getUserForUsername($username)
    {
        if (!$this->_accessControlManager->hasAccessEntityForName(self::ACCESS_ENTITY_TYPE, $username)) {
            return false;
        }
        
        // Get the access entity
        $accessEntity = $this->_accessControlManager->getAccessEntityForName(self::ACCESS_ENTITY_TYPE, $username);

        if ($accessEntity === false) {
            return false;
        }
        
        // Create the user object
        $user = new User(
            $accessEntity->getId(),
            $accessEntity->getUuid(),
            $accessEntity->getName(),
            $accessEntity->getKey(),
            $accessEntity->getMetaDataArray()
        );
        
        $user->setPermissions($accessEntity->getPermissions());
        
        return $user;
    }
    
    /**
     * Returns all user entities for the given data request
     * 
     * @access public
     * @param \Zepi\Core\Utils\Entity\DataRequest $dataRequest
     * @return array
     */
    public function getUsers(DataRequest $dataRequest)
    {
        $dataRequest->addFilter(new Filter('type', self::ACCESS_ENTITY_TYPE, '='));
        
        $users = array();
        $accessEntities = $this->_accessControlManager->getAccessEntities($dataRequest);
        foreach ($accessEntities as $accessEntity) {
            $user = new User(
                $accessEntity->getId(),
                $accessEntity->getUuid(),
                $accessEntity->getName(),
                $accessEntity->getKey(),
                $accessEntity->getMetaDataArray()
            );
            
            $user->setPermissions($accessEntity->getPermissions());
            
            $users[] = $user;
        }
        
        return $users;
    }
    
    /**
     * Returns the number of users for the given data request
     * 
     * @access public
     * @param \Zepi\Core\Utils\Entity\DataRequest $dataRequest
     * @return integer
     */
    public function countUsers(DataRequest $dataRequest)
    {
        $dataRequest->addFilter(new Filter('type', self::ACCESS_ENTITY_TYPE, '='));
        
        return $this->_accessControlManager->countAccessEntities($dataRequest);
    }
}
