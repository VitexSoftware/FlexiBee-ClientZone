<?php
/**
 * clientzone - Uživatel.
 *
 * @author     Vítězslav Dvořák <vitex@arachne.cz>
 * @copyright  2017 VitexSoftware v.s.cz
 */

namespace ClientZone\ui;

class WebPage extends \Ease\TWB\WebPage
{
    /**
     * Hlavní blok stránky.
     *
     * @var \Ease\Html\DivTag
     */
    public $container = null;

    /**
     * První sloupec.
     *
     * @var \Ease\Html\DivTag
     */
    public $columnI = null;

    /**
     * Druhý sloupec.
     *
     * @var \Ease\Html\DivTag
     */
    public $columnII = null;

    /**
     * Třetí sloupec.
     *
     * @var \Ease\Html\DivTag
     */
    public $columnIII = null;

    /**
     * Základní objekt stránky.
     *
     * @param VSUser $userObject
     */
    public function __construct($pageTitle = null, &$userObject = null)
    {
        if (is_null($userObject)) {
            $userObject = \Ease\Shared::user();
        }
        parent::__construct($pageTitle);
        $this->IncludeCss('css/default.css');
        $this->head->addItem('<meta name="viewport" content="width=device-width, initial-scale=1.0">');
        $this->head->addItem('<link rel="shortcut icon" type="image/svg+xml" href="images/logo.png">');
        $this->head->addItem('<link rel="apple-touch-icon-precomposed"  type="image/svg+xml" href="images/logo.png">');
        $this->head->addItem('<link rel="stylesheet" href="/javascript/font-awesome/css/font-awesome.min.css">');

        $this->container = $this->addItem(new \Ease\TWB\Container());
    }

    /**
     * Rozdělí stránku do třísloupcového layoutu
     */
    function addPageColumns()
    {
        $row = $this->container->addItem(new \Ease\Html\Div(null,
            ['class' => 'row']));

        $this->columnI   = $row->addItem(new \Ease\Html\Div(null,
            ['class' => 'col-md-4']));
        $this->columnII  = $row->addItem(new \Ease\Html\Div(null,
            ['class' => 'col-md-4']));
        $this->columnIII = $row->addItem(new \Ease\Html\Div(null,
            ['class' => 'col-md-4']));
    }

    /**
     * Pouze pro admina.
     *
     * @param string $loginPage
     */
    public function onlyForAdmin($loginPage = 'adminlogin.php')
    {
        $user = \Ease\Shared::user();
        if (!$user->getSettingValue('admin')) {
            $user->addStatusMessage(_('Please sign in as admin first'),
                'warning');
            $this->redirect($loginPage);
            exit;
        }
    }

    /**
     * Pouze pro uživatele.
     *
     * @param string $loginPage
     */
    public function onlyForUser($loginPage = 'adminlogin.php')
    {
        $user = \Ease\Shared::user();
        if (get_class($user) != 'ClientZone\User') {
            \Ease\Shared::webPage()->addStatusMessage(_('Please sign in as user first'),
                'warning');
            $this->redirect($loginPage);
            exit;
        }
    }

    /**
     * Nepřihlášeného uživatele přesměruje na přihlašovací stránku.
     *
     * @param string $loginPage adresa přihlašovací stránky
     */
    public function onlyForLogged($loginPage = 'login.php',
                                  $message = null)
    {
        return parent::onlyForLogged($loginPage.'?backurl='.urlencode($_SERVER['REQUEST_URI']),
                $message);
    }

}
