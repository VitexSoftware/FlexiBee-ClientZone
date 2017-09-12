<?php
/**
 * shop4flexibee - Spodek Stránky Webu.
 *
 * @author     Vítězslav Dvořák <vitex@arachne.cz>
 * @copyright  2017 VitexSoftware v.s.cz
 */

namespace Shop4FlexiBee\ui;

class PageBottom extends \Ease\Html\FooterTag
{

    public function __construct($content = null)
    {
        $composer = '/usr/share/shop4flexibee/composer.json';
        if (!file_exists($composer)) {
            $composer = '../composer.json';
        }
        $appInfo = json_decode(file_get_contents($composer));

        parent::__construct($content);
        $this->SetTagID('footer');
        $this->addItem('<hr>');

        $rowFluid1 = new \Ease\TWB\Row();
        $colA      = $rowFluid1->addItem(new \Ease\TWB\Col(2));
        $colB      = $rowFluid1->addItem(new \Ease\TWB\Col(2));
        $colC      = $rowFluid1->addItem(new \Ease\TWB\Col(2));
        $colD      = $rowFluid1->addItem(new \Ease\TWB\Col(2));
        $colE      = $rowFluid1->addItem(new \Ease\TWB\Col(2));
        $listE1    = $colE->addItem(new \Ease\Html\UlTag(_('Hosted by'),
            ['style' => 'list-style-type: none']));
        $listE1->addItemSmart(new \Ease\Html\ATag('https://spoje.net/',
            new \Ease\Html\ImgTag('images/spoje-net_logo.gif', 'Spoje.Net',
            ['class' => 'img-responsive'])));

        $colF      = $rowFluid1->addItem(new \Ease\TWB\Col(2));
        $listF1    = $colF->addItem(new \Ease\Html\UlTag(_('More'),
            ['style' => 'list-style-type: none']));
        $listF1->addItemSmart(new \Ease\Html\ATag('https://www.vitexsoftware.cz/',
            _('About us')));
        $listF1->addItemSmart(new \Ease\Html\ATag('https://github.com/VitexSoftware/Shop4FlexiBee/',
            _('Source code')));
        $listF1->addItemSmart(new \Ease\Html\ATag('https://github.com/VitexSoftware/Shop4FlexiBee/issues',
            _('Issues')));

        $this->addItem($rowFluid1);

        $rowFluid2 = new \Ease\TWB\Row();

        $rowFluid2->addItem(new \Ease\TWB\Col(12,
            [new \Ease\TWB\Col(8, ''), new \Ease\TWB\Col(4,
                _('Version').': '.$appInfo->version.' '._('&copy; 2017 Vitex Software'))]));

        $this->addItem(new \Ease\TWB\Container($rowFluid2));
    }

    /**
     * Zobrazí přehled právě přihlášených a spodek stránky.
     */
    public function finalize()
    {
        if (isset($this->webPage->heroUnit) && !count($this->webPage->heroUnit->pageParts)) {
            unset($this->webPage->container->pageParts['\Ease\Html\DivTag@heroUnit']);
        }

        $this->includeCss('/javascript/font-awesome/css/font-awesome.min.css');
    }
}
