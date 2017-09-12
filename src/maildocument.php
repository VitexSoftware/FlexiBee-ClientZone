<?php

namespace Shop4FlexiBee;

/**
 * shop4flexibee
 *
 * @author     Vítězslav Dvořák <vitex@arachne.cz>
 * @copyright  2017 VitexSoftware v.s.cz
 */
require_once 'includes/Init.php';

$evidence   = $oPage->getRequestValue('evidence');
$documentID = $oPage->getRequestValue('id');


if (!empty($documentID) && !empty($evidence)) {
    $fetcher = new \FlexiPeeHP\FlexiBeeRO(is_numeric($documentID) ? intval($documentID)
            : $documentID, ['evidence' => $evidence]);
    if ($fetcher->sendByMail($oUser->getUserEmail(),
            sprintf(_('%s %s'), $fetcher->getDataValue('poznam'), $fetcher),
            _('Dear Customer, your document is in attachment'))) {
        $oPage->addStatusMessage(_('Invoice was sent'), 'success');
    } else {
        $oPage->addStatusMessage(_('Mailer does not work'), 'warning');
    }
    $oPage->redirect(isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'index.php');
} else {
    die(_('Wrong call'));
}
