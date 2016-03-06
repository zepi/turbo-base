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
use \Zepi\Api\Rest\Exception;
use \Zepi\Api\Rest\Entity\Request as RestRequest;
use \Zepi\Api\Rest\Entity\Response as RestResponse;
use \Zepi\Api\AccessControl\Entity\ApiKey;
use \GuzzleHttp\Client;
use \GuzzleHttp\Exception\RequestException;

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

    public function sendRequest(ApiKey $apiKey, RestRequest $request)
    {
        $hmac = $this->_generateHmac($apiKey->getPrivateKey(), $request->getEndpoint(), array_merge($request->getQueryData(), $request->getPostData()));
        $base = 'http://autopilot.local';
        
        $client = new Client([
            'base_uri' => $base
        ]);
        
        $args = [
            'auth' => [$apiKey->getPublicKey(), $hmac],
            'headers' => [
                'Accept' => 'application/json'
            ]
        ];
        
        switch($request->getRequestMethod()) {
            case 'GET':
            case 'PUT':
            case 'DELETE':
                $args['query'] = $request->getQueryData();
            case 'POST':
                $args['form_params'] = $request->getPostData();
                break;
        }
        
        try {
            $responseRaw = $client->request($request->getRequestMethod(), $request->getEndpoint(), $args);
        } catch (RequestException $e) {
            if (!$e->hasResponse()) {
                throw new Exception('Cannot send REST request.', 0, $e);
            }
            
            $responseRaw = $e->getResponse();
        }
        
        $body = (string) $responseRaw->getBody();
        $parsedResult = json_decode($body);
        if ($parsedResult == false) {
            $parsedResult = new \stdClass();
        }

        $response = new RestResponse($responseRaw->getStatusCode(), $body, $parsedResult, $request);

        return $response;
    }

    
    /**
     * Send the api result to the client
     *
     * @access public
     * @param \Zepi\Turbo\Request\RequestAbstract $request
     * @param \Zepi\Turbo\Response\Response $response
     * @param array $result
     */
    public function sendResponse(RequestAbstract $request, Response $response, $result)
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
    public function validateRequest(ApiKey $apiKey, $hmac, $route, $data)
    {
        // Regenerate the hmac
        $regeneratedHmac = $this->_generateHmac($apiKey->getPrivateKey(), $route, $data);
        
        // Verify the hmac and the time
        if ($hmac === $regeneratedHmac) {
            return true;
        }
        
        return false;
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
