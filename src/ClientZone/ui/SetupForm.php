<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace ClientZone\ui;

/**
 * Description of SetupForm
 *
 * @author vitex
 */
class SetupForm extends \FlexiPeeHP\ui\ConnectionForm
{
    public function __construct($formAction = 'setup.php', $formMethod = 'post',
                                $tagProperties = null)
    {
        parent::__construct($formAction, $formMethod, $tagProperties);

        $this->addInput( new \Ease\Html\InputEmailTag('SEND_MAILS_FROM') , _('Send mails From'), 'clientzone@'.$_SERVER['HTTP_HOST'], _('Outgoing emails sender address'));
        $this->addInput( new \Ease\Html\InputEmailTag('SEND_INFO_TO') , _('Send info To'), _('Your Address').'@'.$_SERVER['HTTP_HOST'], _('Where to send info about new orderers'));
//        $this->addInput( new \Ease\Html\InputEmailTag('EMAIL_FROM') , 'caption', '$placeholder', '$helptext');
//        $this->addInput( new \Ease\Html\InputEmailTag('EASE_EMAILTO') , 'caption', '$placeholder', '$helptext');
        $this->addInput( new \Ease\ui\TWBSwitch('SUPPRESS_EMAILS') , _('Suppress Emails'), null, _('Do not send any emails'));
        $this->addInput( new \Ease\ui\TWBSwitch('ALLOW_REGISTER') , _('Allow Sign Up'), null, _('Customer can create new account'));
        $this->addInput( new \Ease\ui\TWBSwitch('SHOW_PRICELIST') , _('Show Pricelist'), null, _('Show pricelist of items with "eshop" flag or label'));
    }                                                                                                                                                                                                                         
}
