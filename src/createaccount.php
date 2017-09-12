<?php

namespace Shop4FlexiBee;

/**
 * shop4flexibee - Vytvoření účtu.
 *
 * @author     Vítězslav Dvořák <vitex@arachne.cz>
 * @copyright  2017 VitexSoftware v.s.cz
 */
require_once 'includes/Init.php';
$process = false;

$oPage->onlyForAdmin();

$firstname = $oPage->getRequestValue('firstname');
$lastname = $oPage->getRequestValue('lastname');

if ($oPage->isPosted()) {
    $process = true;

    $emailAddress = addslashes(strtolower($_POST['email_address']));

    if (isset($_POST['parent'])) {
        $customerParent = addslashes($_POST['parent']);
    } else {
        $customerParent = $oUser->getUserID();
    }
    $login = addslashes($_POST['login']);
    if (isset($_POST['password'])) {
        $password = addslashes($_POST['password']);
    }
    if (isset($_POST['confirmation'])) {
        $confirmation = addslashes($_POST['confirmation']);
    }

    $error = false;

    if (strlen($emailAddress) < 5) {
        $error = true;
        $oUser->addStatusMessage(_('mailová adresa je příliš krátká'), 'warning');
    } else {
        if (!filter_var($emailAddress, FILTER_VALIDATE_EMAIL)) {
            // $error = true;
            $oUser->addStatusMessage(_('chyba v mailové adrese'), 'warning');
        } else {
            $testuser = new \Ease\User();
            $testuser->setmyKeyColumn('email');
            $testuser->loadFromSQL($testuser->dblink->AddSlashes($emailAddress));
            if ($testuser->getUserName()) {
                $error = true;
                $oUser->addStatusMessage(sprintf(_('Mailová adresa %s je již zaregistrována'), $emailAddress), 'warning');
            }
            unset($testuser);
        }
    }

    if (strlen($password) < 5) {
        $error = true;
        $oUser->addStatusMessage(_('heslo je příliš krátké'), 'warning');
    } elseif ($password != $confirmation) {
        $error = true;
        $oUser->addStatusMessage(_('kontrola hesla nesouhlasí'), 'warning');
    }

    $testuser = new \Ease\User();
    $testuser->setmyKeyColumn('login');
    $testuser->loadFromSQL($testuser->dblink->addSlashes($login));
    $testuser->resetObjectIdentity();

    if ($testuser->getMyKey()) {
        $error = true;
        $oUser->addStatusMessage(sprintf(_('Zadané uživatelské jméno %s je již v databázi použito. Zvolte prosím jiné.'), $login), 'warning');
    }

    if ($error == false) {
        $newOUser = new User();
        //TODO zde by se měly doplnit defaultní hodnoty z konfiguráku registry.php
        $newOUser->setData(
                [
                    'email' => $emailAddress,
//                    'parent' => (int) $customerParent,
                    'login' => $login,
                    $newOUser->passwordColumn => $newOUser->encryptPassword($password),
                    'firstname' => $firstname,
                    'lastname' => $lastname,
                ]
        );

        $userID = $newOUser->insertToSQL();

        if (!is_null($userID)) {
            $newOUser->setMyKey($userID);
            if ($userID == 0) {
                $newOUser->setSettingValue('admin', true);
                $oUser->addStatusMessage(_('Administrátirský účet byl vytvořen'), 'success');
                $newOUser->saveToSQL();
            } else {
                $oUser->addStatusMessage(_('Uživatelský účet byl vytvořen'), 'success');
            }

            $newOUser->loginSuccess();

            $email = $oPage->addItem(new \Ease\Mailer($newOUser->getDataValue('email'), _('Potvrzení registrace')));
            $email->setMailHeaders(['From' => EMAIL_FROM]);
            $email->addItem(new \Ease\Html\Div("Právě jste byl/a zaregistrován/a do Aplikace shop4flexibee s těmito přihlašovacími údaji:\n"));
            $email->addItem(new \Ease\Html\Div(' Login: '.$newOUser->GetUserLogin()."\n"));
            $email->addItem(new \Ease\Html\Div(' Heslo: '.$_POST['password']."\n"));
            $email->send();

            $email = $oPage->addItem(new \Ease\Mailer(SEND_INFO_TO, sprintf(_('Nová registrace do Systému: %s'), $newOUser->GetUserLogin())));
            $email->setMailHeaders(['From' => EMAIL_FROM]);
            $email->addItem(new \Ease\Html\Div(_("Právě byl zaregistrován nový uživatel:\n")));
            $email->addItem(new \Ease\Html\Div(' Login: '.$newOUser->GetUserLogin()."\n"));
            $email->send();

            \Ease\Shared::user($newOUser)->loginSuccess();

            $oPage->redirect('index.php');
            exit;
        } else {
            $oUser->addStatusMessage(_('Zápis do databáze se nezdařil!'), 'error');
            $email = $oPage->addItem(new \Ease\Mail(constant('SEND_ORDERS_TO'), 'Registrace uzivatel se nezdařila'));
            $email->addItem(new \Ease\Html\DivTag('Fegistrace', $oPage->PrintPre($CustomerData)));
            $email->send();
        }
    }
}

$oPage->addItem(new ui\PageTop(_('Registrace')));

$regFace = $oPage->container->addItem(new \Ease\TWB\Panel(_('Registrace')));

$regForm = $regFace->addItem(new ui\ColumnsForm(new User()));
if ($oUser->getUserID()) {
    $regForm->addItem(new \Ease\Html\InputHiddenTag('parent', $oUser->GetUserID()));
}

$regForm->addInput(new \Ease\Html\InputTextTag('firstname', $firstname), _('Name'));
$regForm->addInput(new \Ease\Html\InputTextTag('lastname', $lastname), _('Příjmení'));

$regForm->addInput(new \Ease\Html\InputTextTag('login'), _('přihlašovací jméno').' *');
$regForm->addInput(new \Ease\Html\InputPasswordTag('password'), _('heslo').' *');
$regForm->addInput(new \Ease\Html\InputPasswordTag('confirmation'), _('potvrzení hesla').' *');
$regForm->addInput(new \Ease\Html\InputTextTag('email_address'), _('emailová adresa').' *');

$regForm->addItem(new \Ease\Html\Div(new \Ease\Html\InputSubmitTag('Register', _('Registrovat'), ['title' => _('dokončit registraci'), 'class' => 'btn btn-success'])));

if (isset($_POST)) {
    $regForm->fillUp($_POST);
}

$oPage->addItem(new ui\PageBottom());
$oPage->draw();
