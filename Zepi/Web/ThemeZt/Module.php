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
 * Default theme for zepi Turbo
 * 
 * @package Zepi\Web\ThemeZt
 * @author Matthias Zobrist <matthias.zobrist@zepi.net>
 * @copyright Copyright (c) 2015 zepi
 */

namespace Zepi\Web\ThemeZt;

use \Zepi\Turbo\Module\ModuleAbstract;
use \Zepi\Web\General\Manager\AssetsManager;

/**
 * Default theme for zepi Turbo
 * 
 * @author Matthias Zobrist <matthias.zobrist@zepi.net>
 * @copyright Copyright (c) 2015 zepi
 */
class Module extends ModuleAbstract
{
    /**
     * Initializes and return an instance of the given class name.
     * 
     * @access public
     * @param string $className
     * @return mixed
     */
    public function getInstance($className)
    {
        
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
        // Add the assets
        $assetsManager = $this->_framework->getInstance('\\Zepi\\Web\\General\\Manager\\AssetsManager');
        $assetsManager->addAsset(AssetsManager::CSS, 'bootstrap', $this->_directory . '/assets/vendor/bootstrap-3.3.2/css/bootstrap.css');
        $assetsManager->addAsset(AssetsManager::CSS, 'bootstrap-theme', $this->_directory . '/assets/vendor/bootstrap-3.3.2/css/bootstrap-theme.css', array('bootstrap'));
        $assetsManager->addAsset(AssetsManager::CSS, 'materialdesignicons', $this->_directory . '/assets/vendor/MaterialDesign/css/materialdesignicons.min.css');
        $assetsManager->addAsset(AssetsManager::CSS, 'zt-base', $this->_directory . '/assets/css/base.css', array('bootstrap-theme'));
        $assetsManager->addAsset(AssetsManager::CSS, 'zt-theme', $this->_directory . '/assets/css/theme.css', array('zt-base'));
        $assetsManager->addAsset(AssetsManager::CSS, 'zt-elements', $this->_directory . '/assets/css/elements.css', array('zt-base'));
        $assetsManager->addAsset(AssetsManager::CSS, 'zt-form', $this->_directory . '/assets/css/form.css', array('zt-base'));
        $assetsManager->addAsset(AssetsManager::CSS, 'zt-print', $this->_directory . '/assets/css/print.css', array('zt-base'));
        
        $assetsManager->addAsset(AssetsManager::JS, 'modernizr', $this->_directory . '/assets/js/vendor/modernizr-2.6.2-respond-1.1.0.min.js');
        $assetsManager->addAsset(AssetsManager::JS, 'jquery', $this->_directory . '/assets/js/vendor/jquery-1.10.1.min.js');
        $assetsManager->addAsset(AssetsManager::JS, 'bootstrap', $this->_directory . '/assets/vendor/bootstrap-3.3.2/js/bootstrap.min.js', array('jquery'));
        $assetsManager->addAsset(AssetsManager::JS, 'zt-main', $this->_directory . '/assets/js/main.js', array('jquery'));
        
        $assetsManager->addAsset(AssetsManager::IMAGE, 'logo', $this->_directory . '/assets/images/logo.svg');
        
        // Add the templates
        $templatesManager = $this->_framework->getInstance('\\Zepi\\Web\\General\\Manager\\TemplatesManager');
        $templatesManager->addTemplate('\\Zepi\\Web\\ThemeZt\\Templates\\Header', $this->_directory . '/templates/overall/Header.phtml');
        $templatesManager->addTemplate('\\Zepi\\Web\\ThemeZt\\Templates\\Footer', $this->_directory . '/templates/overall/Footer.phtml');
        $templatesManager->addTemplate('\\Zepi\\Web\\ThemeZt\\Templates\\NavItemRoot', $this->_directory . '/templates/snippets/NavItemRoot.phtml');
        $templatesManager->addTemplate('\\Zepi\\Web\\ThemeZt\\Templates\\NavItemSubmenu', $this->_directory . '/templates/snippets/NavItemSubmenu.phtml');
    }
    
    /**
     * This action will be executed on the deactiviation of the module
     * 
     * @access public
     */
    public function deactivate()
    {
        // Remove the assets
        $assetsManager = $this->_framework->getInstance('\\Zepi\\Web\\General\\Manager\\AssetsManager');
        $assetsManager->removeAsset(AssetsManager::CSS, 'bootstrap');
        $assetsManager->removeAsset(AssetsManager::CSS, 'bootstrap-theme');
        $assetsManager->removeAsset(AssetsManager::CSS, 'materialdesignicons');
        $assetsManager->removeAsset(AssetsManager::CSS, 'zt2-base');
        $assetsManager->removeAsset(AssetsManager::CSS, 'zt2-theme');
        $assetsManager->removeAsset(AssetsManager::CSS, 'zt2-elements');
        $assetsManager->removeAsset(AssetsManager::CSS, 'zt2-form');
        $assetsManager->removeAsset(AssetsManager::CSS, 'zt2-print');

        $assetsManager->removeAsset(AssetsManager::JS, 'jquery');
        $assetsManager->removeAsset(AssetsManager::JS, 'modernizr');
        $assetsManager->removeAsset(AssetsManager::JS, 'bootstrap');
        $assetsManager->removeAsset(AssetsManager::JS, 'zt2-main');
        
        $assetsManager->removeAsset(AssetsManager::IMAGE, 'logo');
        
        // Remove the templates
        $templatesManager = $this->_framework->getInstance('\\Zepi\\Web\\General\\Manager\\TemplatesManager');
        $templatesManager->removeTemplate('\\Zepi\\Web\\ThemeZt\\Templates\\Header', $this->_directory . '/templates/overall/Header.phtml');
        $templatesManager->removeTemplate('\\Zepi\\Web\\ThemeZt\\Templates\\Footer', $this->_directory . '/templates/overall/Footer.phtml');
        $templatesManager->removeTemplate('\\Zepi\\Web\\ThemeZt\\Templates\\NavItemRoot', $this->_directory . '/templates/snippets/NavItemRoot.phtml');
        $templatesManager->removeTemplate('\\Zepi\\Web\\ThemeZt\\Templates\\NavItemSubmenu', $this->_directory . '/templates/snippets/NavItemSubmenu.phtml');
    }
}
