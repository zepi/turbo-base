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
use \Zepi\Core\Utils\Helper\CliHelper;
use \Zepi\Core\Utils\Manager\ConfigurationManager;

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
    protected $cliHelper;
    
    /**
     * @access protected
     * @var \Zepi\Core\Utils\Manager\ConfigurationManager
     */
    protected $configurationManager;
    
    /**
     * Constructs the object
     * 
     * @access public
     * @param \Zepi\Core\Utils\Helper\CliHelper $cliHelper
     * @param \Zepi\Core\Utils\Manager\ConfigurationManager $configurationManager
     */
    public function __construct(CliHelper $cliHelper, ConfigurationManager $configurationManager)
    {
        $this->cliHelper = $cliHelper;
        $this->configurationManager = $configurationManager;
    }
    
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
        // Configure turbo
        $this->configure();
        
        // Save the settings
        $this->configurationManager->saveConfigurationFile();
        
        // Execute the DataSource setups
        $dataSourceManager = $framework->getDataSourceManager();
        foreach ($dataSourceManager->getDataSourceTypeClasses() as $type) {
            $dataSource = $dataSourceManager->getDataSource($type);
            $dataSource->setup();
        }
    }
    
    /**
     * Iterates trough all configuration settings and asks the user for a
     * value.
     */
    protected function configure()
    {
        $configureSettingGroup = $this->cliHelper->confirmAction('Would you like to change the settings?');
        if (!$configureSettingGroup) {
            return;
        }
        
        $this->configureSettings($this->configurationManager->getSettings());
    }
    
    /**
     * Configures one settings group
     * 
     * @access protected
     * @param array $settings
     */
    protected function configureSettings($settings, $path = '')
    {
        foreach ($settings as $key => $node) {
            if (is_array($node)) {
                $this->configureSettings($node, $path . $key . '.');
            } else {
                $this->configureSetting($path . $key, $node);
            }
        }
    }
    
    /**
     * Configures one setting
     * 
     * @access protected
     * @param string $path
     * @param string $value
     */
    protected function configureSetting($path, $value)
    {
        $newValue = $this->cliHelper->inputText('Please enter the value for "' . $path . '":', $value);
        
        if ($newValue === $value) {
            return;
        }
        
        $this->configurationManager->setSetting($path, $newValue);
    }
}
