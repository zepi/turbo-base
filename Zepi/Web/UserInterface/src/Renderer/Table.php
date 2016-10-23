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

use \Zepi\Turbo\Framework;
use \Zepi\Turbo\Request\WebRequest;
use \Zepi\Web\UserInterface\Table\TableAbstract;
use \Zepi\Web\UserInterface\Table\Head;
use \Zepi\Web\UserInterface\Table\Body;
use \Zepi\Web\UserInterface\Table\Foot;
use \Zepi\Web\UserInterface\Table\Row;
use \Zepi\Web\UserInterface\Table\FilterRow;
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
     * @var \Zepi\Web\UserInterface\Renderer\Pagination
     */
    protected $paginationRenderer;
    
    /**
     * @access protected
     * @var integer
     */
    protected $routeParamIndex = 0;
    
    /**
     * Constructs the object
     * 
     * @access public
     * @param \Zepi\Web\UserInterface\Renderer\Pagination
     */
    public function __construct(Pagination $paginationRenderer)
    {
        $this->paginationRenderer = $paginationRenderer;
    }
    
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
        
        // Add the pagination
        if ($table->hasPagination() && $numberOfEntries !== false) {
            $preparedTable->setPagination($this->paginationRenderer->prepare($dataRequest, $paginationUrl, $table->countData($dataRequest), $numberOfEntries));
        }
        
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
        $updateRequired = false;
        if ($request->hasParam('table-filter-update')) {
            $updateRequired = true;
        }
        
        $page = $request->getRouteParam($this->routeParamIndex);
        $pageNotSet = false;
        if ($page == '') {
            $page = 1;
            $pageNotSet = true;
        }

        $sortBy = 'name';
        $sortByDirection = 'ASC';
        
        // If the session has a data request object for the table, load it and refresh the data.
        $savedDataRequestKey = get_class($table) . '.DataRequest.Saved';
        $new = true;
        $dataRequest = false;
        if ($table->shouldSaveDataRequest() && $request->getSessionData($savedDataRequestKey) !== false) {
            $dataRequest = unserialize($request->getSessionData($savedDataRequestKey));
        }
        
        // Check if the data request is valid
        if ($dataRequest !== false) {
            if (!$pageNotSet) {
                $dataRequest->setPage($page);
                $dataRequest->setSortBy($sortBy);
                $dataRequest->setSortByDirection($sortByDirection);
            }
            
            $new = false;
        } else {
            $dataRequest = new DataRequest($page, $numberOfEntries, $sortBy, $sortByDirection);
        }
        
        // Add the filters if the data request is new or the filter has changed
        if ($new || $updateRequired) {
            if ($updateRequired) {
                $dataRequest->clearFilters();
            }
            
            foreach ($table->getColumns() as $column) {
                $key = 'table-filter-' . $column->getKey();

                if ($column->isFilterable() && $request->hasParam($key) && $request->getParam($key) != '') {
                    $value = $table->prepareFilterValue($column->getKey(), $request->getParam($key));
                    
                    $dataRequest->addFilter(new Filter(
                        $column->getKey(),
                        $value,
                        'LIKE'
                    ));
                }
            }
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
        
        // Add the filter row
        if ($table->hasFilters()) {
            $row = new FilterRow($head);
            foreach ($table->getColumns() as $column) {
                if ($column->isFilterable()) {
                    $value = '';
                    $filter = $dataRequest->getFilter($column->getKey());
                    if ($filter !== false) {
                        $value = $filter->getNeededValue();
                    }
            
                    $cell = new Cell($column, $row, $value);
                } else {
                    $cell = new Cell($column, $row, null);
                }
            
                $row->addCell($cell);
            }
            
            $head->addRow($row);
        }
        
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
    public function renderRow(TableAbstract $table, Body $body, $object)
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
