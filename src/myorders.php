<?php

namespace ClientZone;

/**
 * clientzone - Hlavní strana.
 *
 * @author     Vítězslav Dvořák <vitex@arachne.cz>
 * @copyright  2017 VitexSoftware v.s.cz
 */
require_once 'includes/Init.php';

$oPage->onlyForLogged();

$oPage->addItem(new ui\PageTop(_('My Orders')));

$evidencies = ['faktura-vydana', 'objednavka-prijata'];
$fetcher    = new \FlexiPeeHP\FlexiBeeRO();
foreach ($evidencies as $evidence) {
    $fetcher->setEvidence($evidence);
    $oPage->container->addItem(new ui\OrdersListing($fetcher,
        ['firma' => $oUser->adresar], _('My Orders')));
}

$oPage->addItem(new ui\PageBottom());

$oPage->draw();
