<?php
/**
 * shop4flexibee - Hlavní menu.
 *
 * @author     Vítězslav Dvořák <vitex@arachne.cz>
 * @copyright  2017 VitexSoftware v.s.cz
 */

namespace Shop4FlexiBee\ui;

class MainMenu extends \Ease\Html\NavTag
{

    /**
     * Vytvoří hlavní menu.
     */
    public function __construct()
    {
        parent::__construct(null, ['id' => 'MainMenu']);
    }

    /**
     * Data source.
     *
     * @param type   $source
     * @param string $icon   Description
     *
     * @return string
     */
    protected function getMenuList($source, $icon = '')
    {
        $keycolumn  = $source->getmyKeyColumn();
        $namecolumn = $source->nameColumn;
        $lister     = $source->getColumnsFromSQL([$source->getmyKeyColumn(), $namecolumn],
            null, $namecolumn, $keycolumn);

        $itemList = [];
        if ($lister) {
            foreach ($lister as $uID => $uInfo) {
                $itemList[$source->keyword.'.php?'.$keycolumn.'='.$uInfo[$keycolumn]]
                    = \Ease\TWB\Part::GlyphIcon($icon).'&nbsp;'.$uInfo[$namecolumn];
            }
        }

        return $itemList;
    }

    /**
     * Vložení menu.
     */
    public function afterAdd()
    {
        $nav    = $this->addItem(new BootstrapMenu());
        $user   = \Ease\Shared::user();
        $userID = \Ease\Shared::user()->getUserID();

        switch (get_class($user)) {
            case 'Shop4FlexiBee\Shop4FlexiBee\User': //Admin
                $nav->addMenuItem(new NavBarSearchBox('search', 'search.php'));
//                $nav->addDropDownMenu('<img width=30 src=images/gear.svg> '._('Scripts'),
//                    [
//                    'invoicematch.php' => \Ease\TWB\Part::GlyphIcon('piggy-bank').' '._('Invoice matching (Take so long)'),
//                'invoice2flexibee.php' => \Ease\TWB\Part::GlyphIcon('plus') . '&nbsp;' . _('Faktury do Flexibee'),
//                'address2flexibee.php' => \Ease\TWB\Part::GlyphIcon('plus') . '&nbsp;' . _('Adresář do Flexibee'),
//                    ]
//                );
//                $nav->addDropDownMenu('<img width=30 src=images/contract_150.png> '._('Orders'),
//                    array_merge([
//                    'contracttodo.php' => new \Ease\Html\ImgTag('images/copying.svg',
//                    'TODO', ['height' => '20px']).'&nbsp; '._('Orders TODO'),
//                    'zavazky.php' => \Ease\TWB\Part::GlyphIcon('transfer').' '._('Měsíční závazky'),
//                    'pohledavky.php' => \Ease\TWB\Part::GlyphIcon('transfer').' '._('Měsíční pohledávky'),
//                    'contract-reset.php' => \Ease\TWB\Part::GlyphIcon('repeat').' '._('Reset autogenerace'),
//                    'contract.php' => \Ease\TWB\Part::GlyphIcon('plus').' '._('Nová smlouva'),
//                    'contracts.php' => \Ease\TWB\Part::GlyphIcon('list').'&nbsp;'._('Přehled smluv'),
//                    'rspcntrcts.php' => \Ease\TWB\Part::GlyphIcon('user').'&nbsp;'._('Respondenti'),
//                    ])
//                );
                $nav->addDropDownMenu('<img width=30 src=images/order.svg> '._('Proposal'),
                    [
                    'adminpricelist.php' => \Ease\TWB\Part::GlyphIcon('th-list').' '._('Pricelist'),
                    ]
                );

                $nav->addDropDownMenu('<img width=30 src=images/users_150.png> '._('Users'),
                    array_merge([
                    'createaccount.php' => \Ease\TWB\Part::GlyphIcon('plus').' '._('New user'),
                    'users.php' => \Ease\TWB\Part::GlyphIcon('list').'&nbsp;'._('User overview'),
                    '' => '',
                        ], $this->getMenuList(\Ease\Shared::user(), 'user'))
                );
                break;
            case 'Shop4FlexiBee\Shop4FlexiBee\Customer': //Customer
                $nav->addDropDownMenu('<img width=30 src=images/order.svg> '._('Orders'),
                    [
                    'orderform.php' => \Ease\TWB\Part::GlyphIcon('plus').' '._('New order'),
                    'pricelist.php' => \Ease\TWB\Part::GlyphIcon('th-list').' '._('Pricelist'),
                    'myorders.php' => \Ease\TWB\Part::GlyphIcon('list').'&nbsp;'._('My orders')]
                );

                break;
            case 'Ease\Anonym': //Anonymous
            default:
                break;
        }
    }

    /**
     * Přidá do stránky javascript pro skrývání oblasti stavových zpráv.
     */
    public function finalize()
    {
        $this->addCss('body {
                padding-top: 60px;
                padding-bottom: 40px;
            }');

        \Ease\JQuery\Part::jQueryze($this);
        \Ease\Shared::webPage()->addCss('.dropdown-menu { overflow-y: auto } ');
        \Ease\Shared::webPage()->addJavaScript("$('.dropdown-menu').css('max-height',$(window).height()-100);",
            null, true);
        $this->includeJavaScript('js/slideupmessages.js');
    }

}
