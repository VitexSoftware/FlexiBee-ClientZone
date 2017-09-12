<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Shop4FlexiBee\ui;

/**
 * Description of DownloadInvoiceButton
 *
 * @author vitex
 */
class DownloadInvoiceButton extends \Ease\TWB\LinkButton
{

    public function __construct($document)
    {
        parent::__construct('getpdf.php?evidence='.$document->getEvidence().'&id='.$document->getMyKey(),
            new \Ease\TWB\GlyphIcon('download').' '.sprintf(_('Download Your %s'),
                '<strong>'.$document->getDataValue('poznam').' '.
                $document->getDataValue('kod').'</strong>'), 'success');
    }
}
