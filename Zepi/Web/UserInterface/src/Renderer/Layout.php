<?php
/**
 * Layout Renderer
 * 
 * @package Zepi\Web\UserInterface
 * @subpackage Renderer
 * @author Matthias Zobrist <matthias.zobrist@zepi.net>
 * @copyright Copyright (c) 2015 zepi
 */

namespace Zepi\Web\UserInterface\Renderer;

use \Zepi\Turbo\Framework;
use \Zepi\Web\General\Manager\TemplatesManager;
use \Zepi\Web\UserInterface\Layout\AbstractContainer;
use \Zepi\Web\UserInterface\Layout\Page;
use \Zepi\Web\UserInterface\Layout\Part;
use \Zepi\Web\UserInterface\Layout\Tab;
use \Zepi\Web\UserInterface\Layout\Row;
use \Zepi\Web\UserInterface\Layout\Column;

/**
 * Layout Renderer
 * 
 * @author Matthias Zobrist <matthias.zobrist@zepi.net>
 * @copyright Copyright (c) 2015 zepi
 */
class Layout
{
    /**
     * @access protected
     * @var \Zepi\Web\General\Manager\TemplatesManager
     */
    protected $_templatesManager;
    
    /**
     * Constructs the object
     * 
     * @access public
     * @param \Zepi\Web\General\Manager\TemplatesManager $templatesManager
     */
    public function __construct(TemplatesManager $templatesManager)
    {
        $this->_templatesManager = $templatesManager;
    }
    
    /**
     * Renders the given abstract container element and returns the html code
     * for the given container.
     * 
     * @access public
     * @param \Zepi\Web\UserInterface\Layout\AbstractContainer $container
     * @return string
     */
    public function render(AbstractContainer $container)
    {
        $template = $container->getTemplateKey();

        return $this->_templatesManager->renderTemplate($template, array(
            'layoutRenderer' => $this,
            'container' => $container
        ));
    }
}
