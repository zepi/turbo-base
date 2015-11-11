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
 * Transforms all access levels to selector items.
 * 
 * @package Zepi\Web\AccessControl
 * @subpackage Helper
 * @author Matthias Zobrist <matthias.zobrist@zepi.net>
 * @copyright Copyright (c) 2015 zepi
 */

namespace Zepi\Web\AccessControl\Helper;

use \Zepi\Web\AccessControl\Entity\Group;
use \Zepi\Web\AccessControl\Entity\User;
use \Zepi\Web\UserInterface\Entity\SelectorItem;
use \Zepi\Web\AccessControl\Entity\GroupAccessLevel;
use \Zepi\Core\Language\Manager\TranslationManager;

/**
 * Transforms all access levels to selector items.
 * 
 * @author Matthias Zobrist <matthias.zobrist@zepi.net>
 * @copyright Copyright (c) 2015 zepi
 */
class AccessLevelHelper
{
    /**
     * @access protected
     * @var \Zepi\Core\Language\Manager\TranslationManager
     */
    protected $_translationManager;
    
    /**
     * Constructs the object
     * 
     * @access public
     * @param TranslationManager $translationManager
     */
    public function __construct(TranslationManager $translationManager)
    {
        $this->_translationManager = $translationManager;
    }
    
    /**
     * Optimizes the given css content.
     * 
     * @access public
     * @param array $accessLevels
     * @param \Zepi\Web\AccessControl\Entity\User $editedGroup
     * @param \Zepi\Web\AccessControl\Entity\Group $editedGroup
     * @return string
     */
    public function transformAccessLevels($accessLevels, User $user, Group $editedGroup = null)
    {
        $selectorItems = array();
        
        foreach ($accessLevels as $accessLevel) {
            $disabled = false;
            if (!$user->hasAccess($accessLevel->getKey()) || ($editedGroup !== null && $this->_isEditedGroup($accessLevel->getKey(), $editedGroup))) {
                $disabled = true;
            }
            
            $name = $accessLevel->getName();
            $description = $accessLevel->getDescription();
            
            if ($accessLevel instanceof GroupAccessLevel) {
                $icon = 'mdi mdi-account-multiple';
            } else {
                $icon = 'mdi mdi-code-array';

                $name = $this->_translationManager->translate($name, $accessLevel->getNamespace());
                $description = $this->_translationManager->translate($description, $accessLevel->getNamespace());
            }

            $selectorItems[] = new SelectorItem(
                $accessLevel->getKey(),
                $name,
                $description,
                $icon,
                $disabled
            );
        }
        
        return $selectorItems;
    }
    
    /**
     * Returns true if the access level is the edited group.
     * 
     * @access public
     * @param string $accessLevel
     * @param \Zepi\Web\AccessControl\Entity\Group $editedGroup
     * @return boolean
     */
    protected function _isEditedGroup($accessLevel, Group $editedGroup)
    {
        $parts = explode('\\', $accessLevel);
    
        if ($parts[1] === 'Group' && count($parts) === 3 && $parts[2] === $editedGroup->getUuid()) {
            return true;
        }
        
        return false;
    }
}
