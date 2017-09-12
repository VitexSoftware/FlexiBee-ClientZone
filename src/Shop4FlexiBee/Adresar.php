<?php
/**
 * shop4flexibee - Objekt adresáře.
 *
 * @author     Vítězslav Dvořák <vitex@arachne.cz>
 * @copyright  2017 VitexSoftware v.s.cz
 */

namespace Shop4FlexiBee;

class Adresar extends Importer\Importer
{
    public $keyword     = 'klient';
    public $evidence    = 'adresar';
    public $useKeywords = [
        'id' => 'STRING',
        'lastUpdate' => 'DATETIME',
        'kod' => 'STRING',
        'nazev' => 'STRING',
//      "nazevA" => 'STRING',
//      "nazevB" => 'STRING',
//      "nazevC" => 'STRING',
        'poznam' => 'STRING',
//      "popis" => 'STRING',
//      "platiOd" => "INT",
//      "platiDo" => "INT",
//      "ulice" => "STRING",
//      "mesto" => "STRING",
//      "psc" => 'STRING',
//      "tel" => 'STRING',
//      "mobil" => 'STRING',
//      "fax" => 'STRING',
        'email' => 'STRING',
//      "www" => 'STRING',
//      "stat" => "STRING",
////      "stat@showAs" => "Česká republika",
////      "stat@ref" => "/c/spoje_net_s_r_o_/stat/39.json",
//      "eanKod" => 'STRING',
        'ic' => 'STRING',
        'dic' => 'STRING',
//      "postovniShodna" => "true",
//      "faEanKod" => 'STRING',
//      "faJmenoFirmy" => 'STRING',
        'faUlice' => 'STRING',
        'faMesto' => 'STRING',
        'faPsc' => 'STRING',
//      "splatDny" => 'STRING',
//      "limitFak" => "STRING",
//      "limitPoSplatDny" => 'STRING',
//      "limitPoSplatZakaz" => 'BOOLEAN',
//      "platceDph" => 'BOOLEAN',
//      "typVztahuK" => "IDKEY",
////      "typVztahuK@showAs" => "STRING",
//      "kodPojistovny" => 'STRING',
//      "nazevPojistovny" => 'STRING',
//      "osloveni" => 'STRING',
//      "slevaDokl" => "FLOAT",
//      "obpAutomHotovo" => "BOOLEAN",
//      "nazev2" => "NazevII2",
//      "nazev2A" => 'STRING',
//      "nazev2B" => 'STRING',
//      "nazev2C" => 'STRING',
//      "nespolehlivyPlatce" => 'BOOLEAN',
//      "revize" => 'INT',
//      "stitky" => 'STRING',
//      "katastrUzemi" => 'STRING',
//      "parcela" => 'STRING',
//      "datNaroz" => 'STRING',
//      "rodCis" => 'STRING',
//      "datZaloz" => 'STRING',
//      "canceled" => 'BOOLEAN',
        'skupFir' => 'STRING',
//      "stredisko" => 'STRING',
//      "faStat" => 'STRING',
//      "zodpOsoba" => 'STRING',
//      "skupCen" => 'STRING',
//      "formaUhradyCis" => 'INT',
//      "kontakty" => 'ARRAY',
//      "mistaUrceni" => 'ARRAY'
    ];
    public $renameRules = ['id',
        'lastname' => '',
        'name' => '',
        'status' => '',
        'type' => '',
        'email' => 'email',
        'address' => 'faUlice',
        'zip' => 'faPsc',
        'city' => 'faMesto',
        'countryid' => '',
        'ten' => 'dic',
        'ssn' => 'ic',
        'regon' => '',
        'rbe' => '',
        'icn' => '',
        'info' => '',
        'notes' => 'poznam',
        'serviceaddr' => '',
        'creationdate' => '',
        'moddate' => '',
        'creatorid' => '',
        'modid' => '',
        'deleted' => 'canceled',
        'message' => '',
        'pin' => '',
        'cutoffstop' => '',
        'pay_timeout' => '',
        'consentdate' => '',
        'divisionid' => '',
        'paytime' => '',
        'paytype' => '',
        'node' => '',
        'varsym' => '',
        'cust_person' => '',
        'direct' => '',
        'prefix' => '',
        'suffix' => '',
        'notes_unlimited' => '',
        'autopenal' => '',
        'credit' => '',
    ];

    /**
     * External records identifier.
     *
     * @var type
     */
    public $foreginKeyword = 'lms.addr';

    /**
     * Převede data adresáře.
     *
     * @param array $data
     *
     * @return array
     */
    public function convertRow($data = null)
    {
        if (is_null($data)) {
            $data = $this->getData();
        }
        $converted                    = parent::convertRow($data);
        $converted['kod']             = $data[$this->myKeyColumn];
        $converted[$this->nameColumn] = trim(ucfirst($data['name']).' '.ucfirst($data['lastname']));

        return $converted;
    }
}