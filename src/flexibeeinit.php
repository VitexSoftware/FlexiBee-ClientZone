<?php

namespace Shop4FlexiBee;

/**
 * shop4flexibee - předloha stránky.
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
    $changer->addStatusMessage(_('ChangesApi Povoleno'));
    $oPage->container->addItem(new \Ease\TWB\Label('success',
        _('ChangesAPI povoleno')));
} else {
    $oPage->container->addItem(new \Ease\TWB\Label('warning',
        _('ChangesAPI zakázáno')));
}


/* Zaregistrovat WebHook */
$hooker     = new \FlexiPeeHP\Hooks();
$webhook    = $_SERVER['REQUEST_SCHEME'].'://'.$_SERVER['HTTP_HOST'].dirname(\Ease\WebPage::getUri()).'/webhook.php';
$hookResult = $hooker->register($webhook);

$oPage->container->addItem(new \Ease\TWB\LinkButton($webhook, $webhook,
    $hookResult ? 'success' : 'danger'));


$cenik = new \FlexiPeeHP\Cenik();
$stitek        = new \FlexiPeeHP\Stitek();
$labelsDefined = \FlexiPeeHP\Stitek::getAvailbleLabels($cenik);

function createLabel($label)
{
    $stitekData = [
        "kod" => strtoupper($label),
        "nazev" => $label,
        "vsbKatalog" => true,
    ];

    $stitek->insertToFlexiBee($stitekData);
    if ($stitek->lastResponseCode == 201) {
        $stitek->addStatusMessage('label '.$label.' created', 'success');
    }
}

$label = 'ESHOP';
if (!array_key_exists($label, $labelsDefined)) {
    createLabel($label);
} else {
    $stitek->addStatusMessage('label '.$label.' already exits');
}

$label = 'AKTIVNI';
if (!array_key_exists($label, $labelsDefined)) {
    createLabel($label);
} else {
    $stitek->addStatusMessage('label '.$label.' already exits');
}


$oPage->addItem(new ui\PageBottom());

$oPage->draw();
