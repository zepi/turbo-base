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
 * Form Element Extended Entity Select
 * 
 * @package Zepi\Web\UserInterface
 * @subpackage Form\Field
 * @author Matthias Zobrist <matthias.zobrist@zepi.net>
 * @copyright Copyright (c) 2016 zepi
 */

namespace Zepi\Web\UserInterface\Form\Field;

use \Zepi\DataSource\Core\DataAccess\DataAccessInterface;
use \Zepi\Turbo\Request\WebRequest;
use \Zepi\Turbo\Response\Response;
use \Zepi\Web\UserInterface\Form\Form;
use \Zepi\DataSource\Core\Entity\DataRequest;
use \Zepi\DataSource\Core\Entity\Filter;

/**
 * Form Element Extended Entity Select
 * 
 * @author Matthias Zobrist <matthias.zobrist@zepi.net>
 * @copyright Copyright (c) 2016 zepi
 */
class ExtendedEntitySelect extends EntitySelect
{
    /**
     * @var array
     */
    protected $extendedOptions;
    
    /**
     * Constructs the object
     *
     * @access public
     * @param string $label
     * @param \Zepi\DataSource\Core\DataAccess\DataAccessInterface $dataSourceManager
     * @param string $fieldName
     * @param integer $maxNumberOfSelection
     * @param array $extendedOptions
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
        DataAccessInterface $dataSourceManager,
        $fieldName,
        $maxNumberOfSelection = 1,
        $extendedOptions = array(),
        $isMandatory = false,
        $value = null,
        $helpText = '',
        $classes = array(),
        $placeholder = '',
        $tabIndex = null
    ) {
        parent::__construct($key, $label, $dataSourceManager, $fieldName, $maxNumberOfSelection, $isMandatory, $value, $helpText, $classes, $placeholder, $tabIndex);
        
        $this->extendedOptions = $extendedOptions;
        $this->extendedOptions['valueField'] = 'id';
        $this->extendedOptions['labelField'] = $fieldName;
        $this->extendedOptions['searchField'] = $fieldName;
        $this->extendedOptions['load'] = '__ztExtendedSelectAjaxSearch';
        
        $this->classes[] = 'extended-select';
    }
    
    /**
     * Returns the extended options
     * 
     * @return array
     */
    public function getExtendedOptions()
    {
        return $this->extendedOptions;
    }
    
    /**
     * Executes the form update request
     *
     * @param \Zepi\Turbo\Request\WebRequest $request
     * @param \Zepi\Turbo\Response\Response $response
     * @param \Zepi\Web\UserInterface\Form\Form $form
     */
    public function executeFormUpdateRequest(WebRequest $request, Response $response, Form $form)
    {
        $responseData = array('csrf' => $form->generateCsrfToken($request));
        
        // If the search param not set return with an empty response
        if (!$request->hasParam('form-extended-entity-select-query')) {
            $responseData['data'] = array();
            $response->setOutput(json_encode($responseData), true);
            return;
        }
        
        $query = $request->getParam('form-extended-entity-select-query');
        $entities = $this->getAvailableValues($query);
        
        $data = array();
        foreach ($entities as $entity) {
            $data[] = array('id' => $entity->getId(), 'name' => (string) $entity);
        }
        
        $responseData['data'] = $data;
        
        $response->setOutput(json_encode($responseData), true);
    }
}
