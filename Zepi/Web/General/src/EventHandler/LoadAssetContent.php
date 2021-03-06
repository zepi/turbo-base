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
 * Loads the content of the given asset.
 * 
 * @package Zepi\Web\General
 * @subpackage EventHandler
 * @author Matthias Zobrist <matthias.zobrist@zepi.net>
 * @copyright Copyright (c) 2015 zepi
 */

namespace Zepi\Web\General\EventHandler;

use \Zepi\Turbo\FrameworkInterface\WebEventHandlerInterface;
use \Zepi\Turbo\Framework;
use \Zepi\Turbo\Request\WebRequest;
use \Zepi\Turbo\Response\Response;
use \Zepi\Web\General\Manager\AssetCacheManager;

/**
 * Loads the content of the given asset.
 * 
 * @author Matthias Zobrist <matthias.zobrist@zepi.net>
 * @copyright Copyright (c) 2015 zepi
 */
class LoadAssetContent implements WebEventHandlerInterface
{
    /**
     * @access protected
     * @var \Zepi\Web\General\Manager\AssetCacheManager
     */
    protected $assetCacheManager;
    
    /**
     * Constructs the object
     *
     * @access public
     * @param \Zepi\Web\General\Manager\AssetCacheManager $assetCacheManager
     */
    public function __construct(AssetCacheManager $assetCacheManager)
    {
        $this->assetCacheManager = $assetCacheManager;
    }
    
    /**
     * This event handler lists all activated modules with the description
     * of each module.
     * 
     * @access public
     * @param \Zepi\Turbo\Framework $framework
     * @param \Zepi\Turbo\Request\WebRequest $request
     * @param \Zepi\Turbo\Response\Response $response
     */
    public function execute(Framework $framework, WebRequest $request, Response $response, $value = null)
    {
        // Get the route params
        $type = $request->getRouteParam('type'); // Type of the asset
        $hash = $request->getRouteParam('hash'); // Hash of the asset
        $version = $request->getRouteParam('version'); // Version of the file 
        
        // Check if all values are available
        if ($type == false || $hash == false || $version == false) {
            $response->setOutput('/** Zepi Assets Manager: Malformed request! */');
            return;
        }
        
        // If the file isn't cached display nothing
        if (!$this->assetCacheManager->isCached($type, $hash, $version)) {
            $response->setOutput('/** Zepi Assets Manager: Not cached! */');
            return;
        }
        
        // Load the content
        $content = $this->assetCacheManager->getAssetContent($type, $hash, $version);
        if ($content === '') {
            $content = '/** Zepi Assets Manager: File is empty or does not exists! */';
        }
        
        $this->deliverContent($response, $type, $hash, $version, $content);
    }
    
    /**
     * Adds the needed headers and prepares the content
     * 
     * @param \Zepi\Turbo\Response\Response $response
     * @param string $type
     * @param string $hash
     * @param string $version
     * @param string $content
     */
    protected function deliverContent(Response $response, $type, $hash, $version, $content)
    {
        // Define the if modified since timestamp
        $cachedAssetTimestamp = $this->assetCacheManager->getCachedAssetTimestamp($type, $hash, $version);
        $ifModifiedSince = -1;
        if ($this->isHeaderSetAndNotEmpty('HTTP_IF_MODIFIED_SINCE')) {
            $ifModifiedSince = $_SERVER['HTTP_IF_MODIFIED_SINCE'];
        }
        
        // Define the etag
        $eTag = md5($content);
        $eTagHeader = -1;
        if ($this->isHeaderSetAndNotEmpty('HTTP_IF_NONE_MATCH')) {
            $eTagHeader = $_SERVER['HTTP_IF_NONE_MATCH'];
        }
        
        // Set the cache headers
        $cacheTtl = 86400 * 365;
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s', $cachedAssetTimestamp) . ' GMT');
        header('Expires: ' . gmdate("D, d M Y H:i:s", time() + $cacheTtl) . ' GMT');
        header('Pragma: cache');
        header('Etag: ' . $eTag);
        header('Cache-Control: max-age=' . $cacheTtl);
        
        // Verify the cached timestamp and the eTag
        if ($cachedAssetTimestamp === $ifModifiedSince || $eTag === $eTagHeader) {
            header('HTTP/1.1 304 Not Modified');
            exit;
        }
        
        // Set the content type
        $contentType = $this->getContentType($type, $version);
        if ($contentType !== '') {
            header('Content-type: ' . $contentType, true);
        }
        
        // Display the content
        $response->setOutput($content);
    }
    
    /**
     * Returns true if the given header is set and not empty.
     * 
     * @param string $header
     * @return boolean
     */
    protected function isHeaderSetAndNotEmpty($header)
    {
        return (isset($_SERVER[$header]) && $_SERVER[$header] != '');
    }

    /**
     * Returns the content type for the given asset type.
     * 
     * @access protected
     * @param string $type
     * @param string $version
     * @return string
     */
    protected function getContentType($type, $version)
    {
        if ($type === 'css') {
            return 'text/css';
        } else if ($type === 'js') {
            return 'text/javascript';
        } else if ($type === 'image') {
            $fileExtension = pathinfo($version, PATHINFO_EXTENSION);
            
            if ($fileExtension === 'svg') {
                return 'image/svg+xml';
            } else if ($fileExtension === 'jpg') {
                return 'image/jpeg';
            } else if ($fileExtension === 'gif') {
                return 'image/gif';
            } else if ($fileExtension === 'png') {
                return 'image/png';
            }
        }
    }
}
