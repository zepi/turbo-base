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
 * This module delivers the frontend user interface for zepi Turbo
 * 
 * @package Zepi\Web\UserInterface
 * @author Matthias Zobrist <matthias.zobrist@zepi.net>
 * @copyright Copyright (c) 2015 zepi
 */

namespace Zepi\Web\UserInterface;

use \Zepi\Turbo\Module\ModuleAbstract;
use \Zepi\Web\General\Manager\AssetsManager;

/**
 * This module delivers the frontend user interface for zepi Turbo
 * 
 * @author Matthias Zobrist <matthias.zobrist@zepi.net>
 * @copyright Copyright (c) 2015 zepi
 */
class Module extends ModuleAbstract
{
    /**
     * @access protected
     * @var \Zepi\Web\UserInterface\Frontend\FrontendHelper
     */
    protected $frontendHelper;
    
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
            case '\\Zepi\\Web\\UserInterface\\Renderer\\Table':
                // Load the pagination renderer
                $paginationRenderer = $this->framework->getInstance('\\Zepi\\Web\\UserInterface\\Renderer\\Pagination');
                
                $layoutRenderer = new $className(
                    $paginationRenderer
                );
                
                return $layoutRenderer;
            break;
            
            case '\\Zepi\\Web\\UserInterface\\Renderer\\Layout':
                // Load the template manager
                $templatesManager = $this->framework->getInstance('\\Zepi\\Web\\General\\Manager\\TemplatesManager');
                
                $layoutRenderer = new $className(
                    $templatesManager
                );
                
                return $layoutRenderer;
            break;
            
            case '\\Zepi\\Web\\UserInterface\\Frontend\\FrontendHelper':
                if ($this->frontendHelper === null) {
                    $this->frontendHelper = new $className(
                        $this->framework->getInstance('\\Zepi\\Core\\Utils\\Manager\\ConfigurationManager'),
                        $this->framework->getInstance('\\Zepi\\Core\\Language\\Manager\\TranslationManager'),
                        $this->framework->getInstance('\\Zepi\\Web\\General\\Manager\\TemplatesManager'),
                        $this->framework->getInstance('\\Zepi\\Web\\General\\Manager\\MetaInformationManager'),
                        $this->framework->getInstance('\\Zepi\\Web\\General\\Manager\\MenuManager'),
                        $this->getInstance('\\Zepi\\Web\\UserInterface\\Renderer\\Layout'),
                        $this->getInstance('\\Zepi\\Web\\UserInterface\\Renderer\\OverviewPage'),
                        $this->getInstance('\\Zepi\\Web\\UserInterface\\Renderer\\Table')
                    );
                }
            
                return $this->frontendHelper;
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
        $templatesManager = $this->framework->getInstance('\\Zepi\\Web\\General\\Manager\\TemplatesManager');
        
        // Form
        $templatesManager->addTemplate('\\Zepi\\Web\\UserInterface\\Templates\\Form\\Form', $this->directory . '/templates/Form/form.phtml');
        $templatesManager->addTemplate('\\Zepi\\Web\\UserInterface\\Templates\\Form\\Group', $this->directory . '/templates/Form/group.phtml');
        $templatesManager->addTemplate('\\Zepi\\Web\\UserInterface\\Templates\\Form\\ButtonGroup', $this->directory . '/templates/Form/button-group.phtml');
        $templatesManager->addTemplate('\\Zepi\\Web\\UserInterface\\Templates\\Form\\ErrorBox', $this->directory . '/templates/Form/error-box.phtml');
        $templatesManager->addTemplate('\\Zepi\\Web\\UserInterface\\Templates\\Form\\Field\\Base', $this->directory . '/templates/Form/Field/base.phtml');
        $templatesManager->addTemplate('\\Zepi\\Web\\UserInterface\\Templates\\Form\\Field\\NoSpace', $this->directory . '/templates/Form/Field/noSpace.phtml');
        $templatesManager->addTemplate('\\Zepi\\Web\\UserInterface\\Templates\\Form\\Field\\Blank', $this->directory . '/templates/Form/Field/blank.phtml');
        $templatesManager->addTemplate('\\Zepi\\Web\\UserInterface\\Templates\\Form\\Field\\Abstract', $this->directory . '/templates/Form/Field/abstract.phtml');
        $templatesManager->addTemplate('\\Zepi\\Web\\UserInterface\\Templates\\Form\\Field\\Text', $this->directory . '/templates/Form/Field/text.phtml');
        $templatesManager->addTemplate('\\Zepi\\Web\\UserInterface\\Templates\\Form\\Field\\Email', $this->directory . '/templates/Form/Field/email.phtml');
        $templatesManager->addTemplate('\\Zepi\\Web\\UserInterface\\Templates\\Form\\Field\\Hidden', $this->directory . '/templates/Form/Field/hidden.phtml');
        $templatesManager->addTemplate('\\Zepi\\Web\\UserInterface\\Templates\\Form\\Field\\Number', $this->directory . '/templates/Form/Field/number.phtml');
        $templatesManager->addTemplate('\\Zepi\\Web\\UserInterface\\Templates\\Form\\Field\\UnitNumber', $this->directory . '/templates/Form/Field/unitnumber.phtml');
        $templatesManager->addTemplate('\\Zepi\\Web\\UserInterface\\Templates\\Form\\Field\\Price', $this->directory . '/templates/Form/Field/price.phtml');
        $templatesManager->addTemplate('\\Zepi\\Web\\UserInterface\\Templates\\Form\\Field\\Select', $this->directory . '/templates/Form/Field/select.phtml');
        $templatesManager->addTemplate('\\Zepi\\Web\\UserInterface\\Templates\\Form\\Field\\Checkbox', $this->directory . '/templates/Form/Field/checkbox.phtml');
        $templatesManager->addTemplate('\\Zepi\\Web\\UserInterface\\Templates\\Form\\Field\\Textarea', $this->directory . '/templates/Form/Field/textarea.phtml');
        $templatesManager->addTemplate('\\Zepi\\Web\\UserInterface\\Templates\\Form\\Field\\Password', $this->directory . '/templates/Form/Field/password.phtml');
        $templatesManager->addTemplate('\\Zepi\\Web\\UserInterface\\Templates\\Form\\Field\\Button', $this->directory . '/templates/Form/Field/button.phtml');
        $templatesManager->addTemplate('\\Zepi\\Web\\UserInterface\\Templates\\Form\\Field\\Selector', $this->directory . '/templates/Form/Field/selector.phtml');
        $templatesManager->addTemplate('\\Zepi\\Web\\UserInterface\\Templates\\Form\\Field\\DynamicZone', $this->directory . '/templates/Form/Field/dynamicZone.phtml');
        $templatesManager->addTemplate('\\Zepi\\Web\\UserInterface\\Templates\\Form\\Snippet\\SelectorItem', $this->directory . '/templates/Form/Snippet/selector-item.phtml');
        
        // Table
        $templatesManager->addTemplate('\\Zepi\\Web\\UserInterface\\Templates\\Table', $this->directory . '/templates/table.phtml');
        
        // Overview page
        $templatesManager->addTemplate('\\Zepi\\Web\\UserInterface\\Templates\\OverviewPageSection', $this->directory . '/templates/overviewpage.section.phtml');
        $templatesManager->addTemplate('\\Zepi\\Web\\UserInterface\\Templates\\OverviewPageItems', $this->directory . '/templates/overviewpage.items.phtml');
        
        // Pagination
        $templatesManager->addTemplate('\\Zepi\\Web\\UserInterface\\Templates\\Pagination', $this->directory . '/templates/pagination.phtml');
        
        // Layouts
        $templatesManager->addTemplate('\\Zepi\\Web\\UserInterface\\Templates\\Layout\\Page', $this->directory . '/templates/Layout/page.phtml');
        $templatesManager->addTemplate('\\Zepi\\Web\\UserInterface\\Templates\\Layout\\Row', $this->directory . '/templates/Layout/row.phtml');
        $templatesManager->addTemplate('\\Zepi\\Web\\UserInterface\\Templates\\Layout\\Column', $this->directory . '/templates/Layout/column.phtml');
        $templatesManager->addTemplate('\\Zepi\\Web\\UserInterface\\Templates\\Layout\\Tabs', $this->directory . '/templates/Layout/tabs.phtml');
        $templatesManager->addTemplate('\\Zepi\\Web\\UserInterface\\Templates\\Layout\\Tab', $this->directory . '/templates/Layout/tab.phtml');
        
        // Add the assets
        $assetsManager = $this->framework->getInstance('\\Zepi\\Web\\General\\Manager\\AssetsManager');
        $assetsManager->addAsset(AssetsManager::JS, 'ui-tabs', $this->directory . '/assets/js/tabs.js', array('zt-main'));
        $assetsManager->addAsset(AssetsManager::JS, 'ui-price', $this->directory . '/assets/js/price.js', array('zt-main'));
        $assetsManager->addAsset(AssetsManager::JS, 'ui-dynamic-zone', $this->directory . '/assets/js/dynamicZone.js', array('zt-main'));
        $assetsManager->addAsset(AssetsManager::JS, 'ui-jquery-mask', $this->directory . '/assets/js/jquery.maskMoney.js', array('zt-main'));
        
        $assetsManager->addAsset(AssetsManager::CSS, 'ui-form', $this->directory . '/assets/css/form.css', array('zt-form'));
        $assetsManager->addAsset(AssetsManager::JS, 'ui-selector', $this->directory . '/assets/js/selector.js', array('zt-main'));
        
        $assetsManager->addAsset(AssetsManager::CSS, 'ui-spin', $this->directory . '/assets/css/spin.css', array('zt-form'));

        $assetsManager->addAsset(AssetsManager::CSS, 'ui-jquery-is-loading-css', $this->directory . '/assets/css/is-loading.css', array('zt-form'));
        $assetsManager->addAsset(AssetsManager::JS, 'ui-jquery-is-loading', $this->directory . '/assets/js/is-loading/jquery.isloading.js', array('zt-main'));
        $assetsManager->addAsset(AssetsManager::JS, 'ui-loading-helper', $this->directory . '/assets/js/loadingHelper.js', array('ui-jquery-is-loading'));
        
        $assetsManager->addAsset(AssetsManager::CSS, 'ui-datatables-css', $this->directory . '/assets/vendor/DataTables/datatables.min.css', array('zt-form'));
        $assetsManager->addAsset(AssetsManager::JS, 'ui-datatables-js', $this->directory . '/assets/vendor/DataTables/datatables.js', array('zt-main'));
        $assetsManager->addAsset(AssetsManager::JS, 'ui-datatables-initialization-js', $this->directory . '/assets/js/dataTables.js', array('ui-datatables-js'));
    }
    
    /**
     * This action will be executed on the deactiviation of the module
     * 
     * @access public
     */
    public function deactivate()
    {
        $templatesManager = $this->framework->getInstance('\\Zepi\\Web\\General\\Manager\\TemplatesManager');
        
        // Form
        $templatesManager->removeTemplate('\\Zepi\\Web\\UserInterface\\Templates\\Form\\Form', $this->directory . '/templates/Form/form.phtml');
        $templatesManager->removeTemplate('\\Zepi\\Web\\UserInterface\\Templates\\Form\\Group', $this->directory . '/templates/Form/group.phtml');
        $templatesManager->removeTemplate('\\Zepi\\Web\\UserInterface\\Templates\\Form\\ButtonGroup', $this->directory . '/templates/Form/button-group.phtml');
        $templatesManager->removeTemplate('\\Zepi\\Web\\UserInterface\\Templates\\Form\\ErrorBox', $this->directory . '/templates/Form/error-box.phtml');
        $templatesManager->removeTemplate('\\Zepi\\Web\\UserInterface\\Templates\\Form\\Field\\Base', $this->directory . '/templates/Form/Field/base.phtml');
        $templatesManager->removeTemplate('\\Zepi\\Web\\UserInterface\\Templates\\Form\\Field\\NoSpace', $this->directory . '/templates/Form/Field/noSpace.phtml');
        $templatesManager->removeTemplate('\\Zepi\\Web\\UserInterface\\Templates\\Form\\Field\\Blank', $this->directory . '/templates/Form/Field/blank.phtml');
        $templatesManager->removeTemplate('\\Zepi\\Web\\UserInterface\\Templates\\Form\\Field\\Abstract', $this->directory . '/templates/Form/Field/abstract.phtml');
        $templatesManager->removeTemplate('\\Zepi\\Web\\UserInterface\\Templates\\Form\\Field\\Text', $this->directory . '/templates/Form/Field/text.phtml');
        $templatesManager->removeTemplate('\\Zepi\\Web\\UserInterface\\Templates\\Form\\Field\\Email', $this->directory . '/templates/Form/Field/email.phtml');
        $templatesManager->removeTemplate('\\Zepi\\Web\\UserInterface\\Templates\\Form\\Field\\Hidden', $this->directory . '/templates/Form/Field/hidden.phtml');
        $templatesManager->removeTemplate('\\Zepi\\Web\\UserInterface\\Templates\\Form\\Field\\Number', $this->directory . '/templates/Form/Field/number.phtml');
        $templatesManager->removeTemplate('\\Zepi\\Web\\UserInterface\\Templates\\Form\\Field\\UnitNumber', $this->directory . '/templates/Form/Field/unitnumber.phtml');
        $templatesManager->removeTemplate('\\Zepi\\Web\\UserInterface\\Templates\\Form\\Field\\Price', $this->directory . '/templates/Form/Field/price.phtml');
        $templatesManager->removeTemplate('\\Zepi\\Web\\UserInterface\\Templates\\Form\\Field\\Select', $this->directory . '/templates/Form/Field/select.phtml');
        $templatesManager->removeTemplate('\\Zepi\\Web\\UserInterface\\Templates\\Form\\Field\\Checkbox', $this->directory . '/templates/Form/Field/checkbox.phtml');
        $templatesManager->removeTemplate('\\Zepi\\Web\\UserInterface\\Templates\\Form\\Field\\Textarea', $this->directory . '/templates/Form/Field/textarea.phtml');
        $templatesManager->removeTemplate('\\Zepi\\Web\\UserInterface\\Templates\\Form\\Field\\Password', $this->directory . '/templates/Form/Field/password.phtml');
        $templatesManager->removeTemplate('\\Zepi\\Web\\UserInterface\\Templates\\Form\\Field\\Button', $this->directory . '/templates/Form/Field/button.phtml');
        $templatesManager->removeTemplate('\\Zepi\\Web\\UserInterface\\Templates\\Form\\Field\\Selector', $this->directory . '/templates/Form/Field/selector.phtml');
        $templatesManager->removeTemplate('\\Zepi\\Web\\UserInterface\\Templates\\Form\\Field\\DynamicZone', $this->directory . '/templates/Form/Field/dynamicZone.phtml');
        $templatesManager->removeTemplate('\\Zepi\\Web\\UserInterface\\Templates\\Form\\Snippet\\SelectorItem', $this->directory . '/templates/Form/Snippet/selector-item.phtml');
        
        // Table
        $templatesManager->removeTemplate('\\Zepi\\Web\\UserInterface\\Templates\\Table', $this->directory . '/templates/table.phtml');
        
        // Overview page
        $templatesManager->removeTemplate('\\Zepi\\Web\\UserInterface\\Templates\\OverviewPageSection', $this->directory . '/templates/overviewpage.section.phtml');
        $templatesManager->removeTemplate('\\Zepi\\Web\\UserInterface\\Templates\\OverviewPageItems', $this->directory . '/templates/overviewpage.items.phtml');
        
        // Pagination
        $templatesManager->removeTemplate('\\Zepi\\Web\\UserInterface\\Templates\\Pagination', $this->directory . '/templates/pagination.phtml');
        
        // Layouts
        $templatesManager->removeTemplate('\\Zepi\\Web\\UserInterface\\Templates\\Layout\\Page', $this->directory . '/templates/Layout/page.phtml');
        $templatesManager->removeTemplate('\\Zepi\\Web\\UserInterface\\Templates\\Layout\\Row', $this->directory . '/templates/Layout/row.phtml');
        $templatesManager->removeTemplate('\\Zepi\\Web\\UserInterface\\Templates\\Layout\\Column', $this->directory . '/templates/Layout/column.phtml');
        $templatesManager->removeTemplate('\\Zepi\\Web\\UserInterface\\Templates\\Layout\\Tabs', $this->directory . '/templates/Layout/tabs.phtml');
        $templatesManager->removeTemplate('\\Zepi\\Web\\UserInterface\\Templates\\Layout\\Tab', $this->directory . '/templates/Layout/tab.phtml');
        
        // Remove the assets
        $assetsManager = $this->framework->getInstance('\\Zepi\\Web\\General\\Manager\\AssetsManager');
        $assetsManager->removeAsset(AssetsManager::JS, 'ui-tabs');
        $assetsManager->removeAsset(AssetsManager::JS, 'ui-price');
        $assetsManager->removeAsset(AssetsManager::JS, 'ui-dynamic-zone');
        $assetsManager->removeAsset(AssetsManager::JS, 'ui-jquery-mask');

        $assetsManager->removeAsset(AssetsManager::CSS, 'ui-form');
        $assetsManager->removeAsset(AssetsManager::JS, 'ui-selector');
        
        $assetsManager->removeAsset(AssetsManager::CSS, 'ui-spin');
        
        $assetsManager->removeAsset(AssetsManager::CSS, 'ui-jquery-is-loading-css');
        $assetsManager->removeAsset(AssetsManager::JS, 'ui-jquery-is-loading');
        $assetsManager->removeAsset(AssetsManager::JS, 'ui-loading-helper');
    }
}
