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
 * This module delivers the MySQL Database data source.
 * 
 * @package Zepi\DataSource\Mysql
 * @author Matthias Zobrist <matthias.zobrist@zepi.net>
 * @copyright Copyright (c) 2015 zepi
 */

namespace Zepi\DataSource\Mysql;

use \Zepi\Turbo\Module\ModuleAbstract;

/**
 * This module delivers the MySQL Database data source.
 * 
 * @author Matthias Zobrist <matthias.zobrist@zepi.net>
 * @copyright Copyright (c) 2015 zepi
 */
class Module extends ModuleAbstract
{
    /**
     * Initializes the module
     *
     * @access public
     */
    public function initialize()
    {
        $this->_framework->getDataSourceManager()->addDefinition('*', '\\Zepi\\DataSource\\Mysql');
    }
    
    
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
            case '\\Zepi\\DataSource\\Mysql\\Backend\\DatabaseBackend':
                $configurationManager = $this->_framework->getInstance('\\Zepi\\Core\\Utils\\Manager\\ConfigurationManager');
                
                $databaseHost = $configurationManager->getSetting('mysql', 'databaseHost');
                $databaseName = $configurationManager->getSetting('mysql', 'databaseName');
                $pdoDsn = 'mysql:dbname=' . $databaseName . ';host=' . $databaseHost;
                
                $databaseUser = $configurationManager->getSetting('mysql', 'databaseUser');
                $databasePassword = $configurationManager->getSetting('mysql', 'databasePassword');
                
                $pdo = new \Zepi\DataSource\Mysql\Wrapper\Pdo($pdoDsn, $databaseUser, $databasePassword);
                
                $databaseBackend = new $className($pdo);
                return $databaseBackend;
            break;
            
            default: 
                return new $className();
            break;
        }
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
        $configurationManager = $this->_framework->getInstance('\\Zepi\\Core\\Utils\\Manager\\ConfigurationManager');
        $configurationManager->addSettingIfNotSet('mysql', 'databaseHost', 'localhost');
        $configurationManager->addSettingIfNotSet('mysql', 'databaseName', '');
        $configurationManager->addSettingIfNotSet('mysql', 'databaseUser', '');
        $configurationManager->addSettingIfNotSet('mysql', 'databasePassword', '');
        $configurationManager->saveConfigurationFile();
    }
    
    /**
     * This action will be executed on the deactiviation of the module
     * 
     * @access public
     */
    public function deactivate()
    {
        $configurationManager = $this->_framework->getInstance('\\Zepi\\Core\\Utils\\Manager\\ConfigurationManager');
        $configurationManager->removeSettingGroup('mysql');
        $configurationManager->saveConfigurationFile();
    }
}
