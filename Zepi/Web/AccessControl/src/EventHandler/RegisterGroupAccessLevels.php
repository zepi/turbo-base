<?php
/**
 * Registers the groups as access levels.
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
 * Registers the groups as access levels.
 * 
 * @author Matthias Zobrist <matthias.zobrist@zepi.net>
 * @copyright Copyright (c) 2015 zepi
 */
class RegisterGroupAccessLevels implements EventHandlerInterface
{
    /**
     * Registers the groups as access levels.
     * 
     * @access public
     * @param \Zepi\Turbo\Framework $framework
     * @param \Zepi\Turbo\Request\RequestAbstract $request
     * @param \Zepi\Turbo\Response\Response $response
     * @param mixed $value
     */
    public function executeEvent(Framework $framework, RequestAbstract $request, Response $response, $value = null)
    {
        $groupManager = $framework->getInstance('\\Zepi\\Web\\AccessControl\\Manager\\GroupManager');
        $accessLevelManager = $framework->getInstance('\\Zepi\\Core\\AccessControl\\Manager\\AccessLevelManager');
        $translationManager = $framework->getInstance('\\Zepi\\Core\\Language\\Manager\\TranslationManager');
        
        $dataRequest = new DataRequest(1, 0, 'name', 'ASC');
        
        foreach ($groupManager->getGroups($dataRequest) as $group) {
            $accessLevelManager->addAccessLevel(new GroupAccessLevel(
                '\\Group\\' . $group->getUuid(),
                $translationManager->translate('Group', '\\Zepi\\Web\\AccessControl') . ' ' . $group->getName(),
                $translationManager->translate('Inherits all permissions from this group.', '\\Zepi\\Web\\AccessControl'),
                '\\Group'
            ));
        }
    }
}
