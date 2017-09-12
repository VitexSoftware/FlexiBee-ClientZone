<?php

namespace Shop4FlexiBee;

/**
 * Shop4FlexiBee - About Page.
 *
 * @author     Vítězslav Dvořák <dvorak@austro-bohemia.cz>
 * @copyright  2017 VitexSoftware v.s.cz
 */
require_once 'includes/Init.php';

$oPage->onlyForLogged();

$oPage->addItem(new ui\PageTop(_('About')));

$infoBlock = $oPage->container->addItem(
        new \Ease\TWB\Panel(
        _('About'), 'info', null,
    new \Ease\TWB\LinkButton(
    'http://v.s.cz/', _('Vitex Software'), 'info'
        )
        )
);
$listing = $infoBlock->addItem(new \Ease\Html\UlTag());

if (file_exists('../README.md')) {
    $listing->addItem(implode('<br>', file('../README.md')));
} else {
    if (file_exists('/usr/share/doc/shop4flexibee/README.md')) {
        $listing->addItem(implode('<br>', file('/usr/share/doc/shop4flexibee/README.md')));
    }
}

$oPage->draw();
