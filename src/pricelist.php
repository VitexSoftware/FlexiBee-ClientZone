<?php

namespace Shop4FlexiBee;

/**
 * shop4flexibee - Přehled ceníku
 *
 * @author     Vítězslav Dvořák <vitex@arachne.cz>
 * @copyright  2017 VitexSoftware v.s.cz
 */
require_once 'includes/Init.php';

$oPage->addItem(new ui\PageTop(_('Pricelist')));

$oPage->container->addItem(new ui\Pricelist(1, ['ESHOP', 'AKTIVNI']));

$oPage->addItem(new ui\PageBottom());

$oPage->draw();
