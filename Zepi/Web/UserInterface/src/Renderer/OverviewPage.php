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
 * OverviewPage Renderer
 * 
 * @package Zepi\Web\UserInterface
 * @subpackage Renderer
 * @author Matthias Zobrist <matthias.zobrist@zepi.net>
 * @copyright Copyright (c) 2015 zepi
 */

namespace Zepi\Web\UserInterface\Renderer;

use \Zepi\Turbo\Framework;
use \Zepi\Turbo\Request\RequestAbstract;
use \Zepi\Web\General\Entity\MenuEntry;

/**
 * OverviewPage Renderer
 * 
 * @author Matthias Zobrist <matthias.zobrist@zepi.net>
 * @copyright Copyright (c) 2015 zepi
 */
class OverviewPage
{
    /**
     * Renders a overview page for the given MenuEntry object
     * 
     * @access public
     * @param \Zepi\Turbo\Framework $framework
     * @param \Zepi\Web\General\Entity\MenuEntry $menuEntry
     * @return string
     */
    public function render(Framework $framework, MenuEntry $menuEntry)
    {
        // If the MenuEntry hasn't any children we return an empty string.
        if (!$menuEntry->hasChildren()) {
            return '';
        }
        
        $templatesManager = $framework->getInstance('\\Zepi\\Web\\General\\Manager\\TemplatesManager');
        $htmlString = '';

        foreach ($menuEntry->getChildren() as $child) {
            $htmlString .= $templatesManager->renderTemplate('\\Zepi\\Web\\UserInterface\\Templates\\OverviewPage', array(
                'menuEntry' => $child
            ));
        }
        
        return $htmlString;
    }
}
