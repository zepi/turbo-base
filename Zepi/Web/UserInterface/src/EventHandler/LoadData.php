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
use \Zepi\Turbo\Request\WebRequest;
use \Zepi\Turbo\Response\Response;

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
        if (!$request->hasSession() || $request->getRouteParam('token') == '') {
            $response->redirectTo('/');
            return;
        }
        
        $token = $request->getRouteParam('token');
        
        // Verify the datatable session data
        if (!$this->hasValidSessionData($request, $token)) {
            $response->redirectTo('/');
            return;
        }
        
        $class = $request->getSessionData('dt-class-' . $token);
        $time = $request->getSessionData('dt-time-' . $token);
        
        $options = json_decode($request->getSessionData('dt-options-' . $token), true);
        if (!is_array($options)) {
            $options = array();
        }
        
        // Session time expired
        if ($time > time() + 600) {
            $response->redirectTo('/');
            return;
        }
        
        $table = new $class($framework, false);
        $table->setOptions($options);
        $generator = $this->getTableRenderer();
        
        $preparedTable = $generator->prepareTable($request, $table, '');
        $data = array('data' => array());
        foreach ($preparedTable->getBody()->getRows() as $row) {
            $data['data'][] = $row->toArray();
        }
        
        $response->setOutput(json_encode($data));
    }
    
    /**
     * Returns true if the session data has the needed
     * token data
     * 
     * @param string $token
     * @return boolean
     */
    protected function hasValidSessionData(WebRequest $request, $token)
    {
        return ($request->getSessionData('dt-class-' . $token) !== false && $request->getSessionData('dt-time-' . $token) !== false);
    }
}
