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
 * The AccessEntitiesDataSourceMysql communicates with the database and 
 * loads and saves the access entities.
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
use \Zepi\Core\AccessControl\Entity\AccessEntity;
use \Zepi\Core\Utils\Entity\DataRequest;

/**
 * The AccessEntitiesDataSourceMysql communicates with the database and 
 * loads and saves the access entities.
 * 
 * @author Matthias Zobrist <matthias.zobrist@zepi.net>
 * @copyright Copyright (c) 2015 zepi
 */
class AccessEntitiesDataSourceMysql implements DataSourceInterface, AccessEntitiesDataSourceInterface
{
    /**
     * @access protected
     * @var DatabaseBackend
     */
    protected $_databaseBackend;
    
    /**
     * @access protected
     * @var \Zepi\Core\AccessControl\DataSource\PermissionsDataSourceInterface
     */
    protected $_permissionsDataSource;
    
    /**
     * Constructs the object
     * 
     * @access public
     * @param \Zepi\DataSource\Mysql\Backend\DatabaseBackend $databaseBackend
     * @param \Zepi\Core\AccessControl\DataSource\PermissionsDataSourceInterface $permissionsDataSource
     */
    public function __construct(DatabaseBackend $databaseBackend, PermissionsDataSourceInterface $permissionsDataSource)
    {
        $this->_databaseBackend = $databaseBackend;
        $this->_permissionsDataSource = $permissionsDataSource;
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
        $sql = 'CREATE TABLE IF NOT EXISTS `access_entities` (' 
             . '  `access_entity_id` int(11) NOT NULL AUTO_INCREMENT,'
             . '  `access_entity_uuid` varchar(40) NOT NULL,'
             . '  `access_entity_type` varchar(255) NOT NULL,'
             . '  `access_entity_name` varchar(255) NOT NULL,'
             . '  `access_entity_key` text NOT NULL,'
             . '  `access_entity_meta_data` mediumtext NOT NULL,'
             . '  PRIMARY KEY (`access_entity_id`),'
             . '  UNIQUE KEY (`access_entity_uuid`),'
             . '  UNIQUE KEY `access_entity_type_name` (`access_entity_type`, `access_entity_name`)'
             . ') ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;';
        
        $this->_databaseBackend->execute($sql);
        
        return true;
    }
    
    /**
     * Returns an array with all found access entities for the given DataRequest
     * object. 
     *
     * @access public 
     * @param \Zepi\Core\Utils\DataRequest $dataRequest
     * @return array
     * 
     * @throws \Zepi\Core\AccessControl\Exception Cannot load the access entities for the given data request.
     */
    public function getAccessEntities(DataRequest $dataRequest)
    {
        try {
            $dataRequest->setSelectedFields(array('*'));
            $sql = $this->_databaseBackend->buildDataRequestQuery($dataRequest, 'access_entities', 'access_entity');

            $data = $this->_databaseBackend->query($sql)->fetchAll();

            if ($data === false) {
                return array();
            }
            
            $accessEntities = array();
            foreach ($data as $row) {
                $accessEntities[] = $this->_generateAccessEntityObject($row);
            }
            
            return $accessEntities;
        } catch (\Exception $e) {
            throw new Exception('Cannot load the access entities for the given data request from the database.', 0, $e);
        }
    }

    /**
     * Returns the number of all found access entities for the given DataRequest
     * object.
     *
     * @access public 
     * @param \Zepi\Core\Utils\DataRequest $dataRequest
     * @return false|integer
     * 
     * @throws \Zepi\Core\AccessControl\Exception Cannot count the access entities for the given data request.
     */
    public function countAccessEntities(DataRequest $dataRequest)
    {
        try {
            $request = clone $dataRequest;
            
            $request->setSelectedFields(array('COUNT(*) AS countEntries'));
            $request->setPage(0);
            $request->setNumberOfEntries(0);
            
            $sql = $this->_databaseBackend->buildDataRequestQuery($request, 'access_entities', 'access_entity');
            $data = $this->_databaseBackend->query($sql)->fetch();

            if ($data === false) {
                return 0;
            }
            
            return intval($data['countEntries']);
        } catch (\Exception $e) {
            throw new Exception('Cannot count the access entities for the given data request.', 0, $e);
        }
    }
    
    /**
     * Returns the uuid of the given access entity id
     * 
     * @access public
     * @param integer $id
     * @return string|false
     * 
     * @throws \Zepi\Core\AccessControl\Exception Cannot load the uuid for access entitiy id "{id}".
     */
    public function getUuid($id)
    {
        try {
            $sql = 'SELECT access_entity_uuid FROM access_entities '
                 . 'WHERE access_entity_id = ' . $this->_databaseBackend->escape($id);
            
            $data = $this->_databaseBackend->query($sql)->fetch();
            
            if (isset($data['access_entity_uuid'])) {
                return $data['access_entity_uuid'];
            }
            
            return false;
        } catch (\Exception $e) {
            throw new Exception('Cannot load the uuid for access entitiy id "' . $id . '".', 0, $e);
        }
    }
    
    /**
     * Returns true if there is a access entity for the given uuid
     * 
     * @access public
     * @param string $uuid
     * @return boolean
     * 
     * @throws \Zepi\Core\AccessControl\Exception Cannot check if there is an access entitiy for the given uuid "{uuid}".
     */
    public function hasAccessEntityForUuid($uuid)
    {
        try {
            $sql = 'SELECT access_entity_id FROM access_entities '
                 . 'WHERE access_entity_uuid = ' . $this->_databaseBackend->escape($uuid);
            
            $data = $this->_databaseBackend->query($sql)->fetch();
            
            if (isset($data['access_entity_id'])) {
                return true;
            }
            
            return false;
        } catch (\Exception $e) {
            throw new Exception('Cannot check if there is an access entitiy for the given uuid "' . $uuid . '".', 0, $e);
        }
    }
    
    /**
     * Returns true if there is a access entity for the given type and name
     * 
     * @access public
     * @param string $type
     * @param string $name
     * @return boolean
     * 
     * @throws \Zepi\Core\AccessControl\Exception Cannot check if there is an access entitiy for the given type "{type}" and name "{name}".
     */
    public function hasAccessEntityForName($type, $name)
    {
        try {
            $sql = 'SELECT access_entity_id FROM access_entities '
                 . 'WHERE access_entity_name = ' . $this->_databaseBackend->escape($name) . ' '
                 . 'AND access_entity_type = ' . $this->_databaseBackend->escape($type);

            $data = $this->_databaseBackend->query($sql)->fetch();
            
            if (isset($data['access_entity_id'])) {
                return true;
            }
            
            return false;
        } catch (\Exception $e) {
            throw new Exception('Cannot check if there is an access entitiy for the given type "' . $type . '" and name "' . $name . '".', 0, $e);
        }
    }
    
    /**
     * Returns the access entity object for the given uuid
     *
     * @access public 
     * @param string $uuid
     * @return false|\Zepi\Core\AccessControl\Entity\AccessEntity
     * 
     * @throws \Zepi\Core\AccessControl\Exception Cannot load the access entitiy from the database for the given uuid "{uuid}".
     */
    public function getAccessEntityForUuid($uuid)
    {
        try {
            $sql = 'SELECT * FROM access_entities '
                 . 'WHERE access_entity_uuid = ' . $this->_databaseBackend->escape($uuid);
            
            $data = $this->_databaseBackend->query($sql)->fetch();

            if (!isset($data['access_entity_uuid'])) {
                return false;
            }
            
            return $this->_generateAccessEntityObject($data);
        } catch (\Exception $e) {
            throw new Exception('Cannot load the access entitiy from the database for the given uuid "' . $uuid . '".', 0, $e);
        }
    }
    
    /**
     * Returns the access entity object for the given type and name
     * 
     * @access public
     * @param string $type
     * @param string $name
     * @return false|\Zepi\Core\AccessControl\Entity\AccessEntity
     * 
     * @throws \Zepi\Core\AccessControl\Exception Cannot load access entitiy from the database for the given type "{type}" and name "{name}".
     */
    public function getAccessEntityForName($type, $name)
    {
        try {
            $sql = 'SELECT * FROM access_entities '
                 . 'WHERE access_entity_name = ' . $this->_databaseBackend->escape($name)
                 . 'AND access_entity_type = ' . $this->_databaseBackend->escape($type);
            
            $data = $this->_databaseBackend->query($sql)->fetch();

            if (!isset($data['access_entity_uuid'])) {
                return false;
            }
            
            return $this->_generateAccessEntityObject($data);
        } catch (\Exception $e) {
            throw new Exception('Cannot load the access entitiy for the given type "' . $type . '" and name "' . $name . '".', 0, $e);
        }
    }

    /**
     * Generates the access entity object
     * 
     * @access protected
     * @param array $data
     * @return \Zepi\Core\AccessControl\Entity\AccessEntity
     */
    protected function _generateAccessEntityObject(array $data)
    {
        // Unserialize the meta data
        $metaData = unserialize($data['access_entity_meta_data']);
        if ($metaData === false) {
            $metaData = array();
        }
        
        // Initialize the access entity object
        $accessEntity = new AccessEntity(
            intval($data['access_entity_id']),
            $data['access_entity_uuid'],
            $data['access_entity_type'],
            $data['access_entity_name'],
            $data['access_entity_key'],
            $metaData
        );
        
        // Load the permissions for the entity
        $permissions = $this->_permissionsDataSource->getPermissions($accessEntity->getUuid());
        
        if ($permissions !== false) {
            $accessEntity->setPermissions($permissions);
        }

        return $accessEntity;
    }
    
    /**
     * Adds an access entity. Returns the uuid of the access entity
     * or false, if the access entity can not inserted.
     * 
     * @access public
     * @param string $type
     * @param string $name
     * @param string $key
     * @param array $metaData
     * @return string|false
     * 
     * @throws \Zepi\Core\AccessControl\Exception Cannot add the new access entity "{name}".
     */
    public function addAccessEntity($type, $name, $key, $metaData = array())
    {
        try {
            $sql = 'INSERT INTO access_entities VALUES ('
                 . 'NULL, '
                 . 'UUID(), '
                 . $this->_databaseBackend->escape($type) . ', '
                 . $this->_databaseBackend->escape($name) . ', '
                 . $this->_databaseBackend->escape($key) . ', '
                 . $this->_databaseBackend->escape(serialize($metaData)) . ')';
                 
            $this->_databaseBackend->execute($sql);
            
            $lastId = $this->_databaseBackend->getLastId();
            $uuid = $this->getUuid($lastId);
            
            return $uuid;
        } catch (\Exception $e) {
            throw new Exception('Cannot add the new access entitiy "' . $name .'".', 0, $e);
        }
    }
    
    /**
     * Updates the access entity. Returns true if everything worked as excepted or 
     * false if the update didn't worked.
     * 
     * @access public
     * @param string $uuid
     * @param string $name
     * @param string $key
     * @param array $metaData
     * @return boolean
     * 
     * @throws \Zepi\Core\AccessControl\Exception Cannot update the access entity "{name}".
     */
    public function updateAccessEntity($uuid, $name, $key, $metaData)
    {
        try {
            $sql = 'UPDATE access_entities SET '
                 . 'access_entity_name = ' . $this->_databaseBackend->escape($name) . ', '
                 . 'access_entity_key = ' . $this->_databaseBackend->escape($key) . ', '
                 . 'access_entity_meta_data = ' . $this->_databaseBackend->escape(serialize($metaData)) . ' '
                 . 'WHERE access_entity_uuid = ' . $this->_databaseBackend->escape($uuid);
                 
            $this->_databaseBackend->execute($sql);
            
            return true;
        } catch (\Exception $e) {
            throw new Exception('Cannot update the access entitiy "' . $uuid . '".', 0, $e);
        }
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
        try {
            $sql = 'DELETE FROM access_entities '
                 . 'WHERE access_entity_uuid = ' . $this->_databaseBackend->escape($uuid);
                 
            $this->_databaseBackend->execute($sql);
            
            return true;
        } catch (\Exception $e) {
            throw new Exception('Cannot delete the access entitiy "' . $uuid . '".', 0, $e);
        }
    }
}
