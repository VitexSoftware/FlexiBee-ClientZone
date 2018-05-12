<?php
/**
 * clientzone - Formulář úhrady faktury.
 *
 * @author     Vítězslav Dvořák <vitex@arachne.cz>
 * @copyright  2017 VitexSoftware v.s.cz
 */

namespace ClientZone\ui;

class SettleForm extends ColumnsForm
{

    /**
     * Formulář kasy.
     *
     * @param type $transaction
     */
    public function __construct($transaction)
    {
        $this->setTagID('CashForm');
        parent::__construct($transaction);

        $this->addInput(new AdresarSelect('klient'), _('Zákazník'),
            _('Jaroslav Vomáčka'),
            _('Po zadání dvou písmen budou nabídnuty možnosti'));

        $this->addItem(new \Ease\Html\InputHiddenTag('firma'));

        $this->addInput(new FakturaVydanaNezaplacenaSelect(), _('Faktura'),
            null, _('Nejprve vyber Zákazníka'), null);

        $this->newRow();

        $this->addInput(new PokladnaSelect('pokladna'), _('Kasa'));

        parent::addInput(
            new \Ease\Html\InputTextTag(
            'sumCelkem', $transaction->getDataValue('sumCelkem')
            ), _('Celkem')
        );
        parent::addInput(
            new \Ease\Html\InputTextTag('popis',
            $transaction->getDataValue('popis')
            ), _('Popis')
        );

        $this->addItem(new \Ease\Html\InputHiddenTag('typPohybuK',
            $transaction->getDataValue('typPohybuK')));
    }

    public function finalize()
    {
        \Ease\Shared::webPage()->addJavascript('

$(\'input[name="klient"]\').bind(\'typeahead:select\', function(ev, suggestion) {
    nabidkafaktur( suggestion.id );
    $("[name=\'firma\']").val(suggestion.id);
});

  $.validator.addMethod(\'integer\', function(value, element, param) {
            return (value != 0) && (value == parseFloat(value));
        }, \'Prosím vlož nenulovou částku!\');


             $("#'.$this->getTagID().'").validate({
                 rules: {
                    klient: {
                        required: true
                    },
                    popis: {
                        required: true
                    },
                    sumCelkem: {
                        required: true,
                        integer: true,
                        number: true
                    }
                 },
 });
            ');

        return parent::finalize();
    }
}