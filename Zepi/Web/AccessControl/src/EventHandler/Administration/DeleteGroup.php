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
 * Event handler for the delete group function
 * 
 * @package Zepi\Web\AccessControl
 * @subpackage EventHandler\Administration
 * @author Matthias Zobrist <matthias.zobrist@zepi.net>
 * @copyright Copyright (c) 2015 zepi
 */

namespace Zepi\Web\AccessControl\EventHandler\Administration;

use \Zepi\Web\UserInterface\Frontend\FrontendEventHandler;
use \Zepi\Turbo\Framework;
use \Zepi\Turbo\Request\RequestAbstract;
use \Zepi\Turbo\Request\WebRequest;
use \Zepi\Turbo\Response\Response;
use \Zepi\Web\AccessControl\Entity\Group;
use \Zepi\Web\UserInterface\Frontend\FrontendHelper;
use \Zepi\Web\AccessControl\Manager\GroupManager;

/**
 * Event handler for the delete group function
 * 
 * @author Matthias Zobrist <matthias.zobrist@zepi.net>
 * @copyright Copyright (c) 2015 zepi
 */
class DeleteGroup extends FrontendEventHandler
{
    /**
     * @access protected
     * @var \Zepi\Web\AccessControl\Manager\GroupManager
     */
    protected $groupManager;
    
    /**
     * Constructs the object
     * 
     * @access public
     * @param \Zepi\Web\UserInterface\Frontend\FrontendHelper $frontendHelper
     * @param \Zepi\Web\AccessControl\Manager\GroupManager $groupManager
     */
    public function __construct(FrontendHelper $frontendHelper, GroupManager $groupManager)
    {
        $this->frontendHelper = $frontendHelper;
        $this->groupManager = $groupManager;
    }
    
    /**
     * Displays the edit user form and saves the data to the database.
     * 
     * @access public
     * @param \Zepi\Turbo\Framework $framework
     * @param \Zepi\Turbo\Request\WebRequest $request
     * @param \Zepi\Turbo\Response\Response $response
     */
    public function execute(Framework $framework, WebRequest $request, Response $response)
    {
        // Prepare the page
        $additionalTitle = $this->translate('Delete group', '\\Zepi\\Web\\AccessControl');
        $title = $this->translate('Group management', '\\Zepi\\Web\\AccessControl');
        $this->activateMenuEntry('group-administration');
        $this->setTitle($title, $additionalTitle);
        
        // Get the user
        $uuid = $request->getRouteParam('uuid');
        
        // If the UUID does not exists redirect to the overview page
        if (!is_string($uuid) || !$this->groupManager->hasGroupForUuid($uuid)) {
            $response->redirectTo($request->getFullRoute('/administration/groups/'));
            return;
        }
        
        $group = $this->groupManager->getGroupForUuid($uuid);
        
        // If $result isn't true, display the edit user form
        if ($request->getRouteParam('confirmation') === 'confirmed') {
            $this->groupManager->delete($group);
            
            $response->setOutput($this->render('\\Zepi\\Web\\AccessControl\\Templates\\Administration\\DeleteGroupFinished', array(
                'group' => $group
            )));
        } else {
            // Display the delete user confirmation
            $response->setOutput($this->render('\\Zepi\\Web\\AccessControl\\Templates\\Administration\\DeleteGroup', array(
                'group' => $group
            )));
        }
    }
}
