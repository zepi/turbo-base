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
 * Registers the groups as access levels.
 * 
 * @package Zepi\Web\AccessControl
 * @subpackage EventHandler
 * @author Matthias Zobrist <matthias.zobrist@zepi.net>
 * @copyright Copyright (c) 2015 zepi
 */

namespace Zepi\Web\AccessControl\EventHandler;

use \Zepi\Turbo\FrameworkInterface\EventHandlerInterface;
use \Zepi\Turbo\Framework;
use \Zepi\Turbo\Request\RequestAbstract;
use \Zepi\Turbo\Response\Response;
use \Zepi\Core\Utils\Entity\DataRequest;
use \Zepi\Web\AccessControl\Entity\GroupAccessLevel;
use \Zepi\Core\AccessControl\Manager\AccessLevelManager;
use \Zepi\Web\AccessControl\Manager\GroupManager;
use \Zepi\Core\Language\Manager\TranslationManager;

/**
 * Registers the groups as access levels.
 * 
 * @author Matthias Zobrist <matthias.zobrist@zepi.net>
 * @copyright Copyright (c) 2015 zepi
 */
class RegisterGroupAccessLevels implements EventHandlerInterface
{
    /**
     * @access protected
     * @var \Zepi\Core\AccessControl\Manager\AccessLevelManager
     */
    protected $accessLevelManager;
    
    /**
     * @access protected
     * @var \Zepi\Web\AccessControl\Manager\GroupManager
     */
    protected $groupManager;
    
    /**
     * @access protected
     * @var \Zepi\Core\Language\Manager\TranslationManager
     */
    protected $translationManager;
    
    /**
     * Constructs the object
     *
     * @access public
     * @param \Zepi\Core\AccessControl\Manager\AccessLevelManager $accessLevelManager
     * @param \Zepi\Web\AccessControl\Manager\GroupManager $groupManager
     * @param \Zepi\Core\Language\Manager\TranslationManager $translationManager
     */
    public function __construct(AccessLevelManager $accessLevelManager, GroupManager $groupManager, TranslationManager $translationManager)
    {
        $this->accessLevelManager = $accessLevelManager;
        $this->groupManager = $groupManager;
        $this->translationManager = $translationManager;
    }
    
    /**
     * Registers the groups as access levels.
     * 
     * @access public
     * @param \Zepi\Turbo\Framework $framework
     * @param \Zepi\Turbo\Request\RequestAbstract $request
     * @param \Zepi\Turbo\Response\Response $response
     */
    public function execute(Framework $framework, RequestAbstract $request, Response $response)
    {
        $dataRequest = new DataRequest(1, 0, 'name', 'ASC');
        
        foreach ($this->groupManager->getGroups($dataRequest) as $group) {
            $this->accessLevelManager->addAccessLevel(new GroupAccessLevel(
                '\\Group\\' . $group->getUuid(),
                $this->translationManager->translate('Group', '\\Zepi\\Web\\AccessControl') . ' ' . $group->getName(),
                $this->translationManager->translate('Inherits all permissions from this group.', '\\Zepi\\Web\\AccessControl'),
                '\\Group'
            ));
        }
    }
}
