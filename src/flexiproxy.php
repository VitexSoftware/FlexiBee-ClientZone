<?php

namespace Shop4FlexiBee;

/**
 * shop4flexibee - Proxy pro lokální JS.
 *
 * @author     Vítězslav Dvořák <vitex@arachne.cz>
 * @copyright  2017 VitexSoftware v.s.cz
 */
require_once 'includes/Init.php';

$engine = new FlexiBee();
$engine->setAgenda($oPage->getRequestValue('evidence'));
echo json_encode($engine->getFlexiData('', $oPage->getRequestValue('request')));
