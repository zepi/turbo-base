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

use \Zepi\Core\AccessControl\DataSource\AccessEntitiesDataSourceInterface;
use \Zepi\Core\AccessControl\DataSource\PermissionsDataSourceInterface;
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
     * @var \Zepi\Core\AccessControl\DataSource\AccessEntitiesDataSourceInterface
     */
    protected $_accessEntitiesDataSource;
    
    /**
     * @access protected
     * @var \Zepi\Core\AccessControl\DataSource\PermissionsDataSourceInterface
     */
    protected $_permissionsDataSource;
    
    /**
     * Constructs the object
     * 
     * @access public
     * @param \Zepi\Core\AccessControl\DataSource\AccessEntitiesDataSourceInterface $accessEntitiesDataSource
     * @param \Zepi\Core\AccessControl\DataSource\PermissionsDataSourceInterface $permissionsDataSource
     */
    public function __construct(
        AccessEntitiesDataSourceInterface $accessEntitiesDataSource, 
        PermissionsDataSourceInterface $permissionsDataSource
    ) {
        $this->_accessEntitiesDataSource = $accessEntitiesDataSource;
        $this->_permissionsDataSource = $permissionsDataSource;
    }
    
    /**
     * Returns an array with all found access entities for the given DataRequest
     * object.
     * 
     * @access public
     * @param string $class
     * @param \Zepi\Core\Utils\Entity\DataRequest $dataRequest
     * @return array
     */
    public function getAccessEntities($class, DataRequest $dataRequest)
    {
        return $this->_accessEntitiesDataSource->getAccessEntities($class, $dataRequest);
    }
    
    /**
     * Returns the number of all found access entities for the given DataRequest
     * object.
     * 
     * @access public
     * @param string $class
     * @param \Zepi\Core\Utils\Entity\DataRequest $dataRequest
     * @return integer
     */
    public function countAccessEntities($class, DataRequest $dataRequest)
    {
        return $this->_accessEntitiesDataSource->countAccessEntities($class, $dataRequest);
    }
    
    /**
     * Adds an access entity. Returns the uuid of the access entity
     * or false, if the access entity can not be inserted.
     * 
     * @access public
     * @param \Zepi\Core\AccessControl\Entity\AccessEntity $accessEntity
     * @return string|false
     */
    public function addAccessEntity(AccessEntity $accessEntity)
    {
        return $this->_accessEntitiesDataSource->addAccessEntity($accessEntity);
    }
    
    /**
     * Updates an access entity. Returns true if everything worked as excepted
     * or false if the access entity can not be updated.
     * 
     * @access public
     * @param \Zepi\Core\AccessControl\Entity\AccessEntity $accessEntity
     * @return boolean
     */
    public function updateAccessEntity(AccessEntity $accessEntity)
    {
        return $this->_accessEntitiesDataSource->updateAccessEntity($accessEntity);
    }
    
    /**
     * Deletes the given access entity in the database.
     * 
     * @access public
     * @param \Zepi\Core\AccessControl\Entity\AccessEntity $accessEntity
     * @return boolean
     */
    public function deleteAccessEntity(AccessEntity $accessEntity)
    {
        return $this->_accessEntitiesDataSource->deleteAccessEntity($accessEntity);
    }
    
    /**
     * Returns true if there is a access entity for the given uuid
     * 
     * @access public
     * @param string $class
     * @param string $uuid
     * @return boolean
     */
    public function hasAccessEntityForUuid($class, $uuid)
    {
        return $this->_accessEntitiesDataSource->hasAccessEntityForUuid($class, $uuid);
    }
    
    /**
     * Returns true if there is a access entity for the given type and name
     * 
     * @access public
     * @param string $class
     * @param string $name
     * @return boolean
     */
    public function hasAccessEntityForName($class, $name)
    {
        return $this->_accessEntitiesDataSource->hasAccessEntityForName($class, $name);
    }
    
    /**
     * Returns the access entity object for the given uuid
     *
     * @access public 
     * @param string $class
     * @param string $uuid
     * @return false|\Zepi\Core\AccessControl\Entity\Accessentity
     */
    public function getAccessEntityForUuid($class, $uuid)
    {
        return $this->_accessEntitiesDataSource->getAccessEntityForUuid($class, $uuid);
    }
    
    /**
     * Returns the access entity for the given type and name
     * 
     * @param string $class
     * @param string $name
     * @return false|\Zepi\Core\AccessControl\Entity\Accessentity
     */
    public function getAccessEntityForName($class, $name)
    {
        return $this->_accessEntitiesDataSource->getAccessEntityForName($class, $name);
    }
    
    /**
     * Returns an array with all found permissions for the given DataRequest
     * object.
     *
     * @access public
     * @param \Zepi\Core\Utils\Entity\DataRequest $dataRequest
     * @return array
     */
    public function getPermissions(DataRequest $dataRequest)
    {
        $permissions = $this->_permissionsDataSource->getPermissions($dataRequest);
        
        foreach ($permissions as $key => $permission) {
            if (!$this->_accessEntitiesDataSource->hasAccessEntityForUuid($permission->getAccessEntityUuid())) {
                unset($permissions[$key]);
                continue;
            }
            
            $accessEntity = $this->getAccessEntityForUuid($permission->getAccessEntityUuid());
            $permission->setAccessEntity($accessEntity);
        }
        
        return $permissions;
    }
    
    /**
     * Returns the number of all found permissions for the given DataRequest
     * object.
     *
     * @access public
     * @param \Zepi\Core\Utils\Entity\DataRequest $dataRequest
     * @return integer
     */
    public function countPermissions(DataRequest $dataRequest)
    {
        return $this->_permissionsDataSource->countPermissions($dataRequest);
    }
    
    /**
     * Returns true if there is a permission object for the given id
     *
     * @access public
     * @param integer $id
     * @return boolean
     */
    public function hasPermissionForId($id)
    {
        return $this->_permissionsDataSource->hasPermissionForId($id);
    }
    
    /**
     * Returns the permission object for the given id
     *
     * @access public
     * @param integer $id
     * @return false|\Zepi\Core\AccessControl\Entity\Permission
     */
    public function getPermissionForId($id)
    {
        $permission = $this->_permissionsDataSource->getPermissionForId($id);
        
        if ($permission instanceof \Zepi\Core\AccessControl\Entity\Permission) {
            $accessEntity = $this->getAccessEntityForUuid($permission->getAccessEntityUuid());
            $permission->setAccessEntity($accessEntity);
        }
        
        return $permission;
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
        return $this->_permissionsDataSource->hasAccess($accessEntityUuid, $accessLevel);
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
        return $this->_permissionsDataSource->grantPermission($accessEntityUuid, $accessLevel, $grantedBy);
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
        return $this->_permissionsDataSource->revokePermission($accessEntityUuid, $accessLevel);
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
        return $this->_permissionsDataSource->revokePermissions($accessLevel);
    }
    
    /**
     * Returns an array with all granted access levels for the given 
     * access entity uuid without resolving the group access levels
     * 
     * @access public
     * @param string $accessEntityUuid
     * @return array
     */
    public function getPermissionsRawForUuid($accessEntityUuid)
    {
        return $this->_permissionsDataSource->getPermissionsRawForUuid($accessEntityUuid);
    }
    
    /**
     * Returns an array with all granted access levels for the given access entity uuid
     *
     * @access public
     * @param string $accessEntityUuid
     * @return array
     */
    public function getPermissionsForUuid($accessEntityUuid)
    {
        return $this->_permissionsDataSource->getPermissionsForUuid($accessEntityUuid);
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
        $permissions = $this->getPermissionsRawForUuid($accessEntitiyUuid);
        
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
