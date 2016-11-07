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
use \Zepi\Web\General\Manager\AssetManager;

/**
 * Default theme for zepi Turbo
 * 
 * @author Matthias Zobrist <matthias.zobrist@zepi.net>
 * @copyright Copyright (c) 2015 zepi
 */
class Module extends ModuleAbstract
{
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
        $assetManager = $this->framework->getInstance('\\Zepi\\Web\\General\\Manager\\AssetManager');
        $assetManager->addAsset(AssetManager::CSS, 'bootstrap', $this->directory . '/assets/vendor/bootstrap-3.3.7/css/bootstrap.css');
        $assetManager->addAsset(AssetManager::CSS, 'bootstrap-theme', $this->directory . '/assets/vendor/bootstrap-3.3.7/css/bootstrap-theme.css', array('bootstrap'));
        $assetManager->addAsset(AssetManager::CSS, 'materialdesignicons', $this->directory . '/assets/vendor/MaterialDesign/css/materialdesignicons.min.css');
        $assetManager->addAsset(AssetManager::CSS, 'zt-base', $this->directory . '/assets/css/base.css', array('bootstrap-theme'));
        $assetManager->addAsset(AssetManager::CSS, 'zt-theme', $this->directory . '/assets/css/theme.css', array('zt-base'));
        $assetManager->addAsset(AssetManager::CSS, 'zt-elements', $this->directory . '/assets/css/elements.css', array('zt-base'));
        $assetManager->addAsset(AssetManager::CSS, 'zt-form', $this->directory . '/assets/css/form.css', array('zt-base'));
        $assetManager->addAsset(AssetManager::CSS, 'zt-print', $this->directory . '/assets/css/print.css', array('zt-base'));
        
        $assetManager->addAsset(AssetManager::JS, 'modernizr', $this->directory . '/assets/js/vendor/modernizr-2.6.2-respond-1.1.0.min.js');
        $assetManager->addAsset(AssetManager::JS, 'jquery', $this->directory . '/assets/js/vendor/jquery-1.10.1.min.js');
        $assetManager->addAsset(AssetManager::JS, 'bootstrap', $this->directory . '/assets/vendor/bootstrap-3.3.7/js/bootstrap.min.js', array('jquery'));
        $assetManager->addAsset(AssetManager::JS, 'zt-main', $this->directory . '/assets/js/main.js', array('jquery'));
        
        $assetManager->addAsset(AssetManager::IMAGE, 'logo', $this->directory . '/assets/images/logo.svg');
        $assetManager->addAsset(AssetManager::IMAGE, 'logo-mail', $this->directory . '/assets/images/logo-mail.jpg');
        
        // Add the templates
        $templatesManager = $this->framework->getInstance('\\Zepi\\Web\\General\\Manager\\TemplatesManager');
        $templatesManager->addTemplate('\\Zepi\\Web\\ThemeZt\\Templates\\Header', $this->directory . '/templates/overall/Header.phtml');
        $templatesManager->addTemplate('\\Zepi\\Web\\ThemeZt\\Templates\\Footer', $this->directory . '/templates/overall/Footer.phtml');
        $templatesManager->addTemplate('\\Zepi\\Web\\ThemeZt\\Templates\\NavItemRoot', $this->directory . '/templates/snippets/NavItemRoot.phtml');
        $templatesManager->addTemplate('\\Zepi\\Web\\ThemeZt\\Templates\\NavItemSubmenu', $this->directory . '/templates/snippets/NavItemSubmenu.phtml');
        $templatesManager->addTemplate('\\Zepi\\Web\\ThemeZt\\Templates\\Mail\\Header', $this->directory . '/templates/mail/Header.phtml');
        $templatesManager->addTemplate('\\Zepi\\Web\\ThemeZt\\Templates\\Mail\\Footer', $this->directory . '/templates/mail/Footer.phtml');
        
        // Add the settings
        $configurationManager = $this->framework->getInstance('\\Zepi\\Core\\Utils\\Manager\\ConfigurationManager');
        $configurationManager->addSettingIfNotSet('web.defaultPage', '/');
        $configurationManager->saveConfigurationFile();
    }
}
