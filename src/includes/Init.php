<?php
/**
 * shop4flexibee - Init aplikace.
 *
 * @author     Vítězslav Dvořák <vitex@arachne.cz>
 * @copyright  2017 VitexSoftware v.s.cz
 */

namespace Shop4FlexiBee;
require_once '../vendor/autoload.php';
if (!defined('EASE_APPNAME')) {
    define('EASE_APPNAME', 'shop4flexibee');
}

\Ease\Shared::initializeGetText(constant('EASE_APPNAME'), 'en_US', '../i18n');

session_start();

if (\Ease\Shared::isCli()) {
    if (!defined('EASE_LOGGER')) {
        define('EASE_LOGGER', 'syslog|console|email');
    }
} else {
    /* @var $oPage ui\WebPage */
    $oPage = new ui\WebPage();
}

    $engine = new FlexiBeeEngine();

/**
 * Objekt uživatele User nebo Anonym
 * @global \Ease\User
 */
$oUser = \Ease\Shared::user();

