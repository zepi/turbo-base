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
 * The ConfigurationFileBackend loads and saves a configuration file.
 * 
 * @package Zepi\Core\Utils
 * @subpackage Backend
 * @author Matthias Zobrist <matthias.zobrist@zepi.net>
 * @copyright Copyright (c) 2015 zepi
 */

namespace Zepi\Core\Utils\Backend;

use \Zepi\Core\Utils\Exception;

/**
 * The ConfigurationFileBackend loads and saves a configuration file.
 * 
 * @author Matthias Zobrist <matthias.zobrist@zepi.net>
 * @copyright Copyright (c) 2015 zepi
 */
class ConfigurationFileBackend
{
    /**
     * @access protected
     * @var string
     */
    protected $_path;
    
    /**
     * Constructs the object
     * 
     * @access public
     * @param string $path
     */
    public function __construct($path)
    {
        $this->_path = $path;
    }
    
    /**
     * Saves the configuration to the file.
     * 
     * @access public
     * @param array $settings
     * @return boolean
     * 
     * @throws Zepi\Turbo\Exception The file "$path" isn't writable!
     */
    public function saveConfiguration($settings)
    {
        if (file_exists($this->_path) && !is_writable($this->_path)) {
            throw new Exception('The file "' . $this->_path . '" isn\'t writable!');
        }
        
        $content = '';
        
        foreach ($settings as $groupKey => $groupSettings) {
            $content .= '[' . $groupKey . ']' . PHP_EOL;
            
            foreach ($groupSettings as $key => $value) {
                $content .= $key . ' = "' . $value . '"' . PHP_EOL;
            }
        }
        
        return file_put_contents($this->_path, $content);
    }
    
    /**
     * Loads the configuration from the file.
     * 
     * @access public
     * @return string
     * 
     * @throws Zepi\Turbo\Exception The file "$path" isn't readable!
     */
    public function loadConfiguration()
    {
        if (!file_exists($this->_path)) {
            return '';
        }
        
        if (!is_readable($this->_path)) {
            throw new Exception('The file "' . $this->_path . '" isn\'t readable!');
        }
        
        // Parse the ini file
        $settings = parse_ini_file($this->_path, true);
        if ($settings === false) {
            $settings = array();
        }
        
        // Transform the boolean values
        foreach ($settings as $settingGroup => $groupSettings) {
            if (!is_array($groupSettings)) {
                continue;
            }
            
            foreach ($groupSettings as $settingKey => $settingValue) {
                if ($settingValue === 'true') {
                    $settings[$settingGroup][$settingKey] = true;
                } else if ($settingValue === 'false') {
                    $settings[$settingGroup][$settingKey] = false;
                }
            }
        }
        
        return $settings;
    }
}
