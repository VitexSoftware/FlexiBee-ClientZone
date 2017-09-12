<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Shop4FlexiBee\ui;

/**
 * Description of LinkToFlexiBeeButton
 *
 * @author vitex
 */
class LinkToFlexiBeeButton extends \Ease\TWB\LinkButton
{

    /**
     *
     * @param \FlexiPeeHP\FlexiBeeRO $flexibee
     */
    public function __construct($flexibee, $properties = [])
    {
        parent::__construct($flexibee->getFlexiBeeURL(),
            [new \Ease\Html\ImgTag('images/flexibee.png', _('Open in FlexiBee'),
        $properties), ' ', \FlexiPeeHP\EvidenceList::$name[$flexibee->getEvidence()].' <strong>'.str_replace('code:',
                '', $flexibee).'</strong>'], 'warning');
    }

}
