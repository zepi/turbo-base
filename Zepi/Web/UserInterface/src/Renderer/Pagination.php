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
 * Pagination Renderer
 * 
 * @package Zepi\Web\UserInterface
 * @subpackage Renderer
 * @author Matthias Zobrist <matthias.zobrist@zepi.net>
 * @copyright Copyright (c) 2015 zepi
 */

namespace Zepi\Web\UserInterface\Renderer;

use \Zepi\Turbo\Framework;
use \Zepi\Web\UserInterface\Pagination\Entry;
use \Zepi\Web\UserInterface\Pagination\Page;
use \Zepi\Web\UserInterface\Pagination\ActivePage;
use \Zepi\Web\UserInterface\Pagination\Button;
use \Zepi\Web\UserInterface\Pagination\Dots;
use \Zepi\Web\UserInterface\Pagination\Pagination as PaginationObject;
use \Zepi\Core\Utils\Entity\DataRequest;

/**
 * Pagination Renderer
 * 
 * @author Matthias Zobrist <matthias.zobrist@zepi.net>
 * @copyright Copyright (c) 2015 zepi
 */
class Pagination
{
    /**
     * @access protected
     * @var string
     */
    protected $paginationUrl;
    
    /**
     * Prepares the Pagination object for the given DataRequest and number of entries per page
     * 
     * @access public
     * @param \Zepi\Core\Utils\Entity\DataRequest $dataRequest
     * @param string $paginationUrl
     * @param integer $numberOfEntries
     * @param integer $numberOfEntriesPerPage
     * @return \Zepi\Web\UserInterface\Pagination\Pagination
     */
    public function prepare(DataRequest $dataRequest, $paginationUrl, $numberOfEntries, $numberOfEntriesPerPage = 10)
    {
        $this->paginationUrl = $paginationUrl;
        $neededPages = ceil($numberOfEntries / $numberOfEntriesPerPage);
        $activePage = $dataRequest->getPage();

        $pagination = new PaginationObject();
        
        if ($activePage > 1) {
            // Add the first page button
            $button = new Button('&laquo;', $this->buildUrl(1));
            $pagination->addEntry($button); 
            
            
            // Add the prev page button
            $button = new Button('&lsaquo;', $this->buildUrl(($activePage - 1)));
            $pagination->addEntry($button);
        }
        
        $this->addPages($pagination, $activePage, $neededPages);
        
        if ($activePage < $neededPages) {
            // Add the next page button
            $button = new Button('&rsaquo;', $this->buildUrl(($activePage + 1)));
            $pagination->addEntry($button);
            
            // Add the last page button
            $button = new Button('&raquo;', $this->buildUrl($neededPages));
            $pagination->addEntry($button);
        }
        
        return $pagination;
    }
    
    /**
     * Add the pages to the pagination
     * 
     * @param \Zepi\Web\UserInterface\Pagination\Pagination $pagination
     * @param integer $activePage
     * @param integer $neededPages
     */
    protected function addPages(PaginationObject $pagination, $activePage, $neededPages)
    {
        // Add the pages
        for ($i = 1; $i <= $neededPages; $i++) {
            if ($i == $activePage) {
                $page = new ActivePage($i, $this->buildUrl($i));
                $pagination->addEntry($page);
            } else if ($i < 4 || $i > ($neededPages - 3) || ($i > ($activePage - 3) && $i < ($activePage + 3))) {
                $page = new Page($i, $this->buildUrl($i));
                $pagination->addEntry($page);
            } else if (!($pagination->getLatestEntry() instanceof Dots)) {
                $dots = new Dots();
                $pagination->addEntry($dots);
            }
        }
    }
    
    /**
     * Returns the correct url for the page
     * 
     * @access protected
     * @param integer $page
     * @return string
     */
    protected function buildUrl($page)
    {
        return str_replace('{page}', $page, $this->paginationUrl);
    }
}
