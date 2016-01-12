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
 * This module delivers the mail core for zepi Turbo.
 * 
 * @package Zepi\Web\Mail
 * @author Matthias Zobrist <matthias.zobrist@zepi.net>
 * @copyright Copyright (c) 2015 zepi
 */

namespace Zepi\Web\Mail;

use \Zepi\Turbo\Module\ModuleAbstract;

/**
 * This module delivers the mail core for zepi Turbo.
 * 
 * @author Matthias Zobrist <matthias.zobrist@zepi.net>
 * @copyright Copyright (c) 2015 zepi
 */
class Module extends ModuleAbstract
{
    /**
     * @access protected
     * @var \Zepi\Core\Mail\Helper\MailHelper
     */
    protected $_mailHelper;
    
    /**
     * Initializes and return an instance of the given class name.
     * 
     * @access public
     * @param string $className
     * @return mixed
     */
    public function getInstance($className)
    {
        switch ($className) {
            case '\\Zepi\\Web\\Mail\\Helper\\MailHelper':
                if ($this->_mailHelper === null) {
                    $this->_mailHelper = new $className(
                        $this->_framework->getInstance('\\Zepi\\Core\\Utils\\Manager\\ConfigurationManager'),
                        $this->_framework->getInstance('\\Zepi\\Web\\General\\Manager\\TemplatesManager'),
                        $this->_framework->getInstance('\\Zepi\\Core\\Language\\Manager\\TranslationManager')
                    );
                }
                
                return $this->_mailHelper;
            break;
            
            default: 
                return new $className();
            break;
        }
    }
    
    /**
     * This action will be executed on the activation of the module
     * 
     * @access public
     * @param string $versionNumber
     * @param string $oldVersionNumber
     */
    public function activate($versionNumber, $oldVersionNumber = '')
    {
        // Configuration
        $configurationManager = $this->_framework->getInstance('\\Zepi\\Core\\Utils\\Manager\\ConfigurationManager');
        $configurationManager->addSettingIfNotSet('mailer', 'type', 'sendmail');
        $configurationManager->addSettingIfNotSet('mailer', 'sendmailCommand', '/usr/sbin/sendmail -bs');
        $configurationManager->addSettingIfNotSet('mailer', 'smtpHost', '');
        $configurationManager->addSettingIfNotSet('mailer', 'smtpPort', 25);
        $configurationManager->addSettingIfNotSet('mailer', 'smtpUsername', '');
        $configurationManager->addSettingIfNotSet('mailer', 'smtpPassword', '');
        $configurationManager->addSettingIfNotSet('mailer', 'sendFrom', 'info@turbo.local');
        $configurationManager->addSettingIfNotSet('mailer', 'sendFromName', 'zepi Turbo');
        $configurationManager->saveConfigurationFile();
    }
    
    /**
     * This action will be executed on the deactiviation of the module
     * 
     * @access public
     */
    public function deactivate()
    {
        // Configuration
        $configurationManager = $this->_framework->getInstance('\\Zepi\\Core\\Utils\\Manager\\ConfigurationManager');
        $configurationManager->removeSettingGroup('mailer');
        $configurationManager->saveConfigurationFile();
    }
}
