<?php

namespace SNLms;
define('LMS_DIRECTORY', getcwd()); //???
define('EASE_LOGGER', 'console|syslog');

require_once( 'includes/lmsinit.php');

$invoiceSteamer = new FlexiBee\ParovacFaktur();
$invoiceSteamer->addStatusMessage(_('Stahuji Výpisy online'));
if ($invoiceSteamer->banker->stahnoutVypisyOnline()) {
//    $invoiceSteamer->addStatusMessage(_('Zahajuji automatické párování'));
//Nepouziva se:    $invoiceSteamer->banker->automatickeParovani();
    $invoiceSteamer->addStatusMessage(_('Zahajuji programové párování'));
    $invoiceSteamer->invoicesMatchingByBank();
    $invoiceSteamer->addStatusMessage(_('Párování hotovo'));
} else {
    $invoiceSteamer->addStatusMessage('Banka Offline!', 'error');
}
