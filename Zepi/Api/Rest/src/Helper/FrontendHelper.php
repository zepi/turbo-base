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
use Zepi\Api\AccessControl\Manager\TokenManager;
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
    protected $framework;

    /**
     * @access protected
     * @var \Zepi\Api\AccessControl\Manager\TokenManager
     */
    protected $tokenManager;
    
    /**
     * @access protected
     * @var \Zepi\Api\Rest\Helper\RestHelper
     */
    protected $restHelper;
    
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
     * @param \Zepi\Api\AccessControl\Manager\TokenManager $tokenManager
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
        RestHelper $restHelper,
        TokenManager $tokenManager
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
        
        $this->framework = $framework;
        $this->restHelper = $restHelper;
        $this->tokenManager = $tokenManager;
    }
    
    /**
     * Validates the request and returns the access entity
     * if everything is correct or false if the request is wrong
     *
     * @access public
     * @return false|\Zepi\Api\AccessControl\Entity\Token
     */
    public function validateRequest()
    {
        $request = $this->framework->getRequest();
        
        // Parse the authorization information
        $authorization = $this->parseAuthorizationString($request->getHeader('Authorization'));
        $publicKey = $authorization['publicKey'];
        $hmac = $authorization['hmac'];
        
        // Verify the public key
        if (!$this->tokenManager->hasTokenForPublicKey($publicKey)) {
            return false;
        }
        
        // Load the token
        $token = $this->tokenManager->getTokenForPublicKey($publicKey);
        
        // Get the needed data
        $route = $request->getRoute();
        $data = $request->getParams();
        
        $result = $this->restHelper->validateRequest($token->getApiKey(), $hmac, $route, $data);
        
        if ($result) {
            return $token;
        }
        
        return false;
    }
    
    /**
     * Parses the authorization string and returns an array with
     * the public key and the hmac
     *
     * @access protected
     * @param string $authorizationString
     * @return array
     */
    protected function parseAuthorizationString($authorizationString)
    {
        if (strpos($authorizationString, 'Basic') !== false) {
            $authorizationString = trim(substr($authorizationString, 6));
        }
    
        $decoded = base64_decode($authorizationString);
        $delimiterPos = strpos($decoded, ':');
    
        return array(
            'publicKey' => substr($decoded, 0, $delimiterPos),
            'hmac' => substr($decoded, $delimiterPos + 1)
        );
    }
    
    /**
     * Send the api result to the client
     *
     * @param array $result
     */
    public function sendResponse($result)
    {
        return $this->restHelper->sendResponse($this->framework->getRequest(), $this->framework->getResponse(), $result);
    }
}
