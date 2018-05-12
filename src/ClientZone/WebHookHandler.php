<?php

namespace ClientZone;

/**
 * Description of WebHookHandler
 *
 * @author vitex
 */
class WebHookHandler extends \FlexiPeeHP\FlexiBeeRW
{
    /**
     * Sql Tabe to store changes
     * @var string
     */
    public $myTable = 'flexihistory';

    /**
     * Save Change time to column
     * @var string
     */
    public $myCreateColumn = 'when';

    /**
     * Current processed Change id
     * @var int
     */
    public $changeid = null;

    /**
     * WebHook API operation
     * @var string create|update|delete
     */
    public $operation = null;

    /**
     * External IDs for current record
     * @var array 
     */
    public $extids = [];

    /**
     * Handle Incoming change
     *
     * @param int $id changed record id
     * @param array $options 
     */
    public function __construct($id, $options)
    {
        parent::__construct($id, $options);
    }

    public function saveHistory()
    {
        $change = [
            'operation' => $this->operation,
            'evidence' => $this->getEvidence(),
            'recordid' => $this->getMyKey(),
            'json' => $this->dblink->addslashes(json_encode($this->getData()))];

        if ($this->changeid) {
            $change['changeid'] = $this->changeid;
        }

        return $this->insertToSQL($change);
    }

    /**
     * SetUp Object to be ready for connect
     *
     * @param array $options Object Options (company,url,user,password,evidence,
     *                                       prefix,defaultUrlParams,debug)
     */
    public function setUp($options = [])
    {
        parent::setUp($options);
        if (isset($options['changeid'])) {
            $this->changeid = $options['changeid'];
        }
        if (isset($options['operation'])) {
            $this->setOperation($options['operation']);
            if ($options['operation'] == 'delete') {
                $this->ignore404(true);
            }
        }
        if (isset($options['external-ids'])) {
            $this->extids = $options['external-ids'];
        }
    }

    /**
     *
     * @param type $operation
     */
    public function setOperation($operation)
    {
        $this->operation = $operation;
    }

    /**
     *
     */
    public function process($operation)
    {
        $result = false;
        switch ($operation) {
            case 'create':
                $result = $this->create();
                break;
            case 'update':
                $result = $this->update();
                break;
            case 'delete':
                $result = $this->delete();
                break;
            default:
                $this->addToLog(sprintf('Unknown operation %s', $operation),
                    'warning');
                break;
        }
        return $result;
    }

    /**
     *
     */
    public function create()
    {
        $this->addStatusMessage(_('No Update Action Defined'), 'debug');
        return null;
    }

    /**
     *
     */
    public function update()
    {
        $this->addStatusMessage(_('No Update Action Defined'), 'debug');
        return null;
    }

    /**
     *
     */
    public function delete()
    {
        $this->addStatusMessage(_('No Delete Action Defined'), 'debug');
        return null;
    }

    public function getCurrentData()
    {
        $dataRaw = $this->getColumnsFromFlexibee('*', $this->getMyKey());
        return count($dataRaw) ? $dataRaw[0] : null;
    }

    public function getPreviousData()
    {
        $prevData   = null;
        $lastChange = $this->dblink->queryToArray('SELECT json FROM '.$this->getMyTable().' WHERE evidence='.$this->getEvidence().' AND recordid='.$this->getMyKey().'  ORDER BY id DESC LIMIT 1,1');
        if (count($lastChange)) {
            $prevData = json_decode($lastChange['json']);
        }
        return $lastChange;
    }

    public function getChanges()
    {
        $previous = $this->getPreviousData();
        if (empty($previous)) {
            $previous = $this->getData();
        } else {
            $previous = array_diff($this->getData(), $previous);
        }
        return $previous;
    }

    public function getChangedData()
    {
        
    }
}
