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
 * The AccessEntitiesDataSourceDoctrine communicates with the database and 
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
use \Zepi\DataSourceDriver\Doctrine\Manager\EntityManager;
use \Zepi\Core\AccessControl\Entity\AccessEntity;
use \Zepi\Core\Utils\Entity\DataRequest;

/**
 * The AccessEntitiesDataSourceDoctrine communicates with the database and 
 * loads and saves the access entities.
 * 
 * @author Matthias Zobrist <matthias.zobrist@zepi.net>
 * @copyright Copyright (c) 2015 zepi
 */
class AccessEntitiesDataSourceDoctrine implements DataSourceInterface, AccessEntitiesDataSourceInterface
{
    /**
     * @access protected
     * @var \Zepi\DataSourceDriver\Doctrine\Manager\EntityManager
     */
    protected $_entityManager;
    
    /**
     * @access protected
     * @var \Zepi\Core\AccessControl\DataSource\PermissionsDataSourceInterface
     */
    protected $_permissionsDataSource;
    
    /**
     * Constructs the object
     * 
     * @access public
     * @param \Zepi\DataSourceDriver\Doctrine\Manager\EntityManager $entityManager
     * @param \Zepi\Core\AccessControl\DataSource\PermissionsDataSourceInterface $permissionsDataSource
     */
    public function __construct(EntityManager $entityManager, PermissionsDataSourceInterface $permissionsDataSource)
    {
        $this->_entityManager = $entityManager;
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
        return true;
    }
    
    /**
     * Returns an array with all found access entities for the given DataRequest
     * object. 
     *
     * @access public 
     * @param string $class
     * @param \Zepi\Core\Utils\DataRequest $dataRequest
     * @return array
     * 
     * @throws \Zepi\Core\AccessControl\Exception Cannot load the access entities for the given data request.
     */
    public function getAccessEntities($class, DataRequest $dataRequest)
    {
        try {
            $dataRequest->setSelectedFields(array('*'));
            
            $queryBuilder = $this->_entityManager->getQueryBuilder();
            $this->_entityManager->buildDataRequestQuery($dataRequest, $queryBuilder, $class, 'a');
            
            $accessEntities = $queryBuilder->getQuery()->getResult();
            if ($accessEntities == null) {
                return array();
            }
            
            foreach ($accessEntities as $accessEntity) {
                $this->loadPermissions($accessEntity);
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
     * @param string $class
     * @param \Zepi\Core\Utils\DataRequest $dataRequest
     * @return false|integer
     * 
     * @throws \Zepi\Core\AccessControl\Exception Cannot count the access entities for the given data request.
     */
    public function countAccessEntities($class, DataRequest $dataRequest)
    {
        try {
            $request = clone $dataRequest;
            
            $request->setPage(0);
            $request->setNumberOfEntries(0);
            
            $queryBuilder = $this->_entityManager->getQueryBuilder();
            $this->_entityManager->buildDataRequestQuery($request, $queryBuilder, $class, 'a');
            
            // Count
            $queryBuilder->select($queryBuilder->expr()->count('a.id'));

            $data = $queryBuilder->getQuery();
            if ($data === false) {
                return 0;
            }

            return $data->getSingleScalarResult();
        } catch (\Exception $e) {
            throw new Exception('Cannot count the access entities for the given data request.', 0, $e);
        }
    }
    
    /**
     * Returns true if there is a access entity for the given uuid
     * 
     * @access public
     * @param string $class
     * @param string $uuid
     * @return boolean
     * 
     * @throws \Zepi\Core\AccessControl\Exception Cannot check if there is an access entitiy for the given uuid "{uuid}".
     */
    public function hasAccessEntityForUuid($class, $uuid)
    {
        try {
             $em = $this->_entityManager->getDoctrineEntityManager();
             $data = $em->getRepository($class)->findOneBy(array(
                 'uuid' => $uuid,
             ));
             
             if ($data !== null) {
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
     * @param string $class
     * @param string $name
     * @return boolean
     * 
     * @throws \Zepi\Core\AccessControl\Exception Cannot check if there is an access entitiy for the given type "{type}" and name "{name}".
     */
    public function hasAccessEntityForName($class, $name)
    {
        try {
            $em = $this->_entityManager->getDoctrineEntityManager();
            $data = $em->getRepository($class)->findOneBy(array(
                'name' => $name,
            ));
            
            if ($data !== null) {
                return true;
            }
            
            return false;
        } catch (\Exception $e) {
            throw new Exception('Cannot check if there is an access entitiy for the given type "' . $class . '" and name "' . $name . '".', 0, $e);
        }
    }
    
    /**
     * Returns the access entity object for the given uuid
     *
     * @access public 
     * @param string $class
     * @param string $uuid
     * @return false|\Zepi\Core\AccessControl\Entity\AccessEntity
     * 
     * @throws \Zepi\Core\AccessControl\Exception Cannot load the access entitiy from the database for the given uuid "{uuid}".
     */
    public function getAccessEntityForUuid($class, $uuid)
    {
        try {
            $em = $this->_entityManager->getDoctrineEntityManager();
            $accessEntity = $em->getRepository($class)->findOneBy(array(
                'uuid' => $uuid,
            ));
            
            if ($accessEntity !== null) {
                $this->loadPermissions($accessEntity);
                
                return $accessEntity;
            }
            
            return false;
        } catch (\Exception $e) {
            throw new Exception('Cannot load the access entitiy from the database for the given uuid "' . $uuid . '".', 0, $e);
        }
    }
    
    /**
     * Returns the access entity object for the given type and name
     * 
     * @access public
     * @param string $class
     * @param string $name
     * @return false|\Zepi\Core\AccessControl\Entity\AccessEntity
     * 
     * @throws \Zepi\Core\AccessControl\Exception Cannot load access entitiy from the database for the given type "{type}" and name "{name}".
     */
    public function getAccessEntityForName($class, $name)
    {
        try {
            $em = $this->_entityManager->getDoctrineEntityManager();
            $accessEntity = $em->getRepository($class)->findOneBy(array(
                'name' => $name,
            ));
            
            if ($accessEntity !== null) {
                $this->loadPermissions($accessEntity);
            
                return $accessEntity;
            }
            
            return false;
        } catch (\Exception $e) {
            throw new Exception('Cannot load the access entitiy for the given type "' . $type . '" and name "' . $name . '".', 0, $e);
        }
    }
    
    /**
     * Adds an access entity. Returns the uuid of the access entity
     * or false, if the access entity can not inserted.
     * 
     * @access public
     * @param \Zepi\Core\AccessControl\Entity\AccessEntity $accessEntity
     * @return string|false
     * 
     * @throws \Zepi\Core\AccessControl\Exception Cannot add the new access entity "{name}".
     */
    public function addAccessEntity(AccessEntity $accessEntity)
    {
        try {
            $em = $this->_entityManager->getDoctrineEntityManager();
            $em->persist($accessEntity);
            $em->flush();
            
            return $accessEntity->getUuid();
        } catch (\Exception $e) {
            throw new Exception('Cannot add the new access entitiy "' . $accessEntity->getName() . '".', 0, $e);
        }
    }
    
    /**
     * Updates the access entity. Returns true if everything worked as excepted or 
     * false if the update didn't worked.
     * 
     * @access public
     * @param \Zepi\Core\AccessControl\Entity\AccessEntity $accessEntity
     * @return boolean
     * 
     * @throws \Zepi\Core\AccessControl\Exception Cannot update the access entity "{name}".
     */
    public function updateAccessEntity(AccessEntity $accessEntity)
    {
        try {
            $em = $this->_entityManager->getDoctrineEntityManager();
            $em->flush();
            
            return true;
        } catch (\Exception $e) {
            throw new Exception('Cannot update the access entitiy "' . $accessEntity->getUuid() . '".', 0, $e);
        }
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
        try {
            $em = $this->_entityManager->getDoctrineEntityManager();
            $em->remove($accessEntity);
            $em->flush();
            
            return true;
        } catch (\Exception $e) {
            throw new Exception('Cannot delete the access entitiy "' . $uuid . '".', 0, $e);
        }
    }
    
    /**
     * Loads the permissions for the given access entity object
     * 
     * @param \Zepi\Core\AccessControl\Entity\AccessEntity $accessEntity
     */
    protected function loadPermissions(AccessEntity $accessEntity) 
    {
        $permissions = $this->_permissionsDataSource->getPermissionsForUuid($accessEntity->getUuid());
        $accessEntity->setPermissions($permissions);
    }
}
