<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace ClientZone\ui;

/**
 * Description of InvoicesOfAddresButton
 *
 * @author vitex
 */
class InvoicesOfAddresButton extends \Ease\TWB\LinkButton
{

    /**
     * Link Button to Cutomer's invoices
     * 
     * @param \FlexiPeeHP\Adresar $address
     */
    public function __construct($address)
    {
        parent::__construct('customerorders.php?address='.$address,
            _('Customer Invoices'), 'info');
    }
}
