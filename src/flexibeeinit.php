<?php

namespace ClientZone;

/**
 * clientzone - předloha stránky.
 *
 * @author     Vítězslav Dvořák <vitex@arachne.cz>
 * @copyright  2017 VitexSoftware v.s.cz
 */
require_once './includes/Init.php';

$oPage->onlyForAdmin();

$oPage->addItem(new ui\PageTop(_('FlexiBee Init')));

$oPage->container->addItem(new FlexiBeeStatus());

/* Povolit ChangesAPI */

$changer = new \FlexiPeeHP\Changes();
if ($changer->getStatus() == false) {
    $chEnabled = $changer->enable();
}
if ($changer->getStatus()) {
    $changer->addStatusMessage(_('Changes API Enabled'));
    $oPage->container->addItem(new \Ease\TWB\Label('success',
        _('Changes API enabled')));
} else {
    $oPage->container->addItem(new \Ease\TWB\Label('warning',
        _('Changes API disabled')));
}


/* Zaregistrovat WebHook */
$hooker     = new \FlexiPeeHP\Hooks();
$webhook    = $_SERVER['REQUEST_SCHEME'].'://'.$_SERVER['HTTP_HOST'].dirname(\Ease\WebPage::getUri()).'/webhook.php';
$hookResult = $hooker->register($webhook);

$oPage->container->addItem(new \Ease\TWB\LinkButton($webhook, $webhook,
    $hookResult ? 'success' : 'danger'));


$cenik         = new \FlexiPeeHP\Cenik();
$stitek        = new \FlexiPeeHP\Stitek();
$labelsDefined = \FlexiPeeHP\Stitek::getAvailbleLabels($cenik);

function createLabel($label,$stitek)
{
    $stitekData = [
        "kod" => strtoupper($label),
        "nazev" => $label,
        "vsbKatalog" => true,
    ];

    $stitek->insertToFlexiBee($stitekData);
    if ($stitek->lastResponseCode == 201) {
        $stitek->addStatusMessage(sprintf(_('label %s created'), $label),
            'success');
    }
}
$label = 'ESHOP';
if (!array_key_exists($label, $labelsDefined)) {
    createLabel($label,$stitek);
} else {
    $stitek->addStatusMessage(sprintf(_('label %s already exists'), $label));
}

$label = 'AKTIVNI';
if (!array_key_exists($label, $labelsDefined)) {
    createLabel($label,$stitek);
} else {
    $stitek->addStatusMessage(sprintf(_('label %s already exists'), $label));
}

$oPage->addItem(new ui\PageBottom());

$oPage->draw();
