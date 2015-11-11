<?php
/**
 * The Group object representatses the access entity "group". Access levels can be 
 * assigned to a group and the group can be assigned to a user. The user inherits
 * all the access levels of a group.
 * 
 * @package Zepi\Web\AccessControl
 * @subpackage Entity
 * @author Matthias Zobrist <matthias.zobrist@zepi.net>
 * @copyright Copyright (c) 2015 zepi
 */

namespace Zepi\Web\AccessControl\Entity;

use \Zepi\Core\AccessControl\Entity\AccessEntity;

/**
 * The Group object representatses the access entity "group". Access levels can be 
 * assigned to a group and the group can be assigned to a user. The user inherits
 * all the access levels of a group.
 * 
 * @author Matthias Zobrist <matthias.zobrist@zepi.net>
 * @copyright Copyright (c) 2015 zepi
 */
class Group extends AccessEntity
{
    /**
     * Constructs the object
     * 
     * @param integer $id
     * @param string $uuid
     * @param string $name
     * @param string $key
     * @param array $metaData
     */
    public function __construct($id, $uuid, $name, $key, array $metaData)
    {
        parent::__construct(
            $id,
            $uuid,
            get_class($this),
            $name,
            $key,
            $metaData
        );
    }
}
