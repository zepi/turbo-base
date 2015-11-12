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
 * The PermissionsDataSourceMysql communicates with the MySQL Database and 
 * loads and saves the permissions.
 * 
 * @package Zepi\Core\AccessControl
 * @subpackage DataSource
 * @author Matthias Zobrist <matthias.zobrist@zepi.net>
 * @copyright Copyright (c) 2015 zepi
 */

namespace Zepi\Core\AccessControl\DataSource;

use \Zepi\Core\AccessControl\Exception;
use \Zepi\Turbo\FrameworkInterface\DataSourceInterface;
use \Zepi\DataSource\Mysql\Backend\DatabaseBackend;
use \Zepi\Turbo\Manager\EventManager;

/**
 * The PermissionsDataSourceMysql communicates with the MySQL Database and 
 * loads and saves the permissions.
 * 
 * @author Matthias Zobrist <matthias.zobrist@zepi.net>
 * @copyright Copyright (c) 2015 zepi
 */
class PermissionsDataSourceMysql implements DataSourceInterface, PermissionsDataSourceInterface
{
    /**
     * @access protected
     * @var \Zepi\DataSource\Mysql\Backend\DatabaseBackend
     */
    protected $_databaseBackend;
    
    /**
     * @access protected
     * @var \Zepi\Turbo\Manager\EventManager
     */
    protected $_eventManager;
    
    /**
     * Constructs the object
     * 
     * @access public
     * @param \Zepi\DataSource\Mysql\Backend\DatabaseBackend $databaseBackend
     * @param \Zepi\Turbo\Manager\EventManager $eventManager
     */
    public function __construct(DatabaseBackend $databaseBackend, EventManager $eventManager)
    {
        $this->_databaseBackend = $databaseBackend;
        $this->_eventManager = $eventManager;
    }
    
    /**
     * Executes the setup for the data source. Returns true if everything
     * worked as expected or fals if any error occoured.
     *
     * @access public
     * @return boolean
     */
    public function setup()
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
     * 
     * @throws \Zepi\Core\AccessControl\Exception Cannot verifiy the permission for uuid "{uuid}" and access level {accessLevel}.
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
            throw new Exception('Cannot verify the permission for uuid "' . $accessEntityUuid . '" and access level "' . $accessLevel . '".', 0, $e);
        }
    }
    
    /**
     * Returns an array with all granted access levels for the given
     * access entity uuid whithout resolving the group access levels.
     *
     * @access public
     * @param string $accessEntityUuid
     * @return array|false
     * 
     * @throws \Zepi\Core\AccessControl\Exception Cannot load the permission for the given uuid "{uuid}".
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
            throw new Exception('Cannot load the permission for the given uuid "' . $accessEntityUuid . '".', 0, $e);
        }
    }
    
    /**
     * Returns an array with all granted access levels for the given
     * access entity uuid
     *
     * @access public
     * @param string $accessEntityUuid
     * @return array|false
     * 
     * @throws \Zepi\Core\AccessControl\Exception Cannot load the permission for the given uuid "{uuid}".
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
            throw new Exception('Cannot load the permission for the given uuid "' . $accessEntityUuid . '".', 0, $e);
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
     * 
     * @throws \Zepi\Core\AccessControl\Exception Cannot grant the access level "{accessLevel}" for the given uuid "{accessEntityUuid}".
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
            throw new Exception('Cannot grant the access level "' . $accessLevel . '" for the given uuid "' . $accessEntityUuid . '".', 0, $e);
        }
    }
    
    /**
     * Revokes the permission for the given access entity uuid and access level.
     * 
     * @access public
     * @param string $accessEntityUuid
     * @param string $accessLevel
     * @return boolean
     * 
     * @throws \Zepi\Core\AccessControl\Exception Cannot revoke the access level "{accessLevel}" from the given uuid "{accessEntityUuid}".
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
            throw new Exception('Cannot revoke the access level "' . $accessLevel . '" from the given uuid "' . $accessEntityUuid . '".', 0, $e);
        }
    }
    
    /**
     * Revokes the permission for the given access level.
     *
     * @access public
     * @param string $accessLevel
     * @return boolean
     * 
     * @throws \Zepi\Core\AccessControl\Exception Cannot revoke the access levels "{accessLevel}".
     */
    public function revokePermissions($accessLevel)
    {
        // Do not revoke the permissions if we haven't all data
        if ($accessLevel == '') {
            return false;
        }
    
        try {
            $sql = 'DELETE FROM permissions '
                 . 'WHERE permission_access_level_key = ' . $this->_databaseBackend->escape($accessLevel);
             
            $this->_databaseBackend->execute($sql);
    
            return true;
        } catch (\Exception $e) {
            throw new Exception('Cannot revoke the access levels "' . $accessLevel . '".', 0, $e);
        }
    }
}
