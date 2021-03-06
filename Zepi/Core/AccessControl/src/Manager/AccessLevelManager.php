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
 * Manages the access levels.
 * 
 * @package Zepi\Core\AccessControl
 * @subpackage Manager
 * @author Matthias Zobrist <matthias.zobrist@zepi.net>
 * @copyright Copyright (c) 2015 zepi
 */

namespace Zepi\Core\AccessControl\Manager;

use \Zepi\Turbo\Framework;
use \Zepi\Turbo\Request\RequestAbstract;
use \Zepi\Turbo\Response\Response;
use \Zepi\Turbo\Backend\ObjectBackendAbstract;
use \Zepi\Core\AccessControl\Entity\AccessLevel;

/**
 * Manages the access levels.
 * 
 * @author Matthias Zobrist <matthias.zobrist@zepi.net>
 * @copyright Copyright (c) 2015 zepi
 */
class AccessLevelManager
{
    /**
     * @access protected
     * @var array
     */
    protected $accessLevels = array();
    
    /**
     * @access protected
     * @var \Zepi\Turbo\Framework
     */
    protected $framework;
    
    /**
     * @access protected
     * @var \Zepi\Turbo\Backend\ObjectBackendAbstract
     */
    protected $accessLevelsObjectBackend;
    
    /**
     * Constructs the object
     * 
     * @access public
     * @param \Zepi\Turbo\Framework $framework
     * @param \Zepi\Turbo\Backend\ObjectBackendAbstract $accessLevelObjectBackend
     */
    public function __construct(
        Framework $framework,
        ObjectBackendAbstract $accessLevelObjectBackend
    ) {
        $this->framework = $framework;
        $this->accessLevelsObjectBackend = $accessLevelObjectBackend;
    }
    
    /**
     * Initializes the access levels manager.
     * 
     * @access public
     */
    public function initializeAccessLevelManager()
    {
        $this->loadAccessLevels();
    }
    
    /**
     * Loads the access levels from the object backend
     * 
     * @access public
     */
    protected function loadAccessLevels()
    {
        $accessLevels = $this->accessLevelsObjectBackend->loadObject();
        if (!is_array($accessLevels)) {
            $accessLevels = array();
        }
        
        $this->accessLevels = $accessLevels;
    }
    
    /**
     * Saves the access levels to the object backend.
     * 
     * @access public
     */
    protected function saveAccessLevels()
    {
        $this->accessLevelsObjectBackend->saveObject($this->accessLevels);
    }
    
    /**
     * Adds a new access level.
     * 
     * @access public
     * @param AccessLevel $accessLevel
     * @return boolean
     */
    public function addAccessLevel(AccessLevel $accessLevel)
    {
        // Add the access level
        $this->accessLevels[$accessLevel->getKey()] = $accessLevel;
        
        // Save the access levels
        $this->saveAccessLevels();
    }
    
    /**
     * Removes the access level.
     * 
     * @access public
     * @param string $key
     */
    public function removeAccessLevel($key)
    {
        if (!isset($this->accessLevels[$key])) {
            return false;
        }
        
        // Remove the access level
        unset($this->accessLevels[$key]);
        
        // Save the access levels
        $this->saveAccessLevels();
        
        // Revoke all permissions for the given access level key
        $runtimeManager = $this->framework->getRuntimeManager();
        $runtimeManager->executeFilter('\\Zepi\\Core\\AccessControl\\Filter\\AccessLevelManager\\RemoveAccessLevel', $key);
    }
    
    /**
     * Returns all access levels
     * 
     * @access public
     * @return array
     */
    public function getAccessLevels()
    {
        // Give the modules the opportunity to add additional access levels
        $runtimeManager = $this->framework->getRuntimeManager();
        
        $accessLevels = $runtimeManager->executeFilter('\\Zepi\\Core\\AccessControl\\Filter\\AccessLevelManager\\RegisterAccessLevels', $this->accessLevels);
        
        return $accessLevels;
    }
}
