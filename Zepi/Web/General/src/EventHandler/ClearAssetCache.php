<?php
/**
 * Clears the asset cache to rebuild the whole css and javascript cache.
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

/**
 * Clears the asset cache to rebuild the whole css and javascript cache.
 * 
 * @author Matthias Zobrist <matthias.zobrist@zepi.net>
 * @copyright Copyright (c) 2015 zepi
 */
class ClearAssetCache implements EventHandlerInterface
{
    /**
     * This event handler clears the assets cache.
     * 
     * @access public
     * @param \Zepi\Turbo\Framework $framework
     * @param \Zepi\Turbo\Request\RequestAbstract $request
     * @param \Zepi\Turbo\Response\Response $response
     * @param mixed $value
     */
    public function executeEvent(Framework $framework, RequestAbstract $request, Response $response, $value = null)
    {
        $assetsManager = $framework->getInstance('\\Zepi\\Web\\General\\Manager\\AssetsManager');
        
        // Clean the asset cache
        $result = $assetsManager->clearAssetCache();
        $response->setOutputPart('cacheCleared', 'The asset cache was successfully cleared!');
    }
}
