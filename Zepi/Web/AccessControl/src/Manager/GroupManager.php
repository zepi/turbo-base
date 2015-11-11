<?php
/**
 * Manages the access entity type "Group"
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
use \Zepi\Web\AccessControl\Entity\Group;
use \Zepi\Core\Utils\Entity\DataRequest;
use \Zepi\Core\Utils\Entity\Filter;

/**
 * Manages the access entity type "Group"
 * 
 * @author Matthias Zobrist <matthias.zobrist@zepi.net>
 * @copyright Copyright (c) 2015 zepi
 */
class GroupManager
{
    /**
     * @var string
     */
    const ACCESS_ENTITY_TYPE = '\\Zepi\\Web\\AccessControl\\Entity\\Group';
    
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
     * Adds the given group to the access entities
     * 
     * @param \Zepi\Web\AccessControl\Entity\Group $group
     * @return boolean
     * 
     * @throws \Zepi\Core\AccessControl\Exception Cannot add the group. The name of the group is already in use.
     */
    public function addGroup(Group $group)
    {
        // If the name of the group already is used we cannot add a new group
        if ($this->_accessControlManager->hasAccessEntityForName(self::ACCESS_ENTITY_TYPE, $group->getName())) {
            throw new Exception('Cannot add the group. The name of the group is already in use.');
        }
        
        // Add the access entity
        $uuid = $this->_accessControlManager->addAccessEntity(
            self::ACCESS_ENTITY_TYPE, 
            $group->getName(),
            $group->getKey(),
            $group->getMetaDataArray()
        );
        
        return $this->_accessControlManager->getAccessEntityForUuid($uuid);
    }
    
    /**
     * Updates the given Group
     * 
     * @param \Zepi\Web\AccessControl\Entity\Group $group
     * @return boolean
     * 
     * @throws \Zepi\Core\AccessControl\Exception Cannot update the group. The group does not exist.
     */
    public function updateGroup(Group $group)
    {
        // If the uuid does not exists we cannot update the group
        if (!$this->_accessControlManager->hasAccessEntityForUuid($group->getUuid())) {
            throw new Exception('Cannot update the group. The group does not exist.');
        }
        
        // Update the access entity
        return $this->_accessControlManager->updateAccessEntity(
            $group->getUuid(), 
            $group->getName(),
            $group->getKey(),
            $group->getMetaDataArray()
        );
    }
    
    /**
     * Deletes the group with the given uuid
     *
     * @param string $uuid
     * @return boolean
     *
     * @throws \Zepi\Core\AccessControl\Exception Cannot delete the group. Group does not exist.
     */
    public function deleteGroup($uuid)
    {
        // If the uuid does not exists we cannot delete the group
        if (!$this->_accessControlManager->hasAccessEntityForUuid($uuid)) {
            throw new Exception('Cannot update the group. Group does not exist.');
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
    public function hasGroupForUuid($uuid)
    {
        if ($this->_accessControlManager->hasAccessEntityForUuid($uuid)) {
            return true;
        }
        
        return false;
    }
    
    /**
     * Returns true if the given name of the group exists as access entity
     * 
     * @access public
     * @param string $name
     * @return boolean
     */
    public function hasGroupForName($name)
    {
        if ($this->_accessControlManager->hasAccessEntityForName(self::ACCESS_ENTITY_TYPE, $name)) {
            return true;
        }
        
        return false;
    }
    
    /**
     * Returns the group object for the given uuid
     * 
     * @access public
     * @param string $uuid
     * @return boolean
     */
    public function getGroupForUuid($uuid)
    {
        if (!$this->_accessControlManager->hasAccessEntityForUuid($uuid)) {
            return false;
        }
        
        // Get the access entity
        $accessEntity = $this->_accessControlManager->getAccessEntityForUuid($uuid);

        if ($accessEntity === false) {
            return false;
        }
        
        // Create the group object
        $group = new Group(
            $accessEntity->getId(),
            $accessEntity->getUuid(),
            $accessEntity->getName(),
            $accessEntity->getKey(),
            $accessEntity->getMetaDataArray()
        );
        
        return $group;
    }
    
    /**
     * Returns the Group object for the given name
     * 
     * @access public
     * @param string $name
     * @return boolean
     */
    public function getGroupForName($name)
    {
        if (!$this->_accessControlManager->hasAccessEntityForName(self::ACCESS_ENTITY_TYPE, $name)) {
            return false;
        }
        
        // Get the access entity
        $accessEntity = $this->_accessControlManager->getAccessEntityForName(self::ACCESS_ENTITY_TYPE, $name);

        if ($accessEntity === false) {
            return false;
        }
        
        // Create the Group object
        $group = new Group(
            $accessEntity->getId(),
            $accessEntity->getUuid(),
            $accessEntity->getName(),
            $accessEntity->getKey(),
            $accessEntity->getMetaDataArray()
        );
        
        return $group;
    }
    
    /**
     * Returns all group entities for the given data request
     * 
     * @access public
     * @param \Zepi\Core\Utils\Entity\DataRequest $dataRequest
     * @return array
     */
    public function getGroups(DataRequest $dataRequest)
    {
        $dataRequest->addFilter(new Filter('type', self::ACCESS_ENTITY_TYPE, '='));
        
        return $this->_accessControlManager->getAccessEntities($dataRequest);
    }
    
    /**
     * Returns the number of users for the given data request
     *
     * @access public
     * @param \Zepi\Core\Utils\Entity\DataRequest $dataRequest
     * @return integer
     */
    public function countGroups(DataRequest $dataRequest)
    {
        $dataRequest->addFilter(new Filter('type', self::ACCESS_ENTITY_TYPE, '='));
    
        return $this->_accessControlManager->countAccessEntities($dataRequest);
    }
}
