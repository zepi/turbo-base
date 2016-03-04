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
use \Zepi\Api\Rest\Entity\Request as RestRequest;
use \Zepi\Api\Rest\Entity\Response as RestResponse;

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

    /*public function sendRequest(RestRequest $request)
    {
        $headers = array(
            'Accept: ' . $request->getAcceptedMimeType(),
            'Content-Type: application/json',
        );
        $data = $request->getRequestData();
        $publicKey = 'fd4121d2621445fae6dce853362545e9';
        $privateKey = '5d18a7b9016db1cf307201e7a3ca7ef09fe06b7635d604d3ae2dd10f2001ef4f';
        $auth = $publicKey . ':' . $this->_generateHmac($privateKey, $request->getEndpoint(), $data);
        var_dump($auth);
        $host = 'http://autopilot.local';
        
        $handle = curl_init();
        curl_setopt($handle, CURLOPT_URL, $host . $request->getEndpoint());
        curl_setopt($handle, CURLOPT_USERPWD, $auth);
        curl_setopt($handle, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($handle, CURLOPT_HEADER, true);
        
        switch($request->getRequestMethod()) {
            case 'GET':
                break;
            case 'POST':
                curl_setopt($handle, CURLOPT_POST, true);
                curl_setopt($handle, CURLOPT_POSTFIELDS, $data);
                break;
            case 'PUT':
                curl_setopt($handle, CURLOPT_CUSTOMREQUEST, 'PUT');
                curl_setopt($handle, CURLOPT_POSTFIELDS, $data);
                break;
            case 'DELETE':
                curl_setopt($handle, CURLOPT_CUSTOMREQUEST, 'DELETE');
                break;
        }
        
        $result = curl_exec($handle);
        $code = curl_getinfo($handle, CURLINFO_HTTP_CODE);
        
        $headerSize = curl_getinfo($handle, CURLINFO_HEADER_SIZE);
        $headers = $this->_parseHeaders(substr($result, 0, $headerSize));
        $body = substr($result, $headerSize);
        var_dump($code);
        var_dump($headers);
        var_dump($body);
        //if 
        
        $response = new RestResponse($code, $result, $parsedResult, $request);
        
        return $response;
    }
    
    protected function _parseHeaders($headersRaw)
    {
        $lines = explode(PHP_EOL, $headersRaw);
        $headers = array();
        foreach ($lines as $line) {
            if (strpos($line, ':') === false) {
                continue;
            }
            
            list($key, $value) = explode(':', $line);
            
            $headers[$key] = trim($value);
        }
        
        return $headers;
    }*/
    
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
