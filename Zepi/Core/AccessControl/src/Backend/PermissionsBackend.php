<?php
/**
 * The PermissionsBackend communicates with the database and 
 * loads and saves the permissions.
 * 
 * @package Zepi\Core\AccessControl
 * @subpackage Backend
 * @author Matthias Zobrist <matthias.zobrist@zepi.net>
 * @copyright Copyright (c) 2015 zepi
 */

namespace Zepi\Core\AccessControl\Backend;

use \Zepi\DataSources\DatabaseMysql\Backend\DatabaseBackend;
use \Zepi\Turbo\Manager\EventManager;

/**
 * The PermissionsBackend communicates with the database and 
 * loads and saves the permissions.
 * 
 * @author Matthias Zobrist <matthias.zobrist@zepi.net>
 * @copyright Copyright (c) 2015 zepi
 */
class PermissionsBackend
{
    /**
     * @access protected
     * @var DatabaseBackend
     */
    protected $_databaseBackend;
    
    /**
     * @access protected
     * @var EventManager
     */
    protected $_eventManager;
    
    /**
     * Constructs the object
     * 
     * @access public
     * @param \Zepi\DataSources\DatabaseMysql\Backend\DatabaseBackend $databaseBackend
     * @param \Zepi\Turbo\Manager\EventManager $eventManager
     */
    public function __construct(DatabaseBackend $databaseBackend, EventManager $eventManager)
    {
        $this->_databaseBackend = $databaseBackend;
        $this->_eventManager = $eventManager;
    }
    
    /**
     * Sets up the database for the access levels backend
     * 
     * @access public
     */
    public function setupDatabase()
    {
        $sql = 'CREATE TABLE IF NOT EXISTS `permissions` (' 
             . '  `permission_id` int(11) NOT NULL AUTO_INCREMENT,'
             . '  `permission_access_entity_uuid` varchar(40) NOT NULL,'
             . '  `permission_access_level_key` varchar(255) NOT NULL,'
             . '  `permission_granted_by` varchar(40) NOT NULL,'
             . '  PRIMARY KEY (`permission_id`),'
             . '  UNIQUE KEY `permission` (`permission_access_entity_uuid`,`permission_access_level_key`)'
             . ') ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;';
        
        $this->_databaseBackend->execute($sql);
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
        // Do not check the database if we haven't all data
        if ($accessEntityUuid == '' || $accessLevel == '') {
            return false;
        }
        
        try {
            $sql = 'SELECT COUNT(permission_id) AS count FROM permissions '
                 . 'WHERE permission_access_entity_uuid = ' . $this->_databaseBackend->escape($accessEntityUuid) . ' '
                 . 'AND permission_access_level_key = ' . $this->_databaseBackend->escape($accessLevel);
                 
            $result = $this->_databaseBackend->query($sql)->fetch();
 
            if (isset($result['count']) && $result['count'] > 0) {
                return true;
            }
            
            return false;                        
        } catch (\Exception $e) {
            return false;
        }
    }
    
    /**
     * Returns an array with all granted access levels for the given
     * access entity uuid whithout resolving the group access levels.
     *
     * @access public
     * @param string $accessEntityUuid
     * @return array
     */
    public function getPermissionsRaw($accessEntityUuid)
    {
        // Do not check the database if we haven't all data
        if ($accessEntityUuid == '') {
            return array();
        }
    
        try {
            $sql = 'SELECT permission_access_level_key FROM permissions '
                 . 'WHERE permission_access_entity_uuid = ' . $this->_databaseBackend->escape($accessEntityUuid);
             
            $permissions = $this->_databaseBackend->query($sql)->fetchAll();

            $accessLevels = array();
            foreach ($permissions as $permission) {
                $accessLevels[] = $permission['permission_access_level_key'];
            }

            return $accessLevels;
        } catch (\Exception $e) {
            return false;
        }
    }
    
    /**
     * Returns an array with all granted access levels for the given
     * access entity uuid
     *
     * @access public
     * @param string $accessEntityUuid
     * @return array
     */
    public function getPermissions($accessEntityUuid)
    {
        // Do not check the database if we haven't all data
        if ($accessEntityUuid == '') {
            return array();
        }
    
        try {
            $accessLevels = $this->getPermissionsRaw($accessEntityUuid);
    
            $accessLevels = $this->_eventManager->executeEvent('\\Zepi\\Core\\AccessControl\\Event\\PermissionsBackend\\ResolvePermissions', $accessLevels);
    
            return $accessLevels;
        } catch (\Exception $e) {
            return false;
        }
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
        // Do not add the permission if we haven't all data
        if ($accessEntityUuid == '' || $accessLevel == '' || $grantedBy == '') {
            return false;
        }
        
        // If the access entity uuid has already the permission
        if ($this->hasAccess($accessEntityUuid, $accessLevel)) {
            return true;
        }
        
        try {
            $sql = 'INSERT INTO permissions VALUES ('
                 . 'NULL, '
                 . $this->_databaseBackend->escape($accessEntityUuid) . ', '
                 . $this->_databaseBackend->escape($accessLevel) . ', '
                 . $this->_databaseBackend->escape($grantedBy) . ')';
                 
            $this->_databaseBackend->execute($sql);
            
            return true;
        } catch (\Exception $e) {
            return false;
        }
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
        // Do not revoke the permission if we haven't all data
        if ($accessEntityUuid == '' || $accessLevel == '') {
            return false;
        }
        
        // If the access entity uuid hasn't the permission, we have nothing to do...
        if (!$this->hasAccess($accessEntityUuid, $accessLevel)) {
            return true;
        }
        
        try {
            $sql = 'DELETE FROM permissions '
                 . 'WHERE permission_access_entity_uuid = ' . $this->_databaseBackend->escape($accessEntityUuid) . ' '
                 . 'AND permission_access_level_key = ' . $this->_databaseBackend->escape($accessLevel);
                 
            $this->_databaseBackend->execute($sql);
            
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
}
