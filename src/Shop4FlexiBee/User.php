<?php
/**
 * shop4flexibee - Uživatel.
 *
 * @author     Vítězslav Dvořák <vitex@arachne.cz>
 * @copyright  2017 VitexSoftware v.s.cz
 */

namespace Shop4FlexiBee;

/**
 * shop4flexibee User.
 */
class User extends \Ease\User
{
    public $disableColumn = false;
    public $useKeywords  = [
        'login' => 'STRING',
        'firstname' => 'STRING',
        'lastname' => 'STRING',
        'email' => 'STRING',
    ];
    public $keywordsInfo = [
      'login' => [],
      'firstname' => [],
      'lastname' => [],
      'email' => [],
    ];

    /**
     * Tabulka uživatelů.
     *
     * @var string
     */
    public $myTable = 'user';

    /**
     * Sloupeček obsahující datum vložení záznamu do shopu.
     *
     * @var string
     */
    public $createColumn = 'DatCreate';

    /**
     * Slopecek obsahujici datum poslení modifikace záznamu do shopu.
     *
     * @var string
     */
    public $lastModifiedColumn = 'DatSave';

    /**
     * Budeme používat serializovaná nastavení uložená ve sloupečku.
     *
     * @var string
     */
    public $settingsColumn = 'settings';

    /**
     * Klíčové slovo.
     *
     * @var string
     */
    public $keyword = 'user';

    /**
     * Jmenný sloupec.
     *
     * @var string
     */
    public $nameColumn = 'login';

    /**
     * Vrací odkaz na ikonu.
     *
     * @return string
     */
    public function getIcon()
    {
        $Icon = $this->GetSettingValue('icon');
        if (is_null($Icon)) {
            return parent::getIcon();
        } else {
            return $Icon;
        }
    }

    /**
     * Vrací ID aktuálního záznamu.
     *
     * @return int
     */
    public function getId()
    {
        return (int) $this->getMyKey();
    }

    /**
     * Vrací mazací tlačítko.
     *
     * @param string $name   jméno objektu
     * @param string $urlAdd Předávaná část URL
     *
     * @return \EaseJQConfirmedLinkButton
     */
    public function deleteButton($name = null, $urlAdd = '')
    {
        //        return new EaseJQConfirmedLinkButton('?user_id=' . $this->getID() . '&delete=true' . '&' . $urlAdd, _('Smazat ') . ' ' . $this->getUserLogin() . ' ' . \Ease\TWB\Part::GlyphIcon('remove-sign'));

        \Ease\Shared::webPage()->addItem(new IEConfirmationDialog('delete'.$this->getId(),
            '?user_id='.$this->getID().'&delete=true'.'&'.$urlAdd,
            _('Delete').' '.$name,
            sprintf(_('Are you sure %s ?'),
                '<strong>'.$this->getUserName().'</strong>')));

        return new \Ease\Html\ButtonTag(
            [\Ease\TWB\Part::GlyphIcon('remove'), _('Delete').' '.$this->keyword.' '.$this->getUserName()],
            ['style' => 'cursor: default', 'class' => 'btn btn-danger', 'id' => 'triggerdelete'.$this->getId(),
            'data-id' => $this->getId(),
        ]);
    }

    /**
     * Give you user name.
     *
     * @return string
     */
    public function getUserName()
    {
        $longname = trim($this->getDataValue('firstname') . ' ' . $this->getDataValue('lastname'));
        if (strlen($longname)) {
            return $longname;
        } else {
            return parent::getUserName();
        }
    }

    public function getEmail()
    {
        return $this->getDataValue('email');
    }

    /**
     * Vrací sql fragment pro vrácení SQL volání hlavního sloupečku.
     *
     * @return string
     */
    public function getKeySelect()
    {
        $keyColumn = $this->getKeyColumn();
        if (isset($this->keywordsInfo[$keyColumn]['select'])) {
            return $this->keywordsInfo[$keyColumn]['select'];
        }

        return $keyColumn;
    }

    /**
     * Vrací fragment SQL.
     *
     * @return string
     */
    public function getListingQuerySelect()
    {
        return 'SELECT * FROM ' . $this->dblink->getColumnComma() . $this->getMyTable() . $this->dblink->getColumnComma();
    }

    public function htmlizeData($data)
    {
        return $data;
    }

    public function handleUpload()
    {
        
    }

    /**
     * Místní nabídka uživatele.
     *
     * @return \\Ease\TWB\ButtonDropdown
     */
    public function operationsMenu()
    {
        $id     = $this->getId();
        $menu[] = new \Ease\Html\ATag($this->keyword.'.php?action=delete&'.$this->keyColumn.'='.$id,
            \Ease\TWB\Part::glyphIcon('remove').' '._('Delete'));
        $menu[] = new \Ease\Html\ATag($this->keyword.'.php?'.$this->keyColumn.'='.$id,
            \Ease\TWB\Part::glyphIcon('edit').' '._('Edit'));

        return new \Ease\TWB\ButtonDropdown(\Ease\TWB\Part::glyphIcon('cog'),
            'warning', '', $menu);
    }

}
