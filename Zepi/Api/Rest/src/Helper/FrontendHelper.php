<?php
/**
 * This FrontendHelper delivers an interface to the 
 * RestHelper
 * 
 * @package Zepi\Api\Rest
 * @subpackage Helper
 * @author Matthias Zobrist <matthias.zobrist@zepi.net>
 * @copyright Copyright (c) 2016 zepi
 */

namespace Zepi\Api\Rest\Helper;

use \Zepi\Core\Utils\Manager\ConfigurationManager;
use \Zepi\Core\Language\Manager\TranslationManager;
use \Zepi\Web\General\Manager\TemplatesManager;
use \Zepi\Web\General\Manager\MetaInformationManager;
use \Zepi\Web\General\Manager\MenuManager;
use \Zepi\Web\UserInterface\Renderer\Layout;
use \Zepi\Web\UserInterface\Renderer\Table;
use \Zepi\Web\UserInterface\Renderer\OverviewPage;
use \Zepi\Turbo\Framework;
use \Zepi\Web\UserInterface\Frontend\FrontendHelper as UserInterfaceFrontendHelper;
use \Zepi\Api\Rest\Helper\RestHelper;

/**
 * This FrontendHelper delivers an interface to the 
 * RestHelper
 * 
 * @author Matthias Zobrist <matthias.zobrist@zepi.net>
 * @copyright Copyright (c) 2016 zepi
 */
class FrontendHelper extends UserInterfaceFrontendHelper
{
    /**
     * @access protected
     * @var \Zepi\Turbo\Framework
     */
    protected $_framework;
    
    /**
     * @access protected
     * @var \Zepi\Api\Rest\Helper\RestHelper
     */
    protected $_restHelper;
    
    /**
     * Constructs the object
     * 
     * @access public
     * @param \Zepi\Turbo\Framework $framework
     * @param \Zepi\Core\Utils\Manager\ConfigurationManager $configurationManager
     * @param \Zepi\Core\Language\Manager\TranslationManager $translationManager
     * @param \Zepi\Web\General\Manager\TemplatesManager $templatesManager
     * @param \Zepi\Web\General\Manager\MetaInformationManager $metaInformationManager
     * @param \Zepi\Web\General\Manager\MenuManager $menuManager
     * @param \Zepi\Web\UserInterface\Renderer\Layout $layoutRenderer
     * @param \Zepi\Web\UserInterface\Renderer\OverviewPage $overviewPageRenderer
     * @param \Zepi\Web\UserInterface\Renderer\Table $tableRenderer
     * @param \Zepi\Api\Rest\Helper\RestHelper $restHelper
     */
    public function __construct(
        Framework $framework,
        ConfigurationManager $configurationManager,
        TranslationManager $translationManager,
        TemplatesManager $templatesManager,
        MetaInformationManager $metaInformationManager,
        MenuManager $menuManager,
        Layout $layoutRenderer,
        OverviewPage $overviewPageRenderer,
        Table $tableRenderer,
        RestHelper $restHelper
    ) {
        parent::__construct(
            $configurationManager, 
            $translationManager, 
            $templatesManager, 
            $metaInformationManager,
            $menuManager, 
            $layoutRenderer, 
            $overviewPageRenderer, 
            $tableRenderer
        );
        
        $this->_framework = $framework;
        $this->_restHelper = $restHelper;
    }
    
    /**
     * Validates the request and returns the access entity
     * if everything is correct or false if the request is wrong
     * 
     * @access public
     * @return false|\Zepi\Api\AccessControl\Entity\Token
     */
    public function validate()
    {
        return $this->_restHelper->validate($this->_framework->getRequest());
    }
    
    /**
     * Returns an array with all needed data
     * 
     * @access public
     * @param \Zepi\Api\AccessControl\Entity\Token $token
     * @param array $data
     * @return array
     */
    public function encode(Token $token, $data)
    {
        return $this->_restHelper->encode($token, $data);
    }
    
    /**
     * Send the api result to the client
     * 
     * @param array $result
     */
    public function sendApiResult($result)
    {
        return $this->_restHelper->sendApiResult($this->_framework->getRequest(), $this->_framework->getResponse(), $result);
    }
}
