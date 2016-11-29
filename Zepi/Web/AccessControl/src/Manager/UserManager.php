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
use \Zepi\Core\AccessControl\Manager\AccessControlManager;
use \Zepi\Web\AccessControl\Entity\User;
use \Zepi\DataSource\Core\Entity\DataRequest;
use \Zepi\DataSource\Core\Manager\DataSourceManagerInterface;

/**
 * Manages the access entity type "user"
 * 
 * @author Matthias Zobrist <matthias.zobrist@zepi.net>
 * @copyright Copyright (c) 2015 zepi
 */
class UserManager implements DataSourceManagerInterface
{
    /**
     * @var string
     */
    const ACCESS_ENTITY_TYPE = '\\Zepi\\Web\\AccessControl\\Entity\\User';
    
    /**
     * @access protected
     * @var \Zepi\Core\AccessControl\Manager\AccessControlManager
     */
    protected $accessControlManager;
    
    /**
     * Constructs the object
     * 
     * @access public
     * @param \Zepi\Core\AccessControl\Manager\AccessControlManager $accessControlManager
     */
    public function __construct(
        AccessControlManager $accessControlManager
    ) {
        $this->accessControlManager = $accessControlManager;
    }
    
    /**
     * Adds the given user to the access entities
     * 
     * @param \Zepi\Web\AccessControl\Entity\User $user
     * @return false|\Zepi\Web\AccessControl\Entity\User
     * 
     * @throws \Zepi\Web\AccessControl\Exception Cannot add the user. Username is already in use.
     * @throws \Zepi\Web\AccessControl\Exception Cannot add the user. Internal software error.
     */
    public function addUser(User $user)
    {
        // If the username already is used we cannot add a new user
        if ($this->accessControlManager->hasAccessEntityForName(self::ACCESS_ENTITY_TYPE, $user->getName())) {
            throw new Exception('Cannot add the user. Username is already in use.');
        }
        
        // Add the access entity
        $uuid = $this->accessControlManager->addAccessEntity($user);
        
        if ($uuid === false) {
            throw new Exception('Cannot add the user. Internal software error.');
        }
        
        return $user;
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
        if (!$this->accessControlManager->hasAccessEntityForUuid(self::ACCESS_ENTITY_TYPE, $user->getUuid())) {
            throw new Exception('Cannot update the user. User does not exist.');
        }
        
        // Add the access entity
        return $this->accessControlManager->updateAccessEntity($user);
    }
    
    /**
     * Deletes the user with the given uuid
     * 
     * @param \Zepi\Web\AccessControl\Entity\User $user
     * @return boolean
     * 
     * @throws \Zepi\Core\AccessControl\Exception Cannot delete the user. User does not exist.
     */
    public function deleteUser($user)
    {
        // If the uuid does not exists we cannot delete the user
        if (!$this->accessControlManager->hasAccessEntityForUuid(self::ACCESS_ENTITY_TYPE, $user->getUuid())) {
            throw new Exception('Cannot delete the user. User does not exist.');
        }
        
        // Delete the access entity
        return $this->accessControlManager->deleteAccessEntity($user);
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
        if ($this->accessControlManager->hasAccessEntityForUuid(self::ACCESS_ENTITY_TYPE, $uuid)) {
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
        if ($this->accessControlManager->hasAccessEntityForName(self::ACCESS_ENTITY_TYPE, $username)) {
            return true;
        }
        
        return false;
    }
    
    /**
     * Returns the user object for the given uuid
     * 
     * @access public
     * @param string $uuid
     * @return false|\Zepi\Web\AccessControl\Entity\User
     */
    public function getUserForUuid($uuid)
    {
        if (!$this->accessControlManager->hasAccessEntityForUuid(self::ACCESS_ENTITY_TYPE, $uuid)) {
            return false;
        }
        
        // Get the access entity
        $accessEntity = $this->accessControlManager->getAccessEntityForUuid(self::ACCESS_ENTITY_TYPE, $uuid);

        if ($accessEntity === false) {
            return false;
        }
        
        return $accessEntity;
    }
    
    /**
     * Returns the user object for the given username
     * 
     * @access public
     * @param string $username
     * @return false|\Zepi\Web\AccessControl\Entity\User
     */
    public function getUserForUsername($username)
    {
        if (!$this->accessControlManager->hasAccessEntityForName(self::ACCESS_ENTITY_TYPE, $username)) {
            return false;
        }
        
        // Get the access entity
        $accessEntity = $this->accessControlManager->getAccessEntityForName(self::ACCESS_ENTITY_TYPE, $username);

        if ($accessEntity === false) {
            return false;
        }
        
        return $accessEntity;
    }
    
    /**
     * Returns an array with all entities for the given data request.
     * 
     * @param \Zepi\DataSource\Core\DataRequest $dataRequest
     * @return false|array
     */
    public function find(DataRequest $dataRequest)
    {
        return $this->accessControlManager->find(self::ACCESS_ENTITY_TYPE, $dataRequest);
    }
    
    /**
     * Returns the number of entities which are available for the given
     * data request.
     *
     * @param \Zepi\DataSource\Core\DataRequest $dataRequest
     * @return integer
     */
    public function count(DataRequest $dataRequest)
    {
        return $this->accessControlManager->count(self::ACCESS_ENTITY_TYPE, $dataRequest);
    }
    
    /**
     * Returns true if the given entity id exists
     *
     * @param integer $entityId
     * @return boolean
     */
    public function has($entityId)
    {
        return $this->accessControlManager->has(self::ACCESS_ENTITY_TYPE, $entityId);
    }
    
    /**
     * Returns the entity for the given id. Returns false if
     * there is no entity for the given id.
     *
     * @param integer $entityId
     * @return false|mixed
     */
    public function get($entityId)
    {
        return $this->accessControlManager->get(self::ACCESS_ENTITY_TYPE, $entityId);
    }
}
