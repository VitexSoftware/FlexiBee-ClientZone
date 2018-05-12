<?php

namespace ClientZone;

/**
 * clientzone - Stránka štítku.
 *
 * @author     Vítězslav Dvořák <vitex@arachne.cz>
 * @copyright  2017 VitexSoftware v.s.cz
 */
require_once 'includes/Init.php';

$oPage->onlyForUser();

$label    = $oPage->getRequestValue('label');
$evidence = $oPage->getRequestValue('evidence');


$flexiBee = new \FlexiPeeHP\FlexiBeeRO(null, ['evidence' => $evidence]);

$oPage->addItem(new ui\PageTop(_('Label').': '.$label));


//        $operationsMenu = $contact->operationsMenu();
//        $operationsMenu->setTagCss(['float' => 'right']);
//        $operationsMenu->dropdown->addTagClass('pull-right');
$operationsMenu = '';

$topRow = new \Ease\TWB\Row();
$topRow->addColumn(4,
    _('Label').': <strong>'.$label.'</strong>');

$oPage->container->addItem(new \Ease\TWB\Panel($topRow, 'info',
    new ui\RecordsWithLabel($flexiBee, $label)));

$oPage->addItem(new ui\PageBottom());

$oPage->draw();
