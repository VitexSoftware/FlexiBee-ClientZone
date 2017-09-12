<?php

namespace Shop4FlexiBee;

/**
 * shop4flexibee - předloha stránky.
 *
 * @author     Vítězslav Dvořák <vitex@arachne.cz>
 * @copyright  2017 VitexSoftware v.s.cz
 */
require_once './includes/Init.php';

$oPage->onlyForLogged();

$oPage->addItem(new ui\PageTop(_('Spoje.Net')));

$oPage->addItem(new ui\PageBottom());

$oPage->draw();
