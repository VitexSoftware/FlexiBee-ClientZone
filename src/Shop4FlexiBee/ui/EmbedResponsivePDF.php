<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Shop4FlexiBee\ui;

/**
 * Description of EmbedResponsive
 *
 * @author vitex
 */
class EmbedResponsivePDF extends \Ease\Html\Div
{
    public function __construct($url)
    {
        parent::__construct('<object data=\''.$url.'\' type=\'application/pdf\' width=\'100%\' height=\'100%\'></object>',
            ['class' => 'embed-responsive', 'style' => 'padding-bottom:150%']);
    }

    public function finalize()
    {
        $this->addCSS('
.embed-responsive {
    position: relative;
    display: block;
    height: 0;
    padding: 0;
    overflow: hidden;
}
');
        parent::finalize();
    }

}
