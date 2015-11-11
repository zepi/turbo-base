<?php
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
    protected $_assetsManager;
    
    /**
     * @access protected
     * @var FileBackend
     */
    protected $_fileBackend;
    
    /**
     * Constructs the object
     * 
     * @access public
     * @param \Zepi\Turbo\Backend\FileBackend $fileBackend
     */
    public function __construct(FileBackend $fileBackend)
    {
        $this->_fileBackend = $fileBackend;
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
        $this->_assetsManager = $assetsManager;
        
        $content = $this->_optimizeUrls($content, $file);
        
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
    protected function _optimizeUrls($content, $file)
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
            
            $additionalData = $this->_getAdditionalUriData($uri);
            $uri = $this->_removeAdditionalUriData($uri);

            $path = dirname($file);
            $fullFilePath = realpath($path . '/' . $uri);
            $fileInformation = pathinfo($fullFilePath);
            
            $imageExtensions = array('png', 'jpg', 'jpeg', 'gif');

            if (in_array($fileInformation['extension'], $imageExtensions)) {
                // Load the file content
                $fileContent = $this->_fileBackend->loadFromFile($fullFilePath);
                
                // Encode the file content
                $encodedContent = base64_encode($fileContent);
                $urlData = 'data:image/gif;base64,' . $encodedContent;
                
                // Replace the reference in the css content
                $content = str_replace($match[1], '\'' .$urlData . '\'', $content);
            } else {
                $type = AssetsManager::BINARY;
                
                // Cache the file
                $cachedFile = $this->_assetsManager->generateCachedFile($type, $fullFilePath);
                $url = $this->_assetsManager->getUrlToTheAssetLoader($cachedFile['file']);
    
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
    protected function _getAdditionalUriData($uri)
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
    protected function _removeAdditionalUriData($uri)
    {
        if (strpos($uri, '?') !== false) {
            return substr($uri, 0, strpos($uri, '?'));
        } else if (strpos($uri, '#') !== false) {
            return substr($uri, 0, strpos($uri, '#'));
        }
        
        return $uri;
    }
}
