<?php
/**
 * This helpers delivers all functionality to encode and validate
 * an REST API request.
 * 
 * @package Zepi\Api\Rest
 * @subpackage Helper
 * @author Matthias Zobrist <matthias.zobrist@zepi.net>
 * @copyright Copyright (c) 2016 zepi
 */

namespace Zepi\Api\Rest\Helper;

use \Zepi\Api\AccessControl\Manager\TokenManager;
use \Zepi\Turbo\Request\RequestAbstract;
use \Zepi\Turbo\Response\Response;

/**
 * This helpers delivers all functionality to encode and validate
 * an REST API request.
 * 
 * @author Matthias Zobrist <matthias.zobrist@zepi.net>
 * @copyright Copyright (c) 2016 zepi
 */
class RestHelper
{
    /**
     * @access protected
     * @var \Zepi\Api\AccessControl\Manager\TokenManager
     */
    protected $_tokenManager;
    
    /**
     * Constructs the object
     * 
     * @access public
     * @param \Zepi\Api\AccessControl\Manager\TokenManager $tokenManager
     */
    public function __construct(TokenManager $tokenManager)
    {
        $this->_tokenManager = $tokenManager;
    }

    /**
     * Send the api result to the client
     *
     * @access public
     * @param \Zepi\Turbo\Request\RequestAbstract $request
     * @param \Zepi\Turbo\Response\Response $response
     * @param array $result
     */
    public function sendApiResult(RequestAbstract $request, Response $response, $result)
    {
        $dataType = $request->getHeader('Accept');
        
        switch ($dataType) {
            case 'text/xml':
                $xml = new \SimpleXMLElement('<root/>');
                $this->_fillXml($xml, $result);
                
                $response->sendHeader('Content-Type: text/xml');
                $response->setOutput($xml->asXML());
            break;
            
            case 'application/json':
            default:
                $response->sendHeader('Content-Type: application/json');
                $response->setOutput(json_encode($result));
            break;
        }
    }
    
    /**
     * Builds a xml string out of the given array
     * 
     * @access public
     * @param \SimpleXMLElement $element
     * @param array $data
     */
    protected function _fillXml(\SimpleXMLElement $element, $data)
    {
        foreach ($data as $key => $value) {
            if (is_array($value) || is_object($value)) {
                if (!is_numeric($key)) {
                    $child = $element->addChild($key);
                } else {
                    $child = $element;
                }
                
                $this->_fillXml($child, $value);
            } else {
                $element->addChild($key, $value);
            }
        }
    }
    
    /**
     * Validates the request and returns the access entity
     * if everything is correct or false if the request is wrong
     * 
     * @access public
     * @param \Zepi\Turbo\Request\RequestAbstract $request
     * @return false|\Zepi\Api\AccessControl\Entity\Token
     */
    public function validate(RequestAbstract $request)
    {
        // Parse the authorization information
        $authorization = $this->_parseAuthorizationString($request->getHeader('Authorization'));
        $publicKey = $authorization['publicKey'];
        $hmac = $authorization['hmac'];

        // Verify the public key
        if (!$this->_tokenManager->hasTokenForPublicKey($publicKey)) {
            return false;
        }
        
        // Load the token
        $token = $this->_tokenManager->getTokenForPublicKey($publicKey);
        
        // Get the needed data
        $requestedRoute = $request->getRoute();
        $data = $request->getParams();

        // Regenerate the hmac
        $regeneratedHmac = $this->_generateHmac($token->getKey(), $requestedRoute, $data);
        
        // Verify the hmac and the time
        if ($hmac === $regeneratedHmac) {
            return $token;
        }
        
        return false;
    }
    
    /**
     * Parses the authorization string and returns an array with
     * the public key and the hmac
     * 
     * @access protected
     * @param string $authorizationString
     * @return array
     */
    protected function _parseAuthorizationString($authorizationString)
    {
        if (strpos($authorizationString, 'Basic') !== false) {
            $authorizationString = trim(substr($authorizationString, 6));
        }
        
        $decoded = base64_decode($authorizationString);
        $delimiterPos = strpos($decoded, ':');
        
        return array(
            'publicKey' => substr($decoded, 0, $delimiterPos),
            'hmac' => substr($decoded, $delimiterPos + 1)
        );
    }
    
    /**
     * Returns an array with all needed data
     * 
     * @access public
     * @param \Zepi\Api\AccessControl\Entity\Token $token
     * @param array $data
     * @return array
     */
    public function encode(Token $token, $data)
    {
        $hmac = $this->_generateHmac($token->getKey(), $data);
        
        $data['hmac'] = $hmac;
    }
    
    /**
     * Generates the hmac for the given data
     * 
     * @access protected
     * @param string $privateKey
     * @param string $requestedRoute
     * @param array $data
     * @return string
     */
    protected function _generateHmac($privateKey, $requestedRoute, $data)
    {
        $completeString = $requestedRoute . json_encode($data);
        
        return hash_hmac('sha256', $completeString, $privateKey);
    }
}
