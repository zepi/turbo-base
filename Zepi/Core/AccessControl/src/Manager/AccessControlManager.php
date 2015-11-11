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
 * Manages the access entities, access levels and permissions.
 * 
 * @package Zepi\Core\AccessControl
 * @subpackage Manager
 * @author Matthias Zobrist <matthias.zobrist@zepi.net>
 * @copyright Copyright (c) 2015 zepi
 */

namespace Zepi\Core\AccessControl\Manager;

use \Zepi\Core\AccessControl\Backend\AccessEntitiesBackend;
use \Zepi\Core\AccessControl\Backend\PermissionsBackend;
use \Zepi\Core\Utils\Entity\DataRequest;
use \Zepi\Core\AccessControl\Entity\AccessEntity;

/**
 * Manages the access entities, access levels and permissions.
 * 
 * @author Matthias Zobrist <matthias.zobrist@zepi.net>
 * @copyright Copyright (c) 2015 zepi
 */
class AccessControlManager
{
    /**
     * @access protected
     * @var \Zepi\Core\AccessControl\Backend\AccessEntitiesBackend
     */
    protected $_accessEntitiesBackend;
    
    /**
     * @access protected
     * @var \Zepi\Core\AccessControl\Backend\PermissionsBackend
     */
    protected $_permissionsBackend;
    
    /**
     * Constructs the object
     * 
     * @access public
     * @param \Zepi\Core\AccessControl\Backend\AccessEntitiesBackend $accessEntitiesBackend
     * @param \Zepi\Core\AccessControl\Backend\PermissionsBackend $permissionsBackend
     */
    public function __construct(
        AccessEntitiesBackend $accessEntitiesBackend, 
        PermissionsBackend $permissionsBackend
    ) {
        $this->_accessEntitiesBackend = $accessEntitiesBackend;
        $this->_permissionsBackend = $permissionsBackend;
    }
    
    /**
     * Returns an array with all found access entities for the given DataRequest
     * object.
     * 
     * @access public
     * @param \Zepi\Core\Utils\Entity\DataRequest $dataRequest
     * @return array
     */
    public function getAccessEntities(DataRequest $dataRequest)
    {
        return $this->_accessEntitiesBackend->getAccessEntities($dataRequest);
    }
    
    /**
     * Returns the number of all found access entities for the given DataRequest
     * object.
     * 
     * @access public
     * @param \Zepi\Core\Utils\Entity\DataRequest $dataRequest
     * @return integer
     */
    public function countAccessEntities(DataRequest $dataRequest)
    {
        return $this->_accessEntitiesBackend->countAccessEntities($dataRequest);
    }
    
    /**
     * Adds an access entity. Returns the uuid of the access entity
     * or false, if the access entity can not be inserted.
     * 
     * @access public
     * @param string $type
     * @param string $name
     * @param string $key
     * @param array $metaData
     * @return string|false
     */
    public function addAccessEntity($type, $name, $key, $metaData)
    {
        return $this->_accessEntitiesBackend->addAccessEntity($type, $name, $key, $metaData);
    }
    
    /**
     * Updates an access entity. Returns true if everything worked as excepted
     * or false if the access entity can not be updated.
     * 
     * @access public
     * @param string $uuid
     * @param string $name
     * @param string $key
     * @param array $metaData
     * @return boolean
     */
    public function updateAccessEntity($uuid, $name, $key, $metaData)
    {
        return $this->_accessEntitiesBackend->updateAccessEntity($uuid, $name, $key, $metaData);
    }
    
    /**
     * Deletes the given access entity in the database.
     * 
     * @access public
     * @param string $uuid
     * @return boolean
     */
    public function deleteAccessEntity($uuid)
    {
        return $this->_accessEntitiesBackend->deleteAccessEntity($uuid);
    }
    
    /**
     * Returns true if there is a access entity for the given uuid
     * 
     * @access public
     * @param string $uuid
     * @return boolean
     */
    public function hasAccessEntityForUuid($uuid)
    {
        return $this->_accessEntitiesBackend->hasAccessEntityForUuid($uuid);
    }
    
    /**
     * Returns true if there is a access entity for the given type and name
     * 
     * @access public
     * @param string $type
     * @param string $name
     * @return boolean
     */
    public function hasAccessEntityForName($type, $name)
    {
        return $this->_accessEntitiesBackend->hasAccessEntityForName($type, $name);
    }
    
    /**
     * Returns the access entity object for the given uuid
     *
     * @access public 
     * @param string $uuid
     * @return AccessEntity
     */
    public function getAccessEntityForUuid($uuid)
    {
        return $this->_accessEntitiesBackend->getAccessEntityForUuid($uuid);
    }
    
    /**
     * Returns the access entity for the given type and name
     * 
     * @param string $type
     * @param string $name
     * @return AccessEntity
     */
    public function getAccessEntityForName($type, $name)
    {
        return $this->_accessEntitiesBackend->getAccessEntityForName($type, $name);
    }
    
    /**
     * Returns true if the given access entity uuid has already access to the 
     * access level
     * 
     * @access public
     * @param string $accessEntityUuid
     * @param string $accessLevel
     * @return boolean
     */
    public function hasAccess($accessEntityUuid, $accessLevel)
    {
        return $this->_permissionsBackend->hasAccess($accessEntityUuid, $accessLevel);
    }
    
    /**
     * Adds the permission for the given access entity uuid and access level.
     * 
     * @access public
     * @param string $accessEntityUuid
     * @param string $accessLevel
     * @param string $grantedBy
     * @return boolean
     */
    public function grantPermission($accessEntityUuid, $accessLevel, $grantedBy)
    {
        return $this->_permissionsBackend->grantPermission($accessEntityUuid, $accessLevel, $grantedBy);
    }
    
    /**
     * Revokes the permission for the given access entity uuid and access level.
     * 
     * @access public
     * @param string $accessEntityUuid
     * @param string $accessLevel
     * @return boolean
     */
    public function revokePermission($accessEntityUuid, $accessLevel)
    {
        return $this->_permissionsBackend->revokePermission($accessEntityUuid, $accessLevel);
    }
    
    /**
     * Revokes the permissions for the given access level.
     *
     * @access public
     * @param string $accessLevel
     * @return boolean
     */
    public function revokePermissions($accessLevel)
    {
        return $this->_permissionsBackend->revokePermissions($accessLevel);
    }
    
    /**
     * Returns an array with all granted access levels for the given 
     * access entity uuid without resolving the group access levels
     * 
     * @access public
     * @param string $accessEntityUuid
     * @return array
     */
    public function getPermissionsRaw($accessEntityUuid)
    {
        return $this->_permissionsBackend->getPermissionsRaw($accessEntityUuid);
    }
    
    /**
     * Returns an array with all granted access levels for the given access entity uuid
     *
     * @access public
     * @param string $accessEntityUuid
     * @return array
     */
    public function getPermissions($accessEntityUuid)
    {
        return $this->_permissionsBackend->getPermissions($accessEntityUuid);
    }
    
    /**
     * Adds and removes an array with access level to the given access 
     * entity uuid. If the donor hasn't the permission for the access 
     * level no action is taken.
     * 
     * @access public
     * @param string $accessEntitiyUuid
     * @param array $accessLevels
     * @param \Zepi\Core\AccessControl\Entity\AccessEntity $donor
     */
    public function updatePermissions($accessEntitiyUuid, $accessLevels, AccessEntity $donor)
    {
        $permissions = $this->getPermissionsRaw($accessEntitiyUuid);
        
        $grantedPermissions = array_diff($accessLevels, $permissions);
        $revokedPermissions = array_diff($permissions, $accessLevels);
        
        // Grant the added access levels
        foreach ($grantedPermissions as $accessLevel) {
            if (!$donor->hasAccess($accessLevel)) {
                continue;
            }

            $this->grantPermission($accessEntitiyUuid, $accessLevel, $donor->getName());
        }
        
        // Revoke the removed access levels
        foreach ($revokedPermissions as $accessLevel) {
            if (!$donor->hasAccess($accessLevel)) {
                continue;
            }

            $this->revokePermission($accessEntitiyUuid, $accessLevel);
        }
    }
}
