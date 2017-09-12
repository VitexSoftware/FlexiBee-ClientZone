<?php

namespace Shop4FlexiBee;

/**
 * shop4flexibee - Přehled uživatelů.
 *
 * @author     Vítězslav Dvořák <vitex@arachne.cz>
 * @copyright  2017 VitexSoftware v.s.cz
 */
require_once 'includes/Init.php';

$oPage->onlyForLogged();

Engine::doThings($oPage);

$oPage->addItem(new ui\PageTop(_('Přehled uživatelů')));

$oPage->addItem(new \Ease\TWB\Container(new DataGrid(_('Uživatelé'), new User())));

$oPage->addItem(new ui\PageBottom());

$oPage->draw();
