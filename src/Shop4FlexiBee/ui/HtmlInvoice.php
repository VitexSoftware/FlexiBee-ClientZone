<?php

namespace Shop4FlexiBee\ui;

/**
 * Description of htmlInvoice
 *
 * @author vitex
 */
class HtmlInvoice extends \Ease\Container
{

    /**
     * Gives you html FlexiBee invoice
     * 
     * @param \FlexiPeeHP\FlexiBeeRO $invoice
     */
    public function __construct($invoice)
    {
        $pdfFile = $invoice->downloadInFormat('pdf',
            '/var/lib/shop4flexibee/tmp/');
        if (strlen($pdfFile)) {
            \Gufy\PdfToHtml\Config::set('pdftohtml.output',
                '/var/lib/shop4flexibee/tmp');
            $pdf = new \Gufy\PdfToHtml\Pdf($pdfFile);
            preg_match("/<body[^>]*>(.*?)<\/body>/is", $pdf->html(),
                $initialContent);
        }
        parent::__construct($initialContent);
        //   \Ease\Shared::webPage()->includeCss('css/flexibee.css');
    }
}
