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
 * A Form is a form which will be displayed in the frontend.
 * 
 * @package Zepi\Web\UserInterface
 * @subpackage Form
 * @author Matthias Zobrist <matthias.zobrist@zepi.net>
 * @copyright Copyright (c) 2015 zepi
 */

namespace Zepi\Web\UserInterface\Form;

use \Zepi\Turbo\Framework;
use \Zepi\Web\UserInterface\Form\Group;
use \Zepi\Web\UserInterface\Form\Error;
use \Zepi\Turbo\Request\WebRequest;
use \Zepi\Turbo\Response\Response;
use \Zepi\Web\UserInterface\Layout\Part;

/**
 * A Form is a form which will be displayed in the frontend.
 * 
 * @author Matthias Zobrist <matthias.zobrist@zepi.net>
 * @copyright Copyright (c) 2015 zepi
 */
class Form extends Part
{
    const DATA_VALID = 'data_valid';
    const DATA_INVALID = 'data_invalid';
    const DATA_ERROR = 'data_error';
    const NOT_SUBMITTED = 'not_submited';
    
    /**
     * @access protected
     * @var string
     */
    protected $key;
    
    /**
     * @access protected
     * @var string
     */
    protected $url;
    
    /**
     * @access protected
     * @var string
     */
    protected $method;
    
    /**
     * @access protected
     * @var boolean
     */
    protected $isSubmitted = false;
    
    /**
     * @access protected
     * @var string
     */
    protected $templateKey = '\\Zepi\\Web\\UserInterface\\Templates\\Form\\Form';
    
    /**
     * Constructs the object
     * 
     * @access public
     * @param string $key
     * @param string $url
     * @param string $method
     * @param array $parts
     */
    public function __construct($key, $url, $method, $parts = array())
    {
        $this->key = $key;
        $this->url = $url;
        $this->method = $method;
        
        foreach ($parts as $part) {
            $this->addPart($part);
        }
        
        $this->publicKey = $key;
    }
    
    /**
     * Returns the html id of the form
     * 
     * @access public
     * @return string
     */
    public function getHtmlId()
    {
        return $this->key;
    }
    
    /**
     * Returns the url of the Form
     * 
     * @access public
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }
    
    /**
     * Returns the method of the Form
     * 
     * @access public
     * @return string
     */
    public function getMethod()
    {
        return $this->method;
    }
    
    /**
     * Returns all fields of the Form
     * 
     * @access public
     * @return array
     */
    public function getFields()
    {
        $fields = array();
        foreach ($this->parts as $part) {
            $fields = array_merge($fields, $part->getParts());
        }
        
        return $fields;
    }
    
    /**
     * Returns the field for the given group and field key. Return false
     * if the field does not exists.
     * 
     * @access public
     * @param string $groupKey
     * @param string $fieldKey
     * @return boolean|\Zepi\Web\UserInterface\Form\Field\FieldAbstract
     */
    public function getField($groupKey, $fieldKey)
    {
        foreach ($this->parts as $part) {
            if ($part->getKey() !== $groupKey) {
                continue;
            }
            
            foreach ($part->getParts() as $field) {
                if ($field->getKey() !== $fieldKey) {
                    continue;
                }
                
                return $field;
            }
        }
        
        return false;
    }

    /**
     * Processes all form data
     * 
     * @access public
     * @param \Zepi\Turbo\Request\WebRequest $request
     * @param \Zepi\Turbo\Response\Response $response
     */
    public function processFormData(WebRequest $request, Response $response)
    {
        /**
         * If there is no csrf-key or csrf-token we return immediately 
         * because this could be a hacker.
         */ 
        if (!$request->hasParam('csrf-key') || !$request->hasParam('csrf-token')) {
            return;
        }
        
        /**
         * Otherwise lookup the csrf-key and csrf-token in the session and
         * validate them
         */
        $key = $request->getParam('csrf-key');
        $token = $request->getParam('csrf-token');
        $sessionToken = $request->getSessionData($key);
        
        /**
         * Remove the old token
         */
        $request->deleteSessionData($key);
        
        /**
         * If the token from the form not is equal with the token in the session
         * we will return here
         */
        if ($sessionToken !== $token) {
            return;
        }
        
        /**
         * Process the form data if the csrf tokens are valid
         */
        foreach ($this->getChildrenByType('\\Zepi\\Web\\UserInterface\\Form\\Field\\FieldAbstract') as $field) {
            if ($request->hasParam($field->getHtmlName())) {
                $field->setValue($request->getParam($field->getHtmlName()), $request);
            }
        }
        
        /**
         * Execute the form event
         */
        if ($request->hasParam('form-update-request')) {
            $fieldId = $request->getParam('form-update-request');
            
            foreach ($this->getChildrenByType('\\Zepi\\Web\\UserInterface\\Form\\Field\\FieldAbstract') as $field) {
                if ($field->getHtmlId() === $fieldId) {
                    $field->executeFormUpdateRequest($request, $response, $this);
                }
            }
        }
    }
    
    /**
     * Validates the form data and returns an array with errors.
     * If the array is empty there was no error.
     * 
     * @access public
     * @param \Zepi\Turbo\Framework $framework
     * @return array
     */
    public function validateFormData(Framework $framework)
    {
        $translationManager = $framework->getInstance('\\Zepi\\Core\\Language\\Manager\\TranslationManager');
        
        $errors = array();
        foreach ($this->getChildrenByType('\\Zepi\\Web\\UserInterface\\Form\\Field\\FieldAbstract') as $field) {
            // Validate mandatory fields
            if ($field->isMandatory() && !$field->hasValue()) {
                $fieldError = new Error(
                    Error::MANDATORY_FIELD, 
                    sprintf($translationManager->translate('%s is a mandatory field. Please fill out the field.', '\\Zepi\\Web\\UserInterface'), $field->getLabel()), 
                    $field
                );
                
                $errors[] = $fieldError;
                $field->addError($fieldError);
            }
            
            $result = $field->validate($framework);
            
            if ($result !== true) {
                $errors[] = $result;
                $field->addError($result);
            }
        }
        
        return $errors;
    }
    
    /**
     * Returns true if the form was submitted.
     * 
     * @access public
     * @return boolean
     */
    public function isSubmitted()
    {
        return ($this->isSubmitted);
    }
    
    /**
     * Sets the state of the form when it was submitted.
     * 
     * @access public
     * @param boolean $isSubmitted
     */
    public function setIsSubmitted($isSubmitted)
    {
        $this->isSubmitted = $isSubmitted;
    }
    
    /**
     * Generates the csrf key and token and saves them 
     * in the session data.
     * 
     * @access public
     * @param \Zepi\Turbo\Request\WebRequest $request
     * @return array
     */
    public function generateCsrfToken(WebRequest $request)
    {
        $key = 'csrf-' . $this->generateHash(32);
        $token = $this->generateHash(128);
        
        $request->setSessionData($key, $token);
        
        return array('key' => $key, 'token' => $token);
    }
    
    /**
     * Generates a random hash
     * 
     * @param integer $length
     * @return string
     */
    protected function generateHash($length)
    {
        $token = '';
        for ($i = 0; $i < $length; ++ $i) {
            $rand = mt_rand(0, 35);
            
            if ($rand < 26) {
                $char = chr(ord('a') + $rand);
            } else {
                $char = chr(ord('0') + $rand - 26);
            }
            
            $token .= $char;
        }
        return $token;
    }
}
