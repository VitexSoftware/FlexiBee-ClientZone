<?php

namespace Shop4FlexiBee;

/**
 * shop4flexibee - Stránka firmy/adresy.
 *
 * @author     Vítězslav Dvořák <vitex@arachne.cz>
 * @copyright  2017 VitexSoftware v.s.cz
 */
require_once 'includes/Init.php';

$oPage->onlyForUser();

$address_id = $oPage->getRequestValue('id');
if (!strstr($address_id, 'code:')) {
    $address_id = intval($address_id);
}

$address = Engine::doThings($oPage);
if (is_null($address)) {
    $address = new \FlexiPeeHP\Adresar($address_id,
        ['defaultUrlParams' => ['relations' => 'kontakt']]);
}

if ($oPage->getGetValue('delete', 'bool') == 'true') {
    if ($address->delete()) {
        $oPage->redirect('addresar.php');
        exit;
    }
}

$oPage->addItem(new ui\PageTop(_('Address').': '.$address->getDataValue('nazev')));

$addressRow = new \Ease\TWB\Row();
$infoColumn = $addressRow->addColumn(4,
    [new ui\ZewlScoreLabel($address), new ui\InvoicesOfAddresButton($address), new ui\LabelSwitches($address)]);



switch ($oPage->getRequestValue('action')) {
    case 'delete':

        $confirmBlock = new \Ease\TWB\Well();

        $confirmBlock->addItem($address);

        $confirmator = $confirmBlock->addItem(new \Ease\TWB\Panel(_('Delete ?')),
            'danger');
        $confirmator->addItem(new \Ease\TWB\LinkButton('addresar.php?id='.$address->getId(),
            _('No').' '.\Ease\TWB\Part::glyphIcon('ok'), 'success'));
        $confirmator->addItem(new \Ease\TWB\LinkButton('?delete=true&'.$address->keyColumn.'='.$address->getID(),
            _('Yes').' '.\Ease\TWB\Part::glyphIcon('remove'), 'danger'));

        $headerRow = new \Ease\TWB\Row();
        $headerRow->addColumn(8,
            '<strong>'.$address->getContactName().'</strong>');
        $headerRow->addColumn(4,
            new ui\LinkToFlexiBeeButton($address, ['style' => 'width: 20px']));
        $addressRow->addColumn(8,
            new \Ease\TWB\Panel($headerRow, 'info', $confirmBlock));
        break;
    default :

//        $operationsMenu = $address->operationsMenu();
//        $operationsMenu->setTagCss(['float' => 'right']);
//        $operationsMenu->dropdown->addTagClass('pull-right');
        $operationsMenu = [];

        $headerRow = new \Ease\TWB\Row();
        $headerRow->addColumn(8,
            '<strong>'.$address->getDataValue('kod').' - '.$address->getDataValue('nazev').'</strong>');
        $headerRow->addColumn(4,
            new ui\LinkToFlexiBeeButton($address, ['style' => 'width: 20px']));

        $addressRow->addColumn(8,
            new \Ease\TWB\Panel($headerRow, 'info',
            new ui\AddressForm($address),
            [new ui\AddressContactsLinks($address)]));



        break;
}




$oPage->container->addItem($addressRow);


$oPage->addItem(new ui\PageBottom());

$oPage->draw();
