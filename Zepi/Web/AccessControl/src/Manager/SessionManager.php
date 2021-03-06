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
 * Manages the HTTP sessions
 * 
 * @package Zepi\Web\AccessControl
 * @subpackage Manager
 * @author Matthias Zobrist <matthias.zobrist@zepi.net>
 * @copyright Copyright (c) 2015 zepi
 */

namespace Zepi\Web\AccessControl\Manager;

use \Zepi\Turbo\Framework;
use \Zepi\Turbo\Request\WebRequest;
use \Zepi\Turbo\Response\Response;
use \Zepi\Web\AccessControl\Entity\Session;
use \Zepi\Web\AccessControl\Entity\User;
use \Zepi\Web\AccessControl\Manager\UserManager;

/**
 * Manages the HTTP sessions
 * 
 * @author Matthias Zobrist <matthias.zobrist@zepi.net>
 * @copyright Copyright (c) 2015 zepi
 */
class SessionManager
{
    /**
     * @var \Zepi\Web\ACcessControl\Entity\Session
     * @access protected
     */
    protected $session;
    
    /**
     * @access protected
     * @var \Zepi\Web\AccessControl\Manager\UserManager
     */
    protected $userManager;
    
    /**
     * Constructs the object
     * 
     * @access public
     * @param \Zepi\Web\AccessControl\Manager\UserManager $userManager
     */
    public function __construct(UserManager $userManager)
    {
        $this->userManager = $userManager;
    }
    
    /**
     * Initializes the user session
     * 
     * @access public
     * @param \Zepi\Turbo\Request\WebRequest $request
     * @param \Zepi\Turbo\Response\Response $response
     * @param \Zepi\Web\AccessControl\Entity\User $user
     */
    public function initializeUserSession(WebRequest $request, Response $response, User $user)
    {
        // If the session already has user data ...
        if ($request->getSessionData('userUuid') !== false) {
            $sessionToken = $request->getSessionData('userSessionToken');
            $sessionTokenLifetime = $request->getSessionData('userSessionTokenLifetime');
            
            // Cleanup the session
            $this->cleanupSession($request);
            
            // Save the old session token for some requests in the next 60 seconds
            if ($sessionToken !== false) {
                $request->setSessionData('oldUserSessionToken', $sessionToken);
                $request->setSessionData('oldUserSessionTokenLifetime', $sessionTokenLifetime);
            }
        }
        
        // Regenerate the session
        $this->regenerateSession($request);
        
        $sessionToken = md5($user->getUuid()) . '-' . md5(uniqid());
        $sessionTokenLifeTime = time() + 300;
        
        $request->setSessionData('userUuid', $user->getUuid());
        $request->setSessionData('userSessionToken', $sessionToken);
        $request->setSessionData('userSessionTokenLifetime', $sessionTokenLifeTime);

        setcookie($sessionToken, $sessionTokenLifeTime, 0, '/', '', $request->isSsl());
    }
    
    /**
     * Initializes the session
     * 
     * @access public
     * @param \Zepi\Turbo\Request\WebRequest $request
     * @param \Zepi\Turbo\Response\Response $response
     */
    public function reinitializeSession(Framework $framework, WebRequest $request, Response $response)
    {
        // Sets the correct name:
        session_name('ZTSM');
        
        // Start the session
        session_start();
        
        // If the session wasn't started before, we start it now...
        if ($request->getSessionData('sessionStarted') === false) {
            $this->startSession($request);
        }
        
        // Validate the session data
        $valid = $this->validateSessionData($request);
        
        // If the session not is valid we redirect to the start of everything
        if (!$valid) {
            $response->redirectTo('');
        }
        
        // There is a 1% chance that we regenerate the session
        if (mt_rand(1, 100) <= 1) {
            $this->regenerateSession($request);
        }

        // Initialize the user session
        if ($request->getSessionData('userUuid') !== false) {
            $this->reinitializeUserSession($framework, $request, $response);
        }
    }
    
    /**
     * Logouts the logged in user
     * 
     * @access public
     * @param \Zepi\Turbo\Request\WebRequest $request
     * @param \Zepi\Turbo\Response\Response $response
     */
    public function logoutUser(WebRequest $request, Response $response)
    {
        $this->cleanupSession($request);
        
        $request->removeSession();
    }
    
    /**
     * Verifies the session tokens and the session token life time and
     * loads the user for the session.
     * 
     * @access protected
     * @param \Zepi\Turbo\Framework $framework
     * @param \Zepi\Turbo\Request\WebRequest $request
     * @param \Zepi\Turbo\Response\Response $response
     * @return boolean
     */
    protected function reinitializeUserSession(Framework $framework, WebRequest $request, Response $response)
    {
        $token = $request->getSessionData('userSessionToken');
        $lifetime = $request->getSessionData('userSessionTokenLifetime');
        
        list($notValid, $token, $lifetime, $userUuid) = $this->verifyToken($request, $token, $lifetime);

        // We do not load any user session because this session isn't 
        // okey. Our session token is not set or the lifetime is invalid or expired.
        // This is maybe an expired session or a hijacking attack...
        if ($notValid) {
            $this->cleanupSession($request);
            $this->regenerateSession($request);
            
            return false;
        }
        
        // Load the user
        $user = $this->userManager->getUserForUuid($userUuid);
        
        // If the user is disabled we cannot initialize the session
        if (!$user->hasAccess('\\Global\\*') && $user->hasAccess('\\Global\\Disabled')) {
            return false;
        }

        // Generate a new session object
        $session = new Session($user, $token, $lifetime);
        $request->setSession($session);

        // Generate a new token if the lifetime expires soon...
        if ($lifetime - 30 < time()) {
            $this->initializeUserSession($request, $response, $user);
        }

        return true;
    }
    
    /**
     * Verifies the given session token and lifetime
     * 
     * @param \Zepi\Turbo\Request\WebRequest $request
     * @param string $token
     * @param string $lifetime
     * @return array
     */
    protected function verifyToken(WebRequest $request, $token, $lifetime)
    {
        $notValid = false;
        
        // Cookie does not exists - this is maybe a session hijacking attack
        if ($request->getCookieData($token) === false) {
            $notValid = true;
        }
        
        // Check for the old data
        if ($notValid && $request->getSessionData('oldUserSessionToken') !== false) {
            $token = $request->getSessionData('oldUserSessionToken');
            $lifetime = $request->getSessionData('oldUserSessionTokenLifetime');
        
            // Look for the old session token cookie...
            if ($request->getCookieData($token) === false) {
                $notValid = true;
            }
        }
        
        // Check the lifetime of the cookie and the session
        if (!$notValid && $request->getCookieData($token) != $lifetime) {
            $notValid = true;
        }
        
        // If the session token expired more than 30 minutes ago
        // the session isn't valid anymore
        if (!$notValid && $lifetime < time() - 1800) {
            $notValid = true;
        }
        
        $userUuid = $request->getSessionData('userUuid');
        
        // If the given uuid doesn't exists, this session can't be valid
        if (!$notValid && !$this->userManager->hasUserForUuid($userUuid)) {
            $notValid = true;
        }
        
        return array($notValid, $token, $lifetime, $userUuid);
    }
    
    /**
     * Starts the session and saves the base session informations.
     * 
     * @access protected
     * @param \Zepi\Turbo\Request\WebRequest $request
     */
    protected function startSession(WebRequest $request)
    {
        // Save the nonce base on the session
        $request->setSessionData('nonceBase', md5(uniqid()));
        
        // Save the ip address on the session
        $request->setSessionData('ipAddress', $_SERVER['REMOTE_ADDR']);
        
        // Save the user agent on the session
        if (isset($_SERVER['HTTP_USER_AGENT'])) {
            $request->setSessionData('userAgent', $_SERVER['HTTP_USER_AGENT']);
        }
        
        // Save the boolean that we started the session
        $request->setSessionData('sessionStarted', true);
    }
    
    /**
     * Removes all user and token data from the session
     * 
     * @access protected
     * @param \Zepi\Turbo\Request\WebRequest $request
     */
    protected function cleanupSession(WebRequest $request)
    {
        $sessionToken = $request->getSessionData('userSessionToken');
        setcookie($sessionToken, 0, time() - 60, '/', '', $request->isSsl());
        
        $oldSessionToken = $request->getSessionData('oldUserSessionToken');
        if ($oldSessionToken != '') {
            setcookie($oldSessionToken, 0, time() - 60, '/', '', $request->isSsl());
        }
    
        $request->deleteSessionData('userUuid');
        $request->deleteSessionData('userSessionToken');
        $request->deleteSessionData('userSessionTokenLifetime');
        $request->deleteSessionData('oldUserSessionToken');
        $request->deleteSessionData('oldUserSessionTokenLifetime');
    }
    
    /**
     * Validates the session. If the session is obsolete and the max lieftime is reached
     * the function will return false, otherwise true.
     * 
     * @access protected
     * @param \Zepi\Turbo\Request\WebRequest $request
     * @return boolean
     */
    protected function validateSessionData(WebRequest $request)
    {
        if ($request->getSessionData('isObsolete') && $request->getSessionData('maxLifetime') < time()) {
            return false;
        }
        
        return true;
    }
    
    /**
     * Regenerates the session. It makes the old session id obsolete and generates a new 
     * session id.
     * 
     * @access protected
     * @param \Zepi\Turbo\Request\WebRequest $request
     */
    protected function regenerateSession(WebRequest $request)
    {
        // Let the old session expire...
        $request->setSessionData('isObsolete', true);
        $request->setSessionData('maxLifetime', time() + 60);
        
        // Regenerate the session id but don't delete the old one
        session_regenerate_id(false);
        
        // Get the new session id
        $newSessionId = session_id();
        
        // Close both sessions to free them for other requests
        session_write_close();
        
        // Start the session with the new id
        session_id($newSessionId);
        session_start();
        
        // Delete the temporary session data
        $request->deleteSessionData('isObsolete');
        $request->deleteSessionData('maxLifetime');
    }
}
