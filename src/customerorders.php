<?php

namespace Shop4FlexiBee;

/**
 * shop4flexibee - Hlavní strana.
 *
 * @author     Vítězslav Dvořák <vitex@arachne.cz>
 * @copyright  2017 VitexSoftware v.s.cz
 */
require_once 'includes/Init.php';

$oPage->onlyForUser();


$operation = $oPage->getRequestValue('operation');
$addressID = $oPage->getRequestValue('address');
$invoiceID = $oPage->getRequestValue('invoice');


$customer = new \FlexiPeeHP\Adresar(is_numeric($addressID) ? intval($addressID) : $addressID);


$oPage->addItem(new ui\PageTop(_('My Orders')));

$fetcher = new \FlexiPeeHP\FakturaVydana();

$oPage->container->addItem(new ui\OrdersListing($fetcher,
    ['firma' => $customer], _('Invoices')));

$fetcher2 = new \FlexiPeeHP\FlexiBeeRO(null, ['evidence' => 'objednavka-prijata']);

$oPage->container->addItem(new ui\OrdersListing($fetcher2,
    ['firma' => $customer], _('Orders')));


$oPage->addItem(new ui\PageBottom());

$oPage->draw();
