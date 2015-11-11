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
 * Manages all assets like css, js, image or webfont files.
 * 
 * @package Zepi\Web\General
 * @subpackage Manager
 * @author Matthias Zobrist <matthias.zobrist@zepi.net>
 * @copyright Copyright (c) 2015 zepi
 */

namespace Zepi\Web\General\Manager;

use \Zepi\Turbo\Framework;
use \Zepi\Turbo\Backend\ObjectBackendAbstract;
use \Zepi\Turbo\Backend\FileBackend;
use \Zepi\Web\General\Helper\CssHelper;
use \Zepi\Web\General\Entity\Asset;

/**
 * Manages all assets like css, js, image or webfont files.
 * 
 * @author Matthias Zobrist <matthias.zobrist@zepi.net>
 * @copyright Copyright (c) 2015 zepi
 */
class AssetsManager
{
    const CSS = 'css';
    const JS = 'js';
    const IMAGE = 'image';
    const BINARY = 'binary';
    
    /**
     * @access protected
     * @param array
     */
    protected $_assetTypes = array(
        self::CSS => array('minify' => true),
        self::JS => array('minify' => true),
        self::IMAGE => array('minify' => false),
        self::BINARY => array('minify' => false)
    );
    
    /**
     * @access protected
     * @var array
     */
    protected $_assets = array();
    
    /**
     * @access protected
     * @var array
     */
    protected $_cachedFiles = array();
    
    /**
     * @access protected
     * @var \Zepi\Turbo\Framework
     */
    protected $_framework;
    
    /**
     * @access protected
     * @var \Zepi\Turbo\Backend\ObjectBackendAbstract
     */
    protected $_assetsObjectBackend;
    
    /**
     * @access protected
     * @var \Zepi\Turbo\Backend\ObjectBackendAbstract
     */
    protected $_cachedFilesObjectBackend;
    
    /**
     * @access protected
     * @var \Zepi\Turbo\Backend\FileBackend
     */
    protected $_fileBackend;
    
    /**
     * @access protected
     * @var \Zepi\Web\General\Helper\CssHelper
     */
    protected $_cssHelper;
    
    /**
     * @access protected
     * @var boolean
     */
    protected $_minifyAssets;
    
    /**
     * @access protected
     * @var boolean
     */
    protected $_combineAssets;
    
    /**
     * Constructs the object
     * 
     * @access public
     * @param \Zepi\Turbo\Framework $framework
     * @param \Zepi\Turbo\Backend\ObjectBackendAbstract $assetsObjectBackend
     * @param \Zepi\Turbo\Backend\ObjectBackendAbstract $cachedFilesObjectBackend
     * @param \Zepi\Turbo\Backend\FileBackend $fileBackend
     * @param \Zepi\Web\General\Helper\CssHelper $cssHelper
     * @param boolean $minifyAssets
     * @param boolean $combineAssets
     */
    public function __construct(
        Framework $framework,
        ObjectBackendAbstract $assetsObjectBackend, 
        ObjectBackendAbstract $cachedFilesObjectBackend, 
        FileBackend $fileBackend,
        CssHelper $cssHelper,
        $minifyAssets,
        $combineAssets
    ) {
        $this->_framework = $framework;
        $this->_assetsObjectBackend = $assetsObjectBackend;
        $this->_cachedFilesObjectBackend = $cachedFilesObjectBackend;
        $this->_fileBackend = $fileBackend;
        $this->_cssHelper = $cssHelper;
        
        $this->_minifyAssets = $minifyAssets;
        $this->_combineAssets = $combineAssets;
    }
    
    /**
     * Initializes the asset manager.
     * 
     * @access public
     */
    public function initializeAssetManager()
    {
        $this->_loadAssets();
        $this->_loadAssetsCache();
    }
    
    /**
     * Loads cache data for the assets
     * 
     * @access public
     */
    protected function _loadAssets()
    {
        $assets = $this->_assetsObjectBackend->loadObject();
        if (!is_array($assets)) {
            $assets = array();
        }
        
        $this->_assets = $assets;
    }
    
    /**
     * Saves the assets cache to the file backend.
     * 
     * @access public
     */
    protected function _saveAssets()
    {
        $this->_assetsObjectBackend->saveObject($this->_assets);
    }
    
    /**
     * Loads cache data for the assets
     * 
     * @access public
     */
    protected function _loadAssetsCache()
    {
        $cachedFiles = $this->_cachedFilesObjectBackend->loadObject();
        if (!is_array($cachedFiles)) {
            $cachedFiles = array();
        }
        
        $this->_cachedFiles = $cachedFiles;
    }
    
    /**
     * Saves the assets cache to the file backend.
     * 
     * @access public
     */
    protected function _saveAssetsCache()
    {
        $this->_cachedFilesObjectBackend->saveObject($this->_cachedFiles);
    }
    
    /**
     * Returns the file path for the given type, hash and version.
     * 
     * @access protected
     * @param string $type
     * @param string $hash
     * @param string $version
     * @return string
     */
    protected function _buildFilePath($type, $hash, $version)
    {
        return $type . '/' . $hash . '/' . $version;
    }
    
    /**
     * Returns true if the given file is cached, otherwise return false.
     * 
     * @access public
     * @param string $type
     * @param string $hash
     * @param string $version
     * @return boolean
     */
    public function isCached($type, $hash, $version)
    {
        // If the base file isn't cached we do not need to check the file names
        if (!isset($this->_cachedFiles[$hash])) {
            return false;
        }
        
        $searchedFile = $this->_buildFilePath($type, $hash, $version);
        foreach ($this->_cachedFiles as $baseFileHash => $cachedFile) {
            if ($cachedFile['file'] === $searchedFile) {
                return true;
            }
        }
        
        return false;
    }
    
    /**
     * Returns the content of the given type and file.
     * 
     * @access public
     * @param string $type
     * @param string $hash
     * @param string $version
     * @return boolean
     */
    public function getAssetContent($type, $hash, $version)
    {
        if (!$this->isCached($type, $hash, $version)) {
            return '';
        }
        
        $targetFile = $this->_buildFilePath($type, $hash, $version);
        return $this->_fileBackend->loadFromFile($targetFile);
    }
    
    /**
     * Returns the timestamp of the cached asset
     * 
     * @param string $type
     * @param string $hash
     * @param string $version
     * @return integer
     */
    public function getCachedAssetTimestamp($type, $hash, $version)
    {
        if (!$this->isCached($type, $hash, $version)) {
            return 0;
        }

        return $this->_cachedFiles[$hash]['timestamp'];
    }
    
    /**
     * Adds a file as an asset.
     * 
     * @access public
     * @param string $type
     * @param string $assetName
     * @param string $fileName
     * @param array $dependencies
     */
    public function addAsset($type, $assetName, $fileName, $dependencies = array())
    {
        if (!isset($this->_assets[$type]) || !is_array($this->_assets[$type])) {
            $this->_assets[$type] = array();
        }
        
        $asset = new Asset(
            $type,
            $assetName,
            $fileName,
            $dependencies
        );
        
        $this->_assets[$type][$assetName] = $asset;
        $this->_saveAssets();
    }
    
    /**
     * Adds a file as an asset.
     * 
     * @access public
     * @param string $type
     * @param string $fileName
     */
    public function removeAsset($type, $fileName)
    {
        // If the key doesn't exists return false
        if (!isset($this->_assets[$type][$fileName])) {
            return false;
        }
        
        // If the file was found remove the index
        unset($this->_assets[$type][$fileName]);
        
        return true;
    }
    
    /**
     * Make the cache for an asset type and display the html 
     * code to include the asset type.
     * 
     * @access public
     * @param string $type
     * @return string
     */
    public function displayAssetType($type)
    {
        $files = $this->_getAssetFiles($type);

        // If there are no files for the given type return here.
        if ($files === false) {
            return;
        }
        
        if ($this->_combineAssets) {
            $isValid = $this->_validateTypeCache($type, $files);

            // If the cache isn't valid, we build the cache
            $result = $isValid;
            if (!$isValid) {
                $result = $this->_buildTypeCache($type, $files);
            }
            
            // If the cache is valid, load the file
            if ($result) {
                $hash = $this->_getTypeHash($type);
                $cachedFile = $this->_cachedFiles[$hash]['file'];
                
                echo $this->_generateHtmlCode($type, $cachedFile);
            }
        } else {
            foreach ($files as $file) {
                $cachedFile = $this->generateCachedFile($type, $file->getFileName());
                
                if ($cachedFile !== false) {
                    echo $this->_generateHtmlCode($type, $cachedFile['file']);
                }
            }
        }
    }
    
    /**
     * Returns the url for one asset.
     * 
     * @access public
     * @param string $type
     * @param string $assetName
     * @return string
     */
    public function getAssetUrl($type, $assetName)
    {
        $file = $this->_getAssetFile($type, $assetName);
        
        if ($file === false) {
            return;
        }
        
        $cachedFile = $this->generateCachedFile($type, $file->getFileName());
        
        return $this->getUrlToTheAssetLoader($cachedFile['file']);
    }
    
    /**
     * Generates a cached file for the given type and base file.
     * 
     * @access public
     * @param string $type
     * @param string $fileName
     * @return array|false
     */
    public function generateCachedFile($type, $fileName)
    {
        $isValid = $this->_validateCache($fileName);
                
        // If the cache isn't valid, we build the cache
        $result = $isValid;
        if (!$isValid) {
            $result = $this->_buildFileCache($type, $fileName);
        }
        
        // If the cache is valid, load the file
        if ($result) {
            $hash = $this->_getHash($fileName);
            return $this->_cachedFiles[$hash];
        }
        
        return false;
    }

    
    /**
     * Returns the full url to the asset manager to load the
     * given file.
     * 
     * @access public
     * @param string $file
     * @return string
     */
    public function getUrlToTheAssetLoader($file)
    {
        return $this->_framework->getRequest()->getFullRoute('/assets/' . $file);
    }
    
    /**
     * Clears the asset cache.
     * 
     * @access public
     */
    public function clearAssetCache()
    {
        foreach ($this->_cachedFiles as $hash => $fileData) {
            if (!$this->_fileBackend->isWritable($fileData['file'])) {
                continue;
            }
        
            $this->_fileBackend->deleteFile($fileData['file']);
        }
        
        $this->_cachedFiles = array();
        $this->_saveAssetsCache();
    }

    /**
     * Builds the cache for an asset type
     * 
     * @access protected
     * @param string $type
     * @param array $files
     * @return boolean
     */
    protected function _buildTypeCache($type, $files)
    {
        $typeContent = '';
        $fileHashs = array();

        // Generate the content for all the files
        foreach ($files as $file) {
            $fileName = $file->getFileName();
            if (!file_exists($fileName)) {
                continue;
            }

            // Load the content
            $content = $this->_fileBackend->loadFromFile($fileName);
            
            // Optimze the content
            $content = $this->_optimizeContent($type, $fileName, $content);
            
            // Add the content and save the file hash
            $typeContent .= $content;
            $fileHashs[$this->_getHash($fileName)] = md5_file($fileName);
        }

        // Minify the content, if possible and activated
        if (($type === self::CSS || $type === self::JS) && $this->_minifyAssets) {
            $typeContent = $this->_minifyContent($type, $typeContent);
        }
        
        // Generate the hash and the new file name
        $hash = $this->_getTypeHash($type);
        $version = $type . '-' . uniqid() . '.' . $this->_getExtensionForType($type);
        $targetFile = $this->_buildFilePath($type, $hash, $version);

        // Save the cached file
        $this->_removeOldTypeCacheFile($type);
        $this->_fileBackend->saveToFile($typeContent, $targetFile);

        // Save the cached files in the data array
        $this->_cachedFiles[$hash] = array(
            'file' => $targetFile, 
            'checksums' => $fileHashs,
            'timestamp' => time()
        );
        $this->_saveAssetsCache();
        
        return $targetFile;
    }

    /**
     * Generates the cache for the given files.
     * 
     * @access protected
     * @param string $type
     * @param string $fileName
     * @return boolean
     */
    protected function _buildFileCache($type, $fileName)
    {
        if (!file_exists($fileName)) {
            return false;
        }
        
        // Load the content
        $content = $this->_fileBackend->loadFromFile($fileName);
        
        // Optimze the content
        $content = $this->_optimizeContent($type, $fileName, $content);
        
        // Minify the content, if possible and activated
        if (($type === self::CSS || $type === self::JS) && $this->_minifyAssets) {
            $content = $this->_minifyContent($type, $content);
        }
        
        // Generate the new file name
        $fileInfo = pathinfo($fileName);
        $hash = $this->_getHash($fileName);
        $version = $fileInfo['filename'] . '-' . uniqid() . '.' . $fileInfo['extension'];
        $targetFile = $this->_buildFilePath($type, $hash, $version);

        // Save the cached file
        $this->_removeOldCacheFile($fileName);
        $this->_fileBackend->saveToFile($content, $targetFile);

        // Save the cached files in the data array
        $this->_cachedFiles[$hash] = array(
            'file' => $targetFile, 
            'checksum' => md5_file($fileName),
            'timestamp' => time()
        );
        $this->_saveAssetsCache();
        
        return $targetFile;
    }

    /**
     * Optimizes the content. Example: inserts the content of 
     * the images in the css file and replaces all other files 
     * with the absolute url through the asset manager.
     * 
     * @access protected
     * @param string $type
     * @param string $file
     * @param string $content
     * @return string
     */
    protected function _optimizeContent($type, $file, $content)
    {
        if ($type === self::CSS) {
            $content = $this->_cssHelper->optimizeCssContent($this, $content, $file);
        }
        
        return $content;
    }
    
    /**
     * Returns the minified version of the content.
     * Attention: this is only a easy minifing method!
     * 
     * @access protected
     * @param string $type
     * @param string $content
     * @return string
     */
    protected function _minifyContent($type, $content)
    {
        $content = preg_replace("/((?:\/\*(?:[^*]|(?:\*+[^*\/]))*\*+\/)|(?:\s*\/\/\s.*))|(?:\s{1,}\/\/.*)/", "", $content);
        $content = str_replace(array("\r\n","\r","\t","\n",'  ','    ','     '), '', $content);
        $content = preg_replace(array('(( )+\))','(\)( )+)'), ')', $content);
        
        // Use these minifing rules only for css
        if ($type === self::CSS) {
            $content = str_replace('{ ', '{', $content);
            $content = str_replace(' }', '}', $content);
            $content = str_replace('; ', ';', $content);
            $content = str_replace(', ', ',', $content);
            $content = str_replace(' {', '{', $content);
            $content = str_replace('} ', '}', $content);
            $content = str_replace(': ', ':', $content);
            $content = str_replace(' ,', ',', $content);
            $content = str_replace(' ;', ';', $content);
        }

        return $content;
    }
    
    /**
     * Deletes the old type cache file before a new one is saved.
     * 
     * @access protected
     * @param string $type
     */
    protected function _removeOldTypeCacheFile($type)
    {
        $hash = $this->_getTypeHash($type);

        if (!isset($this->_cachedFiles[$hash])) {
            return false;
        }

        $fileData = $this->_cachedFiles[$hash];
        $this->_fileBackend->deleteFile($fileData['file']);
    }
    
    /**
     * Deletes the old cache file before a new one is saved.
     * 
     * @access protected
     * @param string $file
     */
    protected function _removeOldCacheFile($file)
    {
        $hash = $this->_getHash($file);
        if (!isset($this->_cachedFiles[$hash])) {
            return false;
        }

        $fileData = $this->_cachedFiles[$hash];
        $this->_fileBackend->deleteFile($fileData['file']);
    }
    
    /**
     * Returns false, if the cached version of the given
     * file isn't up to date.
     * 
     * @access protected
     * @param string $fileName
     * @return boolean
     */
    protected function _validateCache($fileName)
    {
        $hash = $this->_getHash($fileName);
        if (!file_exists($fileName) || !isset($this->_cachedFiles[$hash])) {
            return false;
        }
        
        $fileChecksum = md5_file($fileName);
        $cachedChecksum = $this->_cachedFiles[$hash]['checksum'];
        
        if ($fileChecksum != $cachedChecksum) {
            return false;
        }
        
        return true;
    }
    
    /**
     * Validates the cache for an asset type. Return false, if the
     * cache isn't valid.
     * 
     * @access protected
     * @param string $type
     * @param array $files
     * @return boolean
     */
    protected function _validateTypeCache($type, $files)
    {
        // If the files isn't cached we have to cache the file
        $hash = $this->_getTypeHash($type);
        if (!isset($this->_cachedFiles[$hash])) {
            return false;
        } 
        
        $data = $this->_cachedFiles[$hash];
        $checksums = $data['checksums'];

        // Are all files existing?
        foreach ($files as $file) {
            if (!file_exists($file->getFileName())) {
                return false;
            }
            
            $fileHash = $this->_getHash($file->getFileName());
            $contentHash = md5_file($file->getFileName());
            if (!isset($checksums[$fileHash]) || $contentHash !== $checksums[$fileHash]) {
                return false;
            }
        }
        
        return true;
    }
    
    /**
     * Sorts the files by dependencies
     * 
     * @access public 
     * @param array $assets
     * @return array
     */
    protected function _sortFilesByDependencies($assets)
    {
        $sortedAssets = array();
        foreach ($assets as $asset) {
            if ($asset->hasDependencies()) {
                $sortedAssets = $this->_resolveDependencies($sortedAssets, $assets, $asset);
            } else {
                $sortedAssets[$asset->getAssetName()] = $asset;
            }
        }
        
        return $sortedAssets;
    }

    /**
     * Returns the sorted assets with all dependencies
     * 
     * @access public
     * @param array $sortedAssets
     * @param array $assets
     * @param \Zepi\Web\General\Entity\Asset $asset
     * @return array
     */
    protected function _resolveDependencies($sortedAssets, $assets, $asset)
    {
        if ($asset->hasDependencies()) {
            foreach ($asset->getDependencies() as $dependency) {
                if (!isset($sortedAssets[$dependency]) && isset($assets[$dependency])) {
                    $sortedAssets = $this->_resolveDependencies($sortedAssets, $assets, $assets[$dependency]);
                }
            }
        }
        
        $sortedAssets[$asset->getAssetName()] = $asset;
        
        return $sortedAssets;
    }
    
    /**
     * Returns an array with all files for the given type, 
     * sorted by the priority of the assets.
     * 
     * @access protected
     * @param string $type
     * @return boolean|array
     */
    protected function _getAssetFiles($type)
    {
        if (!isset($this->_assets[$type])) {
            return false;
        }
        
        // Sort the files by dependnecies
        $files = $this->_sortFilesByDependencies($this->_assets[$type]);

        return $files;
    }
    
    /**
     * Returns the file for the given file name
     * or false if the file does not exist.
     *
     * @access protected
     * @param string $type
     * @param string $assetName
     * @return false|\Zepi\Web\General\Entity\Asset
     */
    protected function _getAssetFile($type, $assetName)
    {
        if (!isset($this->_assets[$type][$assetName])) {
            return false;
        }
    
        // Sort the files by dependnecies
        $file = $this->_assets[$type][$assetName];
    
        return $file;
    }
    
    /**
     * Returns the file extension for the given type.
     * 
     * @access protected
     * @param string $type
     * @return string
     */
    protected function _getExtensionForType($type)
    {
        if ($type === self::CSS) {
            return 'css';
        } else if ($type === self::JS) {
            return 'js';
        }
    }
    
    /**
     * Generates the html code for the given asset
     * 
     * @access protected
     * @param string $type
     * @param string $file
     * @return string
     */
    protected function _generateHtmlCode($type, $file)
    {
        // Generate the url
        $url = $this->getUrlToTheAssetLoader($file);
        
        // Return the correct html tag
        if ($type === self::CSS) {
            return '<link rel="stylesheet" type="text/css" href="' . $url . '">' . PHP_EOL;
        } else if ($type === self::JS) {
            return '<script type="text/javascript" src="' . $url . '"></script>' . PHP_EOL;
        }
    }
    
    /**
     * Returns the hash for the asset type.
     * 
     * @access protected
     * @param string $type
     * @return string
     */
    protected function _getTypeHash($type)
    {
        return md5($type);
    }
    
    /**
     * Returns the hash for the given file path.
     * 
     * @access protected
     * @param string $fileName
     * @return string
     */
    protected function _getHash($fileName)
    {
        return md5($fileName);
    }
}
