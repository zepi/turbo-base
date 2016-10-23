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
 * The Asset representats an asset in the asset manager.
 * 
 * @package Zepi\Web\General
 * @subpackage Entity
 * @author Matthias Zobrist <matthias.zobrist@zepi.net>
 * @copyright Copyright (c) 2015 zepi
 */

namespace Zepi\Web\General\Entity;

/**
 * The Asset representats an asset in the asset manager.
 * 
 * @author Matthias Zobrist <matthias.zobrist@zepi.net>
 * @copyright Copyright (c) 2015 zepi
 */
class Asset
{
    /**
     * @access protected
     * @var string
     */
    protected $type;
    
    /**
     * @access protected
     * @var string
     */
    protected $assetName;
    
    /**
     * @access protected
     * @var string
     */
    protected $fileName;
    
    /**
     * @access protected
     * @var array
     */
    protected $dependencies = array();
    
    /**
     * Constructs the object
     * 
     * @access public
     * @param string $type
     * @param string $assetName
     * @param string $fileName
     * @param array $dependencies
     */
    public function __construct($type, $assetName, $fileName, $dependencies = array())
    {
        $this->type = $type;
        $this->assetName = $assetName;
        $this->fileName = $fileName;
        $this->dependencies = $dependencies;
    }
    
    /**
     * Returns the type of the asset
     * 
     * @access public
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }
    
    /**
     * Returns the name of the asset
     * 
     * @access public
     * @return string
     */
    public function getAssetName()
    {
        return $this->assetName;
    }
    
    /**
     * Returns the file name of the asset
     * 
     * @access public
     * @return string
     */
    public function getFileName()
    {
        return $this->fileName;
    }
    
    /**
     * Returns the dependencies of the asset
     * 
     * @access public
     * @return array
     */
    public function getDependencies()
    {
        return $this->dependencies;
    }
    
    /**
     * Returns true if the asset has any dependencies
     * 
     * @access public
     * @return boolean
     */
    public function hasDependencies()
    {
        return (count($this->dependencies) > 0);
    }
}
