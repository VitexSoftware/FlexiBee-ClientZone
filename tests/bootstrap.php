<?php

/**
 * shop4flexibee - nastavení testů.
 *
 * @author     Vítězslav Dvořák <vitex@arachne.cz>
 * @copyright  2017 VitexSoftware v.s.cz
 */



include_once 'Token.php';
include_once 'Token/Stream.php';
//echo getcwd();

chdir('/home/vitex/Projects/Spoje.Net/shop4flexibee/src/'); //TODO: relative
//exit;
require_once 'includes/config.php';
require_once 'includes/Init.php';

\Ease\Shared::user(new \Shop4FlexiBee\User());
\Ease\Shared::webPage(new namespace Shop4FlexiBee\ui\WebPage());
