<?php
/**
 * Replaces all group access levels with the permissions of the group
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
use \Zepi\Core\Utils\Entity\DataRequest;
use \Zepi\Web\AccessControl\Entity\GroupAccessLevel;

/**
 * Replaces all group access levels with the permissions of the group
 * 
 * @author Matthias Zobrist <matthias.zobrist@zepi.net>
 * @copyright Copyright (c) 2015 zepi
 */
class ResolveGroupPermissions implements EventHandlerInterface
{
    /**
     * Replaces all group access levels with the permissions of the group
     * 
     * @access public
     * @param \Zepi\Turbo\Framework $framework
     * @param \Zepi\Turbo\Request\RequestAbstract $request
     * @param \Zepi\Turbo\Response\Response $response
     * @param mixed $value
     */
    public function executeEvent(Framework $framework, RequestAbstract $request, Response $response, $value = null)
    {
        $accessControlManager = $framework->getInstance('\\Zepi\\Core\\AccessControl\\Manager\\AccessControlManager');
        
        $permissions = array();
        foreach ($value as $accessLevel) {
            $parts = explode('\\', $accessLevel);

            if ($parts[1] === 'Group' && count($parts) === 3) {
                $uuid = $parts[2];
                
                $groupPermissions = $accessControlManager->getPermissions($uuid);
                foreach ($groupPermissions as $groupPermission) {
                    $permissions[] = $groupPermission;
                }
            } else {
                $permissions[] = $accessLevel;
            }
        }
        
        $permissions = array_unique($permissions);
        
        return $permissions;
    }
}
