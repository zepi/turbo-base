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
 * A Table displays a data table in the framework. This function must
 * be extended because the getData function is table specific.
 * 
 * @package Zepi\Web\AccessControl
 * @subpackage Table
 * @author Matthias Zobrist <matthias.zobrist@zepi.net>
 * @copyright Copyright (c) 2015 zepi
 */

namespace Zepi\Web\AccessControl\Table;

use \Zepi\Web\UserInterface\Table\TableAbstract;
use \Zepi\Web\UserInterface\Table\Column;
use \Zepi\DataSource\Core\Entity\DataRequest;

/**
 * A Table displays a data table in the framework. This function must
 * be extended because the getData function is table specific.
 * 
 * @author Matthias Zobrist <matthias.zobrist@zepi.net>
 * @copyright Copyright (c) 2015 zepi
 */
class UserTable extends TableAbstract
{
    /**
     * Returns an array with all data which should be displayed on this page
     * 
     * @access public
     * @param \Zepi\DataSource\Core\Entity\DataRequest $request
     * @return array
     */
    public function getData(DataRequest $request)
    {
        $userManager = $this->framework->getInstance('\\Zepi\\Web\\AccessControl\\Manager\\UserManager');
        $users = $userManager->find($request);
        
        return $users;
    }
    
    /**
     * Returns the total number of entries are available for the given
     * data request object
     * 
     * @access public
     * @param \Zepi\DataSource\Core\Entity\DataRequest $request
     * @return integer
     */
    public function countData(DataRequest $request)
    {
        $userManager = $this->framework->getInstance('\\Zepi\\Web\\AccessControl\\Manager\\UserManager');
        $numberOfUsers = $userManager->count($request);
        
        return $numberOfUsers;
    }
    
    /**
     * Returns an array with all columns
     * 
     * @access public
     * @return array
     */
    public function getColumns()
    {
        $translationManager = $this->framework->getInstance('\\Zepi\\Core\\Language\\Manager\\TranslationManager');
        
        return array(
            new Column('name', $translationManager->translate('Username', '\\Zepi\\Web\\AccessControl'), 50, true, true, Column::DATA_TYPE_HTML, 'icon-column'),
            new Column('uuid', $translationManager->translate('UUID', '\\Zepi\\Web\\AccessControl'), 50, true, true, Column::DATA_TYPE_STRING),
            new Column('actions', '', Column::WIDTH_AUTO, false, false, Column::DATA_TYPE_HTML, 'auto-width button-column')
        );
    }
    
    /**
     * Returns the data for the given key and row (object)
     * 
     * @access public
     * @param string $key
     * @param mixed $object
     * @return mixed
     */
    public function getDataForRow($key, $object)
    {
        $translationManager = $this->framework->getInstance('\\Zepi\\Core\\Language\\Manager\\TranslationManager');

        switch ($key) {
            case 'name':
                return '<span class="mdi mdi-person"></span>' . $object->getName();
            break;
            
            case 'uuid':
                return $object->getUuid();
            break;
            
            case 'actions':
                $request = $this->framework->getRequest();
                return '<a href="' . $request->getFullRoute('administration/users/modify/' . $object->getUuid()) . '" class="btn btn-primary"><span class="glyphicon glyphicon-pencil"></span>'
                     .     $translationManager->translate('Modify', '\\Zepi\\Web\\AccessControl') 
                     . '</a>'
                     . '<a href="' . $request->getFullRoute('administration/users/delete/' . $object->getUuid()) . '" class="btn btn-danger"><span class="glyphicon glyphicon-trash"></span>'
                     .     $translationManager->translate('Delete', '\\Zepi\\Web\\AccessControl')
                     . '</a>';
            break;
                
            default:
                return '-';
            break;
        }
    }
}
