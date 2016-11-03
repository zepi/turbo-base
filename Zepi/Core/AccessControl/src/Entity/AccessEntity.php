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
 * Representats one row in the access_entities table as
 * object.
 * 
 * @package Zepi\Core\AccessControl
 * @subpackage Entity
 * @author Matthias Zobrist <matthias.zobrist@zepi.net>
 * @copyright Copyright (c) 2015 zepi
 */

namespace Zepi\Core\AccessControl\Entity;

use \Zepi\Core\AccessControl\Entity\Permission;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Representats one row in the access_entities table as
 * object.
 * 
 * @author Matthias Zobrist <matthias.zobrist@zepi.net>
 * @copyright Copyright (c) 2015 zepi
 * 
 * @MappedSuperclass
 */
class AccessEntity
{
    /**
     * @ID
     * @Column(type="integer")
     * @GeneratedValue
     * @var integer
     */
    protected $id;
    
    /**
     * @Column(type="string", unique=true)
     * @var string
     */
    protected $uuid;
    
    /**
     * @Column(type="string", unique=true)
     * @var string
     */
    protected $name;
    
    /**
     * @Column(type="string", name="private_key")
     * @var string
     */
    protected $privateKey;
    
    /**
     * @Column(type="array", name="meta_data")
     * @var array
     */
    protected $metaData;
    
    /**
     * @var array
     */
    protected $permissions;
    
    /**
     * Constructs the object
     * 
     * @param integer $id
     * @param string $uuid
     * @param string $name
     * @param string $privateKey
     * @param array $metaData
     */
    public function __construct($id, $uuid, $name, $privateKey, array $metaData)
    {
        if ($uuid == '') {
            $uuid = \Ramsey\Uuid\Uuid::uuid4()->toString();
        }
        
        $this->id = $id;
        $this->uuid = $uuid;
        $this->name = $name;
        $this->privateKey = $privateKey;
        $this->metaData = $metaData;
    }
    
    /**
     * Returns the id of the access entitiy
     * 
     * @access public
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }
    
    /**
     * Returns true if the access entity is new (and not saved yet).
     * 
     * @access public
     * @return boolean
     */
    public function isNew()
    {
        return ($this->id == '');
    }
    
    /**
     * Returns the uuid of the access entity
     * 
     * @access public
     * @return string
     */
    public function getUuid()
    {
        return $this->uuid;
    }
    
    /**
     * Returns the name of the access entitiy
     * 
     * @access public
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }
    
    /**
     * Sets the name of the access entity
     * 
     * @access public
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * Returns the key of the access entity
     * 
     * @access public
     * @return string
     */    
    public function getKey()
    {
        return $this->privateKey;
    }
    
    /**
     * Returns the meta data value for the given key
     * 
     * @access public
     * @param string $key
     * @return mixed
     */
    public function getMetaData($key)
    {
        if (!isset($this->metaData[$key])) {
            return false;
        }
        
        return $this->metaData[$key];
    }
    
    /**
     * Sets the meta data value for the given key
     * 
     * @access public
     * @param string $key
     * @param mixed $value
     */
    public function setMetaData($key, $value)
    {
        $this->metaData[$key] = $value;
    }
    
    /**
     * Returns the whole meta data array
     * 
     * @access public
     * @return array
     */
    public function getMetaDataArray()
    {
        return $this->metaData;
    }
    
    /**
     * Returns an array with all permissions for this access entity
     *
     * @access public
     * @return array
     */
    public function getPermissions()
    {
        return $this->permissions;
    }
    
    /**
     * Sets the permissions of this access entity
     * 
     * @access public
     * @param array $permissions
     */
    public function setPermissions($permissions)
    {
        $this->permissions = $permissions;
    }
    
    /**
     * Returns true if the access entity has access to
     * the given permission access level
     *
     * @access public
     * @param string $accessLevel
     * @return boolean
     */
    public function hasAccess($accessLevel)
    {
        foreach ($this->permissions as $permission) {
            /**
             * If the user has the \Globa\* access level he can
             * do everything
             */
            if ($permission == '\\Global\\*') {
                return true;
            }
            
            if ($permission == $accessLevel) {
                return true;
            }
            
            // If there is a star in the permission and everything before 
            // the star is equal with the access level, the user has access
            // to the given access level.
            $posStar = strpos($permission->getAccessLevelKey(), '*');
            if ($posStar !== false) {
                $startPermission = substr($permission->getAccessLevelKey(), 0, $posStar);
                $startAccessLevel = substr($accessLevel, 0, $posStar);
                
                if ($startPermission == $startAccessLevel) {
                    return true;
                }
            } 
        }
    
        return false;
    }
}
