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
 * Displays the change password site for the profile.
 * 
 * @package Zepi\Web\AccessControl
 * @subpackage EventHandler
 * @author Matthias Zobrist <matthias.zobrist@zepi.net>
 * @copyright Copyright (c) 2015 zepi
 */

namespace Zepi\Web\AccessControl\EventHandler;

use \Zepi\Turbo\FrameworkInterface\EventHandlerInterface;
use \Zepi\Turbo\Framework;
use \Zepi\Turbo\Request\RequestAbstract;
use \Zepi\Turbo\Response\Response;
use \Zepi\Web\AccessControl\Entity\User;
use \Zepi\Web\UserInterface\Form\Form;
use \Zepi\Web\UserInterface\Form\Group;
use \Zepi\Web\UserInterface\Form\ErrorBox;
use \Zepi\Web\UserInterface\Form\Error;
use \Zepi\Web\UserInterface\Form\ButtonGroup;
use \Zepi\Web\UserInterface\Form\Field\Text;
use \Zepi\Web\UserInterface\Form\Field\Password;
use \Zepi\Web\UserInterface\Form\Field\Submit;

/**
 * Displays the change password site for the profile.
 * 
 * @author Matthias Zobrist <matthias.zobrist@zepi.net>
 * @copyright Copyright (c) 2015 zepi
 */
class ProfileChangePassword implements EventHandlerInterface
{
    /**
     * Displays the change password site for the profile.
     * 
     * @access public
     * @param \Zepi\Turbo\Framework $framework
     * @param \Zepi\Turbo\Request\RequestAbstract $request
     * @param \Zepi\Turbo\Response\Response $response
     * @param mixed $value
     */
    public function executeEvent(Framework $framework, RequestAbstract $request, Response $response, $value = null)
    {
        // Redirect if the user hasn't a valid session
        if (!$request->hasSession()) {
            $response->redirectTo('/');
            return;
        }
        
        $translationManager = $framework->getInstance('\\Zepi\\Core\\Language\\Manager\\TranslationManager');
        $templatesManager = $framework->getInstance('\\Zepi\\Web\\General\\Manager\\TemplatesManager');
        
        // Set the title for the page
        $metaInformationManager = $framework->getInstance('\\Zepi\\Web\\General\\Manager\\MetaInformationManager');
        $metaInformationManager->setTitle($translationManager->translate('Profile - Change password', '\\Zepi\\Web\\AccessControl'));
        
        // Get the Layout Renderer
        $layoutRenderer = $framework->getInstance('\\Zepi\\Web\\UserInterface\\Renderer\\Layout');
        
        // Get the Form object
        $changePasswordForm = $this->_createForm($framework, $request, $response);
        
        // Process the submitted form data
        $changePasswordForm->processFormData($request);
        
        $result = false;
        $errors = array();
        if ($changePasswordForm->isSubmitted()) {
            $errors = $changePasswordForm->validateFormData($framework);
            if (count($errors) === 0) {
                $result = $this->_changePassword($changePasswordForm, $framework, $request, $response);
            }
        }
        
        // Fill the errors into the error box
        $errorBox = $changePasswordForm->getPart('login-errors');
        if (($changePasswordForm->isSubmitted() && !$result) || count($errors) > 0) {
            if (count($errors) === 0) {
                $errorBox->addError(new Error(
                    Error::GENERAL_ERROR,
                    $translationManager->translate('Your submitted data weren\'t correct. Please repeat the login with your correct user data or contact the administrator.', '\\Zepi\\Web\\AccessControl')
                ));
            } else {
                foreach ($errors as $error) {
                    $errorBox->addError($error);
                }
            }
        }
        
        // If $result isn't true, display the login form
        if (!$changePasswordForm->isSubmitted() || $errorBox->hasErrors()) {
            $templatesManager = $framework->getInstance('\\Zepi\\Web\\General\\Manager\\TemplatesManager');
            $renderedOutput = $templatesManager->renderTemplate('\\Zepi\\Web\\AccessControl\\Templates\\ProfileChangePasswordForm', array(
                'result' => $result,
                'errors' => $errors,
                'form' => $changePasswordForm, 
                'layoutRenderer' => $layoutRenderer
            ));
            
            $response->setOutput($renderedOutput);
        } else {
            // Display the successful changed message
            $response->setOutput($templatesManager->renderTemplate('\\Zepi\\Web\\AccessControl\\Templates\\ProfileChangePasswordFinished', $framework, $request, $response));
        }
    }

    /**
     * Changes the password for the logged in user.
     * 
     * @access protected
     * @param \Zepi\Web\UserInterface\Form\Form $form
     * @param \Zepi\Turbo\Framework $framework
     * @param \Zepi\Turbo\Request\RequestAbstract $request
     * @param \Zepi\Turbo\Response\Response $response
     */
    protected function _changePassword(Form $form, Framework $framework, RequestAbstract $request, Response $response)
    {
        // Get the logged in user 
        $session = $request->getSession();
        $user = $session->getUser();
        
        // Get the password data
        $oldPassword = trim($form->getField('change-password', 'old-password')->getValue());
        $newPassword = trim($form->getField('change-password', 'new-password')->getValue());
        $newPasswordConfirmed = trim($form->getField('change-password', 'new-password-confirmed')->getValue());
        
        $result = $this->_validateData($framework, $user, $oldPassword, $newPassword, $newPasswordConfirmed);
        
        // If the validate function returned a string there was an error in the validation.
        if ($result !== true) {
            return $result;
        }
        
        // Change the password
        $user->setNewPassword($newPassword);
        
        // Get the UserManager to update the user
        $userManager = $framework->getInstance('\\Zepi\\Web\\AccessControl\\Manager\\UserManager');
        $result = $userManager->updateUser($user);
        
        return $result;
    }

    /**
     * Validates the data for the change password function.
     * 
     * @access protected
     * @param \Zepi\Turbo\Framework $framework
     * @param \Zepi\Web\AccessControl\Entity\User $user
     * @param string $oldPassword
     * @param string $newPassword
     * @param string $newPasswordConfirmed
     */
    protected function _validateData(Framework $framework, User $user, $oldPassword, $newPassword, $newPasswordConfirmed)
    {
        $translationManager = $framework->getInstance('\\Zepi\\Core\\Language\\Manager\\TranslationManager');
        
        // Old password
        if (!$user->comparePasswords($oldPassword)) {
            return $translationManager->translate('The old password is not valid.', '\\Zepi\\Web\\AccessControl');
        }
        
        // New password
        if (strlen($newPassword) < 8) {
            return $translationManager->translate('The new password needs at least 8 characters.', '\\Zepi\\Web\\AccessControl');
        }
        
        if ($newPassword != $newPasswordConfirmed) {
            return $translationManager->translate('The new password are not equal.', '\\Zepi\\Web\\AccessControl');
        }

        return true;
    }

    /**
     * Returns the Form object for the change password form
     * 
     * @access protected
     * @param \Zepi\Turbo\Framework $framework
     * @param \Zepi\Turbo\Request\RequestAbstract $request
     * @param \Zepi\Turbo\Response\Response $response
     * @return \Zepi\Web\UserInterface\Form\Form
     */
    protected function _createForm(Framework $framework, RequestAbstract $request, Response $response)
    {
        $translationManager = $framework->getInstance('\\Zepi\\Core\\Language\\Manager\\TranslationManager');
        
        // Create the form
        $form = new Form('change-password', $request->getFullRoute('profile/change-password'), 'post');
        
        // Add the user data group
        $errorBox = new ErrorBox(
            'login-errors',
            1
        );
        $form->addPart($errorBox);
        
        // Add the user data group
        $group = new Group(
            'change-password',
            $translationManager->translate('Please insert your old and your new password', '\\Zepi\\Web\\AccessControl'),
            array(
                new Password(
                    'old-password',
                    $translationManager->translate('Old password', '\\Zepi\\Web\\AccessControl'),
                    true
                ),
                new Password(
                    'new-password',
                    $translationManager->translate('New password', '\\Zepi\\Web\\AccessControl'),
                    true
                ),
                new Password(
                    'new-password-confirmed',
                    $translationManager->translate('Confirm new password', '\\Zepi\\Web\\AccessControl'),
                    true
                ),
            )
        );
        $form->addPart($group);
        
        // Add the submit button
        $buttonGroup = new ButtonGroup(
            'buttons',
            array(
                new Submit(
                    'submit',
                    $translationManager->translate('Change password', '\\Zepi\\Web\\AccessControl')
                )
            ),
            100
        );
        $form->addPart($buttonGroup);
        
        return $form;
    }
}
