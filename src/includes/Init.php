<?php
/**
 * clientzone - Init aplikace.
 *
 * @author Vítězslav Dvořák <info@vitexsoftware.cz>
 * @copyright  2017-2018 VitexSoftware v.s.cz
 */

namespace ClientZone;

require_once '../vendor/autoload.php';
if (!defined('EASE_APPNAME')) {
    define('EASE_APPNAME', 'ClientZone');
}

new \Ease\Locale('cs_CZ', '../i18n', 'clientzone');

session_start();

$shared = \Ease\Shared::instanced();
$shared->loadConfig('../tests/clientzone.json', true);
$shared->loadConfig('../tests/client.json', true);

if (\Ease\Shared::isCli()) {
    if (!defined('EASE_LOGGER')) {
        define('EASE_LOGGER', 'syslog|console|email');
    }
} else {
    /* @var $oPage ui\WebPage */
    $oPage = new ui\WebPage();
}


/**
 * Objekt uživatele User nebo Anonym
 * @global User|\Ease\Anonym
 */
$oUser = $shared->user();
