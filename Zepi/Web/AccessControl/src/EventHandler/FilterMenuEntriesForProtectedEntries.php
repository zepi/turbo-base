<?php
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

use \Zepi\Turbo\FrameworkInterface\EventHandlerInterface;
use \Zepi\Turbo\Framework;
use \Zepi\Turbo\Request\RequestAbstract;
use \Zepi\Turbo\Response\Response;
use \Zepi\Web\AccessControl\Entity\ProtectedMenuEntry;

/**
 * Filters the menu entries and verifies the access 
 * control levels for the protected menu entries.
 * 
 * @author Matthias Zobrist <matthias.zobrist@zepi.net>
 * @copyright Copyright (c) 2015 zepi
 */
class FilterMenuEntriesForProtectedEntries implements EventHandlerInterface
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
     */
    public function executeEvent(Framework $framework, RequestAbstract $request, Response $response, $value = null)
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
     * @param \Zepi\Turbo\Request\RequestAbstract $request
     * @return $entries
     */
    protected function _verifyEntries($entries, Framework $framework, RequestAbstract $request)
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
     * @param \Zepi\Web\AccessControl\Entity\ProtectedMenuEntry $protectedMenuEntry
     * @param \Zepi\Turbo\Framework $framework
     * @param \Zepi\Turbo\Request\RequestAbstract $request
     * @return boolean
     */
    protected function _verifyProtectedEntry(ProtectedMenuEntry $protectedEntry, Framework $framework, RequestAbstract $request)
    {
        // If the user has no session we do not have to check the permissions
        if (!$request->hasSession()) {
            return false;
        }
        
        // Check the database
        $accessControlManager = $framework->getInstance('\\Zepi\\Core\\AccessControl\\Manager\\AccessControlManager');
        if ($request->getSession()->hasAccess($protectedEntry->getAccessLevelKey())) {
            return true;
        }
        
        // If the user has no access to the database we return false
        return false;
    }
}
