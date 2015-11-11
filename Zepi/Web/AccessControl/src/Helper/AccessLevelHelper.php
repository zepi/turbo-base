<?php
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
