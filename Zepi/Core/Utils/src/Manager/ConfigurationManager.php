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
 * Manages the configuration fields and the framework config file.
 * 
 * @package Zepi\Core\Utils
 * @subpackage Manager
 * @author Matthias Zobrist <matthias.zobrist@zepi.net>
 * @copyright Copyright (c) 2015 zepi
 */

namespace Zepi\Core\Utils\Manager;

use \Zepi\Core\Utils\Backend\ConfigurationFileBackend;

/**
 * Manages the configuration fields and the framework config file.
 * 
 * @author Matthias Zobrist <matthias.zobrist@zepi.net>
 * @copyright Copyright (c) 2015 zepi
 */
class ConfigurationManager
{
    /**
     * @access protected
     * @var \Zepi\Core\Utils\Backend\ConfigurationFileBackend
     */
    protected $_configurationFileBackend;

    /**
     * @access protected
     * @var array
     */
    protected $_settings = array();
    
    /**
     * Constructs the object
     * 
     * @access public
     * @param \Zepi\Core\Utils\Backend\ConfigurationFileBackend $configurationFileBackend
     */
    public function __construct(ConfigurationFileBackend $configurationFileBackend)
    {
        $this->_configurationFileBackend = $configurationFileBackend;
    }
    
    /**
     * Loads the configuration file.
     * 
     * @access public
     */
    public function loadConfigurationFile()
    {
        $this->_settings = $this->_configurationFileBackend->loadConfiguration();
    }
    
    /**
     * Saves the settings to the configuration file
     * 
     * @access public
     */
    public function saveConfigurationFile()
    {
        $this->_configurationFileBackend->saveConfiguration($this->_settings);
    }
    
    /**
     * Returns the value of the given settings key.
     * 
     * @access public
     * @param string $group
     * @param string $key
     * @return false|string
     */
    public function getSetting($group, $key)
    {
        if (!$this->hasSetting($group, $key)) {
            return false;
        }
        
        return $this->_settings[$group][$key];
    }
    
    /**
     * Returns true if the given settings key exists.
     * 
     * @access public
     * @param string $group
     * @param string $key
     * @return boolean
     */
    public function hasSetting($group, $key)
    {
        return (isset($this->_settings[$group][$key]));
    }
    
    /**
     * Saves a setting in the configuration file.
     * 
     * @access public
     * @param string $group
     * @param string $key
     * @param string $value
     */
    public function setSetting($group, $key, $value)
    {
        if (!isset($this->_settings[$group]) || !is_array($this->_settings[$group])) {
            $this->_settings[$group] = array();
        }
        
        $this->_settings[$group][$key] = $value;
    }
    
    /**
     * Adds a setting key and value if the setting isn't set.
     * 
     * @access public
     * @param string $group
     * @param string $key
     * @param string $value
     */
    public function addSettingIfNotSet($group, $key, $value)
    {
        if (isset($this->_settings[$group][$key])) {
            return;
        }
        
        $this->setSetting($group, $key, $value);
    }
    
    /**
     * Removes the given settings group from the settings.
     * 
     * @access public
     * @param string $group
     */
    public function removeSettingGroup($group)
    {
        if (!$this->hasSettingGroup($group)) {
            return false;
        }
        
        unset($this->_settings[$group]);
    }
    
    /**
     * Returns true if the given settings group exists.
     * 
     * @access public
     * @param string $group
     * @return boolean
     */
    public function hasSettingGroup($group)
    {
        return (isset($this->_settings[$group]));
    }
}
