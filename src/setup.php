<?php

namespace ClientZone;

/**
 * clientzone - Application Setup.
 *
 * @author     Vítězslav Dvořák <vitex@arachne.cz>
 * @copyright  2019 VitexSoftware v.s.cz
 */
require_once 'includes/Init.php';

$cfg = new Configurator('../settings-local.json');
if ($oPage->isPosted()) {
    $cfg->takeData($_POST);
    $cfg->publish();
    $configSaved = $cfg->saveData();
} else {
//    $cfg->
}


$cfgForm = new ui\SetupForm();
$cfgForm->fillUp($cfg->getData());

$oPage->addItem(new ui\PageTop(_('ClientZone Setup')));



$setupRow = new \Ease\TWB\Row();
$setupRow->addColumn(8, $cfgForm);


if (!empty($shared->getConfigValue('FLEXIBEE_URL')) &&
    !empty($shared->getConfigValue('FLEXIBEE_LOGIN')) &&
    !empty($shared->getConfigValue('FLEXIBEE_PASSWORD'))
) {
    
    $statuser = new \FlexiPeeHP\ui\StatusInfoBox();

    $statusBlock = new \Ease\Html\DivTag($statuser);
    
    if(($statuser->lastResponseCode == 200) && $configSaved){
        $statusBlock->addItem( new \Ease\TWB\LinkButton('?CONFIGURED=true', _('Finish Configuration'),'success xs') );
    }
    
    $statusBlock->addItem( new \Ease\Html\PreTag( $cfg->getJson() ) );
    
    $setupRow->addColumn(4,
        new \Ease\Html\FieldSet(_('FlexiBee Connection Status'), $statusBlock  ));
    
    
} else {
    $setupRow->addColumn(4,
        new \Ease\Html\FieldSet(_('FlexiBee Connection Status'),
            new \Ease\TWB\LinkButton(null, _('Connection not set'), 'warning')));
}



$oPage->container->addItem(new \Ease\TWB\Panel(_('FlexiBee setup'), 'warning',
        $setupRow));

$oPage->addItem(new ui\PageBottom());

$oPage->draw();
