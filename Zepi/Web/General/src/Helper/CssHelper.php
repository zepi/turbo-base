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
 * Helps the asset manager with css files.
 * 
 * @package Zepi\Web\General
 * @subpackage Helper
 * @author Matthias Zobrist <matthias.zobrist@zepi.net>
 * @copyright Copyright (c) 2015 zepi
 */

namespace Zepi\Web\General\Helper;

use \Zepi\Web\General\Manager\AssetsManager;
use \Zepi\Turbo\Backend\FileBackend;

/**
 * Helps the asset manager with css files.
 * 
 * @author Matthias Zobrist <matthias.zobrist@zepi.net>
 * @copyright Copyright (c) 2015 zepi
 */
class CssHelper
{
    /**
     * @access protected
     * @var AssetsManager
     */
    protected $assetsManager;
    
    /**
     * @access protected
     * @var FileBackend
     */
    protected $fileBackend;
    
    /**
     * Constructs the object
     * 
     * @access public
     * @param \Zepi\Turbo\Backend\FileBackend $fileBackend
     */
    public function __construct(FileBackend $fileBackend)
    {
        $this->fileBackend = $fileBackend;
    }
    
    /**
     * Optimizes the given css content.
     * 
     * @access public
     * @param \Zepi\Web\General\Manager\AssetsManager $assetsManager
     * @param string $content
     * @param string $file
     * @return string
     */
    public function optimizeCssContent(AssetsManager $assetsManager, $content, $file)
    {
        $this->assetsManager = $assetsManager;
        
        $content = $this->optimizeUrls($content, $file);
        
        return $content;
    }
    
    /**
     * Optimizes the urls in the css content.
     * 
     * @access protected
     * @param string $content
     * @param string $file
     * @return string
     */
    protected function optimizeUrls($content, $file)
    {
        preg_match_all('/url\((.[^\)]*)\)/is', $content, $matches, PREG_SET_ORDER);

        foreach ($matches as $match) {
            $uri = $match[1];

            // Remove the apostrophs
            if (strpos($uri, '\'') === 0 || strpos($uri, '"') === 0) {
                $uri = substr($uri, 1, -1);
            }
            
            // If the uri is an absolute url we do not change anything
            if (strpos($uri, 'http') === 0) {
                continue;
            }
            
            $additionalData = $this->getAdditionalUriData($uri);
            $uri = $this->removeAdditionalUriData($uri);

            $path = dirname($file);
            $fullFilePath = realpath($path . '/' . $uri);
            $fileInformation = pathinfo($fullFilePath);
            
            $imageExtensions = array('png', 'jpg', 'jpeg', 'gif');

            if (in_array($fileInformation['extension'], $imageExtensions)) {
                // Load the file content
                $fileContent = $this->fileBackend->loadFromFile($fullFilePath);
                
                // Encode the file content
                $encodedContent = base64_encode($fileContent);
                $urlData = 'data:image/gif;base64,' . $encodedContent;
                
                // Replace the reference in the css content
                $content = str_replace($match[1], '\'' . $urlData . '\'', $content);
            } else {
                $type = AssetsManager::BINARY;
                
                // Cache the file
                $cachedFile = $this->assetsManager->generateCachedFile($type, $fullFilePath);
                $url = $this->assetsManager->getUrlToTheAssetLoader($cachedFile['file']);
    
                // Add the additional data
                if ($additionalData !== '') {
                    $url .= $additionalData;
                }

                // Replace the reference in the css content
                $content = str_replace($match[1], '\'' . $url . '\'', $content);
            }
        }

        return $content;
    }

    /**
     * Returns the additional uri data of the given uri.
     * 
     * @access protected
     * @param string $uri
     * @return string
     */
    protected function getAdditionalUriData($uri)
    {
        if (strpos($uri, '?') !== false) {
            return substr($uri, strpos($uri, '?'));
        } else if (strpos($uri, '#') !== false) {
            return substr($uri, strpos($uri, '#'));
        }
        
        return '';
    }
    
    /**
     * Returns the uri without any additional uri data.
     * 
     * @access protected
     * @param string $uri
     * @return string
     */
    protected function removeAdditionalUriData($uri)
    {
        if (strpos($uri, '?') !== false) {
            return substr($uri, 0, strpos($uri, '?'));
        } else if (strpos($uri, '#') !== false) {
            return substr($uri, 0, strpos($uri, '#'));
        }
        
        return $uri;
    }
}
