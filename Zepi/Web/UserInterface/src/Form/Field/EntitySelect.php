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
 * Form Element Entity Select
 * 
 * @package Zepi\Web\UserInterface
 * @subpackage Form\Field
 * @author Matthias Zobrist <matthias.zobrist@zepi.net>
 * @copyright Copyright (c) 2016 zepi
 */

namespace Zepi\Web\UserInterface\Form\Field;

use \Zepi\Turbo\Request\RequestAbstract;
use \Zepi\DataSource\Core\Manager\DataSourceManagerInterface;
use \Zepi\DataSource\Core\Entity\DataRequest;
use \Zepi\DataSource\Core\Entity\Filter;

/**
 * Form Element Entity Select
 * 
 * @author Matthias Zobrist <matthias.zobrist@zepi.net>
 * @copyright Copyright (c) 2016 zepi
 */
class EntitySelect extends FieldAbstract
{
    /**
     * @var \Zepi\DataSource\Core\Manager\DataSourceManagerInterface
     */
    protected $dataSourceManager = array();
    
    /**
     * @var string
     */
    protected $fieldName;
    
    /**
     * @var integer
     */
    protected $maxNumberOfSelection;
    
    /**
     * @var callable
     */
    protected $displayOptionCallback;
    
    /**
     * Constructs the object
     *
     * @access public
     * @param string $label
     * @param \Zepi\DataSource\Core\Manager\DataSourceManagerInterface $dataSourceManager
     * @param string $fieldName
     * @param integer $maxNumberOfSelection
     * @param callable $displayOptionCallback
     * @param boolean $isMandatory
     * @param array $value
     * @param string $helpText
     * @param array $classes
     * @param string $placeholder
     * @param integer $tabIndex
     */
    public function __construct(
        $key,
        $label,
        DataSourceManagerInterface $dataSourceManager,
        $fieldName,
        $maxNumberOfSelection = 1,
        $displayOptionCallback = false,
        $isMandatory = false,
        $value = array(),
        $helpText = '',
        $classes = array(),
        $placeholder = '',
        $tabIndex = null
    ) {
        $this->dataSourceManager = $dataSourceManager;
        $this->fieldName = $fieldName;
        $this->maxNumberOfSelection = $maxNumberOfSelection;
        $this->displayOptionCallback = $displayOptionCallback;
    
        parent::__construct($key, $label, $isMandatory, $value, $helpText, $classes, $placeholder, $tabIndex);
    }
    
    /**
     * Returns the name of the template to render the field
     * 
     * @access public
     * @return string
     */
    public function getTemplateName()
    {
        return '\\Zepi\\Web\\UserInterface\\Templates\\Form\\Field\\EntitySelect';
    }
    
    /**
     * Returns the available values
     * 
     * @access public
     * @return array
     */
    public function getAvailableValues($query = '')
    {
        $dataRequest = new DataRequest(0, 0, $this->fieldName, 'ASC');
        
        if ($query != '') {
            $dataRequest->addFilter(new Filter($this->fieldName, '*' . $query . '*', 'LIKE'));
        }
        
        $values = $this->dataSourceManager->find($dataRequest);
        
        return $values;
    }
    
    /**
     * Sets the html form value of the field
     *
     * @access public
     * @param mixed $value
     * @param \Zepi\Turbo\Request\RequestAbstract $request
     */
    public function setValue($value, RequestAbstract $request)
    {
        if (!$this->dataSourceManager->has($value)) {
            return;
        }
        
        if (is_array($value)) {
            $this->value = array();
            foreach ($value as $id) {
                $this->value[] = $this->dataSourceManager->get($id);
            }
        } else {
            $this->value = $this->dataSourceManager->get($value);
        }
    }
    
    /**
     * Returns the maximum number of selected entities
     * 
     * @return integer
     */
    public function getMaxNumberOfSelection()
    {
        return $this->maxNumberOfSelection;
    }
    
    /**
     * Returns true if the field contains the given entity as value
     * 
     * @param mixed $entity
     * @return boolean
     */
    public function hasEntity($entity)
    {
        if (is_array($this->value) && in_array($entity, $this->value)) {
            return true;
        } else if ($this->value === $entity) {
            return true;
        }
        
        return false;
    }
}
