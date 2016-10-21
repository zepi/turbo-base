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
 * Entity Manager to work with the doctrine entity manager
 * 
 * @package Zepi\DataSourceDriver\Doctrine
 * @subpackage Manager
 * @author Matthias Zobrist <matthias.zobrist@zepi.net>
 * @copyright Copyright (c) 2015 zepi
 */

namespace Zepi\DataSourceDriver\Doctrine\Manager;

use \Zepi\DataSourceDriver\Doctrine\Exception;
use \Zepi\Core\Utils\Entity\DataRequest;
use \Doctrine\ORM\EntityManager as DoctrineEntityManager;
use \Doctrine\ORM\QueryBuilder;

/**
 * Entity Manager to work with the doctrine entity manager
 * 
 * @author Matthias Zobrist <matthias.zobrist@zepi.net>
 * @copyright Copyright (c) 2015 zepi
 */
class EntityManager
{
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    protected $_doctrineEntityManager;
    
    /**
     * Constructs the object.
     *
     * @param \Doctrine\ORM\EntityManager $entityManager
     */
    public function __construct(DoctrineEntityManager $entityManager)
    {
        $this->_doctrineEntityManager = $entityManager;
    }
    
    /**
     * Builds the query for the given data request object
     * 
     * @access public
     * @param \Zepi\Core\Utils\Entity\DataRequest $dataRequest
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder
     * @param string $entity
     * @param string $tableCode
     */
    public function buildDataRequestQuery(DataRequest $dataRequest, QueryBuilder $queryBuilder, $entity, $tableCode)
    {
        $queryBuilder->select($tableCode)
                     ->from($entity, $tableCode);
        
        $hasWhere = false;
        $i = 1;
        foreach ($dataRequest->getFilters() as $filter) {
            $whereQuery = $tableCode . '.' . $filter->getFieldName() . ' ' . $filter->getMode() . ' :' . $i;
            if ($hasWhere) {
                $queryBuilder->andWhere($whereQuery);
            } else {
                $queryBuilder->where($whereQuery);
                $hasWhere = true;
            }
            
            $queryBuilder->setParameter($i, $filter->getNeededValue());
            $i++;
        }
        
        // Sorting
        if ($dataRequest->getSortBy() != '') {
            $mode = 'ASC';
            if (in_array($dataRequest->getSortByDirection(), array('ASC', 'DESC'))) {
                $mode = $dataRequest->getSortByDirection();
            }
            
            $queryBuilder->orderBy($tableCode . '.' . $dataRequest->getSortBy(), $mode);
        }
        
        // Offset
        if ($dataRequest->getOffset() > 0 || $dataRequest->getNumberOfEntries() > 0) {
            $queryBuilder->setFirstResult($dataRequest->getOffset());
            $queryBuilder->setMaxResults($dataRequest->getNumberOfEntries());
        }
    }
    
    /**
     * Returns the doctrine entity manager
     * 
     * @return \Doctrine\ORM\EntityManager
     */
    public function getDoctrineEntityManager()
    {
        return $this->_doctrineEntityManager;
    }
    
    /**
     * Returns a new query builder instance
     * 
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function getQueryBuilder()
    {
        return $this->_doctrineEntityManager->createQueryBuilder();
    }
}