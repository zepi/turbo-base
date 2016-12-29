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
 * Form Element IP Field
 * 
 * @package Zepi\Web\UserInterface
 * @subpackage Form\Field
 * @author Matthias Zobrist <matthias.zobrist@zepi.net>
 * @copyright Copyright (c) 2016 zepi
 */

namespace Zepi\Web\UserInterface\Form\Field;

use \Zepi\Turbo\Framework;
use \Zepi\Web\UserInterface\Form\Error;
use \PhpExtended\Ip\Ipv4;
use \PhpExtended\Ip\Ipv4Network;
use \PhpExtended\Ip\Ipv6;
use \PhpExtended\Ip\Ipv6Network;
use \PhpExtended\Ip\ParseException;

/**
 * Form Element IP Field
 * 
 * @author Matthias Zobrist <matthias.zobrist@zepi.net>
 * @copyright Copyright (c) 2016 zepi
 */
class IpField extends FieldAbstract
{
    const TYPE_IP_ADDRESS = 'ip-address';
    const TYPE_SUBNET = 'subnet';
    
    /**
     * @var string
     */
    protected $type;
    
    /**
     * Constructs the object
     *
     * @access public
     * @param string $label
     * @param boolean $isMandatory
     * @param mixed $value
     * @param string $type
     * @param string $helpText
     * @param array $classes
     * @param string $placeholder
     * @param integer $tabIndex
     * @param boolean $autocomplete
     */
    public function __construct($key, $label, $isMandatory = false, $value = '', $type = self::TYPE_IP_ADDRESS, $helpText = '', $classes = array(), $placeholder = '', $tabIndex = null, $autocomplete = true)
    {
        parent::__construct($key, $label, $isMandatory, $value, $helpText, $classes, $placeholder, $tabIndex, $autocomplete);
        
        $this->type = $type;
    }
    
    /**
     * Returns the type of the ip field
     * 
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }
    
    /**
     * Returns the name of the template to render the field
     * 
     * @access public
     * @return string
     */
    public function getTemplateName()
    {
        return '\\Zepi\\Web\\UserInterface\\Templates\\Form\\Field\\IpField';
    }
    
    /**
     * Validates the value. Returns true if everything is okey or an Error
     * object if there was an error.
     *
     * @access public
     * @param \Zepi\Turbo\Framework $framework
     * @return boolean|\Zepi\Web\UserInterface\Form\Error
     */
    public function validate(Framework $framework)
    {
        $translationManager = $framework->getInstance('\\Zepi\\Core\\Language\\Manager\\TranslationManager');
        if ($this->value == '' && !$this->isMandatory) {
            return true;
        }
        
        try {
            $ipObject = $this->createIpObject($this->value);
        } catch (ParseException $e) {
            $ipObject = false;
        }
        
        if ($ipObject === false) {
            return new Error(
                Error::INVALID_VALUE,
                $translationManager->translate(
                    'The value for the field %field% is not valid.',
                    '\\Zepi\\Web\\UserInterface',
                    array(
                        'field' => $this->label,
                    )
                )
            );
        }
    
        return true;
    }
    
    protected function createIpObject($rawIp)
    {
        $ipObject = false;
        
        if ($this->type == self::TYPE_SUBNET) {
            $bitmask = substr($rawIp, strrpos($rawIp, '/'));
            $subnetIp = substr($rawIp, 0, (strlen($bitmask) * -1));
            
            if (filter_var($subnetIp, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)) {
                $ipObject = new IPv6Network($rawIp);
            } else if (filter_var($subnetIp, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
                $ipObject = new IPv4Network($rawIp);
            }
        } else {
            if (filter_var($this->value, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)) {
                $ipObject = new IPv6($this->value);
            } else if (filter_var($this->value, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
                $ipObject = new IPv4($this->value);
            }
        }
        
        return $ipObject;
    }
}
