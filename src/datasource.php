<?php

namespace Shop4FlexiBee;

/**
 * shop4flexibee - Zdroj dat datagridu.
 *
 * @author     Vítězslav Dvořák <vitex@arachne.cz>
 * @copyright  2017 VitexSoftware v.s.cz
 */
require_once 'includes/Init.php';

$oPage->onlyForLogged();

$class = $oPage->getRequestValue('class');
if ($class) {
    $class  = str_replace('_', '\\', $class);
    $source = new DataSource(new $class());
    $source->output();
}
