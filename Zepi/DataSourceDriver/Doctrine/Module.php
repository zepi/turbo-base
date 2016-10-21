<?php
/*
 * The MIT License (MIT)
 *
 * Copyright (c) 2016 zepi
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
 * This module delivers the Doctrine data source.
 * 
 * @package Zepi\DataSourceDriver\Doctrine
 * @author Matthias Zobrist <matthias.zobrist@zepi.net>
 * @copyright Copyright (c) 2016 zepi
 */

namespace Zepi\DataSourceDriver\Doctrine;

use \Zepi\Turbo\Module\ModuleAbstract;

/**
 * This module delivers the Doctrine data source.
 * 
 * @author Matthias Zobrist <matthias.zobrist@zepi.net>
 * @copyright Copyright (c) 2016 zepi
 */
class Module extends ModuleAbstract
{
    /**
     * @access protected
     * @var \Doctrine\ORM\EntityManager
     */
    protected $_entityManager;
    
    /**
     * Initializes the module
     *
     * @access public
     */
    public function initialize()
    {
        $this->_framework->getDataSourceManager()->addDefinition('*', '\\Zepi\\DataSourceDriver\\Doctrine');
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
            case '\\Zepi\\DataSourceDriver\\Doctrine\\Manager\\EntityManager':
                if ($this->_entityManager === null) {
                    $configurationManager = $this->_framework->getInstance('\\Zepi\\Core\\Utils\\Manager\\ConfigurationManager');
                    
                    $params = array(
                        'driver' => $configurationManager->getSetting('doctrine', 'databaseDriver'),
                        'host' => $configurationManager->getSetting('doctrine', 'databaseHost'),
                        'dbname' => $configurationManager->getSetting('doctrine', 'databaseName'),
                        'user' => $configurationManager->getSetting('doctrine', 'databaseUser'),
                        'password' => $configurationManager->getSetting('doctrine', 'databasePassword')
                    );
                    
                    $paths = array();
                    foreach ($this->_framework->getModuleManager()->getModules() as $module) {
                        $paths[] = $module->getDirectory() . '/src/';
                    }
                    
                    $isDevMode = true;
                    
                    $config = \Doctrine\ORM\Tools\Setup::createAnnotationMetadataConfiguration($paths, $isDevMode);
                    $doctrineEntityManager = \Doctrine\ORM\EntityManager::create($params, $config);
                    
                    $this->_entityManager = new \Zepi\DataSourceDriver\Doctrine\Manager\EntityManager($doctrineEntityManager);
                }
                
                return $this->_entityManager;
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
        $configurationManager->addSettingIfNotSet('doctrine', 'databaseDriver', 'pdo_mysql');
        $configurationManager->addSettingIfNotSet('doctrine', 'databaseHost', 'localhost');
        $configurationManager->addSettingIfNotSet('doctrine', 'databaseName', '');
        $configurationManager->addSettingIfNotSet('doctrine', 'databaseUser', '');
        $configurationManager->addSettingIfNotSet('doctrine', 'databasePassword', '');
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
        $configurationManager->removeSettingGroup('doctrine');
        $configurationManager->saveConfigurationFile();
    }
}
