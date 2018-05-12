<?php
/**
 * Description of SysFlexiBeeStatus.
 *
 * @author vitex
 */

namespace ClientZone;

class FlexiBeeStatus extends \FlexiPeeHP\Company
{
    /**
     * FlexiBee Status
     * @var array
     */
    public $info = null;

    /**
     * Try to connect to FlexiBee
     */
    public function __construct()
    {
        parent::__construct();
        $this->info = $this->getFlexiData();
    }

    /**
     * Draw result
     */
    public function draw()
    {
        if (count($this->info)) {
            $infos = self::reindexArrayBy($this->info, 'dbNazev');
            if (isset($infos[constant('FLEXIBEE_COMPANY')])) {
                $return = new \Ease\TWB\LinkButton(constant('FLEXIBEE_URL').'/c/'.constant('FLEXIBEE_COMPANY'),
                    $infos[constant('FLEXIBEE_COMPANY')]['nazev'], 'success');
            } else {
                $return = new \Ease\TWB\LinkButton(constant('FLEXIBEE_URL'),
                    _('Zvolená firma neexistuje'), 'warning');
            }
        } else {
            $return = new \Ease\TWB\LinkButton(constant('FLEXIBEE_URL'),
                _('Chyba komunikace'), 'danger');
        }
        $return->draw();
    }
}
