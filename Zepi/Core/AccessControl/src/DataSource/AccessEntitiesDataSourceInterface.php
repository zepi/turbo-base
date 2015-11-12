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
 * The AccessEntities DataSource Interface defines the 
 * api for AccessEntities DataSources.
 * 
 * @package Zepi\Core\AccessControl
 * @subpackage DataSource
 * @author Matthias Zobrist <matthias.zobrist@zepi.net>
 * @copyright Copyright (c) 2015 zepi
 */

namespace Zepi\Core\AccessControl\DataSource;

use \Zepi\Core\Utils\Entity\DataRequest;

/**
 * The AccessEntities DataSource Interface defines the 
 * api for AccessEntities DataSources.
 *  
 * @author Matthias Zobrist <matthias.zobrist@zepi.net>
 * @copyright Copyright (c) 2015 zepi
 */
interface AccessEntitiesDataSourceInterface
{
    /**
     * Returns an array with all found access entities for the given DataRequest
     * object. 
     *
     * @access public 
     * @param \Zepi\Core\Utils\DataRequest $dataRequest
     * @return array
     */
    public function getAccessEntities(DataRequest $dataRequest);

    /**
     * Returns the number of all found access entities for the given DataRequest
     * object.
     *
     * @access public 
     * @param \Zepi\Core\Utils\DataRequest $dataRequest
     * @return integer
     */
    public function countAccessEntities(DataRequest $dataRequest);
    
    /**
     * Returns the uuid of the given access entity id
     * 
     * @access public
     * @param integer $id
     * @return string|false
     */
    public function getUuid($id);
    
    /**
     * Returns true if there is a access entity for the given uuid
     * 
     * @access public
     * @param string $uuid
     * @return boolean
     */
    public function hasAccessEntityForUuid($uuid);
    
    /**
     * Returns true if there is a access entity for the given type and name
     * 
     * @access public
     * @param string $type
     * @param string $name
     * @return boolean
     */
    public function hasAccessEntityForName($type, $name);
    
    /**
     * Returns the access entity object for the given uuid
     *
     * @access public 
     * @param string $uuid
     * @return false|\Zepi\Core\AccessControl\Entity\AccessEntity
     */
    public function getAccessEntityForUuid($uuid);
    
    /**
     * Returns the access entity object for the given type and name
     * 
     * @access public
     * @param string $type
     * @param string $name
     * @return false|\Zepi\Core\AccessControl\Entity\AccessEntity
     */
    public function getAccessEntityForName($type, $name);

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
     */
    public function addAccessEntity($type, $name, $key, $metaData = array());
    
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
     */
    public function updateAccessEntity($uuid, $name, $key, $metaData);
    
    /**
     * Deletes the given access entity in the database.
     * 
     * @access public
     * @param string $uuid
     * @return boolean
     */
    public function deleteAccessEntity($uuid);
}
