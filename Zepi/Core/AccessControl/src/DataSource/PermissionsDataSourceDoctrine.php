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
 * The PermissionsDataSourceDoctrine communicates with the MySQL Database and 
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
use \Zepi\DataSourceDriver\Doctrine\Manager\EntityManager;
use \Zepi\Turbo\Manager\RuntimeManager;
use \Zepi\Core\AccessControl\Entity\Permission;
use \Zepi\Core\Utils\Entity\DataRequest;

/**
 * The PermissionsDataSourceDoctrine communicates with the MySQL Database and 
 * loads and saves the permissions.
 * 
 * @author Matthias Zobrist <matthias.zobrist@zepi.net>
 * @copyright Copyright (c) 2015 zepi
 */
class PermissionsDataSourceDoctrine implements DataSourceInterface, PermissionsDataSourceInterface
{
    /**
     * @access protected
     * @var \Zepi\DataSourceDriver\Doctrine\Manager\EntityManager
     */
    protected $entityManager;
    
    /**
     * @access protected
     * @var \Zepi\Turbo\Manager\RuntimeManager
     */
    protected $runtimeManager;
    
    /**
     * Constructs the object
     * 
     * @access public
     * @param \Zepi\DataSourceDriver\Doctrine\Manager\EntityManager $entityManager
     * @param \Zepi\Turbo\Manager\RuntimeManager $runtimeManager
     */
    public function __construct(EntityManager $entityManager, RuntimeManager $runtimeManager)
    {
        $this->entityManager = $entityManager;
        $this->runtimeManager = $runtimeManager;
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
     * Returns an array with all found permissions for the given DataRequest
     * object.
     *
     * @access public
     * @param \Zepi\Core\Utils\DataRequest $dataRequest
     * @return array
     * 
     * @throws \Zepi\Core\AccessControl\Exception Cannot load the permissions for the given data request.
     */
    public function getPermissions(DataRequest $dataRequest)
    {
        try {
            $dataRequest->setSelectedFields(array('*'));
            
            $queryBuilder = $this->entityManager->getQueryBuilder();
            $this->entityManager->buildDataRequestQuery($dataRequest, $queryBuilder, '\\Zepi\\Core\\AccessControl\\Entity\\Permission', 'p');
            
            $permissions = $queryBuilder->getQuery()->getResult();
            if ($permissions == null) {
                return array();
            }
            
            return $permissions;
        } catch (\Exception $e) {
            throw new Exception('Cannot load the permissions for the given data request from the database.', 0, $e);
        }
    }
    
    /**
     * Returns the number of all found permissions for the given DataRequest
     * object.
     *
     * @access public
     * @param \Zepi\Core\Utils\DataRequest $dataRequest
     * @return false|integer
     * 
     * @throws \Zepi\Core\AccessControl\Exception Cannot count the permissions for the given data request.
     */
    public function countPermissions(DataRequest $dataRequest)
    {
        try {
            $request = clone $dataRequest;
        
            $request->setPage(0);
            $request->setNumberOfEntries(0);
        
            $queryBuilder = $this->entityManager->getQueryBuilder();
            $this->entityManager->buildDataRequestQuery($request, $queryBuilder, '\\Zepi\\Core\\AccessControl\\Entity\\Permission', 'p');
            
            // Count
            $queryBuilder->select($queryBuilder->expr()->count('p.id'));
            
            $data = $queryBuilder->getQuery();
            if ($data === false) {
                return 0;
            }
            
            return $data->getSingleScalarResult();
        } catch (\Exception $e) {
            throw new Exception('Cannot count the permissions for the given data request.', 0, $e);
        }
    }
    
    /**
     * Returns true if there is a permission for the given id
     *
     * @access public
     * @param integer $id
     * @return boolean
     *
     * @throws \Zepi\Core\AccessControl\Exception Cannot check if there is a permission for the given id "{id}".
     */
    public function hasPermissionForId($id)
    {
        try {
            $em = $this->entityManager->getDoctrineEntityManager();
            $permission = $em->getRepository('\\Zepi\\Core\\AccessControl\\Entity\\Permission')->find($id);
            
            if ($permission !== null) {
                return true;
            }
            
            return false;
        } catch (\Exception $e) {
            throw new Exception('Cannot check if there is a permission for the given id "' . $id . '".', 0, $e);
        }
    }
    
    /**
     * Returns the permission object for the given id
     *
     * @access public
     * @param string $id
     * @return false|\Zepi\Core\AccessControl\Entity\Permission
     *
     * @throws \Zepi\Core\AccessControl\Exception Cannot load the permission from the database for the given id "{id}".
     */
    public function getPermissionForId($id)
    {
        try {
            $em = $this->entityManager->getDoctrineEntityManager();
            $permission = $em->getRepository('\\Zepi\\Core\\AccessControl\\Entity\\Permission')->find($id);
            
            if ($permission !== null) {
                return $permission;
            }
            
            return false;
        } catch (\Exception $e) {
            throw new Exception('Cannot load the permission from the database for the given id "' . $id . '".', 0, $e);
        }
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
            $queryBuilder = $this->entityManager->getQueryBuilder();
            $queryBuilder->select($queryBuilder->expr()->count('p.id'))
                         ->from('\\Zepi\\Core\\AccessControl\\Entity\\Permission', 'p')
                         ->where('p.accessEntityUuid = :accessEntityUuid')
                         ->andWhere('p.accessLevelKey = :accessLevel')
                         ->setParameter('accessEntityUuid', $accessEntityUuid)
                         ->setParameter('accessLevel', $accessLevel);
            
            $data = $queryBuilder->getQuery();
            if ($data === false) {
                return false;
            }
            
            return ($data->getSingleScalarResult() > 0);
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
    public function getPermissionsRawForUuid($accessEntityUuid)
    {
        // Do not check the database if we haven't all data
        if ($accessEntityUuid == '') {
            return array();
        }
    
        try {
            $em = $this->entityManager->getDoctrineEntityManager();
            $permissions = $em->getRepository('\\Zepi\\Core\\AccessControl\\Entity\\Permission')->findBy(array(
                'accessEntityUuid' => $accessEntityUuid
            ));
            
            $accessLevels = array();
            foreach ($permissions as $permission) {
                $accessLevels[] = $permission->getAccessLevelKey();
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
    public function getPermissionsForUuid($accessEntityUuid)
    {
        // Do not check the database if we haven't all data
        if ($accessEntityUuid == '') {
            return array();
        }
    
        try {
            $accessLevels = $this->getPermissionsRawForUuid($accessEntityUuid);
    
            $accessLevels = $this->runtimeManager->executeFilter('\\Zepi\\Core\\AccessControl\\Filter\\PermissionsBackend\\ResolvePermissions', $accessLevels);

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
            $permission = new Permission(null, $accessEntityUuid, $accessLevel, $grantedBy);
            
            $em = $this->entityManager->getDoctrineEntityManager();
            $em->persist($permission);
            $em->flush();
            
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
            $em = $this->entityManager->getDoctrineEntityManager();
            $permissions = $em->getRepository('\\Zepi\\Core\\AccessControl\\Entity\\Permission')->findBy(array(
                'accessEntityUuid' => $accessEntityUuid,
                'accessLevelKey' => $accessLevel
            ));
            
            foreach ($permissions as $permission) {
                $em->remove($permission);
            }
            $em->flush();
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
            $em = $this->entityManager->getDoctrineEntityManager();
            $permissions = $em->getRepository('\\Zepi\\Core\\AccessControl\\Entity\\Permission')->findBy(array(
                'accessLevelKey' => $accessLevel
            ));
            
            foreach ($permissions as $permission) {
                $em->remove($permission);
            }
            $em->flush();
        } catch (\Exception $e) {
            throw new Exception('Cannot revoke the access levels "' . $accessLevel . '".', 0, $e);
        }
    }
}
