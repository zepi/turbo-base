<?php
/*
 * The MIT License (MIT)
 *
 * Copyright (c) 2016 zepi
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
 * Manages the access entity type "Token"
 * 
 * @package Zepi\Api\AccessControl
 * @subpackage Manager
 * @author Matthias Zobrist <matthias.zobrist@zepi.net>
 * @copyright Copyright (c) 2016 zepi
 */

namespace Zepi\Api\AccessControl\Manager;

use \Zepi\Core\AccessControl\Exception;
use \Zepi\Core\AccessControl\Manager\AccessControlManager;
use \Zepi\Api\AccessControl\Entity\Token;
use \Zepi\DataSource\Core\Entity\EntityInterface;
use \Zepi\DataSource\Core\Entity\DataRequest;
use \Zepi\DataSource\Core\DataAccess\DataAccessInterface;

/**
 * Manages the access entity type "Token"
 * 
 * @author Matthias Zobrist <matthias.zobrist@zepi.net>
 * @copyright Copyright (c) 2016 zepi
 */
class TokenManager implements DataAccessInterface
{
    /**
     * @var string
     */
    const ACCESS_ENTITY_TYPE = '\\Zepi\\Api\\AccessControl\\Entity\\Token';
    
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
     * Adds the given token to the access entities
     * 
     * @param \Zepi\DataSource\Core\Entity\EntityInterface $token
     * @return false|\Zepi\Api\AccessControl\Entity\Token
     * 
     * @throws \Zepi\Api\AccessControl\Exception The given entity is not compatible with this data source.
     * @throws \Zepi\Api\AccessControl\Exception Cannot add the token. Name is already in use.
     * @throws \Zepi\Api\AccessControl\Exception Cannot add the token. Internal softeware error.
     */
    public function add(EntityInterface $token)
    {
        if (!is_a($token, self::ACCESS_ENTITY_TYPE)) {
            throw new Exception('The given entity (' . get_class($token) . ') is not compatible with this data source (' . self::class . '.');
        }
        
        // If the name already is used we cannot add a new token
        if ($this->accessControlManager->hasAccessEntityForName(self::ACCESS_ENTITY_TYPE, $token->getName())) {
            throw new Exception('Cannot add the token. Name is already in use.');
        }
        
        // Add the access entity
        $uuid = $this->accessControlManager->addAccessEntity($token);
        
        if ($uuid === false) {
            throw new Exception('Cannot add the token. Internal software error.');
        }
        
        return $token;
    }
    
    /**
     * Updates the given token
     * 
     * @param \Zepi\DataSource\Core\Entity\EntityInterface $token
     * @return boolean
     * 
     * @throws \Zepi\Api\AccessControl\Exception The given entity is not compatible with this data source.
     * @throws \Zepi\Api\AccessControl\Exception Cannot update the token. Token does not exist.
     */
    public function update(EntityInterface $token)
    {
        if (!is_a($token, self::ACCESS_ENTITY_TYPE)) {
            throw new Exception('The given entity (' . get_class($token) . ') is not compatible with this data source (' . self::class . '.');
        }
        
        // If the uuid does not exists we cannot update the token
        if (!$this->accessControlManager->hasAccessEntityForUuid(self::ACCESS_ENTITY_TYPE, $token->getUuid())) {
            throw new Exception('Cannot update the token. Token does not exist.');
        }
        
        // Add the access entity
        return $this->accessControlManager->updateAccessEntity($token);
    }
    
    /**
     * Deletes the token with the given uuid
     * 
     * @param \Zepi\DataSource\Core\Entity\EntityInterface $token
     * @return boolean
     * 
     * @throws \Zepi\Api\AccessControl\Exception The given entity is not compatible with this data source.
     * @throws \Zepi\Api\AccessControl\Exception Cannot delete the token. Token does not exist.
     */
    public function delete(EntityInterface $token)
    {
        if (!is_a($token, self::ACCESS_ENTITY_TYPE)) {
            throw new Exception('The given entity (' . get_class($token) . ') is not compatible with this data source (' . self::class . '.');
        }
        
        // If the uuid does not exists we cannot delete the token
        if (!$this->accessControlManager->hasAccessEntityForUuid(self::ACCESS_ENTITY_TYPE, $token->getUuid())) {
            throw new Exception('Cannot update the token. Token does not exist.');
        }
        
        // Delete the access entity
        return $this->accessControlManager->deleteAccessEntity($token);
    }
    
    /**
     * Returns true if the given uuid exists as access entity
     * 
     * @access public
     * @param string $uuid
     * @return boolean
     */
    public function hasTokenForUuid($uuid)
    {
        if ($this->accessControlManager->hasAccessEntityForUuid(self::ACCESS_ENTITY_TYPE, $uuid)) {
            return true;
        }
        
        return false;
    }
    
    /**
     * Returns true if the given public key exists as access entity
     * 
     * @access public
     * @param string $publicKey
     * @return boolean
     */
    public function hasTokenForPublicKey($publicKey)
    {
        if ($this->accessControlManager->hasAccessEntityForName(self::ACCESS_ENTITY_TYPE, $publicKey)) {
            return true;
        }
        
        return false;
    }
    
    /**
     * Returns the token object for the given uuid
     * 
     * @access public
     * @param string $uuid
     * @return boolean
     */
    public function getTokenForUuid($uuid)
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
     * Returns the token object for the given public key
     * 
     * @access public
     * @param string $publicKey
     * @return boolean
     */
    public function getTokenForPublicKey($publicKey)
    {
        if (!$this->accessControlManager->hasAccessEntityForName(self::ACCESS_ENTITY_TYPE, $publicKey)) {
            return false;
        }
        
        // Get the access entity
        $accessEntity = $this->accessControlManager->getAccessEntityForName(self::ACCESS_ENTITY_TYPE, $publicKey);

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
    
    /**
     * Returns an public key
     * 
     * @access public
     * @return string
     */
    public function generatePublicKey()
    {
        return md5(uniqid('public-key-', true));
    }
    
    /**
     * Returns an secret key
     * 
     * @access public
     * @return string
     */
    public function generateSecretKey()
    {
        return md5(uniqid('private-key-', true)) . md5(microtime(true));
    }
}
