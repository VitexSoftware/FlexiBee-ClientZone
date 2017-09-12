<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Shop4FlexiBee\ui;

/**
 * Description of LabelGroup
 *
 * @author vitex
 */
class LabelGroup extends \Ease\Html\Span
{

    /**
     * FlexiBee
     * 
     * @param \FlexiPeeHP\FlexiBeeRO $flexibee
     */
    public function __construct($flexibee)
    {
        $labels = [];
        $stitky = $flexibee->getDataValue('stitky');
        parent::__construct();
        if (count($stitky)) {
            $stitkyArr = explode(',', $stitky);
            foreach ($stitkyArr as $stitek) {
                $this->addItem(new \Ease\TWB\Label('info',
                    new \Ease\Html\ATag('labels.php?label='.trim($stitek).'&evidence='.$flexibee
                        ->getEvidence(), trim($stitek))));
            }
        }
    }
}
