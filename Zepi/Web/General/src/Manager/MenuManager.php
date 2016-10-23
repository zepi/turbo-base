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
    protected $menuEntries = array();
    
    /**
     * @access protected
     * @var MenuEntry
     */
    protected $activeMenuEntry = null;
    
    /**
     * @access protected
     * @var \Zepi\Turbo\Framework
     */
    protected $framework;
    
    /**
     * @access protected
     * @var string
     */
    protected $breadcrumbFunction = '';
    
    /**
     * Constructs the object
     * 
     * @access public
     * @param \Zepi\Turbo\Framework $framework
     */
    public function __construct(Framework $framework)
    {
        $this->framework = $framework;
    }
    
    /**
     * Sets the breadcrumb function
     * 
     * @access public
     * @param string $breadcrumbFunction
     */
    public function setBreadcrumbFunction($breadcrumbFunction)
    {
        $this->breadcrumbFunction = $breadcrumbFunction;
    }
    
    /**
     * Adds the given menu entry to the menu manager.
     * 
     * @access public
     * @param string $location
     * @param \Zepi\Web\General\Entity\MenuEntry $menuEntry
     * @param integer $priority
     * @return boolean
     */
    public function addMenuEntry($location, MenuEntry $menuEntry, $priority = 50)
    {
        if (!isset($this->menuEntries[$location])) {
            $this->menuEntries[$location] = array();
        }
        
        if (!isset($this->menuEntries[$location][$priority])) {
            $this->menuEntries[$location][$priority] = array();
        }

        if (isset($this->menuEntries[$location][$priority][$menuEntry->getKey()])) {
            return false;
        }
        
        $this->menuEntries[$location][$priority][$menuEntry->getKey()] = $menuEntry;
        
        ksort($this->menuEntries[$location]);
        uasort($this->menuEntries[$location][$priority], array($this, 'sortMenuEntries'));
        
        return true;
    }
    
    /**
     * Sorts two menu entries
     * 
     * @access public
     * @param Zepi\Web\General\Entity\MenuEntry $a
     * @param Zepi\Web\General\Entity\MenuEntry $b
     * @return integer
     */
    public function sortMenuEntries($a, $b)
    {
        if ($a->getName() > $b->getName()) {
            return 1;
        } else if ($a->getName() < $b->getName()) {
            return -1;
        } else {
            return 0;
        }
    }
    
    /**
     * Returns all menu entries
     * 
     * @access public
     * @param string $location
     * @return array
     */
    public function getMenuEntries($location = null)
    {
        $runtimeManager = $this->framework->getRuntimeManager();

        // Give the modules the opportunity to add additional menu entries
        $runtimeManager->executeEvent('\\Zepi\\Web\\General\\Event\\MenuManager\\RegisterAdditionalMenuEntries');
        
        if ($location === null) {
            return $this->menuEntries;
        }
        
        // If this location does not exists, return an empty array
        if (!isset($this->menuEntries[$location])) {
            return array();
        }
        
        // Execute the event
        $menuEntries = $runtimeManager->executeFilter('\\Zepi\\Web\\General\\Filter\\MenuManager\\FilterMenuEntries', $this->menuEntries[$location]);
        
        return $menuEntries;
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
        foreach ($this->menuEntries as $location => $priorities) {
            foreach ($priorities as $priority=> $menuEntries) {
                $result = $this->searchMenuEntryForKey($menuEntries, $key);
            
                if ($result !== false) {
                    return $result;
                }
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
        $this->activeMenuEntry = $menuEntry;
    }
    
    /**
     * Returns the active menu entry
     * 
     * @access public
     * @return \Zepi\Web\General\Entity\MenuEntry
     */
    public function getActiveMenuEntry()
    {
        return $this->activeMenuEntry;
    }
    
    /**
     * Activates the correct menu entry based on the event which is active.
     * 
     * @access public
     */
    public function activateCorrectMenuEntry()
    {
        $runtimeManager = $this->framework->getRuntimeManager();
        
        // Execute the pre search correct menu entry event.
        $runtimeManager->executeEvent('\\Zepi\\Web\\General\\Event\\MenuManager\\RegisterAdditionalMenuEntries');
        
        // Search the correct menu entry, if no menu entry is set
        if ($this->activeMenuEntry == null) {
            $menuEntry = $this->searchCorrectMenuEntry();
            
            if ($menuEntry !== false) {
                $this->activeMenuEntry = $menuEntry;
            }
        }
        
        // Execute the post search correct menu entry event
        $runtimeManager->executeEvent('\\Zepi\\Web\\General\\Event\\MenuManager\\PostSearchCorrectMenuEntry');

        // Activate the menu entry
        if ($this->activeMenuEntry !== null) {
            $this->activeMenuEntry->setActive(true);
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
            $entry = $this->activeMenuEntry;
            
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
            if ($this->breadcrumbFunction != '') {
                array_push($entries, new HiddenMenuEntry(
                    $this->breadcrumbFunction,
                    $this->framework->getRequest()->getRoute()
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
    protected function searchCorrectMenuEntry()
    {
        foreach ($this->menuEntries as $location => $priorities) {
            foreach ($priorities as $priority => $menuEntries) {
                $menuEntry = $this->searchCorrectMenuEntryInArray($menuEntries);
    
                if ($menuEntry !== false) {
                    return $menuEntry;
                }
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
    protected function searchCorrectMenuEntryInArray($menuEntries)
    {
        foreach ($menuEntries as $key => $menuEntry) {
            if (trim($menuEntry->getTarget(), '/') === trim($this->framework->getRequest()->getRoute(), '/')) {
                return $menuEntry;
            }
            
            if ($menuEntry->hasChildren()) {
                $menuEntry = $this->searchCorrectMenuEntryInArray($menuEntry->getChildren());
                
                if ($menuEntry !== false) {
                    return $menuEntry;
                }
            }
        }
        
        return false;
    }
}
