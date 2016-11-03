<?php
/*
 * The MIT License (MIT)
 *
 * Copyright (c) 2016 zepi
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
 * Representats one row in the permissions table as
 * object.
 * 
 * @package Zepi\Core\AccessControl
 * @subpackage Entity
 * @author Matthias Zobrist <matthias.zobrist@zepi.net>
 * @copyright Copyright (c) 2016 zepi
 */

namespace Zepi\Core\AccessControl\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Representats one row in the permissions table as
 * object.
 * 
 * @author Matthias Zobrist <matthias.zobrist@zepi.net>
 * @copyright Copyright (c) 2016 zepi
 * 
 * @Entity 
 * @Table(name="permissions")
 */
class Permission
{
    /**
     * @ID
     * @Column(type="integer")
     * @GeneratedValue
     * @var integer
     */
    protected $id;
    
    /**
     * @Column(type="string", name="access_entity_uuid")
     * @var string
     */
    protected $accessEntityUuid;
    
    /**
     * @var \Zepi\Core\AccessControl\Entity\AccessEntity
     */
    protected $accessEntity;
    
    /**
     * @Column(type="string", name="access_level_key")
     * @var string
     */
    protected $accessLevelKey;
    
    /**
     * @Column(type="string", name="granted_by")
     * @var string
     */
    protected $grantedBy;
    
    /**
     * Constructs the object
     * 
     * @access public
     * @param integer $id
     * @param string $accessEntityUuid
     * @param string $accessLevelKey
     * @param string $grantedBy
     */
    public function __construct($id, $accessEntityUuid, $accessLevelKey, $grantedBy)
    {
        $this->id = $id;
        $this->accessEntityUuid = $accessEntityUuid;
        $this->accessLevelKey = $accessLevelKey;
        $this->grantedBy = $grantedBy;
    }
    
    /**
     * Returns the id of the permission
     * 
     * @access public
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }
    
    /**
     * Returns the uuid of the access entity
     * 
     * @access public
     * @return string
     */
    public function getAccessEntityUuid()
    {
        return $this->accessEntityUuid;
    }
    
    /**
     * Returns the access level key
     * 
     * @access public
     * @return string
     */
    public function getAccessLevelKey()
    {
        return $this->accessLevelKey;
    }
    
    /**
     * Returns the name of the permission creator
     * 
     * @access public
     * @return string
     */
    public function getGrantedBy()
    {
        return $this->grantedBy;
    }
    
    /**
     * Returns the access entity
     * 
     * @access public
     * @return \Zepi\Core\AccessControl\Entity\AccessEntity
     */
    public function getAccessEntity()
    {
        return $this->accessEntity;
    }
    
    /**
     * Sets the access entity
     * 
     * @access public
     * @param \Zepi\Core\AccessControl\Entity\AccessEntity $accessEntity
     */
    public function setAccessEntity(AccessEntity $accessEntity)
    {
        $this->accessEntity = $accessEntity;
    }
}
