<?php

namespace Shop4FlexiBee;

/**
 * shop4flexibee - Obnova hesla.
 *
 * @author     Vítězslav Dvořák <vitex@arachne.cz>
 * @copyright  2017 VitexSoftware v.s.cz
 */
require_once 'includes/Init.php';

$success = false;

$emailTo = $oPage->getPostValue('Email');

$oPage->includeJavaScript('js/jquery.validate.js');
$oPage->addJavascript('$("#PassworRecovery").validate({
  rules: {
    Email: {
      required: true,
      email: true
    }
  }
});', null, true);

if ($emailTo) {
    $userEmail = \Ease\Shared::db()->easeAddSlashes($emailTo);
    $userDbFound = \Ease\Shared::db()->queryToArray('SELECT id,login FROM user WHERE email=\'' . $userEmail . '\'');
    if (count($userDbFound)) {
        $userID = intval($userDbFound[0]['id']);
        $userLogin = $userDbFound[0]['login'];
        $newPassword = $oPage->randomString(8);

        $passChanger = new User($userID);
        $passChanger->passwordChange($newPassword);

        $email = $oPage->addItem(new \Ease\Mailer($userEmail, _('shop4flexibee') . ' - ' . sprintf(_('New password for %s'), $_SERVER['SERVER_NAME'])));
        $email->setMailHeaders(['From' => constant('EMAIL_FROM')]);
        $email->addItem(_("Sign On informations was changed:\n"));

        $email->addItem(_('Username') . ': ' . $userLogin . "\n");
        $email->addItem(_('Password') . ': ' . $newPassword . "\n");

        $email->send();

        $oPage->addStatusMessage(sprintf(_('Your new password was sent to %s'), '<strong>' . $emailTo . '</strong>'));
        $success = true;

    }
} else {
    $oPage->addStatusMessage(_('Please enter your email.'));
}

$oPage->addItem(new ui\PageTop(_('Lost password recovery')));

$row = $oPage->container->addItem(new \Ease\Html\Div(null, ['class' => 'row']));
$columnI = $row->addItem(new \Ease\Html\Div(null, ['class' => 'col-md-4']));
$columnII = $row->addItem(new \Ease\Html\Div(null, ['class' => 'col-md-4']));
$columnIII = $row->addItem(new \Ease\Html\Div(null, ['class' => 'col-md-4']));

if (!$success) {
    $columnI->addItem(new \Ease\Html\H1Tag('Lost password'));

    $columnII->addItem(_('Forgot your password? Enter your e-mail address you entered during the registration and we will send you a new one.'));

    $emailForm = $columnII->addItem(new \Ease\TWB\Form('PassworRecovery'));


    $emailForm->addInput(new \Ease\Html\InputTextTag('Email', null, ['type' => 'email']), _('Email'));
    $emailForm->addItem(new \Ease\TWB\SubmitButton(_('Send New Password'), 'warning'));

    if (isset($_POST)) {
        $emailForm->fillUp($_POST);
    }
} else {
    $columnII->addItem(new \Ease\TWB\Well([_('Please check your mailbox for new password')
      , ' ' . _('and') . ' ', new \Ease\TWB\LinkButton('customerlogin.php', _('Sign In'), 'success')]));
}

$oPage->addItem(new ui\PageBottom());

$oPage->draw();
