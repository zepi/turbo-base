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
 * 
 * @Entity 
 * @Table(name="groups")
 */
class Group extends AccessEntity
{
    /**
     * Constructs the object
     * 
     * @param integer $id
     * @param string $uuid
     * @param string $name
     * @param string $privateKey
     * @param array $metaData
     */
    public function __construct($id, $uuid, $name, $privateKey, array $metaData)
    {
        parent::__construct(
            $id,
            $uuid,
            $name,
            $privateKey,
            $metaData
        );
    }
}
