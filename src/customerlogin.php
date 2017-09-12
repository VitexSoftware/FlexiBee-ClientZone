<?php

namespace Shop4FlexiBee;

/**
 * Přihlašovací stránka.
 *
 * @author    Vitex <vitex@hippy.cz>
 * @copyright Vitex@hippy.cz (G) 2017
 */
require_once 'includes/Init.php';

if (!is_object($oUser)) {
    die(_('Please enable cookies in your browser'));
}

$oPage->addStatusMessage(_('Sign in first please'), 'warning');

$login = $oPage->getRequestValue('email');
if ($login) {
    try {
        $oUser = \Ease\Shared::user(new Customer());
    } catch (PDOException $exc) {
        $oPage->addStatusMessage($exc->getTraceAsString(), 'error');
    }

    if ($oUser->tryToLogin($_POST)) {
        $oPage->redirect('index.php');
        exit;
    } else {
        $oUser = \Ease\Shared::user(new \Ease\Anonym());
    }
}

$oPage->addItem(new ui\PageTop(_('Sign In')));

$loginFace = new \Ease\Html\Div(null, ['id' => 'LoginFace']);

$oPage->container->addItem($loginFace);

$loginRow   = new \Ease\TWB\Row();
$infoColumn = $loginRow->addItem(new \Ease\TWB\Col(4));

$infoBlock = $infoColumn->addItem(new \Ease\TWB\Well(new \Ease\Html\ImgTag('images/password.png')));
$infoBlock->addItem(_('Welcome to Shop4FlexiBee'));

$loginColumn = $loginRow->addItem(new \Ease\TWB\Col(4));

$submit = new \Ease\TWB\SubmitButton(_('Sign in'), 'success');

$loginPanel = new \Ease\TWB\Panel(new \Ease\Html\ImgTag('images/shop4flexibee-logo.svg',
    'Shop4FlexiBee', ['style' => 'width:100px']), 'success', null, $submit);
$loginPanel->addItem(new \Ease\TWB\FormGroup(_('Username'),
    new \Ease\Html\InputTextTag('email', $login), 'name@domain.tld',
    _('usually your email address')));
$loginPanel->addItem(new \Ease\TWB\FormGroup(_('Password'),
    new \Ease\Html\InputPasswordTag('password')));
$loginPanel->body->setTagCss(['margin' => '20px']);

$loginColumn->addItem($loginPanel);

$passRecoveryColumn = $loginRow->addItem(new \Ease\TWB\Col(4,
    new \Ease\TWB\LinkButton('custmpassrecv.php?email='.$login,
    '<i class="fa fa-key"></i>
'._('Lost password recovery'), 'warning')));


$passRecoveryColumn->additem(new \Ease\TWB\LinkButton('newcustomer.php',
    '<i class="fa fa-user"></i>
'._('Sign On'), 'success'));


$oPage->container->addItem(new \Ease\TWB\Form('Login', null, 'POST', $loginRow));

$oPage->addItem(new ui\PageBottom());

$oPage->draw();
