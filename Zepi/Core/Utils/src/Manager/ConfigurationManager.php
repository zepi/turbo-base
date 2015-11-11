<?php
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
     * @param \Zepi\Core\Utils\Backend\ConfigurationFileBackend $objectBackend
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
     * @return string
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
