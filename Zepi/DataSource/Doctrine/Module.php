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
 * @package Zepi\DataSource\Doctrine
 * @author Matthias Zobrist <matthias.zobrist@zepi.net>
 * @copyright Copyright (c) 2016 zepi
 */

namespace Zepi\DataSource\Doctrine;

use \Zepi\Turbo\Module\ModuleAbstract;
use \Doctrine\Common\Proxy\AbstractProxyFactory;
use \Zepi\Turbo\Request\WebRequest;

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
     * @var \Zepi\DataSource\Doctrine\Manager\EntityManager
     */
    protected $entityManager;
    
    /**
     * Initializes the module
     *
     * @access public
     */
    public function initialize()
    {
        $this->framework->getDataSourceManager()->addDefinition('*', '\\Zepi\\DataSource\\Doctrine');
    }
    
    protected function prepareCacheDirectory($environment)
    {
        $path = $this->framework->getRootDirectory() . '/cache/doctrine/';
        $request = $this->framework->getRequest();
        
        if ($environment === 'DEV') {
            if ($request instanceof WebRequest) {
                $path .= 'web/';
            } else {
                $path .= 'cli/';
            }
        }
        
        if (!file_exists($path)) {
            mkdir($path, 0755, true);
        }
        
        return $path;
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
            case '\\Zepi\\DataSource\\Doctrine\\Manager\\EntityManager':
                if ($this->entityManager === null) {
                    $configurationManager = $this->framework->getInstance('\\Zepi\\Core\\Utils\\Manager\\ConfigurationManager');
                    
                    $params = array(
                        'driver' => $configurationManager->getSetting('doctrine.databaseDriver'),
                        'host' => $configurationManager->getSetting('doctrine.databaseHost'),
                        'dbname' => $configurationManager->getSetting('doctrine.databaseName'),
                        'user' => $configurationManager->getSetting('doctrine.databaseUser'),
                        'password' => $configurationManager->getSetting('doctrine.databasePassword')
                    );
                    
                    $paths = array();
                    foreach ($this->framework->getModuleManager()->getModules() as $module) {
                        $paths[] = $module->getDirectory() . '/src/';
                    }
                    
                    $environment = strtoupper($configurationManager->getSetting('environment'));
                    $isDevMode = false;
                    if ($environment == 'DEV') {
                        $isDevMode = true;
                    }
                      
                    $config = \Doctrine\ORM\Tools\Setup::createAnnotationMetadataConfiguration($paths, $isDevMode);
                    
                    $path = $this->prepareCacheDirectory($environment);
                    $config->setProxyDir($path);
                    
                    if ($environment == 'DEV') {
                        $config->setAutoGenerateProxyClasses(AbstractProxyFactory::AUTOGENERATE_ALWAYS);
                    } else {
                        $config->setAutoGenerateProxyClasses(AbstractProxyFactory::AUTOGENERATE_NEVER);
                    }
                    
                    $doctrineEntityManager = \Doctrine\ORM\EntityManager::create($params, $config);
                    
                    $this->entityManager = new \Zepi\DataSource\Doctrine\Manager\EntityManager($doctrineEntityManager);
                }
                
                return $this->entityManager;
            break;
            
            default: 
                return $this->framework->initiateObject($className);
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
        $configurationManager = $this->framework->getInstance('\\Zepi\\Core\\Utils\\Manager\\ConfigurationManager');
        $configurationManager->addSettingIfNotSet('doctrine.databaseDriver', 'pdo_mysql');
        $configurationManager->addSettingIfNotSet('doctrine.databaseHost', 'localhost');
        $configurationManager->addSettingIfNotSet('doctrine.databaseName', '');
        $configurationManager->addSettingIfNotSet('doctrine.databaseUser', '');
        $configurationManager->addSettingIfNotSet('doctrine.databasePassword', '');
        $configurationManager->saveConfigurationFile();
        
        $runtimeManager = $this->framework->getRuntimeManager();
        $runtimeManager->addEventHandler('\\Zepi\\Installation\\ExecuteInstallation', '\\Zepi\\DataSource\\Doctrine\\EventHandler\\ExecuteInstallation', 51);
    }
}
