<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace ClientZone;

/**
 * Description of HostsManager
 *
 * @author vitex
 */
class HostsFileManager
{
    public $lines = [];

    /**
     *
     * @var \FlexiPeeHP\Adresar
     */
    public $adresar = null;

    public function __construct($hostsFile)
    {
        $this->parseHosts($hostsFile);
        $this->adresar = new \FlexiPeeHP\Adresar();
    }

    public function parseLine($lineRaw)
    {

        if (strlen($lineRaw)) {

            if ($lineRaw[0] == '#') {
                $lineInfo['commented'] = true;
                $lineRaw               = substr($lineRaw, 1);
            } else {
                $lineInfo['commented'] = false;
            }

            if (preg_match('/^(\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3})\s([a-z0-9\.\-\s]*)($|#.*)/',
                    $lineRaw, $matches)) {

                $lineInfo['type'] = 'host';
                $lineInfo['ip']   = $matches[1];

                if (strstr($matches[2], ' ')) {
                    $names                = explode(' ', $matches[2]);
                    $lineInfo['hostname'] = $names[0];
                    array_shift($names);
                    $lineInfo['aliases']  = $names;
                } else {
                    $lineInfo['hostname'] = $matches[2];
                }

                if (isset($matches[3]) && strlen(trim($matches[3]))) {
                    $lineInfo['flags'] = explode(' ', substr($matches[3], 1));
                }
            } else {
                $lineInfo['comment'] = $lineRaw;
                $lineInfo['type']    = 'comment';
            }
        } else {
            $lineInfo['type']      = 'empty';
            $lineInfo['commented'] = false;
        }
        return $lineInfo;
    }

    public function parseHosts($filename)
    {
        $fileRaw = file_get_contents($filename);
        $lines   = explode("\n", $fileRaw);
        foreach ($lines as $lineno => $linetext) {
            $this->lines[$lineno] = $this->parseLine(trim($linetext));
        }
    }

    public function getLmsID($hostFlags)
    {
        $lmsID = null;
        if (count($hostFlags)) {
            foreach ($hostFlags as $hostFlag) {
                if (strlen($hostFlag) && ($hostFlag[0] == '{')) {
                    $lmsID = substr($hostFlag, 1, strlen($hostFlag) - 2);
                    break;
                }
            }
        }
        return $lmsID;
    }

    public function updateHosts()
    {

    }

    public function addFlexiBeeID()
    {
        
    }

    public function compileHostData($hostData)
    {
        $lineraw = '';
        if ($hostData['commented'] === true) {
            $lineraw .= '#';
        }

        switch ($hostData['type']) {
            case 'comment':
                $lineraw .= $hostData['comment'];
                break;
            case 'host':
                $lineraw .= $hostData['ip']."\t".$hostData['hostname'];
                if (isset($hostData['alias'])) {
                    $lineraw .= ' '.implode(' ', $hostData['alias']);
                }
                if (isset($hostData['flags'])) {
                    $lineraw .= ' # '.implode(' ', $hostData['flags']);
                }
                break;
            case 'empty':
                break;
        }

        return $lineraw;
    }

    public function getCompiled()
    {
        $hostLines = [];
        foreach ($this->lines as $lineno => $hostdata) {
            $hostLines[$lineno] = $this->compileHostData($hostdata);
        }
        return implode("\n", $hostLines);
    }

    public function saveToFile($filename)
    {
        return(file_put_contents($filename, $this->getCompiled()));
    }
}
