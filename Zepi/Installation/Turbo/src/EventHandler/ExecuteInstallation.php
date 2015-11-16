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
 * Execute the installation of Turbo
 * 
 * @package Zepi\Installation\Turbo
 * @subpackage EventHandler
 * @author Matthias Zobrist <matthias.zobrist@zepi.net>
 * @copyright Copyright (c) 2015 zepi
 */

namespace Zepi\Installation\Turbo\EventHandler;

use \Zepi\Turbo\FrameworkInterface\CliEventHandlerInterface;
use \Zepi\Turbo\Framework;
use \Zepi\Turbo\Request\RequestAbstract;
use \Zepi\Turbo\Request\CliRequest;
use \Zepi\Turbo\Response\Response;
use \Zepi\Web\Test\Exception;

/**
 * Execute the installation of Turbo
 * 
 * @author Matthias Zobrist <matthias.zobrist@zepi.net>
 * @copyright Copyright (c) 2015 zepi
 */
class ExecuteInstallation implements CliEventHandlerInterface
{
    /**
     * @access protected
     * @var \Zepi\Core\Utils\Helper\CliHelper
     */
    protected $_cliHelper;
    
    /**
     * @access protected
     * @var \Zepi\Core\Utils\Manager\ConfigurationManager;
     */
    protected $_configurationManager;
    
    
    /**
     * Execute the installation of Turbo
     * 
     * @access public
     * @param \Zepi\Turbo\Framework $framework
     * @param \Zepi\Turbo\Request\CliRequest $request
     * @param \Zepi\Turbo\Response\Response $response
     */
    public function execute(Framework $framework, CliRequest $request, Response $response)
    {
        $this->_cliHelper = $framework->getInstance('\\Zepi\\Core\\Utils\\Helper\\CliHelper');
        $this->_configurationManager = $framework->getInstance('\\Zepi\\Core\\Utils\\Manager\\ConfigurationManager');
        
        // Configure turbo
        foreach ($this->_configurationManager->getSettings() as $settingGroupKey => $groupSettings) {
            $this->_configureGroup($settingGroupKey, $groupSettings);
            
            $this->_cliHelper->newLine();
        }
        
        // Save the settings
        $this->_configurationManager->saveConfigurationFile();
        
        // Execute the DataSource setups
        $dataSourceManager = $framework->getDataSourceManager();
        
    }
    
    /**
     * Configures one settings group
     * 
     * @access protected
     * @param string $settingGroupKey
     * @param array $groupSettings
     */
    protected function _configureGroup($settingGroupKey, $groupSettings)
    {
        $configureSettingGroup = $this->_cliHelper->confirmAction('Would you like to configure the configuration group "' . $settingGroupKey . '"?');
        if (!$configureSettingGroup) {
            return;
        }
        
        foreach ($groupSettings as $key => $value) {
            $this->_configureSetting($settingGroupKey, $key, $value);
        }
    }
    
    /**
     * Configures one setting
     * 
     * @access protected
     * @param string $settingGroupKey
     * @param string $key
     * @param string $value
     */
    protected function _configureSetting($settingGroupKey, $key, $value)
    {
        $newValue = $this->_cliHelper->inputText('Please enter the value for "' . $key . '":', $value);
        
        if ($newValue === $value) {
            return;
        }
        
        $this->_configurationManager->setSetting($settingGroupKey, $key, $newValue);
    }
}
