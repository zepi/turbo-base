<?php
/**
 * Dots
 * 
 * @package Zepi\Web\UserInterface
 * @subpackage Pagination
 * @author Matthias Zobrist <matthias.zobrist@zepi.net>
 * @copyright Copyright (c) 2015 zepi
 */

namespace Zepi\Web\UserInterface\Pagination;

/**
 * Dots
 * 
 * @author Matthias Zobrist <matthias.zobrist@zepi.net>
 * @copyright Copyright (c) 2015 zepi
 */
class Dots extends Entry
{
    /**
     * Constructs the object
     * 
     * @param string $label
     */
    public function __construct($label = '...')
    {
        parent::__construct($label);
    }
}
