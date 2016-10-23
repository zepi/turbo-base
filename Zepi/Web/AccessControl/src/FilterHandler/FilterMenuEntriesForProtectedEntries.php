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
 * @subpackage FilterHandler
 * @author Matthias Zobrist <matthias.zobrist@zepi.net>
 * @copyright Copyright (c) 2015 zepi
 */

namespace Zepi\Web\AccessControl\FilterHandler;

use \Zepi\Turbo\FrameworkInterface\FilterHandlerInterface;
use \Zepi\Turbo\Framework;
use \Zepi\Turbo\Request\RequestAbstract;
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
class FilterMenuEntriesForProtectedEntries implements FilterHandlerInterface
{
    /**
     * Filters the given menu entries and removes all protected menu
     * entries for which the sender hasn't the correct permission.
     * 
     * @access public
     * @param \Zepi\Turbo\Framework $framework
     * @param \Zepi\Turbo\Request\RequestAbstract $request
     * @param \Zepi\Turbo\Response\Response $response
     * @param mixed $value
     * @return mixed
     */
    public function execute(Framework $framework, RequestAbstract $request, Response $response, $value = null)
    {
        // If the request is not a web request return the value as we received it
        if (!is_a($request, '\\Zepi\\Turbo\\Request\\WebRequest')) {
            return $value;
        }
        
        // Verify the entries
        return $this->verifyMainEntries($value, $request);
    }
    
    /**
     * Verifies the main access levels for the given entries.
     *
     * @access protected
     * @param array $priorities
     * @param \Zepi\Turbo\Request\WebRequest $request
     * @return array
     */
    protected function verifyMainEntries($priorities, WebRequest $request)
    {
        foreach ($priorities as $priority => $entries) {
            $priorities[$priority] = $this->verifyEntries($entries, $request);
        }
    
        return $priorities;
    }
    
    /**
     * Verifies the access levels for the given entries.
     * 
     * @access protected
     * @param array $entries
     * @param \Zepi\Turbo\Request\WebRequest $request
     * @return array
     */
    protected function verifyEntries($entries, WebRequest $request)
    {
        foreach ($entries as $key => $entry) {
            // If the entry is a ProtectedMenuEntry, verify the entry
            if ($entry instanceof \Zepi\Web\AccessControl\Entity\ProtectedMenuEntry) {
                $result = $this->verifyProtectedEntry($entry, $request);
        
                // If the entry isn't allowed remove it from the array
                if (!$result) {
                    unset($entries[$key]);
                }
            }
        
            // If the entry has children, verify all children
            if ($entry->hasChildren()) {
                $children = $this->verifyEntries($entry->getChildren(), $request);
        
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
     * @param \Zepi\Turbo\Request\WebRequest $request
     * @return boolean
     */
    protected function verifyProtectedEntry(ProtectedMenuEntry $protectedEntry, WebRequest $request)
    {
        // If the user has no session we do not have to check the permissions
        if (!$request->hasSession()) {
            return false;
        }
        
        // If the access level key is empty but the user has a
        // session everything is fine with this entry.
        if ($request->hasSession() && $protectedEntry->getAccessLevelKey() === '') {
            return true;
        }
        
        // Check the database
        if ($request->getSession()->hasAccess($protectedEntry->getAccessLevelKey())) {
            return true;
        }
        
        // If the user has no access to the database we return false
        return false;
    }
}
