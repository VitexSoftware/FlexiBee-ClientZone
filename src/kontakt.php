<?php

namespace Shop4FlexiBee;

/**
 * shop4flexibee - Stránka kontaktu.
 *
 * @author     Vítězslav Dvořák <vitex@arachne.cz>
 * @copyright  2017 VitexSoftware v.s.cz
 */
require_once 'includes/Init.php';

$oPage->onlyForLogged();

$contact_id = $oPage->getRequestValue('id');
if (!strstr($contact_id, 'code:')) {
    $contact_id = intval($contact_id);
}


$contact = Engine::doThings($oPage);
if (is_null($contact)) {
    $contact = new \FlexiPeeHP\Kontakt($contact_id,
        ['defaultUrlParams' => ['relations' => 'adresar']]);

    $firma = $oPage->getRequestValue('firma', 'int');
    if ($firma) {
        $contact->setDataValue('firma', $firma);
        $contact->setDataValue('email', $oPage->getRequestValue('email'));
        $contact->setDataValue('login', $oPage->getRequestValue('email'));
        $contact->insertToFlexiBee();
        $oPage->redirect('kontakt.php?id='.$contact->getMyKey());
    }

//    $contact = new \FlexiPeeHP\Kontakt($contact_id.'.json?relations=adresar');
}

if ($oPage->getGetValue('delete', 'bool') == 'true') {
    if ($contact->delete()) {
        $oPage->redirect('contacts.php');
        exit;
    }
}

$oPage->addItem(new ui\PageTop(_('Contact').' '.$contact->getDataValue('id')));

switch ($oPage->getRequestValue('action')) {
    case 'delete':

        $confirmBlock = new \Ease\TWB\Well();

        $confirmBlock->addItem($contact);

        $confirmator = $confirmBlock->addItem(new \Ease\TWB\Panel(_('Delete ?')),
            'danger');
        $confirmator->addItem(new \Ease\TWB\LinkButton('contact.php?id='.$contact->getId(),
            _('No').' '.\Ease\TWB\Part::glyphIcon('ok'), 'success'));
        $confirmator->addItem(new \Ease\TWB\LinkButton('?delete=true&'.$contact->keyColumn.'='.$contact->getID(),
            _('Yes').' '.\Ease\TWB\Part::glyphIcon('remove'), 'danger'));

        $oPage->container->addItem(new \Ease\TWB\Panel('<strong>'.$contact->getContactName().'</strong>',
            'info', $confirmBlock));

        break;
    default :

//        $operationsMenu = $contact->operationsMenu();
//        $operationsMenu->setTagCss(['float' => 'right']);
//        $operationsMenu->dropdown->addTagClass('pull-right');
        $operationsMenu = '';

        $topRow = new \Ease\TWB\Row();
        $topRow->addColumn(4,
            _('Contact').': <strong>'.$contact->getDataValue('prijmeni').' '.$contact->getDataValue('jmeno').'</strong>');
        $topRow->addColumn(4,
            new \Ease\TWB\LinkButton('adresar.php?id='.$contact->getDataValue('firma'),
            _('Address').' '.$contact->getDataValue('firma@showAs'), 'info'));
        $topRow->addColumn(4,
            new ui\LinkToFlexiBeeButton($contact, ['style' => 'width: 20px']));

        $oPage->container->addItem(new \Ease\TWB\Panel($topRow, 'info',
            new ui\KontaktForm($contact),
            new \Ease\TWB\LinkButton('adresar.php?id='.$contact->getDataValue('firma'),
            $contact->getDataValue('firma@showAs'), 'info')));
        break;
}

$oPage->addItem(new ui\PageBottom());

$oPage->draw();
