#!/usr/bin/php -f
<?php
/**
 * clientzone - Zpracování zatím nezachycených úprav
 *
 * @author     Vítězslav Dvořák <info@vitexsofware.cz>
 * @copyright  2017 VitexSoftware v.s.cz
 */

namespace ClientZone;

define('EASE_APPNAME', 'HistoryInitializer');
$inc = 'includes/Init.php';
if (!file_exists($inc)) {
    chdir('..');
}
require_once $inc;

$hooker                   = new ClientZone\HookReciever();
$hooker->defaultUrlParams = ['limit' => 1000];
$hooker->debug            = false;

$topVersion = $hooker->getGlobalVersion();

$hooker->addStatusMessage(sprintf(_('Last processed change %d Last Availble Change: %d'),
        $hooker->lastProcessedVersion, $topVersion), 'info');

while ($topVersion > $hooker->lastProcessedVersion) {
    $hooker->getColumnsFromFlexibee('*',
        empty($hooker->lastProcessedVersion) ? [] :
            ['start' => $hooker->lastProcessedVersion + 1]);
    $hooker->takeChanges(json_decode($hooker->lastCurlResponse, TRUE));
    if (count($hooker->changes)) {
        $hooker->addStatusMessage(sprintf(_('%d unprocessed changes found'),
                count($hooker->changes)));
        $hooker->processChanges();
        $hooker->addStatusMessage(sprintf(_('Done for now. Last processed change %d Last Availble Change: %d'),
                $hooker->lastProcessedVersion, $hooker->getGlobalVersion()),
            'success');
    } else {
        $hooker->addStatusMessage(_('No changes to process'));
        break;
    }
} 
