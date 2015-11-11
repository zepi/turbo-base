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
 * Manages all menu entries and returns the correct menu entries for 
 * the frontend.
 * 
 * @package Zepi\Web\General
 * @subpackage Manager
 * @author Matthias Zobrist <matthias.zobrist@zepi.net>
 * @copyright Copyright (c) 2015 zepi
 */

namespace Zepi\Web\General\Manager;

use \Zepi\Turbo\Framework;
use \Zepi\Turbo\Request\RequestAbstract;
use \Zepi\Turbo\Response\Response;
use \Zepi\Web\General\Entity\MenuEntry;
use \Zepi\Web\General\Entity\HiddenMenuEntry;

/**
 * Manages all menu entries and returns the correct menu entries for 
 * the frontend.
 * 
 * @author Matthias Zobrist <matthias.zobrist@zepi.net>
 * @copyright Copyright (c) 2015 zepi
 */
class MenuManager
{
    /**
     * @access protected
     * @var array
     */
    protected $_menuEntries = array();
    
    /**
     * @access protected
     * @var MenuEntry
     */
    protected $_activeMenuEntry = null;
    
    /**
     * @access protected
     * @var \Zepi\Turbo\Framework
     */
    protected $_framework;
    
    /**
     * @access protected
     * @var string
     */
    protected $_breadcrumbFunction = '';
    
    /**
     * Constructs the object
     * 
     * @access public
     * @param \Zepi\Turbo\Framework $framework
     */
    public function __construct(Framework $framework)
    {
        $this->_framework = $framework;
    }
    
    /**
     * Sets the breadcrumb function
     * 
     * @access public
     * @param string $breadcrumbFunction
     */
    public function setBreadcrumbFunction($breadcrumbFunction)
    {
        $this->_breadcrumbFunction = $breadcrumbFunction;
    }
    
    /**
     * Adds the given menu entry to the menu manager.
     * 
     * @access public
     * @param string $location
     * @param \Zepi\Web\General\Entity\MenuEntry $menuEntry
     * @return boolean
     */
    public function addMenuEntry($location, MenuEntry $menuEntry)
    {
        if (!isset($this->_menuEntries[$location])) {
            $this->_menuEntries[$location] = array();
        }
        
        if (isset($this->_menuEntries[$location][$menuEntry->getKey()])) {
            return false;
        }
        
        $this->_menuEntries[$location][$menuEntry->getKey()] = $menuEntry;
        
        return true;
    }
    
    /**
     * Returns all menu entries
     * 
     * @access public
     * @param string $location
     * @return array
     */
    public function getMenuEntries($location)
    {
        $eventManager = $this->_framework->getEventManager();
        
        // Give the modules the opportunity to add additional menu entries
        $eventManager->executeEvent('\\Zepi\\Web\\General\\Event\\MenuManager\\RegisterAdditionalMenuEntries');
        
        // If this location does not exists, return an empty array
        if (!isset($this->_menuEntries[$location])) {
            return array();
        }
        
        // Save the data in the response
        $response = $this->_framework->getResponse();
        $response->setData('menu.location', $location);
        $response->setData('menu.entries', $this->_menuEntries[$location]);
        
        // Execute the event
        $eventManager->executeEvent('\\Zepi\\Web\\General\\Event\\MenuManager\\FilterMenuEntries');
        
        return $response->getData('menu.entries');
    }
    
    /**
     * Returns the MenuEntry object for the given key or false if the
     * key not is registred
     * 
     * @access public
     * @param string $key
     * @return false|\Zepi\Web\General\Entity\MenuEntry
     */
    public function getMenuEntryForKey($key)
    {
        foreach ($this->_menuEntries as $location => $menuEntries) {
            $result = $this->searchMenuEntryForKey($menuEntries, $key);
        
            if ($result !== false) {
                return $result;
            }
        }
        
        return false;
    }
    
    /**
     * Searches the given key in the array of menu entries.
     * 
     * @access public
     * @param array $menuEntries
     * @param string $key
     * @return false|\Zepi\Web\General\Entity\MenuEntry
     */
    public function searchMenuEntryForKey($menuEntries, $key)
    {
        foreach ($menuEntries as $menuEntry) {
            if ($menuEntry->getKey() === $key) {
                return $menuEntry;
            } 
            
            if ($menuEntry->hasChildren()) {
                $result = $this->searchMenuEntryForKey($menuEntry->getChildren(), $key);
                
                if ($result !== false) {
                    return $result;
                }
            }
        }
        
        return false;
    }
    
    /**
     * Sets the active menu entry
     * 
     * @access public
     * @param \Zepi\Web\General\Entity\MenuEntry $menuEntry
     */
    public function setActiveMenuEntry(MenuEntry $menuEntry)
    {
        $this->_activeMenuEntry = $menuEntry;
    }
    
    /**
     * Returns the active menu entry
     * 
     * @access public
     * @return \Zepi\Web\General\Entity\MenuEntry
     */
    public function getActiveMenuEntry()
    {
        return $this->_activeMenuEntry;
    }
    
    /**
     * Activates the correct menu entry based on the event which is active.
     * 
     * @access public
     */
    public function activateCorrectMenuEntry()
    {
        $eventManager = $this->_framework->getEventManager();
        
        // Execute the pre search correct menu entry event.
        $eventManager->executeEvent('\\Zepi\\Web\\General\\Event\\MenuManager\\PreSearchCorrectMenuEntry');
        
        // Search the correct menu entry, if no menu entry is set
        if ($this->_activeMenuEntry == null) {
            $menuEntry = $this->_searchCorrectMenuEntry();
            
            if ($menuEntry !== false) {
                $this->_activeMenuEntry = $menuEntry;
            }
        }
        
        // Execute the post search correct menu entry event
        $eventManager->executeEvent('\\Zepi\\Web\\General\\Event\\MenuManager\\PostSearchCorrectMenuEntry');

        // Activate the menu entry
        if ($this->_activeMenuEntry !== null) {
            $this->_activeMenuEntry->setActive(true);
        }
    }
    
    /**
     * Returns an array with all breadcrumb entries
     * 
     * @access public
     * @param MenuEntry $entry
     * @return array
     */
    public function getBreadcrumbEntries(MenuEntry $entry = null)
    {
        $this->activateCorrectMenuEntry();
        
        $startEntry = false;
        if ($entry === null) {
            $startEntry = true;
            $entry = $this->_activeMenuEntry;
            
            if ($entry == null) {
                return array();
            }
        }
        
        $entries = array(
            $entry
        );
        
        if ($entry->hasParent()) {
            $parentEntries = $this->getBreadcrumbEntries($entry->getParent());
            
            foreach ($parentEntries as $parentEntry) {
                $entries[] = $parentEntry;
            }
        }
        
        // If we are in the initial call we reverse the order of the entries
        if ($startEntry) {
            $entries = array_reverse($entries, false);
            
            // If we have additional breadcrumb function for the breadcrumb navigation
            // we add the function here to the entries array
            if ($this->_breadcrumbFunction != '') {
                array_push($entries, new HiddenMenuEntry(
                    $this->_breadcrumbFunction,
                    $this->_framework->getRequest()->getRoute()
                ));
            }
        }
        
        return $entries;
    }
    
    /**
     * Searches the correct menu entry based on the requested route.
     * 
     * @access protected
     * @return false|\Zepi\Web\General\Entity\MenuEntry
     */
    protected function _searchCorrectMenuEntry()
    {
        foreach ($this->_menuEntries as $location => $menuEntries) {
            $menuEntry = $this->_searchCorrectMenuEntryInArray($menuEntries);

            if ($menuEntry !== false) {
                return $menuEntry;
            }
        }
        
        return false;
    }
    
    /**
     * Iterates trough an array and searches the needed menu entry.
     * If found, return the menu entry, otherwise return false.
     * 
     * @access protected
     * @param array $menuEntries
     * @return false|\Zepi\Web\General\Entity\MenuEntry
     */
    protected function _searchCorrectMenuEntryInArray($menuEntries)
    {
        foreach ($menuEntries as $key => $menuEntry) {
            if (trim($menuEntry->getTarget(), '/') === trim($this->_framework->getRequest()->getRoute(), '/')) {
                return $menuEntry;
            }
            
            if ($menuEntry->hasChildren()) {
                $menuEntry = $this->_searchCorrectMenuEntryInArray($menuEntry->getChildren());
                
                if ($menuEntry !== false) {
                    return $menuEntry;
                }
            }
        }
        
        return false;
    }
}
