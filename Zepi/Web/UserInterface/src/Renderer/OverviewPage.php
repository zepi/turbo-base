<?php
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
    public function render(Framework $framework, MenuEntry $menuEntry, $level = 1)
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
