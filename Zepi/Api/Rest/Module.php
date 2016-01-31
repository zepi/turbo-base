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
 * This module delivers the API helper for REST api's.
 * 
 * @package Zepi\Api\Rest
 * @author Matthias Zobrist <matthias.zobrist@zepi.net>
 * @copyright Copyright (c) 2016 zepi
 */

namespace Zepi\Api\Rest;

use \Zepi\Turbo\Module\ModuleAbstract;

/**
 * This module delivers the API helper for REST api's.
 * 
 * @author Matthias Zobrist <matthias.zobrist@zepi.net>
 * @copyright Copyright (c) 2015 zepi
 */
class Module extends ModuleAbstract
{
    /**
     * @access protected
     * @var \Zepi\Api\Rest\Helper\FrontendHelper
     */
    protected $_frontendHelper;
    
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
            case '\\Zepi\\Api\\Rest\\Helper\\FrontendHelper':
                if ($this->_frontendHelper === null) {
                    $this->_frontendHelper = new $className(
                        $this->_framework,
                        $this->_framework->getInstance('\\Zepi\\Core\\Utils\\Manager\\ConfigurationManager'),
                        $this->_framework->getInstance('\\Zepi\\Core\\Language\\Manager\\TranslationManager'),
                        $this->_framework->getInstance('\\Zepi\\Web\\General\\Manager\\TemplatesManager'),
                        $this->_framework->getInstance('\\Zepi\\Web\\General\\Manager\\MetaInformationManager'),
                        $this->_framework->getInstance('\\Zepi\\Web\\General\\Manager\\MenuManager'),
                        $this->_framework->getInstance('\\Zepi\\Web\\UserInterface\\Renderer\\Layout'),
                        $this->_framework->getInstance('\\Zepi\\Web\\UserInterface\\Renderer\\OverviewPage'),
                        $this->_framework->getInstance('\\Zepi\\Web\\UserInterface\\Renderer\\Table'),
                        $this->_framework->getInstance('\\Zepi\\Api\\Rest\\Helper\\RestHelper')
                    );
                }
            
                return $this->_frontendHelper;
            break;
            
            case '\\Zepi\\Api\\Rest\\Helper\\RestHelper':
                if ($this->_frontendHelper === null) {
                    $this->_frontendHelper = new $className(
                        $this->_framework->getInstance('\\Zepi\\Api\\AccessControl\\Manager\\TokenManager')
                    );
                }
            
                return $this->_frontendHelper;
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
        
    }
    
    /**
     * This action will be executed on the deactiviation of the module
     * 
     * @access public
     */
    public function deactivate()
    {
        
    }
}
