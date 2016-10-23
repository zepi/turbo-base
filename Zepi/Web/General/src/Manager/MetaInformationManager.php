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
 * Manages template files and manages the renderer.
 * 
 * @package Zepi\Web\General
 * @subpackage Manager
 * @author Matthias Zobrist <matthias.zobrist@zepi.net>
 * @copyright Copyright (c) 2015 zepi
 */

namespace Zepi\Web\General\Manager;

use \Zepi\Turbo\Framework;
use \Zepi\Turbo\Request\RequestAbstract;
use \Zepi\Turbo\Response\Response;
use \Zepi\Turbo\Backend\ObjectBackendAbstract;
use \Zepi\Web\General\Template\RendererAbstract;

/**
 * Manages template files and manages the renderer.
 * 
 * @author Matthias Zobrist <matthias.zobrist@zepi.net>
 * @copyright Copyright (c) 2015 zepi
 */
class MetaInformationManager
{
    /**
     * @access protected
     * @var string
     */
    protected $title;
    
    /**
     * @access protected
     * @var string
     */
    protected $delimiter = ' - ';
    
    /**
     * @access protected
     * @var string
     */
    protected $name;
    
    /**
     * Sets the title for the request
     * 
     * @access public
     * @param string $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }
    
    /**
     * Sets the delimitier
     * 
     * @access public
     * @param string $delimiter
     */
    public function setDelimiter($delimiter)
    {
        $this->delimiter = $delimiter;
    }
    
    /**
     * Sets the name of the framework instance
     * 
     * @access public
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }
    
    /**
     * Returns the name of the framework instance
     * 
     * @access public
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }
    
    /**
     * Returns the title for the request
     * 
     * @access public
     * @return string
     */
    public function getTitle()
    {
        $title = '';

        /**
         * If there is a title, add it to the returned title
         */
        if (trim($this->title) != '') {
            $title = trim($this->title);
        }
        
        /**
         * If there is a name of the framework instance, add it to 
         * the returned title
         */
        if (trim($this->name) != '') {
            if (trim($title) != '') {
                $title .= $this->delimiter;
            }
            
            $title .= trim($this->name);
        }
        
        /**
         * If the title is empty, return a default title
         */
        if (trim($title) == '') {
            $title = 'zepi Turbo';
        }
        
        return $title;
    }
}
