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

$shared  = \Ease\Shared::instanced();
$inSetup = strstr(ui\WebPage::phpSelf(), 'setup.php');

if (file_exists('/etc/flexibee/client.json')) {
    $shared->loadConfig('/etc/flexibee/client.json', !$inSetup);
}

if (file_exists('../clientzone.json')) {
    $shared->loadConfig('../clientzone.json', !$inSetup);
}

if (\Ease\Shared::isCli()) {
    if (!defined('EASE_LOGGER')) {
        define('EASE_LOGGER', 'syslog|console|email');
    }
} else {
    /* @var $oPage ui\WebPage */
    $oPage = new ui\WebPage();

    if (!$inSetup) {
        if (empty($shared->getConfigValue('FLEXIBEE_URL')) || !$shared->getConfigValue('CONFIGURED')) {
            $oPage->redirect('setup.php');
        }
    }
}


/**
 * Objekt uživatele User nebo Anonym
 * @global User|\Ease\Anonym
 */
$oUser = $shared->user();
