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
 * Generates the layout for the EditUser event
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
use \Zepi\Web\UserInterface\Form\Field\Password;
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
use \Zepi\Web\AccessControl\Entity\User;

/**
 * Generates the layout for the EditUser event
 * 
 * @author Matthias Zobrist <matthias.zobrist@zepi.net>
 * @copyright Copyright (c) 2016 zepi
 */
class EditUserLayout extends LayoutAbstract
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
     * @var \Zepi\Web\AccessControl\Entity\User
     */
    protected $user;
    
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
     * @param \Zepi\Web\AccessControl\Entity\User $user
     */
    public function setUser(User $user)
    {
        $this->user = $user;
    }
    
    /**
     * Generates the layout
     *
     * @return \Zepi\Web\UserInterface\Layout\AbstractContainer
     * 
     * @throws \Zepi\Web\AccessControl\Exception User is not set.
     */
    protected function generateLayout()
    {
        if ($this->user === null) {
            throw new Exception('User is not set.');
        }
        
        $request = $this->framework->getRequest();
        
        $accessLevelSelectorItems = $this->accessLevelHelper->transformAccessLevels(
            $this->accessLevelManager->getAccessLevels(),
            $request->getSession()->getUser()
        );
        
        $rawPermissionsForUuid = $this->accessControlManager->getPermissionsRawForUuid($this->user->getUuid());
        if ($rawPermissionsForUuid === false) {
            $rawPermissionsForUuid = array();
        }
        
        $page = new Page(
            array(
                new Form('edit-user', $request->getFullRoute(), 'post', array(
                    new ErrorBox(
                        'edit-user-errors'
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
                                                            'username',
                                                            $this->translate('Username', '\\Zepi\\Web\\AccessControl'),
                                                            true,
                                                            $this->user->getName(),
                                                            $this->translate('The username must be unique. Only one user can use an username.', '\\Zepi\\Web\\AccessControl')
                                                        ),
                                                        new Password(
                                                            'password',
                                                            $this->translate('Password', '\\Zepi\\Web\\AccessControl'),
                                                            $this->user->isNew()
                                                        ),
                                                        new Password(
                                                            'password-confirmed',
                                                            $this->translate('Confirm password', '\\Zepi\\Web\\AccessControl'),
                                                            $this->user->isNew()
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
                                                        new Text(
                                                            'email',
                                                            $this->translate('Email address', '\\Zepi\\Web\\AccessControl'),
                                                            false,
                                                            $this->user->getMetaData('email')
                                                        ),
                                                        new Text(
                                                            'location',
                                                            $this->translate('Location', '\\Zepi\\Web\\AccessControl'),
                                                            false,
                                                            $this->user->getMetaData('location')
                                                        ),
                                                        new Text(
                                                            'website',
                                                            $this->translate('Website', '\\Zepi\\Web\\AccessControl'),
                                                            false,
                                                            $this->user->getMetaData('website')
                                                        ),
                                                        new Text(
                                                            'twitter',
                                                            $this->translate('Twitter', '\\Zepi\\Web\\AccessControl'),
                                                            false,
                                                            $this->user->getMetaData('twitter')
                                                        ),
                                                        new Textarea(
                                                            'biography',
                                                            $this->translate('Biography', '\\Zepi\\Web\\AccessControl'),
                                                            false,
                                                            $this->user->getMetaData('biography')
                                                        )
                                                    ),
                                                    2
                                                )
                                            ), array('col-md-6')),
                                        )
                                    ),
                                ),
                                array(),
                                'user-tab',
                                $this->translate('User informations', '\\Zepi\\Web\\AccessControl')
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
                                            $request->getFullRoute('/administration/users/')
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
                                            'mdi mdi-save'
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
