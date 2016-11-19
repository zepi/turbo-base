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

use \Zepi\Turbo\Backend\ObjectBackendAbstract;
use \Zepi\Web\General\Entity\Asset;

/**
 * Manages all assets like css, js, image or webfont files.
 * 
 * @author Matthias Zobrist <matthias.zobrist@zepi.net>
 * @copyright Copyright (c) 2015 zepi
 */
class AssetManager
{
    const CSS = 'css';
    const JS = 'js';
    const IMAGE = 'image';
    const BINARY = 'binary';
    
    /**
     * @var array
     */
    protected $assets = array();
    
    /**
     * @var \Zepi\Turbo\Backend\ObjectBackendAbstract
     */
    protected $assetsObjectBackend;
    
    /**
     * @var string
     */
    protected $rootDirectory;
    
    /**
     * Constructs the object
     * 
     * @param \Zepi\Turbo\Backend\ObjectBackendAbstract $assetsObjectBackend
     * @param string $rootDirectory
     */
    public function __construct(ObjectBackendAbstract $assetsObjectBackend, $rootDirectory)
    {
        $this->assetsObjectBackend = $assetsObjectBackend;
        $this->rootDirectory = $rootDirectory;
    }
    
    /**
     * Initializes the asset manager.
     */
    public function initializeAssetManager()
    {
        $this->loadAssets();
    }
    
    /**
     * Loads cache data for the assets
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
     */
    protected function saveAssets()
    {
        $this->assetsObjectBackend->saveObject($this->assets);
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
            $this->buildSourceFilePath($fileName),
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
     * Sorts the files by dependencies
     * 
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
     * @param string $type
     * @return boolean|array
     */
    public function getAssetFiles($type)
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
     * @param string $type
     * @param string $assetName
     * @return false|\Zepi\Web\General\Entity\Asset
     */
    public function getAssetFile($type, $assetName)
    {
        if (!$this->hasAsset($type, $assetName)) {
            return false;
        }
    
        // Sort the files by dependnecies
        $file = $this->assets[$type][$assetName];
    
        return $file;
    }
    
    /**
     * Returns true if the given path is an absolute path
     *
     * @param string $filePath
     * @return boolean
     */
    protected function isAbsolutePath($filePath)
    {
        if ($filePath === null || $filePath === '') {
            return false;
        }
    
        if ($filePath[0] === DIRECTORY_SEPARATOR || preg_match('~\A[A-Z]:(?![^/\\\\])~i', $filePath) > 0) {
            return true;
        }
    
        return false;
    }
    
    /**
     * Returns the absolute path to the given (relative) file path.
     * The framework root directory is added if the file path is relative.
     *
     * @param string $filePath
     * @return string
     */
    protected function buildSourceFilePath($filePath)
    {
        if ($this->isAbsolutePath($filePath)) {
            return $filePath;
        }
    
        return $this->rootDirectory . '/' . $filePath;
    }
}
