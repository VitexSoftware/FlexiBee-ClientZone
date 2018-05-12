<?php
/**
 * clientzone - Reminder class
 *
 * @author     Vítězslav Dvořák <vitex@arachne.cz>
 * @copyright  2017 VitexSoftware v.s.cz
 */

namespace ClientZone;

/**
 * Description of Upominka
 *
 * @author vitex
 */
class Upominac extends \FlexiPeeHP\FlexiBeeRW
{
    /**
     *
     * @var Customer
     */
    public $customer = null;

    /**
     * Invoice
     * 
     * @var \FlexiPeeHP\FakturaVydana
     */
    public $invoicer = null;

    /**
     * FlexiBee => LMS id mapping
     *
     * @var array
     */
    public $lmsID = [];

    /**
     * LMS IDs to remove dashboard
     * @var array
     */
    public $lmsIDtoEnable = [];

    /**
     * Reminder
     * 
     * @param array $init
     * @param array $options
     */
    public function __construct($init = null, $options = array())
    {
        parent::__construct($init, $options);
        $this->customer = new Customer();
        $this->invoicer = new \FlexiPeeHP\FakturaVydana();
    }

    /**
     * Obtain customer debths Array
     *
     * @return Customer
     */
    public function getDebths()
    {
        $allDebts  = [];
        $this->addStatusMessage(_('Getting clients'), 'debug');
        $clients   = $this->customer->getCustomerList();
        $debtCount = 0;

        $this->addStatusMessage(sprintf(_('%s Clients Found'), count($clients)));
        $this->addStatusMessage(_('Getting debts'), 'debug');
        foreach ($clients as $cid => $clientIDs) {
            $stitky = $clientIDs['stitky'];
            if (array_key_exists('external-ids', $clientIDs) && strstr($clientIDs['external-ids'][0],
                    'ext:lms.cstmr:')) {
                $this->lmsID[$cid] = str_replace('ext:lms.cstmr:', '',
                    $clientIDs['external-ids'][0]);
            }

            $debts = $this->customer->getCustomerDebts((int) $clientIDs['id']);
            if (count($debts)) {
                foreach ($debts as $did => $debtInfo) {
                    $allDebts[$cid][$did] = $debtInfo;
                    $debtCount++;
                }
            } else { //All OK
                if (isset($this->lmsID[$cid])) {
                    $this->lmsIDtoEnable[$cid] = $this->lmsID[$cid];
                }
                $this->enableCustomer($stitky, $cid);
            }
        }
        $this->addStatusMessage(sprintf(_('%s Debts Found'), $debtCount));
        return $allDebts;
    }

    /**
     * Enable customer
     *
     * @param string $stitky Labels
     * @param int    $cid    FlexiBee AddressID
     *
     * @return boolean Customer connect status
     */
    function enableCustomer($stitky, $cid)
    {
        $result = true;
        if (strstr($stitky, 'UPOMINKA') || strstr($stitky, 'NEPLATIC') || strstr($stitky,
                'ODPOJENO')) {
            $newStitky = array_combine(explode(',',
                    str_replace(' ', '', $stitky)), explode(',', $stitky));
            unset($newStitky['UPOMINKA1']);
            unset($newStitky['UPOMINKA2']);
            unset($newStitky['UPOMINKA3']);
            unset($newStitky['ODPOJENO']);
            unset($newStitky['NEPLATIC']);

            if ($this->customer->adresar->insertToFlexiBee(['id' => $cid, 'stitky@removeAll' => 'true',
                    'stitky' => $newStitky])) {
                $this->addStatusMessage(sprintf(_('Clear %s Remind labels'),
                        $cid), 'success');
            } else {
                $this->addStatusMessage(sprintf(_('Clear %s Remind labels'),
                        $cid), 'error');
                $result = false;
            }
        }
        return $result;
    }

    /**
     * Process All Debts of All Customers
     *
     * @return int All Debts count
     */
    public function processAllDebts()
    {
        $dasboardIDs = ['dashboard' => [], 'disconnect' => []];

        $allDebths = $this->getDebths();
        $this->addStatusMessage(sprintf(_('%d clients to remind process'),
                count($allDebths)));
        $counter   = 0;
        foreach ($allDebths as $cid => $debts) {
            $counter++;
            $this->customer->adresar->loadFromFlexiBee($cid);
            $this->addStatusMessage(sprintf(_('(%d / %d) #%s LMS# %s %s '),
                    $counter, count($allDebths),
                    $this->customer->adresar->getDataValue('id'),
                    $this->customer->adresar->getDataValue('kod'),
                    $this->customer->adresar->getDataValue('nazev')), 'debug');

            $zewlScore = $this->processUserDebts($cid, $debts);

            $stitky = $this->customer->adresar->getDataValue('stitky');

            $remindAs = $this->prepareDashboard($cid, $zewlScore, $stitky);
            if (!isset($this->lmsID[$cid])) {
                $this->addStatusMessage(sprintf(_('User %s not present in LMS; cannot set dashboard '),
                        $cid));
                continue;
            }

            $dasboardIDs[$remindAs][$cid] = $this->lmsID[$cid];
        }
        if (count($dasboardIDs['dashboard'])) {
            $this->setDashboards($dasboardIDs['dashboard']);
        }
        if (count($dasboardIDs['disconnect'])) {
            $this->setDisconnect($dasboardIDs['disconnect']);
        }
        if (count($this->lmsIDtoEnable)) {
            $this->setConnect($this->lmsIDtoEnable);
        }
        return $counter;
    }

    /**
     * Prepare remind to Sent
     *
     * @param int    $cid    FlexiBee address ID
     * @param int    $score  Zewl Score
     * @param string $stitky Labels divided by columns
     * 
     * @return string Operation code connect|dashboard|disconnect
     */
    function prepareDashboard($cid, $score, $stitky)
    {
        $operation = 'connect';
        switch ($score) {
            case 0:
                $this->enableCustomer($stitky, $cid);
                break;
            case 1:
                break;
            case 2:
                if (!strstr($stitky, 'VIP') && !strstr($stitky, 'NODASHBOARD') && !strstr($stitky,
                        'PROFI') && !strstr($stitky, 'ZAINTERESOVANI')) {
                    $this->customer->adresar->insertToFlexiBee(['id' => $cid, 'stitky' => 'NEPLATIC']);
                    $operation = 'dashboard';
                    $this->addStatusMessage(sprintf(_('Dashboard for: %d '),
                            $cid));
                }
                break;
            case 3:
                if (!strstr($stitky, 'VIP') && !strstr($stitky, 'NODISCONNECT') && !strstr($stitky,
                        'NEODPOJOVAT') && !strstr($stitky, 'PROFI')) {
                    $this->customer->adresar->insertToFlexiBee(['id' => $cid, 'stitky' => 'ODPOJENO']);
                    $operation = 'disconnect';
                    $this->addStatusMessage(sprintf(_('Disconnect for: %d '),
                            $cid));
                }
                break;
        }
        return $operation;
    }

    /**
     * Process Customer debts
     *
     * @param int   $cid         FlexiBee Address (Customer) ID
     * @param array $clientDebts Array provided by customer::getCustomerDebts()
     *
     * @return int max debt score 1: 0-7 days 1: 8-14 days 3: 15 days and more
     */
    public function processUserDebts($cid, $clientDebts)
    {
        $zewlScore      = 0;
        $stitky         = $this->customer->adresar->getDataValue('stitky');
        $ddifs          = [];
        $invoicesToSave = [];
        $invoicesToLock = [];
        foreach ($clientDebts as $did => $debt) {
            switch ($debt['zamekK']) {
                case 'zamek.zamceno':
                    $this->invoicer->dataReset();
                    $this->invoicer->setMyKey($did);
                    $unlock = $this->invoicer->performAction('unlock', 'int');
                    if ($unlock['success'] == 'false') {
                        $this->addStatusMessage(_('Invoice locked: skipping process'),
                            'warning');
                        break;
                    }
                    $invoicesToLock[$debt['id']] = ['id' => $did];
                case 'zamek.otevreno':
                default:

                    $invoicesToSave[$debt['id']] = ['id' => $did];
                    $ddiff                       = self::poSplatnosti($debt['datSplat']);
                    $ddifs[$debt['id']]          = $ddiff;

                    if (($ddiff <= 7) && ($ddiff >= 1)) {
                        $zewlScore = self::maxScore($zewlScore, 1);
                    } else {
                        if (($ddiff > 7 ) && ($ddiff <= 14)) {
                            $zewlScore = self::maxScore($zewlScore, 2);
                        } else {
                            if ($ddiff > 14) {
                                $zewlScore = self::maxScore($zewlScore, 3);
                            }
                        }
                    }

                    break;
            }
        }

        if ($zewlScore == 3 && !strstr($stitky, 'UPOMINKA2')) {
            $zewlScore = 2;
        }

        if (!strstr($stitky, 'UPOMINKA1')) {
            $zewlScore = 1;
        }
        if ($zewlScore > 0 && (array_sum($ddifs) > 0) && count($invoicesToSave)) {
            if (!strstr($stitky, 'UPOMINKA'.$zewlScore)) {
                if (!strstr($stitky, 'NEUPOMINKOVAT')) {
                    if ($this->posliUpominku($zewlScore, $cid, $clientDebts)) {
                        foreach ($invoicesToSave as $invoiceID => $invoiceData) {
                            switch ($zewlScore) {
                                case 1:
                                    $colname = 'datUp1';
                                    break;
                                case 2:
                                    $colname = 'datUp2';
                                    break;
                                case 3:
                                    $colname = 'datSmir';
                                    break;
                            }
                            $invoiceData[$colname] = self::timestampToFlexiDate(time());
                            if ($this->invoicer->insertToFlexiBee($invoiceData)) {
                                $this->addStatusMessage(sprintf(_('Invoice %s remind %s date saved'),
                                        $invoiceID, $colname), 'info');
                            } else {
                                $this->addStatusMessage(sprintf(_('Invoice %s remind %s date save failed'),
                                        $invoiceID, $colname), 'error');
                            }
                        }
                    }
                } else {
                    $this->addStatusMessage(_('Remind send disbled'));
                }
            } else {
                $this->addStatusMessage(sprintf(_('Remind %d already sent'),
                        $zewlScore));
            }
        } else {
            $this->addStatusMessage(_('No debts to remind'), 'debug');
        }

        if (count($invoicesToLock)) {
            foreach ($invoicesToLock as $invoiceID => $invoiceData) {
                $this->invoicer->dataReset();
                $this->invoicer->setMyKey($did);
                $lock = $this->invoicer->performAction('lock', 'int');
                if ($lock['success'] == 'true') {
                    $this->addStatusMessage(sprintf(_('Invoice %s locked again'),
                            $invoiceID), 'info');
                } else {
                    $this->addStatusMessage(sprintf(_('Invoice %s locking failed'),
                            $invoiceID), 'error');
                }
            }
        }

        return $zewlScore;
    }

    /**
     * Obtain Customer "Score"
     *
     * @param int $addressID FlexiBee user ID
     * 
     * @return int ZewlScore
     */
    public function getCustomerScore($addressID)
    {
        $score     = 0;
        $debts     = $this->customer->getCustomerDebts($addressID);
        $stitkyRaw = $this->customer->adresar->getColumnsFromFlexiBee(['stitky'],
            ['id' => $addressID]);
        $stitky    = $stitkyRaw[0]['stitky'];
        if (count($debts)) {
            foreach ($debts as $did => $debt) {
                $ddiff = self::poSplatnosti($debt['datSplat']);

                if (($ddiff <= 7) && ($ddiff >= 1)) {
                    $score = self::maxScore($score, 1);
                } else {
                    if (($ddiff > 7 ) && ($ddiff <= 14)) {
                        $score = self::maxScore($score, 2);
                    } else {
                        if ($ddiff > 14) {
                            $score = self::maxScore($score, 3);
                        }
                    }
                }
            }
        }
        if ($score == 3 && !strstr($stitky, 'UPOMINKA2')) {
            $score = 2;
        }

        if (!strstr($stitky, 'UPOMINKA1') && count($debts)) {
            $score = 1;
        }

        return $score;
    }

    /**
     * Send remind
     *
     * @param int   $score       ZewlScore
     * @param int   $cid         FlexiBee address (customer) ID
     * @param array $clientDebts Array provided by customer::getCustomerDebts()
     * 
     * @return boolean
     */
    public function posliUpominku($score, $cid, $clientDebts)
    {
        $result   = false;
        $upominka = new Upominka();
        switch ($score) {
            case 1:
                $upominka->loadTemplate('prvniUpominka');
                break;
            case 2:
                $upominka->loadTemplate('druhaUpominka');
                break;
            case 3:
                $upominka->loadTemplate('pokusOSmir');
                break;
        }
        if ($upominka->compile($cid, $clientDebts)) {
            $result = $upominka->send();
        } else {
            $this->addStatusMessage(_('UPOMINKA NEBYLA ODESLANA!!!!'), 'warning');
        }
        $this->customer->adresar->insertToFlexiBee(['id' => $cid, 'stitky' => 'UPOMINKA'.$score]);
        $this->customer->adresar->loadFromFlexiBee($cid);
        $this->addStatusMessage(sprintf(_('Set Label %s '), 'UPOMINKA'.$score));
        return $result;
    }

    /**
     * Set Dashoards
     *
     * @param array $userIDs
     */
    public function setDashboards($userIDs)
    {
        $this->addStatusMessage(sprintf(_('Set dashboards for %d customers'),
                count($userIDs)));
        $sysStr = constant('DASHBOARD_SCRIPTS')."set-dashboards.py neplatic ".implode(' ',
                $userIDs);
        $this->addStatusMessage($sysStr, 'debug');
        system("sudo $sysStr");
    }

    /**
     * Set Disconnect
     *
     * @param array $userIDs
     */
    public function setDisconnect($userIDs)
    {
        $this->addStatusMessage(sprintf(_('Disconnect %d customers'),
                count($userIDs)));
        $sysStr = constant('DASHBOARD_SCRIPTS')."set-dashboards.py odpojen ".implode(' ',
                $userIDs);
        system("sudo $sysStr");
        $this->addStatusMessage($sysStr, 'debug');
    }

    /**
     * Connect Again
     *
     * @param array $userIDs
     */
    public function setConnect($userIDs)
    {
        $this->addStatusMessage(sprintf(_('Connect %d customers'),
                count($userIDs)));
        $sysStr = constant('DASHBOARD_SCRIPTS')."reset-dashboards.py  ".implode(' ',
                $userIDs);
        system("sudo $sysStr");
        $this->addStatusMessage($sysStr, 'debug');
    }

    /**
     * Overdue group
     *
     * @param int $score current score value
     * @param int $level current level
     *
     * @return int max of all levels processed
     */
    static private function maxScore($score, $level)
    {
        if ($level > $score) {
            $score = $level;
        }
        return $score;
    }

    /**
     * Get Number of days overdue
     * 
     * @param string $dueDate FlexiBee date
     * @return int
     */
    static public function poSplatnosti($dueDate)
    {
        $dateDiff = date_diff(\FlexiPeeHP\FlexiBeeRO::flexiDateToDateTime($dueDate),
            new \DateTime());

        if ($dateDiff->invert == 1) {
            $ddif = $dateDiff->days * -1;
        } else {
            $ddif = $dateDiff->days;
        }

        return $ddif;
    }
}
