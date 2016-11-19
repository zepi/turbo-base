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
use \Zepi\Web\General\Manager\AssetManager;

/**
 * This module delivers the frontend user interface for zepi Turbo
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
        switch ($className) {
            case '\\Zepi\\Web\\UserInterface\\Frontend\\FrontendHelper':
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
        $templatesManager->addTemplate('\\Zepi\\Web\\UserInterface\\Templates\\Form\\Field\\IpField', $this->directory . '/templates/Form/Field/ip-field.phtml');
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
        $assetManager = $this->framework->getInstance('\\Zepi\\Web\\General\\Manager\\AssetManager');
        $assetManager->addAsset(AssetManager::JS, 'ui-tabs', $this->directory . '/assets/js/tabs.js', array('zt-main'));
        $assetManager->addAsset(AssetManager::JS, 'ui-responsive-tabs', $this->directory . '/assets/vendor/bootstrap-responsive-tabs/js/responsive-tabs.js', array('ui-tabs'));
        $assetManager->addAsset(AssetManager::JS, 'ui-price', $this->directory . '/assets/js/price.js', array('zt-main'));
        $assetManager->addAsset(AssetManager::JS, 'ui-dynamic-zone', $this->directory . '/assets/js/dynamicZone.js', array('zt-main'));
        $assetManager->addAsset(AssetManager::JS, 'ui-jquery-mask', $this->directory . '/assets/vendor/jquery-maskmoney/jquery.maskMoney.js', array('zt-main'));
        
        $assetManager->addAsset(AssetManager::CSS, 'ui-form', $this->directory . '/assets/css/form.css', array('zt-form'));
        $assetManager->addAsset(AssetManager::JS, 'ui-selector', $this->directory . '/assets/js/selector.js', array('zt-main'));
        $assetManager->addAsset(AssetManager::JS, 'ui-ip-field', $this->directory . '/assets/js/ip-field.js', array('zt-main'));
        
        $assetManager->addAsset(AssetManager::CSS, 'ui-spin', $this->directory . '/assets/css/spin.css', array('zt-form'));

        $assetManager->addAsset(AssetManager::CSS, 'ui-jquery-is-loading-css', $this->directory . '/assets/css/is-loading.css', array('ui-form'));
        $assetManager->addAsset(AssetManager::JS, 'ui-jquery-is-loading', $this->directory . '/assets/vendor/is-loading/jquery.isloading.js', array('zt-main'));
        $assetManager->addAsset(AssetManager::JS, 'ui-loading-helper', $this->directory . '/assets/js/loadingHelper.js', array('ui-jquery-is-loading'));
        
        $assetManager->addAsset(AssetManager::CSS, 'ui-datatables-css', 'vendor/datatables/datatables/media/css/dataTables.bootstrap.min.css', array('ui-form'));
        $assetManager->addAsset(AssetManager::CSS, 'ui-datatables-responsive-css', 'vendor/drmonty/datatables-responsive/css/dataTables.responsive.min.css', array('ui-datatables-css'));
        $assetManager->addAsset(AssetManager::CSS, 'ui-datatables-responsive-bootstrap-css', 'vendor/drmonty/datatables-responsive/css/responsive.bootstrap.min.css', array('ui-datatables-responsive-css'));
        $assetManager->addAsset(AssetManager::JS, 'ui-datatables-js', 'vendor/datatables/datatables/media/js/jquery.dataTables.js', array('zt-main'));
        $assetManager->addAsset(AssetManager::JS, 'ui-datatables-bootstrap-js', 'vendor/datatables/datatables/media/js/dataTables.bootstrap.js', array('ui-datatables-js'));
        $assetManager->addAsset(AssetManager::JS, 'ui-datatables-responsive-js', 'vendor/drmonty/datatables-responsive/js/dataTables.responsive.js', array('ui-datatables-bootstrap-js'));
        $assetManager->addAsset(AssetManager::JS, 'ui-datatables-responsive-bootstrap-js', 'vendor/drmonty/datatables-responsive/js/responsive.bootstrap.js', array('ui-datatables-responsive-js'));
        $assetManager->addAsset(AssetManager::JS, 'ui-datatables-initialization-js', $this->directory . '/assets/js/dataTables.js', array('ui-datatables-js'));
        
        // Register the event handler
        $runtimeManager = $this->framework->getRuntimeManager();
        $runtimeManager->addEventHandler('\\Zepi\\Web\\UserInterface\\Event\\LoadData', '\\Zepi\\Web\\UserInterface\\EventHandler\\LoadData');
        
        // Register the route
        $routeManager = $this->framework->getRouteManager();
        $routeManager->addRoute('user-interface|load-data|[s:token]', '\\Zepi\\Web\\UserInterface\\Event\\LoadData');
    }
}
