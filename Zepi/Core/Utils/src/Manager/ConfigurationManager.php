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
    protected $configurationFileBackend;

    /**
     * @access protected
     * @var array
     */
    protected $settings;
    
    /**
     * Constructs the object
     * 
     * @access public
     * @param \Zepi\Core\Utils\Backend\ConfigurationFileBackend $configurationFileBackend
     */
    public function __construct(ConfigurationFileBackend $configurationFileBackend)
    {
        $this->configurationFileBackend = $configurationFileBackend;
    }
    
    /**
     * Loads the configuration file.
     * 
     * @access public
     */
    public function loadConfigurationFile()
    {
        $this->settings = $this->configurationFileBackend->loadConfiguration();
    }
    
    /**
     * Saves the settings to the configuration file
     * 
     * @access public
     */
    public function saveConfigurationFile()
    {
        $this->configurationFileBackend->saveConfiguration($this->settings);
    }
    
    /**
     * Returns all setting groups and settings
     *
     * @access public
     * @return array
     */
    public function getSettings()
    {
        return $this->settings;
    }
    
    /**
     * Returns the value of the given settings key.
     * 
     * @access public
     * @param string $path
     * @return boolean|string
     */
    public function getSetting($path)
    {
        if (!$this->hasSetting($path)) {
            return null;
        }
        
        $value = $this->resolvePath($path);
        
        return $value;
    }

    /**
     * Returns true if the given settings key exists.
     *
     * @access public
     * @param string $path
     * @return boolean
     */
    public function hasSetting($path)
    {
        $value = $this->resolvePath($path);
    
        if ($value === null) {
            return false;
        }
        
        return true;
    }
    
    /**
     * Resolves the path in the loaded settings
     * 
     * @param string $path
     * @return NULL|mixed
     */
    protected function resolvePath($path)
    {
        $parts = explode('.', $path);
        $settings = $this->settings;
        foreach ($parts as $part) {
            if (!isset($settings[$part])) {
                return null;
            }
        
            $settings = $settings[$part];
        }
        
        return $settings;
    }
    
    /**
    * Saves a setting in the configuration file.
    *
    * @access public
    * @param string $path
    * @param string $value
    */
    public function setSetting($path, $value)
    {
        if ($value === 'false') {
            $value = false;
        } else if ($value === 'true') {
            $value = true;
        } else if ($value === 'null') {
            $value = null;
        }
        
        $parts = explode('.', $path);
        $this->settings = $this->updateSetting($this->settings, $parts, $value);
    }
    
    /**
     * Updates the given setting and adds not-existing configuration
     * nodes to the configuration tree.
     * 
     * @param array $settings
     * @param array $parts
     * @param mixed $value
     * @return mixed
     */
    protected function updateSetting($settings, $parts, $value)
    {
        $part = current(array_slice($parts, 0, 1));

        if ((string) intval($part) == $part) {
            $part = intval($part);
        }
        
        if (count($parts) > 0) {
            $parts = array_slice($parts, 1);
            
            if (!isset($settings[$part])) {
                $settings[$part] = array();
            }

            $settings[$part] = $this->updateSetting($settings[$part], $parts, $value);
        } else {
            $settings = $value;
        }
        
        return $settings;
    }
        
    /**
     * Adds a setting key and value if the setting isn't set.
     * 
     * @access public
     * @param string $path
     * @param string $value
     */
    public function addSettingIfNotSet($path, $value)
    {
        if ($this->hasSetting($path)) {
            return;
        }

        $this->setSetting($path, $value);
    }
}
