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
 * Generates the layout for the EditGroup event
 * 
 * @package Zepi\Web\AccessControl
 * @subpackage Layout
 * @author Matthias Zobrist <matthias.zobrist@zepi.net>
 * @copyright Copyright (c) 2016 zepi
 */

namespace Zepi\Web\AccessControl\Layout;

use \Zepi\Turbo\Framework;
use \Zepi\Web\AccessControl\Exception;
use \Zepi\Web\UserInterface\Layout\LayoutAbstract;
use \Zepi\Web\UserInterface\Form\Form;
use \Zepi\Web\UserInterface\Form\Group;
use \Zepi\Web\UserInterface\Form\ErrorBox;
use \Zepi\Web\UserInterface\Form\ButtonGroup;
use \Zepi\Web\UserInterface\Form\Field\Text;
use \Zepi\Web\UserInterface\Form\Field\Textarea;
use \Zepi\Web\UserInterface\Form\Field\Button;
use \Zepi\Web\UserInterface\Form\Field\Submit;
use \Zepi\Web\UserInterface\Form\Field\Selector;
use \Zepi\Web\UserInterface\Layout\AbstractContainer;
use \Zepi\Web\UserInterface\Layout\Page;
use \Zepi\Web\UserInterface\Layout\Tabs;
use \Zepi\Web\UserInterface\Layout\Tab;
use \Zepi\Web\UserInterface\Layout\Row;
use \Zepi\Web\UserInterface\Layout\Column;
use \Zepi\Core\AccessControl\Manager\AccessControlManager;
use \Zepi\Core\AccessControl\Manager\AccessLevelManager;
use \Zepi\Core\Language\Manager\TranslationManager;
use \Zepi\Web\AccessControl\Helper\AccessLevelHelper;
use \Zepi\Web\AccessControl\Entity\Group as EntityGroup;

/**
 * Generates the layout for the EditGroup event
 * 
 * @author Matthias Zobrist <matthias.zobrist@zepi.net>
 * @copyright Copyright (c) 2016 zepi
 */
class EditGroupLayout extends LayoutAbstract
{
    /**
     * @var \Zepi\Core\AccessControl\Manager\AccessControlManager
     */
    protected $accessControlManager;
    
    /**
     * @var \Zepi\Core\AccessControl\Manager\AccessLevelManager
     */
    protected $accessLevelManager;
    
    /**
     * @var \Zepi\Web\AccessControl\Helper\AccessLevelHelper
     */
    protected $accessLevelHelper;
    
    /**
     * @var \Zepi\Web\AccessControl\Entity\Group
     */
    protected $group;
    
    /**
     * Construct the object
     *
     * @param \Zepi\Turbo\Framework $framework
     * @param \Zepi\Core\Language\Manager\TranslationManager $translationManager
     * @param \Zepi\Core\AccessControl\Manager\AccessControlManager $accessControlManager
     * @param \Zepi\Core\AccessControl\Manager\AccessLevelManager $accessLevelManager
     * @param \Zepi\Web\AccessControl\Helper\AccessLevelHelper $accessLevelHelper
     */
    public function __construct(
        Framework $framework,
        TranslationManager $translationManager,
        AccessControlManager $accessControlManager,
        AccessLevelManager $accessLevelManager,
        AccessLevelHelper $accessLevelHelper
    ) {
        parent::__construct($framework, $translationManager);
        
        $this->accessControlManager = $accessControlManager;
        $this->accessLevelManager = $accessLevelManager;
        $this->accessLevelHelper = $accessLevelHelper;
    }
    
    /**
     * Sets the user
     * 
     * @param \Zepi\Web\AccessControl\Entity\Group $group
     */
    public function setGroup(EntityGroup $group)
    {
        $this->group = $group;
    }
    
    /**
     * Generates the layout
     *
     * @return \Zepi\Web\UserInterface\Layout\AbstractContainer
     * 
     * @throws \Zepi\Web\AccessControl\Exception Group is not set.
     */
    protected function generateLayout()
    {
        if ($this->group === null) {
            throw new Exception('Group is not set.');
        }
        
        $request = $this->framework->getRequest();
        
        $accessLevelSelectorItems = $this->accessLevelHelper->transformAccessLevels(
            $this->accessLevelManager->getAccessLevels(),
            $request->getSession()->getUser(),
            $this->group
        );
        
        $rawPermissionsForUuid = $this->accessControlManager->getPermissionsRawForUuid($this->group->getUuid());
        if ($rawPermissionsForUuid === false) {
            $rawPermissionsForUuid = array();
        }
        
        $page = new Page(
            array(
                new Form('edit-group', $request->getFullRoute(), 'post', array(
                    new ErrorBox(
                        'edit-group-errors'
                    ),
                    new Tabs(
                        array(
                            new Tab(
                                array(
                                    new Row(
                                        array(
                                            new Column(array(
                                                new Group(
                                                    'required-data',
                                                    $this->translate('Required data', '\\Zepi\\Web\\AccessControl'),
                                                    array(
                                                        new Text(
                                                            'groupname',
                                                            $this->translate('Group name', '\\Zepi\\Web\\AccessControl'),
                                                            true,
                                                            $this->group->getName(),
                                                            $this->translate('The group name must be unique. Only one group can use a group name.', '\\Zepi\\Web\\AccessControl')
                                                        ),
                                                    ),
                                                    1
                                                )
                                            ), array('col-md-6')),
                                            new Column(array(
                                                new Group(
                                                    'optional-data',
                                                    $this->translate('Optional data', '\\Zepi\\Web\\AccessControl'),
                                                    array(
                                                        new Textarea(
                                                            'description',
                                                            $this->translate('Description', '\\Zepi\\Web\\AccessControl'),
                                                            false,
                                                                $this->group->getMetaData('description')
                                                        ),
                                                    ),
                                                    2
                                                )
                                            ), array('col-md-6')),
                                        )
                                    ),
                                ),
                                array(),
                                'group-tab',
                                $this->translate('Group informations', '\\Zepi\\Web\\AccessControl')
                            ),
                            new Tab(
                                array(
                                    new Selector(
                                        'access-levels',
                                        $this->translate('Access Level Selector', '\\Zepi\\Web\\AccessControl'),
                                        false,
                                        $rawPermissionsForUuid,
                                        $accessLevelSelectorItems,
                                        $this->translate('Available Access Levels', '\\Zepi\\Web\\AccessControl'),
                                        $this->translate('Granted Access Levels', '\\Zepi\\Web\\AccessControl'),
                                        '\\Zepi\\Web\\AccessControl\\Templates\\Form\\Snippet\\AccessLevel'
                                    ),
                                ),
                                array(),
                                'access-tab',
                                $this->translate('Permissions', '\\Zepi\\Web\\AccessControl')
                            )
                        )        
                    ),
                    new Row(
                        array(
                            new Column(array(
                                new ButtonGroup(
                                    'buttons-left',
                                    array(
                                        new Button(
                                            'back',
                                            $this->translate('Back', '\\Zepi\\Web\\AccessControl'),
                                            array('btn-default'),
                                            '',
                                            'a',
                                            $request->getFullRoute('/administration/groups/')
                                        )
                                    ),
                                    1000,
                                    array('text-left')
                                )
                            ), array('col-md-4')),
                            new Column(array(
                                new ButtonGroup(
                                    'buttons',
                                    array(
                                        new Submit(
                                            'submit',
                                            $this->translate('Save', '\\Zepi\\Web\\AccessControl'), 
                                            array('btn-large', 'btn-primary'),
                                            'mdi mdi-floppy'
                                        )
                                    ),
                                    1000
                                )
                            ), array('col-md-4'))
                        )
                    )
                ))
            )
        );
        
        return $page;
    }
}
