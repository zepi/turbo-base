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
     * @var \Zepi\Web\General\Manager\AssetsManager
     */
    protected $assetsManager;
    
    /**
     * @access protected
     * @var \Zepi\Web\General\Manager\TemplatesManager
     */
    protected $templatesManager;
    
    /**
     * @access protected
     * @var \Zepi\Web\General\Manager\MenuManager
     */
    protected $menuManager;
    
    /**
     * @access protected
     * @var \Zepi\Web\General\Manager\MetaInformationManager
     */
    protected $metaInformationManager;
    
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
            case '\\Zepi\\Web\\General\\Manager\\AssetsManager':
                if ($this->assetsManager === null) {
                    // Get the assets backend
                    $path = $this->framework->getRootDirectory() . '/data/assets.data';
                    $assetsObjectBackend = new \Zepi\Turbo\Backend\FileObjectBackend($path);
                    
                    // Get the cache backends
                    $path = $this->directory . '/cache/';
                    $fileObjectBackend = new \Zepi\Turbo\Backend\FileObjectBackend($path . 'cachedFiles.data');
                    $fileBackend = new \Zepi\Turbo\Backend\FileBackend($path);
                    
                    // CSS helper
                    $cssHelper = new \Zepi\Web\General\Helper\CssHelper($fileBackend);
                    
                    // Load the configuration
                    $configurationManager = $this->framework->getInstance('\\Zepi\\Core\\Utils\\Manager\\ConfigurationManager');
                    $minifyAssets = $configurationManager->getSetting('assets', 'minifyAssets');
                    $combineAssetGroups = $configurationManager->getSetting('assets', 'combineAssetGroups');
                    
                    $this->assetsManager = new $className(
                        $this->framework, 
                        $assetsObjectBackend, 
                        $fileObjectBackend, 
                        $fileBackend,
                        $cssHelper,
                        $minifyAssets,
                        $combineAssetGroups
                    );
                    $this->assetsManager->initializeAssetManager();
                }
                
                return $this->assetsManager;
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
                if ($this->menuManager === null) {
                    $this->menuManager = new $className(
                        $this->framework
                    );
                }
                
                return $this->menuManager;
            break;
            
            case '\\Zepi\\Web\\General\\Manager\\MetaInformationManager':
                if ($this->metaInformationManager === null) {
                    $this->metaInformationManager = new $className();
                }
                
                return $this->metaInformationManager;
            break;
            
            case '\\Zepi\\Web\\General\\EventHandler\\Administration':
                return new $className($this->framework->getInstance('\\Zepi\\Web\\UserInterface\\Frontend\\FrontendHelper'));
            break;
            
            case '\\Zepi\\Web\\General\\EventHandler\\ClearAssetCache':
            case '\\Zepi\\Web\\General\\EventHandler\\DisplayAssets':
            case '\\Zepi\\Web\\General\\EventHandler\\LoadAssetContent':
                return new $className($this->getInstance('\\Zepi\\Web\\General\\Manager\\AssetsManager'));
            break;
            
            default: 
                return new $className();
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

        $menuEntry = new \Zepi\Web\General\Entity\MenuEntry(
            'administration',
            'Administration',
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
        
        $routeManager = $this->framework->getRouteManager();
        $routeManager->addRoute('assets|[s]|[s]|[s]', '\\Zepi\\Web\\General\\Event\\LoadAssetContent', 1);
        $routeManager->addRoute('assets|clearAssetCache', '\\Zepi\\Web\\General\\Event\\ClearAssetCache', 1);
        $routeManager->addRoute('administration', '\\Zepi\\Web\\General\\Event\\Administration', 1);
        
        $configurationManager = $this->framework->getInstance('\\Zepi\\Core\\Utils\\Manager\\ConfigurationManager');
        $configurationManager->addSettingIfNotSet('assets', 'minifyAssets', 'true');
        $configurationManager->addSettingIfNotSet('assets', 'combineAssetGroups', 'true');
        $configurationManager->saveConfigurationFile();
        
        $templatesManager = $this->framework->getInstance('\\Zepi\\Web\\General\\Manager\\TemplatesManager');
        $templatesManager->addTemplate('\\Zepi\\Web\\General\\Templates\\Administration', $this->directory . '/templates/Administration.phtml');
    }
}
