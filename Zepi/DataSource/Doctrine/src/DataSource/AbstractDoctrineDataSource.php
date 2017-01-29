<?php
/**
 * The AbstractDoctrineDataSource enables the default communication
 * between a DataAccess Manager and doctrine.
 * 
 * @package Zepi\DataSource\Doctrine\DataSource
 * @subpackage DataSource
 * @author Matthias Zobrist <matthias.zobrist@zepi.net>
 * @copyright Copyright (c) 2017 zepi
 */

namespace Zepi\DataSource\Doctrine\DataSource;

use \Zepi\DataSource\Doctrine\Exception;
use \Zepi\DataSource\Core\Entity\DataRequest;
use \Zepi\DataSource\Core\Entity\EntityInterface;

/**
 * The AbstractDoctrineDataSource enables the default communication
 * between a DataAccess Manager and doctrine.
 * 
 * @author Matthias Zobrist <matthias.zobrist@zepi.net>
 * @copyright Copyright (c) 2017 zepi
 */
abstract class AbstractDoctrineDataSource implements DataAccessInterface
{
    /**
     * @access protected
     * @var \Zepi\DataSourceDriver\Doctrine\Manager\EntityManager
     */
    protected $entityManager;
    
    /**
     * Constructs the object
     *
     * @access public
     * @param \Zepi\DataSourceDriver\Doctrine\Manager\EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
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
     * Returns an array with all found entities for the given DataRequest
     * object. 
     *
     * @param \Zepi\Core\Utils\DataRequest $dataRequest
     * @return array
     * 
     * @throws \Zepi\DataSource\Doctrine\Exception Cannot load the entities for the given data request.
     */
    public function find(DataRequest $dataRequest)
    {
        try {
            $dataRequest->setSelectedFields(array('*'));
            
            $queryBuilder = $this->entityManager->getQueryBuilder();
            $this->entityManager->buildDataRequestQuery($dataRequest, $queryBuilder, $this->getEntityClass(), 'ir');
            
            $ipRanges = $queryBuilder->getQuery()->getResult();
            if ($ipRanges == null) {
                return array();
            }
            
            return $ipRanges;
        } catch (\Exception $e) {
            throw new Exception('Cannot load the IP ranges for the given data request from the database.', 0, $e);
        }
    }

    /**
     * Returns the number of all found entities for the given DataRequest
     * object.
     *
     * @access public 
     * @param \Zepi\Core\Utils\DataRequest $dataRequest
     * @return false|integer
     * 
     * @throws \Zepi\DataSource\Doctrine\Exception Cannot count the entities for the given data request.
     */
    public function count(DataRequest $dataRequest)
    {
        try {
            $request = clone $dataRequest;
            
            $request->setPage(0);
            $request->setNumberOfEntries(0);
            
            $queryBuilder = $this->entityManager->getQueryBuilder();
            $this->entityManager->buildDataRequestQuery($request, $queryBuilder, $this->getEntityClass(), 'ir');
            
            // Count
            $queryBuilder->select($queryBuilder->expr()->count('ir.id'));

            $data = $queryBuilder->getQuery();
            if ($data === false) {
                return 0;
            }

            return $data->getSingleScalarResult();
        } catch (\Exception $e) {
            throw new Exception('Cannot count the IP ranges for the given data request.', 0, $e);
        }
    }
    
    /**
     * Returns true if there is an entity for the given id
     * 
     * @access public
     * @param integer $id
     * @return boolean
     * 
     * @throws \Zepi\DataSource\Doctrine\Exception Cannot check if there is an entity for the given id "{id}".
     */
    public function has($id)
    {
        try {
             $em = $this->entityManager->getDoctrineEntityManager();
             $data = $em->getRepository($this->getEntityClass())->findOneBy(array(
                 'id' => $id,
             ));
             
             if ($data !== null) {
                 return true;
             }
             
             return false;
        } catch (\Exception $e) {
            throw new Exception('Cannot check if there is an entity for the given id "' . $id . '".', 0, $e);
        }
    }
    
    /**
     * Returns the entity object for the given id
     *
     * @param integer $id
     * @return false|mixed
     * 
     * @throws \Zepi\DataSource\Doctrine\Exception Cannot load the entity from the database for the given id "{id}".
     */
    public function get($id)
    {
        try {
            $em = $this->entityManager->getDoctrineEntityManager();
            $accessEntity = $em->getRepository($this->getEntityClass())->findOneBy(array(
                'id' => $id,
            ));
            
            if ($accessEntity !== null) {
                return $accessEntity;
            }
            
            return false;
        } catch (\Exception $e) {
            throw new Exception('Cannot load the entity from the database for the given id "' . $id . '".', 0, $e);
        }
    }
    
    /**
     * Adds an entity. Returns the id of the entity
     * or false, if the entity can not inserted.
     * 
     * @param \Zepi\DataSource\Core\Entity\EntityInterface $entity
     * @return string|false
     * 
     * @throws \Zepi\DataSource\Doctrine\Exception The given entity is not compatible with this data source.
     * @throws \Zepi\DataSource\Doctrine\Exception Cannot add the entity "{entity}".
     */
    public function add(EntityInterface $entity)
    {
        if (!is_a($entity, $this->getEntityClass())) {
            throw new Exception('The given entity (' . get_class($entity) . ') is not compatible with this data source (' . self::class . '.');
        }
        
        try {
            $em = $this->entityManager->getDoctrineEntityManager();
            $em->persist($entity);
            $em->flush();
            
            return $ipRange->getId();
        } catch (\Exception $e) {
            throw new Exception('Cannot add the entity "' . $entity . '".', 0, $e);
        }
    }
    
    /**
     * Updates the entity. Returns true if everything worked as excepted or 
     * false if the update didn't worked.
     * 
     * @param \Zepi\DataSource\Core\Entity\EntityInterface $entity
     * @return boolean
     * 
     * @throws \Zepi\DataSource\Doctrine\Exception The given entity is not compatible with this data source.
     * @throws \Zepi\DataSource\Doctrine\Exception Cannot update the entity "{entity}".
     */
    public function update(EntityInterface $entity)
    {
        if (!is_a($entity, $this->getEntityClass())) {
            throw new Exception('The given entity (' . get_class($entity) . ') is not compatible with this data source (' . self::class . '.');
        }
        
        try {
            $em = $this->entityManager->getDoctrineEntityManager();
            $em->flush();
            
            return true;
        } catch (\Exception $e) {
            throw new Exception('Cannot update the entity"' . $entity . '".', 0, $e);
        }
    }
    
    /**
     * Deletes the entity in the database.
     * 
     * @param \Zepi\DataSource\Core\Entity\EntityInterface $entity
     * @return boolean
     * 
     * @throws \Zepi\DataSource\Doctrine\Exception The given entity is not compatible with this data source.
     * @throws \Zepi\DataSource\Doctrine\Exception Cannot delete the entity "{entity}".
     */
    public function delete(EntityInterface $entity)
    {
        if (!is_a($entity, $this->getEntityClass())) {
            throw new Exception('The given entity (' . get_class($entity) . ') is not compatible with this data source (' . self::class . '.');
        }
        
        try {
            $em = $this->entityManager->getDoctrineEntityManager();
            $em->remove($entity);
            $em->flush();
            
            return true;
        } catch (\Exception $e) {
            throw new Exception('Cannot delete the entity "' . $entity . '".', 0, $e);
        }
    }
}
