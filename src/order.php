<?php

namespace Shop4FlexiBee;

/**
 * shop4flexibee
 *
 * @author     Vítězslav Dvořák <vitex@arachne.cz>
 * @copyright  2017 VitexSoftware v.s.cz
 */
require_once 'includes/Init.php';
$oPage->onlyForLogged();

$invoiceID = $oPage->getRequestValue('id', 'int');

if ($invoiceID) {
    $proforma = new \FlexiPeeHP\FakturaVydana($invoiceID);

    $oPage->addItem(new ui\PageTop(_('Order').' '.$proforma->getDataValue('kod')));


    $oPage->addItem(
        new \Ease\TWB\Container(
        new \Ease\TWB\Panel(_('Order').' '.$proforma->getDataValue('kod'),
        'success', new ui\HtmlInvoice($proforma),
        new \Ease\TWB\LinkButton('getpdf.php?id='.$invoiceID,
        new \Ease\TWB\GlyphIcon('download').' '.sprintf(_('Download Your Proforma #%s Here'),
            $proforma->getDataValue('kod')), 'success')
    )));
}
$oPage->addItem(new ui\PageBottom());

$oPage->draw();
