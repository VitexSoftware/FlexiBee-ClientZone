<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace ClientZone;

/**
 * Description of Configurator
 *
 * @author Vítězslav Dvořák <info@vitexsoftware.cz>
 */
class Configurator extends \Ease\Brick
{
    /**
     *
     * @var string 
     */
    public $settingsFile = 'settings.json';

    /**
     * Saves obejct instace (singleton...).
     *
     * @var Shared
     */
    private static $_instance = null;

    /**
     *
     * @var array 
     */
    private $myKeywords       = [
        "SEND_MAILS_FROM",
        "SEND_INFO_TO",
        "EMAIL_FROM",
        "EASE_EMAILTO",
        "SUPPRESS_EMAILS",
        "ALLOW_REGISTER",
        "SHOW_PRICELIST",
        "DEBUG",
        "CONFIGURED",
        "EASE_LOGGER",
        "FLEXIBEE_URL",
        "FLEXIBEE_LOGIN",
        "FLEXIBEE_PASSWORD",
        "FLEXIBEE_COMPANY"
    ];

    /**
     * 
     * @param string $settingsFile
     * @param array $intialData
     */
    public function __construct($settingsFile = '../settings.json',
                                $intialData = [])
    {
        parent::__construct();
        $this->loadData();
    }

    public function takeData($data): int
    {
        foreach ($data as $key => $value) {
            if (!in_array($key, $this->myKeywords)) {
                unset($data[$key]);
            }
        }
        return parent::takeData($data);
    }

    /**
     * Pri vytvareni objektu pomoci funkce singleton (ma stejne parametry, jako konstruktor)
     * se bude v ramci behu programu pouzivat pouze jedna jeho Instance (ta prvni).
     *
     * @param string $class název třídy jenž má být zinstancována
     *
     * @link   http://docs.php.net/en/language.oop5.patterns.html Dokumentace a priklad
     *
     * @return \Ease\Shared
     */
    public static function singleton($class = null)
    {
        if (!isset(self::$_instance)) {
            if (is_null($class)) {
                $class = __CLASS__;
            }
            self::$_instance = new $class();
        }

        return self::$_instance;
    }

    /**
     * 
     * @return int
     */
    public function loadData()
    {
        if (file_exists($this->settingsFile)) {
            return $this->takeData(json_decode(file_get_contents($this->settingsFile),
                        true));
        }
    }

    /**
     * 
     * @return int
     */
    public function saveData()
    {
        return file_put_contents($this->settingsFile,
            json_encode($this->getData(), JSON_PRETTY_PRINT));
    }
}
