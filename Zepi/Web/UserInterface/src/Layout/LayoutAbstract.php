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
 * LayoutAbstract
 * 
 * @package Zepi\Web\UserInterface
 * @subpackage Layout
 * @author Matthias Zobrist <matthias.zobrist@zepi.net>
 * @copyright Copyright (c) 2016 zepi
 */

namespace Zepi\Web\UserInterface\Layout;

use \Zepi\Turbo\Framework;
use \Zepi\Turbo\Request\WebRequest;
use \Zepi\Core\Language\Manager\TranslationManager;
use \Zepi\Web\UserInterface\Form\Form;

/**
 * LayoutAbstract
 * 
 * @author Matthias Zobrist <matthias.zobrist@zepi.net>
 * @copyright Copyright (c) 2016 zepi
 */
abstract class LayoutAbstract
{
    /**
     * @var \Zepi\Turbo\Framework
     */
    protected $framework;
    
    /**
     * @var \Zepi\Core\Language\Manager\TranslationManager
     */
    protected $translationManager;
    
    /**
     * @var \Zepi\Web\UserInterface\Layout\AbstractContainer
     */
    protected $layout;
    
    /**
     * Construct the object
     * 
     * @param \Zepi\Turbo\Framework $framework
     * @param \Zepi\Core\Language\Manager\TranslationManager $translationManager
     */
    public function __construct(Framework $framework, TranslationManager $translationManager)
    {
        $this->framework = $framework;
        $this->translationManager = $translationManager;
    }
    
    /**
     * Translates the given string
     * 
     * @param string $string
     * @param string $namespace
     * @param array $arguments
     * @return string
     */
    protected function translate($string, $namespace, $arguments = array())
    {
        return $this->translationManager->translate($string, $namespace, $arguments);
    }
    
    /**
     * Generates the layout
     * 
     * @return \Zepi\Web\UserInterface\Layout\AbstractContainer
     */
    abstract protected function generateLayout();
    
    /**
     * Returns the object for the layout
     * 
     * @return \Zepi\Web\UserInterface\Layout\AbstractContainer
     */
    public function getLayout()
    {
        $this->verifyLayout();
        
        return $this->layout;
    }
    
    /**
     * Searches the submitted Form object in the layout and validates the data
     * 
     * @param \Zepi\Turbo\Request\WebRequest $request
     * @param callable $callback
     * @return string
     */
    public function validateFormData(WebRequest $request, callable $callback = null)
    {
        $form = $this->searchSubmittedForm($request);
        
        if ($form === null) {
            return Form::NOT_SUBMITTED;
        }
        
        // Validate the data
        $errors = $form->validateFormData($this->framework);
        
        // Execute the custom validation method
        if (is_callable($callback)) {
            $callbackErrors = call_user_func_array($callback, array($this->getFormValues()));
            
            if (is_array($callbackErrors)) {
                $errors = array_merge($errors, $callbackErrors);
            }
        }
        
        if (is_array($errors) && count($errors) > 0) {
            $this->updateErrorBox($form, $errors);
            return Form::DATA_INVALID;
        }
        
        return Form::DATA_VALID;
    }
    
    /**
     * Searches the submitted form object in the layout.
     * 
     * @param \Zepi\Turbo\Request\WebRequest $request
     * @return null|\Zepi\Web\UserInterface\Form\Form
     */
    protected function searchSubmittedForm(WebRequest $request)
    {
        $this->verifyLayout();
        
        $form = null;
        foreach ($this->layout->getChildrenByType('\\Zepi\\Web\\UserInterface\\Form\\Form', true) as $availableForm) {
            $availableForm->processFormData($request);
        
            if ($availableForm->isSubmitted()) {
                $form = $availableForm;
                break;
            }
        }
        
        return $form;
    }
    
    /**
     * Updates all error boxes inside the given form
     * 
     * @param \Zepi\Web\UserInterface\Form\Form $form
     * @param array $errors
     */
    protected function updateErrorBox(Form $form, $errors)
    {
        foreach ($form->getChildrenByType('\\Zepi\\Web\\UserInterface\\Form\\ErrorBox', true) as $errorBox) {
            $errorBox->updateErrorBox($form, false, $errors);
        }
    }
    
    /**
     * Returns all the values of the form fields
     * 
     * @return array
     */
    public function getFormValues()
    {
        $this->verifyLayout();
        
        $values = array();

        foreach ($this->layout->getChildrenByType('\\Zepi\\Web\\UserInterface\\Form\\Form', true) as $form) {
            $values = $this->extractFormValues($form);
        }
        
        return $values;
    }
    
    /**
     * Extracts all values from the given form and returns
     * an array with the field path and the field value.
     * 
     * @param \Zepi\Web\UserInterface\Form\Form $form
     * @return array
     */
    protected function extractFormValues(Form $form)
    {
        $values = array();

        foreach ($form->getChildrenByType('\\Zepi\\Web\\UserInterface\\Form\\Field\\FieldAbstract', true) as $field) {
            if ($field->getValue() === null) {
                continue;
            }
            
            $values[$field->getPath($form)] = $field->getValue();
        }
        
        return $values;
    }
    
    /**
     * Generates the layout if the layout is not set
     */
    protected function verifyLayout()
    {
        if ($this->layout === null) {
            $this->layout = $this->generateLayout();
        }
    }
}
