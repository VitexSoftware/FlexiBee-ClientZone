#!/usr/bin/php -f
<?php
/**
 * shop4flexibee - Odeslání Upomínek
 *
 * @author     Vítězslav Dvořák <info@vitexsofware.cz>
 * @copyright  2017 VitexSoftware v.s.cz
 */

namespace Shop4FlexiBee;

define('EASE_APPNAME', 'HistoryInitializer');
$inc = 'includes/Init.php';
if (!file_exists($inc)) {
    chdir('..');
}
require_once $inc;

$prehistoric = new Shop4FlexiBee\WebHookHandler(null, ['operation' => 'import']);

if ($prehistoric->dblink->queryToValue('SELECT COUNT(*) FROM '.$prehistoric->getMyTable())) {
    $prehistoric->addStatusMessage(sprintf(_('History table %s is not empty'),
            $prehistoric->getMyTable()), 'warning');
} else {
    foreach (['adresar', 'kontakt', 'faktura-vydana'] as $evidence) {
        $position = 0;
        $prehistoric->setEvidence($evidence);
        $prehistoric->addStatusMessage('Processing: '.$prehistoric->getEvidenceURL());
        $ids      = $prehistoric->getColumnsFromFlexibee(['id']);
        $allids   = count($ids);
        $prehistoric->addStatusMessage(sprintf(_('%d records found in evidence %s'),
                $allids, $evidence));
        foreach ($ids as $id) {
            $position++;
            if ($prehistoric->loadFromFlexiBee(intval($id['id']))) {
                $info   = $position.'/'.$allids.' '.$prehistoric->getDataValue('kod');
                $result = $prehistoric->saveHistory();
                $prehistoric->addStatusMessage(sprintf(_('Saving record %s into history table'),
                        $info), $result ? 'success' : 'error');
                if ($result == false) {
                    $prehistoric->addStatusMessage(
                        $prehistoric->dblink->errorNumber.' '.
                        is_array($prehistoric->dblink->errorText) ? end(
                                $prehistoric->dblink->errorText) : $prehistoric->dblink->errorText,
                        'warning');
                    $prehistoric->addStatusMessage($prehistoric->dblink->lastQuery,
                        'debug');
                }
            }
        }
    }
}
