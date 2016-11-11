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
 * Manages the access to events.
 * 
 * @package Zepi\Core\AccessControl
 * @subpackage Manager
 * @author Matthias Zobrist <matthias.zobrist@zepi.net>
 * @copyright Copyright (c) 2016 zepi
 */

namespace Zepi\Core\AccessControl\Manager;

use \Zepi\Turbo\Backend\ObjectBackendAbstract;

/**
 * Manages the access to events.
 * 
 * @author Matthias Zobrist <matthias.zobrist@zepi.net>
 * @copyright Copyright (c) 2016 zepi
 */
class EventAccessManager
{
    /**
     * @var array
     */
    protected $items = array();
    
    /**
     * @var \Zepi\Turbo\Backend\ObjectBackendAbstract
     */
    protected $eventAccessObjectBackend;
    
    /**
     * Constructs the object
     * 
     * @access public
     * @param \Zepi\Turbo\Backend\ObjectBackendAbstract $eventAccessObjectBackend
     */
    public function __construct(
        ObjectBackendAbstract $eventAccessObjectBackend
    ) {
        $this->eventAccessObjectBackend = $eventAccessObjectBackend;
    }
    
    /**
     * Initializes the event access manager.
     * 
     * @access public
     */
    public function initializeEventAccessManager()
    {
        $this->loadItems();
    }
    
    /**
     * Loads the items from the object backend
     * 
     * @access public
     */
    protected function loadItems()
    {
        $items = $this->eventAccessObjectBackend->loadObject();
        if (!is_array($items)) {
            $items = array();
        }
        
        $this->items = $items;
    }
    
    /**
     * Saves the items to the object backend.
     * 
     * @access public
     */
    protected function saveItems()
    {
        $this->eventAccessObjectBackend->saveObject($this->items);
    }
    
    /**
     * Adds the  access levels for the event name
     * 
     * @access public
     * @param string $eventName
     * @param string $accessLevel
     */
    public function addItem($eventName, $accessLevel)
    {
        if (!isset($this->items[$eventName])) {
            $this->items[$eventName] = array();
        }
        
        if (in_array($accessLevel, $this->items[$eventName])) {
            return;
        }
        
        // Add the definition
        $this->items[$eventName][] = $accessLevel;
        
        // Save the items
        $this->saveItems();
    }
    
    /**
     * Removes all access levels for the given event name.
     * 
     * @access public
     * @param string $key
     * @return false|void
     */
    public function removeItems($eventName)
    {
        if (!isset($this->items[$eventName])) {
            return false;
        }
        
        // Remove the event
        unset($this->items[$eventName]);
        
        // Save the items
        $this->saveItems();
    }
    
    /**
     * Returns all items
     * 
     * @access public
     * @return array
     */
    public function getItems()
    {
        return $this->items;
    }
    
    /**
     * Returns the access levels for the given event name
     * or false if the event name is not set.
     *
     * @param string $eventName
     * @return false|array
     */
    public function getAccessLevelsForEvent($eventName)
    {
        if (!isset($this->items[$eventName])) {
            return false;
        }
        
        return $this->items[$eventName];
    }
}
