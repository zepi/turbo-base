<?php
/**
 * Event handler to handle the load data event for datatables
 * 
 * @package Zepi\Web\UserInterface
 * @subpackage EventHandler
 * @author Matthias Zobrist <matthias.zobrist@zepi.net>
 * @copyright Copyright (c) 2016 zepi
 */

namespace Zepi\Web\UserInterface\EventHandler;

use \Zepi\Web\UserInterface\Frontend\FrontendEventHandler;
use \Zepi\Turbo\Framework;
use \Zepi\Turbo\Request\RequestAbstract;
use \Zepi\Turbo\Request\WebRequest;
use \Zepi\Turbo\Response\Response;
use \Zepi\Web\UserInterface\Form\Form;
use \Zepi\Web\UserInterface\Form\Group;
use \Zepi\Web\UserInterface\Form\ErrorBox;
use \Zepi\Web\UserInterface\Form\Error;
use \Zepi\Web\UserInterface\Form\ButtonGroup;
use \Zepi\Web\UserInterface\Form\Field\Text;
use \Zepi\Web\UserInterface\Form\Field\Password;
use \Zepi\Web\UserInterface\Form\Field\Checkbox;
use \Zepi\Web\UserInterface\Form\Field\Submit;
use \Zepi\Web\UserInterface\Frontend\FrontendHelper;
use \Zepi\Web\AccessControl\Entity\User;
use \Zepi\Web\AccessControl\Manager\UserManager;
use \Zepi\Core\AccessControl\Manager\AccessControlManager;
use \Zepi\Web\Mail\Helper\MailHelper;

/**
 * Event handler to handle the load data event for datatables
 * 
 * @author Matthias Zobrist <matthias.zobrist@zepi.net>
 * @copyright Copyright (c) 2016 zepi
 */
class LoadData extends FrontendEventHandler
{
    /**
     * Loads the data from the server
     * 
     * @access public
     * @param \Zepi\Turbo\Framework $framework
     * @param \Zepi\Turbo\Request\WebRequest $request
     * @param \Zepi\Turbo\Response\Response $response
     */
    public function execute(Framework $framework, WebRequest $request, Response $response)
    {
        // Verify the session
        if (!$request->hasSession() || $request->getRouteParam(0) == '') {
            $response->redirectTo('/');
            return;
        }
        
        $token = $request->getRouteParam(0);
        
        // Verify the datatable session data
        if ($request->getSessionData('dt-class-' . $token) === false || $request->getSessionData('dt-time-' . $token) === false) {
            $response->redirectTo('/');
            return;
        }
        
        $class = $request->getSessionData('dt-class-' . $token);
        $time = $request->getSessionData('dt-time-' . $token);
        
        // Session time expired
        if ($time > time() + 600) {
            $response->redirectTo('/');
            return;
        }
        
        $table = new $class($framework, false);
        $generator = $this->getTableRenderer();
        
        $preparedTable = $generator->prepareTable($request, $table, '');
        $data = array('data' => array());
        foreach ($preparedTable->getBody()->getRows() as $row) {
            $data['data'][] = $row->toArray();
        }
        
        $response->setOutput(json_encode($data));
    }
}
