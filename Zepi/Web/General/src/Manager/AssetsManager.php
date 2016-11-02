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
    protected $assetTypes = array(
        self::CSS => array('minify' => true),
        self::JS => array('minify' => true),
        self::IMAGE => array('minify' => false),
        self::BINARY => array('minify' => false)
    );
    
    /**
     * @access protected
     * @var array
     */
    protected $assets = array();
    
    /**
     * @access protected
     * @var array
     */
    protected $cachedFiles = array();
    
    /**
     * @access protected
     * @var \Zepi\Turbo\Framework
     */
    protected $framework;
    
    /**
     * @access protected
     * @var \Zepi\Turbo\Backend\ObjectBackendAbstract
     */
    protected $assetsObjectBackend;
    
    /**
     * @access protected
     * @var \Zepi\Turbo\Backend\ObjectBackendAbstract
     */
    protected $cachedFilesObjectBackend;
    
    /**
     * @access protected
     * @var \Zepi\Turbo\Backend\FileBackend
     */
    protected $fileBackend;
    
    /**
     * @access protected
     * @var \Zepi\Web\General\Helper\CssHelper
     */
    protected $cssHelper;
    
    /**
     * @access protected
     * @var boolean
     */
    protected $minifyAssets;
    
    /**
     * @access protected
     * @var boolean
     */
    protected $combineAssets;
    
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
        $this->framework = $framework;
        $this->assetsObjectBackend = $assetsObjectBackend;
        $this->cachedFilesObjectBackend = $cachedFilesObjectBackend;
        $this->fileBackend = $fileBackend;
        $this->cssHelper = $cssHelper;
        
        $this->minifyAssets = $minifyAssets;
        $this->combineAssets = $combineAssets;
    }
    
    /**
     * Initializes the asset manager.
     * 
     * @access public
     */
    public function initializeAssetManager()
    {
        $this->loadAssets();
        $this->loadAssetsCache();
    }
    
    /**
     * Loads cache data for the assets
     * 
     * @access public
     */
    protected function loadAssets()
    {
        $assets = $this->assetsObjectBackend->loadObject();
        if (!is_array($assets)) {
            $assets = array();
        }
        
        $this->assets = $assets;
    }
    
    /**
     * Saves the assets cache to the file backend.
     * 
     * @access public
     */
    protected function saveAssets()
    {
        $this->assetsObjectBackend->saveObject($this->assets);
    }
    
    /**
     * Loads cache data for the assets
     * 
     * @access public
     */
    protected function loadAssetsCache()
    {
        $cachedFiles = $this->cachedFilesObjectBackend->loadObject();
        if (!is_array($cachedFiles)) {
            $cachedFiles = array();
        }
        
        $this->cachedFiles = $cachedFiles;
    }
    
    /**
     * Saves the assets cache to the file backend.
     * 
     * @access public
     */
    protected function saveAssetsCache()
    {
        $this->cachedFilesObjectBackend->saveObject($this->cachedFiles);
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
    protected function buildFilePath($type, $hash, $version)
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
        if (!isset($this->cachedFiles[$hash])) {
            return false;
        }
        
        $searchedFile = $this->buildFilePath($type, $hash, $version);
        foreach ($this->cachedFiles as $baseFileHash => $cachedFile) {
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
     * @return string
     */
    public function getAssetContent($type, $hash, $version)
    {
        if (!$this->isCached($type, $hash, $version)) {
            return '';
        }
        
        $targetFile = $this->buildFilePath($type, $hash, $version);
        return $this->fileBackend->loadFromFile($targetFile);
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

        return $this->cachedFiles[$hash]['timestamp'];
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
        if (!isset($this->assets[$type]) || !is_array($this->assets[$type])) {
            $this->assets[$type] = array();
        }
        
        $asset = new Asset(
            $type,
            $assetName,
            $fileName,
            $dependencies
        );
        
        $this->assets[$type][$assetName] = $asset;
        $this->saveAssets();
    }
    
    /**
     * Returns true if the asset for the given type
     * and file name exists.
     * 
     * @access public
     * @param string $type
     * @param string $fileName
     * @return boolean
     */
    public function hasAsset($type, $fileName)
    {
        return (isset($this->assets[$type][$fileName]));
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
        if (!$this->hasAsset($type, $fileName)) {
            return false;
        }
        
        // If the file was found remove the index
        unset($this->assets[$type][$fileName]);
        
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
        $files = $this->getAssetFiles($type);

        // If there are no files for the given type return here.
        if ($files === false) {
            return;
        }
        
        if ($this->combineAssets) {
            $isValid = $this->validateTypeCache($type, $files);

            // If the cache isn't valid, we build the cache
            $result = $isValid;
            if (!$isValid) {
                $result = $this->buildTypeCache($type, $files);
            }
            
            // If the cache is valid, load the file
            if ($result) {
                $hash = $this->getTypeHash($type);
                $cachedFile = $this->cachedFiles[$hash]['file'];
                
                echo $this->generateHtmlCode($type, $cachedFile);
            }
        } else {
            foreach ($files as $file) {
                $cachedFile = $this->generateCachedFile($type, $file->getFileName());
                
                if ($cachedFile !== false) {
                    echo $this->generateHtmlCode($type, $cachedFile['file']);
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
        $file = $this->getAssetFile($type, $assetName);
        
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
     * @return string|false
     */
    public function generateCachedFile($type, $fileName)
    {
        $isValid = $this->validateCache($fileName);
                
        // If the cache isn't valid, we build the cache
        $result = $isValid;
        if (!$isValid) {
            $result = $this->buildFileCache($type, $fileName);
        }
        
        // If the cache is valid, load the file
        if ($result) {
            $hash = $this->getHash($fileName);
            return $this->cachedFiles[$hash];
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
        return $this->framework->getRequest()->getFullRoute('/assets/' . $file);
    }
    
    /**
     * Clears the asset cache.
     * 
     * @access public
     */
    public function clearAssetCache()
    {
        foreach ($this->cachedFiles as $hash => $fileData) {
            if (!$this->fileBackend->isWritable($fileData['file'])) {
                continue;
            }
        
            $this->fileBackend->deleteFile($fileData['file']);
        }
        
        $this->cachedFiles = array();
        $this->saveAssetsCache();
    }

    /**
     * Builds the cache for an asset type
     * 
     * @access protected
     * @param string $type
     * @param array $files
     * @return string
     */
    protected function buildTypeCache($type, $files)
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
            $content = $this->fileBackend->loadFromFile($fileName);
            
            // Optimze the content
            $content = $this->optimizeContent($type, $fileName, $content);
            
            // Add the content and save the file hash
            $typeContent .= $content;
            $fileHashs[$this->getHash($fileName)] = md5_file($fileName);
        }

        // Minify the content, if possible and activated
        if (($type === self::CSS || $type === self::JS) && $this->minifyAssets) {
            $typeContent = $this->minifyContent($type, $typeContent);
        }
        
        // Generate the hash and the new file name
        $hash = $this->getTypeHash($type);
        $version = $type . '-' . uniqid() . '.' . $this->getExtensionForType($type);
        $targetFile = $this->buildFilePath($type, $hash, $version);

        // Save the cached file
        $this->removeOldTypeCacheFile($type);
        $this->fileBackend->saveToFile($typeContent, $targetFile);

        // Save the cached files in the data array
        $this->cachedFiles[$hash] = array(
            'file' => $targetFile, 
            'checksums' => $fileHashs,
            'timestamp' => time()
        );
        $this->saveAssetsCache();
        
        return $targetFile;
    }

    /**
     * Generates the cache for the given files.
     * 
     * @access protected
     * @param string $type
     * @param string $fileName
     * @return false|string
     */
    protected function buildFileCache($type, $fileName)
    {
        if (!file_exists($fileName)) {
            return false;
        }
        
        // Load the content
        $content = $this->fileBackend->loadFromFile($fileName);
        
        // Optimze the content
        $content = $this->optimizeContent($type, $fileName, $content);
        
        // Minify the content, if possible and activated
        if (($type === self::CSS || $type === self::JS) && $this->minifyAssets) {
            $content = $this->minifyContent($type, $content);
        }
        
        // Generate the new file name
        $fileInfo = pathinfo($fileName);
        $hash = $this->getHash($fileName);
        $version = $fileInfo['filename'] . '-' . uniqid() . '.' . $fileInfo['extension'];
        $targetFile = $this->buildFilePath($type, $hash, $version);

        // Save the cached file
        $this->removeOldCacheFile($fileName);
        $this->fileBackend->saveToFile($content, $targetFile);

        // Save the cached files in the data array
        $this->cachedFiles[$hash] = array(
            'file' => $targetFile, 
            'checksum' => md5_file($fileName),
            'timestamp' => time()
        );
        $this->saveAssetsCache();
        
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
    protected function optimizeContent($type, $file, $content)
    {
        if ($type === self::CSS) {
            $content = $this->cssHelper->optimizeCssContent($this, $content, $file);
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
    protected function minifyContent($type, $content)
    {
        if ($type === self::CSS) {
            $minifier = new \MatthiasMullie\Minify\CSS($content);
        } else if ($type === self::JS) {
            $minifier = new \MatthiasMullie\Minify\JS($content);
        }
        
        return $minifier->minify();
    }
    
    /**
     * Returns true if there is a cached file for the given
     * hash
     * 
     * @access protected
     * @param string $hash
     * @return boolean
     */
    protected function hasCachedFile($hash)
    {
        return (isset($this->cachedFiles[$hash]));
    }
    
    /**
     * Deletes the old cache file before a new one is saved.
     * 
     * @access protected
     * @param string $file
     */
    protected function removeOldCacheFile($file)
    {
        $hash = $this->getHash($file);
        $this->removeCacheFile($hash);
    }
    

    /**
     * Deletes the old type cache file before a new one is saved.
     *
     * @access protected
     * @param string $type
     */
    protected function removeOldTypeCacheFile($type)
    {
        $hash = $this->getTypeHash($type);
        $this->removeCacheFile($hash);
    }
    
    /**
     * Deletes the cache file for the given hash
     * 
     * @param string $hash
     * @return boolean
     */
    protected function removeCacheFile($hash)
    {
        if (!$this->hasCachedFile($hash)) {
            return false;
        }
        
        $fileData = $this->cachedFiles[$hash];
        $this->fileBackend->deleteFile($fileData['file']);
    }
    
    /**
     * Returns false, if the cached version of the given
     * file isn't up to date.
     * 
     * @access protected
     * @param string $fileName
     * @return boolean
     */
    protected function validateCache($fileName)
    {
        $hash = $this->getHash($fileName);
        if (!file_exists($fileName) || !isset($this->cachedFiles[$hash])) {
            return false;
        }
        
        $fileChecksum = md5_file($fileName);
        $cachedChecksum = $this->cachedFiles[$hash]['checksum'];
        
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
    protected function validateTypeCache($type, $files)
    {
        // If the files isn't cached we have to cache the file
        $hash = $this->getTypeHash($type);
        if (!isset($this->cachedFiles[$hash])) {
            return false;
        } 
        
        $data = $this->cachedFiles[$hash];
        $checksums = $data['checksums'];

        // Are all files existing?
        foreach ($files as $file) {
            if (!file_exists($file->getFileName())) {
                return false;
            }
            
            $fileHash = $this->getHash($file->getFileName());
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
    protected function sortFilesByDependencies($assets)
    {
        $sortedAssets = array();
        foreach ($assets as $asset) {
            if ($asset->hasDependencies()) {
                $sortedAssets = $this->resolveDependencies($sortedAssets, $assets, $asset);
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
    protected function resolveDependencies($sortedAssets, $assets, $asset)
    {
        if ($asset->hasDependencies()) {
            foreach ($asset->getDependencies() as $dependency) {
                if (!isset($sortedAssets[$dependency]) && isset($assets[$dependency])) {
                    $sortedAssets = $this->resolveDependencies($sortedAssets, $assets, $assets[$dependency]);
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
    protected function getAssetFiles($type)
    {
        if (!isset($this->assets[$type])) {
            return false;
        }
        
        // Sort the files by dependnecies
        $files = $this->sortFilesByDependencies($this->assets[$type]);

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
    protected function getAssetFile($type, $assetName)
    {
        if (!$this->hasAsset($type, $assetName)) {
            return false;
        }
    
        // Sort the files by dependnecies
        $file = $this->assets[$type][$assetName];
    
        return $file;
    }
    
    /**
     * Returns the file extension for the given type.
     * 
     * @access protected
     * @param string $type
     * @return string
     */
    protected function getExtensionForType($type)
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
    protected function generateHtmlCode($type, $file)
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
    protected function getTypeHash($type)
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
    protected function getHash($fileName)
    {
        return md5($fileName);
    }
}
