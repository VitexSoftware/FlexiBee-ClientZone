<?php

namespace ClientZone;

/**
 * Description of Upominka
 *
 * @author vitex
 */
class Upominka extends \FlexiPeeHP\FlexiBeeRW
{
    public $evidence = 'sablona-upominky';
    public $mailer   = null;
    public $firmer   = null;

    /**
     * Invoice
     * @var \FlexiPeeHP\FakturaVydana
     */
    public $invoicer = null;

    public function __construct($init = null, $options = array())
    {
        parent::__construct($init, $options);

        $this->invoicer = new \FlexiPeeHP\FakturaVydana();
        $this->firmer   = new \FlexiPeeHP\Adresar();
    }

    /**
     * Load
     * @param string $template prvniUpominka|druhaUpominka|pokusOSmir|inventarizace
     */
    public function loadTemplate($template)
    {
        $this->takeData(current($this->getColumnsFromFlexibee('*',
                    ['typSablonyK' => 'typSablony.'.$template])));
    }

    /**
     * 
     * @param int $cid
     * @param array $clientDebts
     */
    public function compile($cid, $clientDebts)
    {
        $result  = false;
        $kontakt = $this->firmer->getColumnsFromFlexibee(['nazev', 'email'],
            ['id' => $cid]);

        if (strlen($kontakt[0]['email'])) {
            $sumCelkem = 0;
            $invoices  = [];
            foreach ($clientDebts as $debt) {
                $sumCelkem += $debt['sumCelkem'];

                $ddiff      = Upominac::poSplatnosti($debt['datSplat']);
                $invoices[] = $debt['kod'].' v.s.: '.$debt['varSym'].' '.$debt['sumCelkem'].' '.str_replace('code:',
                        '',
                        $debt['mena'].'  '.\FlexiPeeHP\FlexiBeeRO::flexiDateToDateTime($debt['datSplat'])->format('d.m.Y').' ('.$ddiff.' dní po splatnosti)');
            }

            $to = $kontakt[0]['email'];
            $to .= ',fakturace@spoje.net';

            $dnes    = new \DateTime();
            $subject = $this->getDataValue('hlavicka').' ke dni '.$dnes->format('d.m.Y');
            $body    = $this->getDataValue('uvod').
                "\n\n".$this->getDataValue('textNad')."\n\n".
                sprintf(_("%s \n\n-----------------------\n celkem za %s,-"),
                    implode("\n", $invoices), $sumCelkem).
                "\n\n".$this->getDataValue('textPod')."\n".
                "\n\n".$this->getDataValue('zapati')."\n";


            if (defined('SUPPRESS_EMAILS') && (constant('SUPPRESS_EMAILS') == 'true')) {
                $body = "OriginalTO: ".$to."\n\n ".$body;
                $to   = constant('EASE_EMAILTO');
            }

            $this->mailer = new \Ease\Mailer($to, $subject, $body);
            $this->addAttachments($clientDebts);
            $result       = true;
        } else {
            $this->addStatusMessage(sprintf(_('Klient %s nema email - HELPDESK RESIT %s !!!'),
                    $kontakt[0]['nazev'],
                    $this->firmer->getEvidenceURL('adresar/'.$cid)), 'error');
        }
        return $result;
    }

    /**
     *
     * @param array $clientDebts
     */
    public function addAttachments($clientDebts)
    {
        foreach ($clientDebts as $debt) {
            $this->invoicer->setMyKey($debt['id']);
            $this->mailer->addFile($this->invoicer->downloadInFormat('pdf',
                    '/tmp/'),
                \FlexiPeeHP\Formats::$formats['PDF']['content-type']);
            $this->mailer->addFile($this->invoicer->downloadInFormat('isdocx',
                    '/tmp/'),
                \FlexiPeeHP\Formats::$formats['ISDOCx']['content-type']);
        }
    }

    /**
     * Send Remind
     * 
     * @return boolean
     */
    public function send()
    {
        return $this->mailer->send();
    }
}
