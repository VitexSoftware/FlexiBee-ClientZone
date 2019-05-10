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
    private $myKeywords = [
        "SEND_MAILS_FROM" => 'string',
        "SEND_INFO_TO" => 'string',
        "EMAIL_FROM" => 'string',
        "EASE_EMAILTO" => 'string',
        "SUPPRESS_EMAILS" => 'boolean',
        "ALLOW_REGISTER" => 'boolean',
        "SHOW_PRICELIST" => 'boolean',
        "DEBUG" => 'boolean',
        "CONFIGURED" => 'boolean',
        "EASE_LOGGER" => 'string',
        "FLEXIBEE_URL" => 'string',
        "FLEXIBEE_LOGIN" => 'string',
        "FLEXIBEE_PASSWORD" => 'string',
        "FLEXIBEE_COMPANY" => 'string'
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
        $this->settingsFile = $settingsFile;
        $this->setDefaults();
        $this->loadData();
    }

    public function setDefaults()
    {
        foreach ($this->myKeywords as $keyWord => $type) {
            switch ($type) {
                case 'boolean':
                    $this->setDataValue($keyWord, false);
                    break;
                default:
                    $this->setDataValue($keyWord, '');
                    break;
            }
        }
    }

    /**
     * Publish Options as constants
     */
    public function publish()
    {
        $shared = \Ease\Shared::instanced();
        foreach ($this->getData() as $configKey => $configValue) {
            $shared->setConfigValue($configKey, $configValue);
            if ((strtoupper($configKey) == $configKey) && (!defined($configKey))) {
                define($configKey, $configValue);
            }
        }
    }

    public function takeData($data): int
    {
        foreach ($data as $key => $value) {
            if (!array_key_exists($key, $this->myKeywords)) {
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
     * @return string
     */
    public function getJson()
    {
        return json_encode($this->getData(), JSON_PRETTY_PRINT);
    }
    
    /**
     * Save Configuration to file
     *  
     * @return boolean
     */
    public function saveData()
    {
        if (file_put_contents($this->settingsFile, $this->getJson())) {
            $this->addStatusMessage(sprintf(_('Configuration file %s was saved'),
                    $this->settingsFile), 'success');
            return true;
        } else {
            $this->addStatusMessage(sprintf(_('Configuration file %s was not saved'),
                    $this->settingsFile), 'error');
        }
    }
}
