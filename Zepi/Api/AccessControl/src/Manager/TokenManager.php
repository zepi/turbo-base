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
use \Zepi\Core\Utils\Entity\DataRequest;
use \Zepi\Core\Utils\Entity\Filter;

/**
 * Manages the access entity type "Token"
 * 
 * @author Matthias Zobrist <matthias.zobrist@zepi.net>
 * @copyright Copyright (c) 2016 zepi
 */
class TokenManager
{
    /**
     * @var string
     */
    const ACCESS_ENTITY_TYPE = '\\Zepi\\Api\\AccessControl\\Entity\\Token';
    
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
     * Adds the given token to the access entities
     * 
     * @param \Zepi\Api\AccessControl\Entity\Token $token
     * @return false|\Zepi\Api\AccessControl\Entity\Token
     * 
     * @throws \Zepi\Api\AccessControl\Exception Cannot add the token. Name is already in use.
     * @throws \Zepi\Api\AccessControl\Exception Cannot add the token. Internal softeware error.
     */
    public function addToken(Token $token)
    {
        // If the name already is used we cannot add a new token
        if ($this->_accessControlManager->hasAccessEntityForName(self::ACCESS_ENTITY_TYPE, $token->getName())) {
            throw new Exception('Cannot add the token. Name is already in use.');
        }
        
        // Add the access entity
        $uuid = $this->_accessControlManager->addAccessEntity(
            self::ACCESS_ENTITY_TYPE, 
            $token->getName(),
            $token->getKey(),
            $token->getMetaDataArray()
        );
        
        if ($uuid === false) {
            throw new Exception('Cannot add the token. Internal softeware error.');
        }
        
        return $this->getTokenForUuid($uuid);
    }
    
    /**
     * Updates the given token
     * 
     * @param \Zepi\Web\AccessControl\Entity\Token $token
     * @return boolean
     * 
     * @throws \Zepi\Core\AccessControl\Exception Cannot update the token. Token does not exist.
     */
    public function updateToken(Token $token)
    {
        // If the uuid does not exists we cannot update the token
        if (!$this->_accessControlManager->hasAccessEntityForUuid($token->getUuid())) {
            throw new Exception('Cannot update the token. Token does not exist.');
        }
        
        // Add the access entity
        return $this->_accessControlManager->updateAccessEntity(
            $token->getUuid(), 
            $token->getName(),
            $token->getKey(),
            $token->getMetaDataArray()
        );
    }
    
    /**
     * Deletes the token with the given uuid
     * 
     * @param string $uuid
     * @return boolean
     * 
     * @throws \Zepi\Core\AccessControl\Exception Cannot delete the token. Token does not exist.
     */
    public function deleteToken($uuid)
    {
        // If the uuid does not exists we cannot delete the token
        if (!$this->_accessControlManager->hasAccessEntityForUuid($uuid)) {
            throw new Exception('Cannot update the token. Token does not exist.');
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
    public function hasTokenForUuid($uuid)
    {
        if ($this->_accessControlManager->hasAccessEntityForUuid($uuid)) {
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
        if ($this->_accessControlManager->hasAccessEntityForName(self::ACCESS_ENTITY_TYPE, $publicKey)) {
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
        if (!$this->_accessControlManager->hasAccessEntityForUuid($uuid)) {
            return false;
        }
        
        // Get the access entity
        $accessEntity = $this->_accessControlManager->getAccessEntityForUuid($uuid);

        if ($accessEntity === false) {
            return false;
        }
        
        // Create the token object
        $token = new Token(
            $accessEntity->getId(),
            $accessEntity->getUuid(),
            $accessEntity->getName(),
            $accessEntity->getKey(),
            $accessEntity->getMetaDataArray()
        );
        
        $token->setPermissions($accessEntity->getPermissions());
        
        return $token;
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
        if (!$this->_accessControlManager->hasAccessEntityForName(self::ACCESS_ENTITY_TYPE, $publicKey)) {
            return false;
        }
        
        // Get the access entity
        $accessEntity = $this->_accessControlManager->getAccessEntityForName(self::ACCESS_ENTITY_TYPE, $publicKey);

        if ($accessEntity === false) {
            return false;
        }
        
        // Create the token object
        $token = new Token(
            $accessEntity->getId(),
            $accessEntity->getUuid(),
            $accessEntity->getName(),
            $accessEntity->getKey(),
            $accessEntity->getMetaDataArray()
        );
        
        $token->setPermissions($accessEntity->getPermissions());
        
        return $token;
    }
    
    /**
     * Returns all token entities for the given data request
     * 
     * @access public
     * @param \Zepi\Core\Utils\Entity\DataRequest $dataRequest
     * @return array
     */
    public function getTokens(DataRequest $dataRequest)
    {
        $dataRequest->addFilter(new Filter('type', self::ACCESS_ENTITY_TYPE, '='));
        
        $tokens = array();
        $accessEntities = $this->_accessControlManager->getAccessEntities($dataRequest);
        foreach ($accessEntities as $accessEntity) {
            $token = new Token(
                $accessEntity->getId(),
                $accessEntity->getUuid(),
                $accessEntity->getName(),
                $accessEntity->getKey(),
                $accessEntity->getMetaDataArray()
            );
            
            $token->setPermissions($accessEntity->getPermissions());
            
            $tokens[] = $token;
        }
        
        return $tokens;
    }
    
    /**
     * Returns the number of tokens for the given data request
     * 
     * @access public
     * @param \Zepi\Core\Utils\Entity\DataRequest $dataRequest
     * @return integer
     */
    public function countTokens(DataRequest $dataRequest)
    {
        $dataRequest->addFilter(new Filter('type', self::ACCESS_ENTITY_TYPE, '='));
        
        return $this->_accessControlManager->countAccessEntities($dataRequest);
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
