<?php

namespace ClientZone;

/**
 * clientzone - Application Setup.
 *
 * @author     Vítězslav Dvořák <vitex@arachne.cz>
 * @copyright  2019 VitexSoftware v.s.cz
 */
require_once 'includes/Init.php';

$cfg = new Configurator();
if($oPage->isPosted()){
    $cfg->takeData($_POST);
    $cfg->saveData();
} else {
//    $cfg->
}

$oPage->addItem(new ui\PageTop(_('ClientZone Setup')));


$oPage->container->addItem( new \Ease\TWB\Panel(_('FlexiBee setup'), 'warning',  new ui\SetupForm(), (
!empty($shared->getConfigValue('FLEXIBEE_URL')) && 
!empty($shared->getConfigValue('FLEXIBEE_LOGIN')) &&
!empty($shared->getConfigValue('FLEXIBEE_PASSWORD'))  
) ? new \FlexiPeeHP\ui\StatusInfoBox() : '' )  );


$oPage->addItem(new ui\PageBottom());

$oPage->draw();
