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
 * Helper to send e-mails
 * 
 * @package Zepi\Web\Mail
 * @subpackage Helper
 * @author Matthias Zobrist <matthias.zobrist@zepi.net>
 * @copyright Copyright (c) 2015 zepi
 */

namespace Zepi\Web\Mail\Helper;

use \Zepi\Turbo\Framework;
use \Zepi\Turbo\Backend\ObjectBackendAbstract;
use \Zepi\Web\Mail\Exception;
use \Zepi\Core\Utils\Manager\ConfigurationManager;
use \Zepi\Web\General\Manager\TemplatesManager;
use \Zepi\Core\Language\Manager\TranslationManager;

/**
 * Helper to send e-mails
 * 
 * @author Matthias Zobrist <matthias.zobrist@zepi.net>
 * @copyright Copyright (c) 2015 zepi
 */
class MailHelper
{
    /**
     * @access protected
     * @var \Zepi\Core\Utils\Manager\ConfigurationManager
     */
    protected $configurationManager;
    
    /**
     * @access protected
     * @var \Zepi\Web\General\Manager\TemplatesManager
     */
    protected $templatesManager;
    
    /**
     * @access protected
     * @var \Zepi\Core\Language\Manager\TranslationManager
     */
    protected $translationManager;
    
    /**
     * Constructs the object
     * 
     * @access public
     * @param \Zepi\Core\Utils\Manager\ConfigurationManager $configurationManager
     * @param \Zepi\Web\General\Manager\TemplatesManager $templatesManager
     * @param \Zepi\Core\Language\Manager\TranslationManager $translationManager
     */
    public function __construct(ConfigurationManager $configurationManager, TemplatesManager $templatesManager, TranslationManager $translationManager)
    {
        $this->configurationManager = $configurationManager;
        $this->templatesManager = $templatesManager;
        $this->translationManager = $translationManager;
    }
    
    /**
     * Returns the translated string
     * 
     * @param string $string
     * @param string $namespace
     * @param array $args
     * @return string
     */
    public function translate($string, $namespace, $args = array())
    {
        return $this->translationManager->translate($string, $namespace, $args);
    }
    
    /**
     * Returns the mailer object
     *
     * @access protected
     * @return \Swift_Mailer
     * 
     * @throws \Zepi\Web\Mail\Exception No mail transport defined. Please check your configuration.
     */
    protected function getMailer()
    {
        $transport = null;
        if ($this->configurationManager->getSetting('mailer', 'type') === 'sendmail') {
            $transport = \Swift_SendmailTransport::newInstance($this->configurationManager->getSetting('mailer', 'sendmailCommand'));
        } else if ($this->configurationManager->getSetting('mailer', 'type') === 'smtp') {
            $host = $this->configurationManager->getSetting('mailer', 'smtpHost');
            $port = $this->configurationManager->getSetting('mailer', 'smtpPort');
            $username = $this->configurationManager->getSetting('mailer', 'smtpUsername');
            $password = $this->configurationManager->getSetting('mailer', 'smtpPassword');
            
            $transport = \Swift_SmtpTransport::newInstance($host, $port);
            $transport->setUsername($username);
            $transport->setPassword($password);
        }
        
        if ($transport === null) {
            throw new Exception('No mail transport defined. Please check your configuration.');
        }

        return \Swift_Mailer::newInstance($transport);
    }
    
    /**
     * Sends a email
     *
     * @access public
     * @param string $recipient
     * @param string $subject
     * @param string $htmlBody
     * @param string $textBody
     * @return number
     */
    public function sendMail($recipient, $subject, $htmlBody, $textBody = false)
    {
        $message = \Swift_Message::newInstance();
    
        // Subject
        $message->setSubject($subject);
    
        // From
        $fromEmail = $this->configurationManager->getSetting('mailer', 'sendFrom');
        $fromName = $this->configurationManager->getSetting('mailer', 'sendFromName');
        $message->setFrom(array($fromEmail => $fromName));
    
        // To
        $message->setTo($recipient);
    
        // HTML body
        $message->setBody($htmlBody, 'text/html');
        
        // Text body
        if ($textBody === false) {
            $textBody = $this->createTextBody($htmlBody);
        }
        $message->addPart($textBody, 'text/plain');
    
        $mailer = $this->getMailer();
        return $mailer->send($message);
    }
    
    /**
     * Convert the html of the body to text
     * 
     * @access protected
     * @param string $html
     * @return string
     */
    protected function createTextBody($html)
    {
        $text = \Html2Text\Html2Text::convert($html);
        
        return $text;
    }
}
