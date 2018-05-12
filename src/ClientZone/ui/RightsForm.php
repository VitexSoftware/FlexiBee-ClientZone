<?php
/**
 * Description of SysRightsForm.
 *
 * @author vitex
 */

namespace ClientZone\ui;

class RightsForm extends \Ease\TWB\Form
{
    public $allUsers;

    public function finalize()
    {
        $rights         = [];
        $this->allUsers = \Ease\Shared::user()->getAllFromSQL();

        $rightsTable = new \Ease\Html\TableTag();

        $rightsHeader = ['login' => _('Uživatel')];

        $rightsTable->addRowHeaderColumns($rightsHeader);

        foreach ($this->allUsers as $user) {
            $rightsRow['login'] = $user['login'];
            $rightsTable->addRowColumns($rightsRow);
        }

        $this->addItem($rightsTable);
    }
}