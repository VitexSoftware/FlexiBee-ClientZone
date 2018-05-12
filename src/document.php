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

$id       = $oPage->getRequestValue('id');
$evidence = $oPage->getRequestValue('evidence');

$document = new \FlexiPeeHP\FlexiBeeRO(is_numeric($id) ? intval($id) : $id,
    ['evidence' => $evidence]);

$oPage->addItem(new ui\PageTop($document->getEvidence().' '.$document));

$oPage->container->addItem(new ui\EmbedResponsivePDF('getpdf.php?evidence='.$document->getEvidence().'&id='.$document->getMyKey().'&embed=true'));

$oPage->addItem(new ui\PageBottom());

$oPage->draw();
