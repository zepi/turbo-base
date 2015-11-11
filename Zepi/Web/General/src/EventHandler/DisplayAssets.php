<?php
/**
 * Displays the assets.
 * 
 * @package Zepi\Web\General
 * @subpackage EventHandler
 * @author Matthias Zobrist <matthias.zobrist@zepi.net>
 * @copyright Copyright (c) 2015 zepi
 */

namespace Zepi\Web\General\EventHandler;

use \Zepi\Turbo\FrameworkInterface\EventHandlerInterface;
use \Zepi\Turbo\Framework;
use \Zepi\Turbo\Request\RequestAbstract;
use \Zepi\Turbo\Response\Response;
use \Zepi\Web\Test\Exception;
use \Zepi\Web\General\Manager\AssetsManager;

/**
 * Displays the assets.
 * 
 * @author Matthias Zobrist <matthias.zobrist@zepi.net>
 * @copyright Copyright (c) 2015 zepi
 */
class DisplayAssets implements EventHandlerInterface
{
    /**
     * This event handler lists all activated modules with the description
     * of each module.
     * 
     * @access public
     * @param \Zepi\Turbo\Framework $framework
     * @param \Zepi\Turbo\Request\RequestAbstract $request
     * @param \Zepi\Turbo\Response\Response $response
     * @param mixed $value
     * 
     * @throws Zepi\Core\Management\Exception The list with the activated modules can only be viewed from command line!
     */
    public function executeEvent(Framework $framework, RequestAbstract $request, Response $response, $value = null)
    {
        $assetsManager = $framework->getInstance('\\Zepi\\Web\\General\\Manager\\AssetsManager');
        
        // Display the main css group
        $assetsManager->displayAssetType(AssetsManager::CSS);
        
        // Display the main js group
        $assetsManager->displayAssetType(AssetsManager::JS);
    }
}
