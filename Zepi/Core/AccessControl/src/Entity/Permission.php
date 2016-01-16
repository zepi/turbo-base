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

/**
 * Representats one row in the permissions table as
 * object.
 * 
 * @author Matthias Zobrist <matthias.zobrist@zepi.net>
 * @copyright Copyright (c) 2016 zepi
 */
class Permission
{
    /**
     * @access protected
     * @var integer
     */
    protected $_id;
    
    /**
     * @access protected
     * @var string
     */
    protected $_accessEntityUuid;
    
    /**
     * @access protected
     * @var string
     */
    protected $_accessLevelKey;
    
    /**
     * @access protected
     * @var string
     */
    protected $_grantedBy;
    
    /**
     * @access protected
     * @var \Zepi\Core\AccessControl\Entity\AccessEntity
     */
    protected $_accessEntity;
    
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
        $this->_id = $id;
        $this->_accessEntityUuid = $accessEntityUuid;
        $this->_accessLevelKey = $accessLevelKey;
        $this->_grantedBy = $grantedBy;
    }
    
    /**
     * Returns the id of the permission
     * 
     * @access public
     * @return integer
     */
    public function getId()
    {
        return $this->_id;
    }
    
    /**
     * Returns the uuid of the access entity
     * 
     * @access public
     * @return string
     */
    public function getAccessEntityUuid()
    {
        return $this->_accessEntityUuid;
    }
    
    /**
     * Returns the access level key
     * 
     * @access public
     * @return string
     */
    public function getAccessLevelKey()
    {
        return $this->_accessLevelKey;
    }
    
    /**
     * Returns the name of the permission creator
     * 
     * @access public
     * @return string
     */
    public function getGrantedBy()
    {
        return $this->_grantedBy;
    }
    
    /**
     * Returns the access entity
     * 
     * @access public
     * @return \Zepi\Core\AccessControl\Entity\AccessEntity
     */
    public function getAccessEntity()
    {
        return $this->_accessEntity;
    }
    
    /**
     * Sets the access entity
     * 
     * @access public
     * @param \Zepi\Core\AccessControl\Entity\AccessEntity $accessEntity
     */
    public function setAccessEntity(AccessEntity $accessEntity)
    {
        $this->_accessEntity = $accessEntity;
    }
}
