<?php
/**
 * shop4flexibee - Vršek stránky.
 *
 * @author     Vítězslav Dvořák <vitex@arachne.cz>
 * @copyright  2017 VitexSoftware v.s.cz
 */

namespace Shop4FlexiBee\ui;

/**
 * Page TOP.
 */
class PageTop extends \Ease\Html\Div
{
    /**
     * Titulek stránky.
     *
     * @var type
     */
    public $pageTitle = 'Page Heading';

    /**
     * Nastavuje titulek.
     *
     * @param string $pageTitle
     */
    public function __construct($pageTitle = null)
    {
        parent::__construct();
        if (!is_null($pageTitle)) {
            \Ease\Shared::webPage()->setPageTitle($pageTitle);
        }
    }

    /**
     * Vloží vršek stránky a hlavní menu.
     */
    public function finalize()
    {
        $this->SetupWebPage();
        $this->addItem(new MainMenu());

        if (get_class(\Ease\Shared::user()) == 'Shop4FlexiBee\Shop4FlexiBee\User') {
            $this->addItem(new History());
        }
    }

}
