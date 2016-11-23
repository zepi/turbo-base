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
 * Table Renderer
 * 
 * @package Zepi\Web\UserInterface
 * @subpackage Renderer
 * @author Matthias Zobrist <matthias.zobrist@zepi.net>
 * @copyright Copyright (c) 2015 zepi
 */

namespace Zepi\Web\UserInterface\Renderer;

use \Zepi\Turbo\Request\WebRequest;
use \Zepi\Web\UserInterface\Table\TableAbstract;
use \Zepi\Web\UserInterface\Table\Head;
use \Zepi\Web\UserInterface\Table\Body;
use \Zepi\Web\UserInterface\Table\Foot;
use \Zepi\Web\UserInterface\Table\Row;
use \Zepi\Web\UserInterface\Table\Column;
use \Zepi\Web\UserInterface\Table\Cell;
use \Zepi\Web\UserInterface\Table\PreparedTable;
use \Zepi\Core\Utils\Entity\DataRequest;
use \Zepi\Core\Utils\Entity\Filter;

/**
 * Table Renderer
 * 
 * @author Matthias Zobrist <matthias.zobrist@zepi.net>
 * @copyright Copyright (c) 2015 zepi
 */
class Table
{
    /**
     * @access protected
     * @var integer
     */
    protected $routeParamIndex = 0;
    
    /**
     * Sets the route param index
     * 
     * @access public
     * @param integer $index
     */
    public function setRouteParamIndexForPage($index)
    {
        $this->routeParamIndex = $index;
    }
    
    /**
     * Renders the whole table
     * 
     * @access public
     * @param \Zepi\Turbo\Request\WebRequest $request
     * @param \Zepi\Web\UserInterface\Table\TableAbstract $table
     * @param string $paginationUrl
     * @param integer $numberOfEntries
     * @return \Zepi\Web\UserInterface\Table\PreparedTable
     */
    public function prepareTable(WebRequest $request, TableAbstract $table, $paginationUrl, $numberOfEntries = 10)
    {
        // If the table has no pagination we do not limit the number of entries 
        // which will be displayed
        if (!$table->hasPagination()) {
            $numberOfEntries = false;
        }
        
        // Generate the data request
        $dataRequest = $this->generateDataRequest($request, $table, $numberOfEntries);
        
        // Get the data
        $data = $table->getData($dataRequest);
        if (!is_array($data)) {
            $data = array();
        }

        $preparedTable = new PreparedTable($table, $table->getColumns());
        
        // Add the table head
        $preparedTable->setHead($this->renderHead($table, $dataRequest));
        
        // Render the body and add it to the rendered table
        $body = new Body();
        foreach ($data as $object) {
            $body->addRow($this->renderRow($table, $body, $object));
        }
        $preparedTable->setBody($body);
        
        // Add the table foot
        $preparedTable->setFoot($this->renderFoot($table));
        
        // Create the token
        $token = uniqid('dt');
        $preparedTable->setToken($token);
        $request->setSessionData('dt-class-' . $token, get_class($table));
        $request->setSessionData('dt-time-' . $token, time());
        
        return $preparedTable;
    }

    /**
     * Generates a DataRequest object
     * 
     * @access protected
     * @param \Zepi\Turbo\Request\WebRequest $request
     * @param \Zepi\Web\UserInterface\Table\TableAbstract $table
     * @param false|integer $numberOfEntries
     * @return \Zepi\Web\UserInterface\Table\DataRequest
     */
    protected function generateDataRequest(WebRequest $request, TableAbstract $table, $numberOfEntries)
    {
        $sortBy = 'name';
        $sortByDirection = 'ASC';
        
        // If the session has a data request object for the table, load it and refresh the data.
        $savedDataRequestKey = get_class($table) . '.DataRequest.Saved';
        $dataRequest = false;
        if ($table->shouldSaveDataRequest() && $request->getSessionData($savedDataRequestKey) !== false) {
            $dataRequest = unserialize($request->getSessionData($savedDataRequestKey));
        }
        
        // Check if the data request is valid
        if ($dataRequest === false) {
            $dataRequest = new DataRequest(1, $numberOfEntries, $sortBy, $sortByDirection);
        }
        
        // Save the data request to the session if needed
        if ($table->shouldSaveDataRequest()) {
            $request->setSessionData($savedDataRequestKey, serialize($dataRequest));
        }

        return $dataRequest;
    }
    
    /**
     * Generates the object structure for the head
     * 
     * @access protected
     * @param \Zepi\Web\UserInterface\Table\TableAbstract $table
     * @param \Zepi\Core\Utils\Entity\DataRequest $dataRequest
     * @return \Zepi\Web\UserInterface\Table\Head
     */
    protected function renderHead(TableAbstract $table, DataRequest $dataRequest)
    {
        $head = new Head();

        // Add the name row
        $row = new Row($head);
        foreach ($table->getColumns() as $column) {
            $cell = new Cell($column, $row, $column->getName());
            
            $row->addCell($cell);
        }
        
        $head->addRow($row);
        
        return $head;
    }
    
    /**
     * Renders the table row with the given row data
     * 
     * @access protected
     * @param \Zepi\Web\UserInterface\Table\TableAbstract $table
     * @param \Zepi\Web\UserInterface\Table\Body $body
     * @param mixed $object
     * @return \Zepi\Web\UserInterface\Table\Row
     */
    protected function renderRow(TableAbstract $table, Body $body, $object)
    {
        $row = new Row($body);
        foreach ($table->getColumns() as $column) {
            $value = $table->getDataForRow($column->getKey(), $object);
            
            $cell = new Cell($column, $row, $value);
            
            $row->addCell($cell);
        }
        
        return $row;
    }
    
    /**
     * Generates the object structure for the foot
     * 
     * @access protected
     * @param \Zepi\Web\UserInterface\Table\TableAbstract $table
     * @return \Zepi\Web\UserInterface\Table\Foot
     */
    protected function renderFoot(TableAbstract $table)
    {
        $foot = new Foot();
        
        return $foot;
    }
}
