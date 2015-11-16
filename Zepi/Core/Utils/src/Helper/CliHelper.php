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
 * CliHelper to process the user input
 * 
 * @package Zepi\Core\Utils
 * @subpackage Helper
 * @author Matthias Zobrist <matthias.zobrist@zepi.net>
 * @copyright Copyright (c) 2015 zepi
 */

namespace Zepi\Core\Utils\Helper;

/**
 * CliHelper to process the user input
 * 
 * @author Matthias Zobrist <matthias.zobrist@zepi.net>
 * @copyright Copyright (c) 2015 zepi
 */
class CliHelper
{
    /**
     * Asks the cli user for an answer (yes/no) and returns true if
     * the user wrote "yes" or false if the user wrote "no"
     * 
     * @access public
     * @param string $text
     * @param string $defaultValue
     * @return boolean
     */
    public function confirmAction($text, $defaultValue = 'yes')
    {
        echo $text . ' (yes/no) [' . $defaultValue . '] ';
        
        $handle = fopen('php://stdin', 'r');
        $line = fgets($handle);
        $preparedLine = strtolower(trim($line));
        
        if ($preparedLine === '') {
            $preparedLine = $defaultValue;
        }
        
        if ($preparedLine === 'yes' || $preparedLine === 'y') {
            return true;
        }
        
        return false;
    }
    
    /**
     * Asks the cli user for input text.
     *
     * @access public
     * @param string $text
     * @param string $defaultValue
     * @return string
     */
    public function inputText($text, $defaultValue = 'yes')
    {
        echo $text . ' [' . $defaultValue . '] ';
    
        $handle = fopen('php://stdin', 'r');
        $line = fgets($handle);
        $preparedLine = strtolower(trim($line));
    
        if ($preparedLine === '') {
            $preparedLine = $defaultValue;
        }
    
        return $preparedLine;
    }
    
    /**
     * Adds a new line to the output
     * 
     * @access public
     */
    public function newLine()
    {
        echo PHP_EOL;
    }
}
