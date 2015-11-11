<?php
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
    protected $_type;
    
    /**
     * @access protected
     * @var string
     */
    protected $_assetName;
    
    /**
     * @access protected
     * @var string
     */
    protected $_fileName;
    
    /**
     * @access protected
     * @var array
     */
    protected $_dependencies = array();
    
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
        $this->_type = $type;
        $this->_assetName = $assetName;
        $this->_fileName = $fileName;
        $this->_dependencies = $dependencies;
    }
    
    /**
     * Returns the type of the asset
     * 
     * @access public
     * @return string
     */
    public function getType()
    {
        return $this->_type;
    }
    
    /**
     * Returns the name of the asset
     * 
     * @access public
     * @return string
     */
    public function getAssetName()
    {
        return $this->_assetName;
    }
    
    /**
     * Returns the file name of the asset
     * 
     * @access public
     * @return string
     */
    public function getFileName()
    {
        return $this->_fileName;
    }
    
    /**
     * Returns the dependencies of the asset
     * 
     * @access public
     * @return array
     */
    public function getDependencies()
    {
        return $this->_dependencies;
    }
    
    /**
     * Returns true if the asset has any dependencies
     * 
     * @access public
     * @return boolean
     */
    public function hasDependencies()
    {
        return (count($this->_dependencies) > 0);
    }
}
