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
 * Filters the menu entries and verifies the access 
 * control levels for the protected menu entries.
 * 
 * @package Zepi\Web\AccessControl
 * @subpackage EventHandler
 * @author Matthias Zobrist <matthias.zobrist@zepi.net>
 * @copyright Copyright (c) 2015 zepi
 */

namespace Zepi\Web\AccessControl\EventHandler;

use \Zepi\Turbo\FrameworkInterface\WebEventHandlerInterface;
use \Zepi\Turbo\Framework;
use \Zepi\Turbo\Request\WebRequest;
use \Zepi\Turbo\Response\Response;
use \Zepi\Web\AccessControl\Entity\ProtectedMenuEntry;

/**
 * Filters the menu entries and verifies the access 
 * control levels for the protected menu entries.
 * 
 * @author Matthias Zobrist <matthias.zobrist@zepi.net>
 * @copyright Copyright (c) 2015 zepi
 */
class FilterMenuEntriesForProtectedEntries implements WebEventHandlerInterface
{
    /**
     * Filters the given menu entries and removes all protected menu
     * entries for which the sender hasn't the correct permission.
     * 
     * @access public
     * @param \Zepi\Turbo\Framework $framework
     * @param \Zepi\Turbo\Request\WebRequest $request
     * @param \Zepi\Turbo\Response\Response $response
     */
    public function execute(Framework $framework, WebRequest $request, Response $response)
    {
        // Get the entries
        $entries = $response->getData('menu.entries');
        
        // Verify the entries
        $entries = $this->_verifyEntries($entries, $framework, $request);
        
        // Save the entries
        $response->setData('menu.entries', $entries);
    }
    
    /**
     * Verifies the access levels for the given entries.
     * 
     * @access protected
     * @param array $entries
     * @param \Zepi\Turbo\Framework $framework
     * @param \Zepi\Turbo\Request\WebRequest $request
     * @return array
     */
    protected function _verifyEntries($entries, Framework $framework, WebRequest $request)
    {
        foreach ($entries as $key => $entry) {
            // If the entry is a ProtectedMenuEntry, verify the entry
            if ($entry instanceof \Zepi\Web\AccessControl\Entity\ProtectedMenuEntry) {
                $result = $this->_verifyProtectedEntry($entry, $framework, $request);
                
                // If the entry isn't allowed remove it from the array
                if (!$result) {
                    unset($entries[$key]);
                }
            }
            
            // If the entry has children, verify all children
            if ($entry->hasChildren()) {
                $children = $this->_verifyEntries($entry->getChildren(), $framework, $request);
                
                $entry->setChildren($children);
            }
        }
        
        return $entries;
    }
    
    /**
     * Verifies a protected menu entry.
     * 
     * @access protected
     * @param \Zepi\Web\AccessControl\Entity\ProtectedMenuEntry $protectedEntry
     * @param \Zepi\Turbo\Framework $framework
     * @param \Zepi\Turbo\Request\WebRequest $request
     * @return boolean
     */
    protected function _verifyProtectedEntry(ProtectedMenuEntry $protectedEntry, Framework $framework, WebRequest $request)
    {
        // If the user has no session we do not have to check the permissions
        if (!$request->hasSession()) {
            return false;
        }
        
        // Check the database
        if ($request->getSession()->hasAccess($protectedEntry->getAccessLevelKey())) {
            return true;
        }
        
        // If the user has no access to the database we return false
        return false;
    }
}
