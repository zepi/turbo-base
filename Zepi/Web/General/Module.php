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
 * This module delivers some basic ressources like an asset manager or an css/js minifier.
 * 
 * @package Zepi\Web\General
 * @author Matthias Zobrist <matthias.zobrist@zepi.net>
 * @copyright Copyright (c) 2015 zepi
 */

namespace Zepi\Web\General;

use \Zepi\Turbo\Module\ModuleAbstract;
use \Zepi\Web\General\Entity\MenuEntry;

/**
 * This module delivers some basic ressources like an asset manager or an css/js minifier.
 * 
 * @author Matthias Zobrist <matthias.zobrist@zepi.net>
 * @copyright Copyright (c) 2015 zepi
 */
class Module extends ModuleAbstract
{
    /**
     * @access protected
     * @var \Zepi\Web\General\Manager\AssetManager
     */
    protected $assetManager;
    
    /**
     * @access protected
     * @var \Zepi\Web\General\Manager\AssetCacheManager
     */
    protected $assetCacheManager;
    
    /**
     * @access protected
     * @var \Zepi\Web\General\Manager\TemplatesManager
     */
    protected $templatesManager;
    
    /**
     * Initializes and return an instance of the given class name.
     * 
     * @access public
     * @param string $className
     * @return mixed
     */
    public function getInstance($className)
    {
        switch ($className) {
            case '\\Zepi\\Web\\General\\Manager\\AssetManager':
                if ($this->assetManager === null) {
                    // Get the assets backend
                    $path = $this->framework->getRootDirectory() . '/data/assets.data';
                    $assetsObjectBackend = new \Zepi\Turbo\Backend\FileObjectBackend($path);

                    $this->assetManager = new $className(
                        $assetsObjectBackend
                    );
                    $this->assetManager->initializeAssetManager();
                }
                
                return $this->assetManager;
            break;
            
            case '\\Zepi\\Web\\General\\Manager\\AssetCacheManager':
                if ($this->assetCacheManager === null) {
                    // Get the cache backends
                    $path = $this->directory . '/cache/';
                    $fileObjectBackend = new \Zepi\Turbo\Backend\FileObjectBackend($path . 'cachedFiles.data');
                    $fileBackend = new \Zepi\Turbo\Backend\FileBackend($path);
            
                    // CSS helper
                    $cssHelper = new \Zepi\Web\General\Helper\CssHelper($fileBackend);
            
                    // Load the configuration
                    $configurationManager = $this->framework->getInstance('\\Zepi\\Core\\Utils\\Manager\\ConfigurationManager');
                    $minifyAssets = $configurationManager->getSetting('assets.minifyAssets');
                    $combineAssetGroups = $configurationManager->getSetting('assets.combineAssetGroups');
            
                    $this->assetCacheManager = new $className(
                        $this->framework,
                        $this->getInstance('\\Zepi\\Web\\General\\Manager\\AssetManager'),
                        $fileObjectBackend,
                        $fileBackend,
                        $cssHelper,
                        $minifyAssets,
                        $combineAssetGroups
                    );
                    $this->assetCacheManager->initializeAssetCacheManager();
                }
            
                return $this->assetCacheManager;
            break;
            
            case '\\Zepi\\Web\\General\\Manager\\TemplatesManager':
                if ($this->templatesManager === null) {
                    // Get the templates backend
                    $path = $this->framework->getRootDirectory() . '/data/templates.data';
                    $assetsObjectBackend = new \Zepi\Turbo\Backend\FileObjectBackend($path);
                    
                    $this->templatesManager = new $className(
                        $this->framework, 
                        $assetsObjectBackend
                    );
                    $this->templatesManager->initializeTemplatesManager();
                    
                    // Execute the register renderer event
                    $runtimeManager = $this->framework->getRuntimeManager();
                    $runtimeManager->executeEvent('\\Zepi\\Web\\General\\Event\\RegisterRenderers');
                }
                
                return $this->templatesManager;
            break;
            
            case '\\Zepi\\Web\\General\\Manager\\MenuManager':
            case '\\Zepi\\Web\\General\\Manager\\MetaInformationManager':
                return $this->framework->initiateObject($className, array(), true);
            break;
            
            default: 
                return $this->framework->initiateObject($className);
            break;
        }
    }
    
    /**
     * Initializes the module
     * 
     * @access public
     */
    public function initialize()
    {
        $menuManager = $this->framework->getInstance('\\Zepi\\Web\\General\\Manager\\MenuManager');

        $menuEntry = new \Zepi\Web\General\Entity\ProtectedMenuEntry(
            'administration',
            'Administration',
            '\\Global\\Administrator',
            'administration',
            'mdi-settings',
            '',
            'self',
            true
        );
        $menuManager->addMenuEntry('menu-right', $menuEntry);
    }
    
    /**
     * This action will be executed on the activation of the module
     * 
     * @access public
     * @param string $versionNumber
     * @param string $oldVersionNumber
     */
    public function activate($versionNumber, $oldVersionNumber = '')
    {
        $runtimeManager = $this->framework->getRuntimeManager();
        $runtimeManager->addEventHandler('\\Zepi\\Web\\General\\Event\\DisplayAssets', '\\Zepi\\Web\\General\\EventHandler\\DisplayAssets');
        $runtimeManager->addEventHandler('\\Zepi\\Web\\General\\Event\\LoadAssetContent', '\\Zepi\\Web\\General\\EventHandler\\LoadAssetContent');
        $runtimeManager->addEventHandler('\\Zepi\\Web\\General\\Event\\ClearAssetCache', '\\Zepi\\Web\\General\\EventHandler\\ClearAssetCache');
        $runtimeManager->addEventHandler('\\Zepi\\Web\\General\\Event\\Administration', '\\Zepi\\Web\\General\\EventHandler\\Administration');
        
        $runtimeManager->addFilterHandler('\\Zepi\\Turbo\\Filter\\VerifyEventName', '\\Zepi\\Web\\General\\FilterHandler\\VerifyEventName');
        $runtimeManager->addFilterHandler('\\Zepi\\Web\\General\\Filter\\MenuManager\\FilterMenuEntries', '\\Zepi\\Web\\General\\FilterHandler\\FilterMenuEntriesForProtectedEntries');
        
        // Add the permissions
        $eventAccessManager = $this->framework->getInstance('\\Zepi\\Core\\AccessControl\\Manager\\EventAccessManager');
        $eventAccessManager->addItem('\\Zepi\\Web\\General\\Event\\Administration', '\\Global\\Administrator');
        
        $routeManager = $this->framework->getRouteManager();
        $routeManager->addRoute('assets|[s:type]|[s:hash]|[s:version]', '\\Zepi\\Web\\General\\Event\\LoadAssetContent', 1);
        $routeManager->addRoute('assets|clearAssetCache', '\\Zepi\\Web\\General\\Event\\ClearAssetCache', 1);
        $routeManager->addRoute('administration', '\\Zepi\\Web\\General\\Event\\Administration', 1);
        
        $configurationManager = $this->framework->getInstance('\\Zepi\\Core\\Utils\\Manager\\ConfigurationManager');
        $configurationManager->addSettingIfNotSet('assets.minifyAssets', 'true');
        $configurationManager->addSettingIfNotSet('assets.combineAssetGroups', 'true');
        $configurationManager->saveConfigurationFile();
        
        $templatesManager = $this->framework->getInstance('\\Zepi\\Web\\General\\Manager\\TemplatesManager');
        $templatesManager->addTemplate('\\Zepi\\Web\\General\\Templates\\Administration', $this->directory . '/templates/Administration.phtml');
        
        // Register the access level
        $accessLevelsManager = $this->framework->getInstance('\\Zepi\\Core\\AccessControl\\Manager\\AccessLevelManager');
        $accessLevelsManager->addAccessLevel(new \Zepi\Core\AccessControl\Entity\AccessLevel(
            '\\Global\\Administrator',
            'Administrator',
            'Can view the administrator overview page.',
            '\\Zepi\\Web\\AccessControl'
        ));
    }
}
