<?php
/**
 * shop4flexibee - Vraci score uzivatele
 *
 * @author     Vítězslav Dvořák <info@vitexsofware.cz>
 * @copyright  2017 VitexSoftware v.s.cz
 */
define('EASE_APPNAME', 'getCustomerScoreAPI');
$inc = 'includes/Init.php';
if (!file_exists($inc)) {
    chdir('..');
}
require_once $inc;

$addressID = $oPage->getRequestValue('id', 'int');

$container = $oPage->addItem(new Ease\TWB\Container());
$form      = new \Ease\TWB\Form('Score');
$form->addInput(new Ease\Html\InputNumberTag('id', $addressID),
    _('FlexiBee Address ID'));
$form->addItem(new \Ease\TWB\SubmitButton(_('Get Score')));

$container->addItem($form);

if ($addressID) {
    $reminder = new \Shop4FlexiBee\Upominac();
    $container->addItem(new Ease\Html\H1Tag($reminder->getCustomerScore($addressID)));
    $adresar  = new FlexiPeeHP\Adresar($addressID);
    $container->addItem(new \Ease\Html\H2Tag($adresar->getDataValue('nazev')));
    $container->addItem($adresar->getDataValue('stitky'));
}


$oPage->draw();

