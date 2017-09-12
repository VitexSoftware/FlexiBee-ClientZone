#!/usr/bin/php -f
<?php
/**
 * shop4flexibee - Odeslání Upomínek
 *
 * @author     Vítězslav Dvořák <info@vitexsofware.cz>
 * @copyright  2017 VitexSoftware v.s.cz
 */

namespace Shop4FlexiBee;

define('EASE_APPNAME', 'Upominkovac');
$inc = 'includes/Init.php';
if (!file_exists($inc)) {
    chdir('..');
}
require_once $inc;


$reminder = new Shop4FlexiBee\Upominac();
$reminder->addStatusMessage($reminder->getEvidenceURL(), 'debug');
$reminder->processAllDebts();
