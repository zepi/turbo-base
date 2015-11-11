<?php
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
     * Prepares the Pagination object for the given DataRequest and number of entries per page
     * 
     * @access public
     * @param \Zepi\Core\Utils\Entity\DataRequest $dataRequest
     * @param integer $numberOfEntriesPerPage
     * @return string
     */
    public function prepare(DataRequest $dataRequest, $numberOfEntries, $numberOfEntriesPerPage = 10)
    {
        $neededPages = ceil($numberOfEntries / $numberOfEntriesPerPage);
        $activePage = $dataRequest->getPage();

        $pagination = new PaginationObject();
        
        if ($activePage > 1) {
            // Add the first page button
            $button = new Button('&laquo;', '/page/1');
            $pagination->addEntry($button); 
            
            
            // Add the prev page button
            $button = new Button('&lsaquo;', '/page/' . ($activePage - 1));
            $pagination->addEntry($button);
        }
        
        // Add the pages
        for ($i = 1; $i <= $neededPages; $i++) {
            if ($i == $activePage) {
                $page = new ActivePage($i, '/page/' . $i);
                $pagination->addEntry($page);
            } else if ($i < 4 || $i > ($neededPages - 3) || ($i > ($activePage - 3) && $i < ($activePage + 3))) {
                $page = new Page($i, '/page/' . $i);
                $pagination->addEntry($page);
            } else if (!($pagination->getLatestEntry() instanceof Dots)) {
                $dots = new Dots();
                $pagination->addEntry($dots);
            }
        }
        
        if ($activePage < $neededPages) {
            // Add the next page button
            $button = new Button('&rsaquo;', '/page/' . ($activePage + 1));
            $pagination->addEntry($button);
            
            // Add the last page button
            $button = new Button('&raquo;', '/page/' . $neededPages);
            $pagination->addEntry($button);
        }
        
        return $pagination;
    }
}
