<?php
/*
 * The MIT License (MIT)
*
* Copyright (c) 2017 zepi
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
 * Interface to define the DataAccess methods
*
* @package Zepi\DataSource\Core
* @subpackage DataAccess
* @author Matthias Zobrist <matthias.zobrist@zepi.net>
* @copyright Copyright (c) 2017 zepi
*/

namespace Zepi\DataSource\Core\DataAccess;

use \Zepi\DataSource\Core\Entity\EntityInterface;
use \Zepi\DataSource\Core\Entity\DataRequest;

/**
 * Interface to define the DataAccess methods
 *
 * @author Matthias Zobrist <matthias.zobrist@zepi.net>
 * @copyright Copyright (c) 2017 zepi
 */
interface DataAccessInterface
{
    /**
     * Returns an array with all entities for the given data request.
     * 
     * @param \Zepi\DataSource\Core\DataRequest $dataRequest
     * @return false|array
     */
    public function find(DataRequest $dataRequest);
    
    /**
     * Returns the number of entities which are available for the given
     * data request.
     *
     * @param \Zepi\DataSource\Core\DataRequest $dataRequest
     * @return integer
     */
    public function count(DataRequest $dataRequest);
    
    /**
     * Returns true if the given entity id exists
     * 
     * @param integer $entityId
     * @return boolean
     */
    public function has($entityId);
    
    /**
     * Returns the entity for the given id. Returns false if
     * there is no entity for the given id.
     * 
     * @param integer $entityId
     * @return false|mixed
     */
    public function get($entityId);
    
    /**
     * Adds an entity. Returns the id of the entity
     * or false, if the entity can not inserted.
     *
     * @param \Zepi\DataSource\Core\Entity\EntityInterface $entity
     * @return string|false
     */
    public function add(EntityInterface $entity);

    /**
     * Updates the entity. Returns true if everything worked as excepted or
     * false if the update didn't worked.
     *
     * @param \Zepi\DataSource\Core\Entity\EntityInterface $entity
     * @return boolean
     */
    public function update(EntityInterface $entity);
    
    /**
     * Deletes the entity in the database.
     *
     * @param \Zepi\DataSource\Core\Entity\EntityInterface $entity
     * @return boolean
     */
    public function delete(EntityInterface $entity);
}